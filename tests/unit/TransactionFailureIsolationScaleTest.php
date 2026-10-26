<?php

declare(strict_types=1);

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use Tests\Support\Scale\ScaleDb;

/**
 * Section 7 - transaction failure isolation.
 *
 * A confirmation that fails part-way through must roll back every partial
 * write performed earlier in the same transaction: school-point details, the
 * is_finish flip, the rank/grade recomputation, publication - none may survive.
 *
 * The failure is injected deterministically in-process using two connections:
 *
 *  1. The model's connection begins its transaction and takes its REPEATABLE
 *     READ consistent snapshot BEFORE a ghost sd_result_time row is committed
 *     by a second connection (so the model can never see it via consistent
 *     reads).
 *  2. The model therefore takes the "insert" branch and allocates a candidate
 *     result_no that collides with the ghost row:
 *       - same item (#99652)  -> (sub_district_code, item_code) PRIMARY key
 *         -> 1062 on PRIMARY, not retryable -> immediate FALSE
 *       - same number (#99799)-> (sub_district_code, result_no) unique key
 *         -> bounded retries (each re-read of MAX returns the stale snapshot)
 *         -> FALSE
 *  3. Either way the caller receives FALSE and the transaction is rolled back.
 *
 * @internal audit tooling
 */
final class TransactionFailureIsolationScaleTest extends CIUnitTestCase
{
    private const SUB1    = 9105;
    private const ITEM1   = 99651;
    // The ghost shares the confirmed item's code, so the model's
    // stale-snapshot INSERT collides on the (sub_district_code, item_code)
    // PRIMARY key rather than the result_no unique key.
    private const SAME_ITEM_GHOST = self::ITEM1;

    private const SUB2    = 9106;
    private const ITEM2   = 99653;
    private const NUMBER_GHOST = 99799; // uk_sd_scope_result_no collision

    private const SUCCESS_SUB    = 9106;
    private const SUCCESS_ITEM   = 99654;
    private const SUCCESS_GHOST  = 99798;

    public function testSameItemConflictFailsAndRollsBackEverything(): void
    {
        $conn = ScaleDb::connect($this);
        $ghostConn = ScaleDb::connect($this);

        try {
            ScaleDb::seedSdItem($conn, self::SUB1, self::ITEM1);

            $conn->transBegin();
            try {
                // Fix the consistent snapshot BEFORE the ghost row exists.
                $conn->query('SELECT COUNT(*) FROM sd_result_time');
                $this->insertGhostRow($ghostConn, self::SUB1, self::SAME_ITEM_GHOST, 999);

                $model = ScaleDb::wireSdModel($conn);
                ScaleDb::setSdSession(self::SUB1, self::ITEM1);
                $this->assertFalse($model->set_confirm_result(self::ITEM1));
            } finally {
                if ($conn->transDepth > 0) {
                    $conn->transRollback();
                }
            }

            $this->assertRolledBack(self::SUB1, self::ITEM1, 1);

            // The pre-existing ghost row must be untouched by the failed,
            // fully rolled-back attempt.
            $ghost = ScaleDb::sdResultTime($conn, self::SUB1, self::SAME_ITEM_GHOST);
            $this->assertNotEmpty($ghost, 'the conflicting ghost row must survive');
            $this->assertSame('999', (string) $ghost['result_no']);
        } finally {
            $_POST = [];
            ScaleDb::cleanupSdItem($conn, self::SUB1, self::ITEM1);
            $ghostConn->table('sd_result_time')->where('sub_district_code', self::SUB1)
                ->where('item_code', self::SAME_ITEM_GHOST)->delete();
        }
    }

    public function testExhaustedResultNoCollisionRollsBackEverything(): void
    {
        $conn = ScaleDb::connect($this);
        $ghostConn = ScaleDb::connect($this);

        try {
            ScaleDb::seedSdItem($conn, self::SUB2, self::ITEM2);

            $conn->transBegin();
            try {
                $conn->query('SELECT COUNT(*) FROM sd_result_time');
                $this->insertGhostRow($ghostConn, self::SUB2, self::NUMBER_GHOST, 1);

                $model = ScaleDb::wireSdModel($conn);
                ScaleDb::setSdSession(self::SUB2, self::ITEM2);
                $this->assertFalse($model->set_confirm_result(self::ITEM2));
            } finally {
                if ($conn->transDepth > 0) {
                    $conn->transRollback();
                }
            }

            $this->assertRolledBack(self::SUB2, self::ITEM2);
            $ghost = ScaleDb::sdResultTime($conn, self::SUB2, self::NUMBER_GHOST);
            $this->assertNotEmpty($ghost, 'ghost row must be untouched by the rolled-back attempt');
            $this->assertSame('1', (string) $ghost['result_no']);
        } finally {
            $_POST = [];
            ScaleDb::cleanupSdItem($conn, self::SUB2, self::ITEM2);
            $ghostConn->table('sd_result_time')->where('sub_district_code', self::SUB2)
                ->where('item_code', self::NUMBER_GHOST)->delete();
        }
    }

    public function testSuccessfulConfirmAllocatesPastAGhostRow(): void
    {
        $conn = ScaleDb::connect($this);
        $ghostConn = ScaleDb::connect($this);

        try {
            ScaleDb::seedSdItem($conn, self::SUCCESS_SUB, self::SUCCESS_ITEM);
            $this->insertGhostRow($ghostConn, self::SUCCESS_SUB, self::SUCCESS_GHOST, 1);

            $model = ScaleDb::wireSdModel($conn);
            ScaleDb::setSdSession(self::SUCCESS_SUB, self::SUCCESS_ITEM);
            $this->assertTrue($model->set_confirm_result(self::SUCCESS_ITEM));

            // The next allocation skips the ghost's number: MAX=1 -> result_no 2.
            $row = ScaleDb::sdResultTime($conn, self::SUCCESS_SUB, self::SUCCESS_ITEM);
            $this->assertSame('2', (string) $row['result_no']);
            $this->assertSame('Y', (string) $row['is_finalized']);
            $this->assertSame(3, ScaleDb::sdFinishedCount($conn, self::SUCCESS_SUB, self::SUCCESS_ITEM));
        } finally {
            $_POST = [];
            ScaleDb::cleanupSdItem($conn, self::SUCCESS_SUB, self::SUCCESS_ITEM);
            $ghostConn->table('sd_result_time')->where('sub_district_code', self::SUCCESS_SUB)
                ->where('item_code', self::SUCCESS_GHOST)->delete();
        }
    }

    /**
     * Assert a failed confirmation left the scope/item in its pre-confirm
     * state: no is_finish flips, no rank/grade/point rewrites, no school
     * points, no publication, no sd_result_time row.
     */
    private function assertRolledBack(int $sub, int $item, int $expectedResultTimeRows = 0): void
    {
        $conn = ScaleDb::connect($this);

        $this->assertSame(0, ScaleDb::sdFinishedCount($conn, $sub, $item), 'is_finish must be N after rollback');
        $this->assertSame($expectedResultTimeRows, (int) $conn->table('sd_result_time')
            ->where('sub_district_code', $sub)->where('item_code', $item)->countAllResults());
        $this->assertSame(0, (int) $conn->table('sd_school_point_details')
            ->where('sub_district_code', $sub)->where('item_code', $item)->countAllResults());
        $this->assertSame(0, (int) $conn->table('sd_result_publish')
            ->where('sub_district_code', $sub)->where('item_code', $item)->countAllResults());
        $this->assertSame(0, (int) $conn->table('sd_certificate_master')
            ->where('sub_district_code', $sub)->where('item_code', $item)->countAllResults());

        $rows = $conn->table('sd_result_master')
            ->where('sub_district_code', $sub)->where('item_code', $item)
            ->orderBy('participant_id', 'ASC')->get()->getResultArray();
        $this->assertCount(3, $rows);
        foreach ($rows as $row) {
            $this->assertSame('0', (string) $row['rank'], 'rank must be restored by rollback');
            $this->assertSame('Y', (string) $row['is_taken']);
            $this->assertNotEmpty(trim((string) $row['grade']), 'seeded grade must survive');
            $this->assertGreaterThan(0, (int) $row['point'], 'seeded point must survive');
        }
    }

    private function insertGhostRow($conn, int $sub, int $item, int $resultNo): void
    {
        $conn->table('sd_result_time')->insert([
            'result_no'         => $resultNo,
            'sub_district_code' => $sub,
            'district_code'     => 0,
            'item_code'         => $item,
            'confirm_date'      => date('Y-m-d'),
            'confirm_time'      => date('H:i:s'),
            'is_finalized'      => 'Y',
            'is_reset'          => 0,
        ]);
    }
}