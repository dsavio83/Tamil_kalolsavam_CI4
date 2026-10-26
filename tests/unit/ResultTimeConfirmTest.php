<?php

namespace Tests\Unit;

use App\Libraries\CiDatabase;
use App\Models\Result\Result_Model;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Database;

/**
 * Regression tests for the "confirmed result never shows in Result List"
 * defect:
 *
 * 1. sd_result_time / d_result_time were PRIMARY KEYed by item_code only, so a
 *    second sub-district confirming the same item code hit a duplicate-key
 *    error, the transaction rolled back and is_finish stayed 'N'
 *    ("Pending"). The key is now scoped: sd_result_time (sub_district_code,
 *    item_code), d_result_time (district_code, item_code).
 * 2. The re-confirm (update) branch wrote confirm_time as date('H:i:s a'),
 *    which MySQL strict mode rejects.
 *
 * These tests require a dedicated, disposable MySQL database (the CI test
 * group is an in-memory SQLite and cannot exercise MySQL key/transaction
 * behavior). The connection is built from CI_TEST_MYSQL_* environment
 * variables and defaults to the isolated "tk2025_test" schema. The tests
 * refuse to run against any database that is not a dedicated *_test schema.
 * They are read-only or wrapped in an outer transaction that is rolled back.
 *
 * @internal
 */
final class ResultTimeConfirmTest extends CIUnitTestCase
{
    private const SD      = 218;
    private const ITEM    = 351;
    private const KEEP_SD = 213; // sub-district that already owns this item in sd_result_time

    /** Only disposable schemas (never tk2025 / production) may be targeted. */
    private const DISPOSABLE_SUFFIXES = ['_test', '_preflight', '_conc', '_rb', '_fail', '_stage'];

    public function testResultTimeTablesUsePerScopePrimaryKeys(): void
    {
        $conn = $this->mysqlConnectionOrSkip();

        $this->assertSame(['sub_district_code', 'item_code'], $this->primaryKeyColumns($conn, 'sd_result_time'));
        $this->assertSame(['district_code', 'item_code'], $this->primaryKeyColumns($conn, 'd_result_time'));
    }

    public function testSecondSubDistrictCanInsertItsOwnResultTimeRow(): void
    {
        $conn = $this->mysqlConnectionOrSkip();

        $conn->transBegin();
        try {
            // This is the exact INSERT that used to collide on PRIMARY KEY (item_code).
            $conn->table('sd_result_time')->insert([
                'result_no'         => 1,
                'sub_district_code' => self::SD,
                'district_code'     => 0,
                'item_code'         => self::ITEM,
                'confirm_date'      => date('Y-m-d'),
                'confirm_time'      => date('H:i:s'),
                'is_finalized'      => 'Y',
                'is_reset'          => 0,
            ]);

            $row = $conn->table('sd_result_time')
                ->where('sub_district_code', self::SD)
                ->where('item_code', self::ITEM)
                ->get()->getRowArray();

            $this->assertNotEmpty($row);
            $this->assertSame((string) self::ITEM, (string) $row['item_code']);
            $this->assertSame((string) self::SD, (string) $row['sub_district_code']);

            // The row for the pre-existing sub-district must still be intact.
            $kept = $conn->table('sd_result_time')
                ->where('sub_district_code', self::KEEP_SD)
                ->where('item_code', self::ITEM)
                ->get()->getRowArray();
            $this->assertNotEmpty($kept);
        } finally {
            if ($conn->transDepth > 0) {
                $conn->transRollback();
            }
        }
    }

    public function testReconfirmWrites24HourTimeNotAmPm(): void
    {
        $conn = $this->mysqlConnectionOrSkip();

        // date('H:i:s a') produces e.g. "08:29:17 PM" which MySQL 8 strict mode
        // rejects for a TIME column ("Truncated incorrect time value"). The
        // update branch of set_confirm_result() must persist 24h time.
        $conn->transBegin();
        try {
            $conn->table('sd_result_time')->insert([
                'result_no'         => 999,
                'sub_district_code' => self::SD,
                'district_code'     => 0,
                'item_code'         => 999,
                'confirm_date'      => date('Y-m-d'),
                'confirm_time'      => date('H:i:s'),
                'is_finalized'      => 'Y',
                'is_reset'          => 0,
            ]);

            $updated = $conn->table('sd_result_time')
                ->where('sub_district_code', self::SD)
                ->where('item_code', 999)
                ->update(['confirm_time' => date('H:i:s')]);

            $this->assertTrue($updated);

            $row = $conn->table('sd_result_time')
                ->where('sub_district_code', self::SD)
                ->where('item_code', 999)
                ->get()->getRowArray();
            $this->assertNotEmpty($row);
            $this->assertMatchesRegularExpression('/^\d{2}:\d{2}:\d{2}$/', (string) $row['confirm_time']);
        } finally {
            if ($conn->transDepth > 0) {
                $conn->transRollback();
            }
        }
    }

    public function testConfirmFlowForSecondSubDistrictMarksItemConfirmed(): void
    {
        $conn = $this->mysqlConnectionOrSkip();

        // Point the model at the isolated MySQL test connection (in
        // ENVIRONMENT testing, Database::connect() defaults to SQLite).
        $model = new Result_Model();
        $dbRef = new \ReflectionProperty(Result_Model::class, 'db');
        $db1Ref = new \ReflectionProperty(Result_Model::class, 'db1');
        $ciDb = new CiDatabase($conn);
        if (PHP_VERSION_ID >= 80100 && method_exists($dbRef, 'setAccessible')) {
            // no-op; ReflectionProperty::setValue allows access regardless
        }
        try {
            $dbRef->setValue($model, $ciDb);
            $db1Ref->setValue($model, $ciDb);
        } catch (\ReflectionException $e) {
            $this->markTestSkipped('Cannot inspect model DB property: ' . $e->getMessage());
        }

        session()->set('SUB_DISTRICT', (string) self::SD);
        session()->set('USER_TYPE', '3');
        $_POST['hidItemId'] = (string) self::ITEM;

        $conn->transBegin();
        try {
            $this->seedResultRows($conn);

            $confirmed = $model->set_confirm_result(self::ITEM);
            $this->assertTrue($confirmed);

            $finish = $conn->table('sd_result_master')
                ->select('COUNT(*) AS c')
                ->where('sub_district_code', self::SD)
                ->where('item_code', self::ITEM)
                ->where('is_finish', 'Y')
                ->get()->getRowArray();
            $this->assertSame('3', (string) $finish['c']);

            $rt = $conn->table('sd_result_time')
                ->select('result_no, is_finalized, confirm_time')
                ->where('sub_district_code', self::SD)
                ->where('item_code', self::ITEM)
                ->get()->getRowArray();
            $this->assertNotEmpty($rt);
            $this->assertSame('Y', (string) $rt['is_finalized']);
            $this->assertMatchesRegularExpression('/^\d{2}:\d{2}:\d{2}$/', (string) $rt['confirm_time']);

            // The Result List query the controller runs must now report yes.
            $list = $model->get_item_result_single(2);
            $found = null;
            foreach ($list as $row) {
                if ((string) $row['item_code'] === (string) self::ITEM) {
                    $found = $row;
                    break;
                }
            }
            $this->assertNotNull($found, 'Confirmed item must appear in the Result List');
            $this->assertSame('Yes', (string) $found['is_confirmed']);
            $this->assertSame('3', (string) $found['participated_no']);
        } finally {
            $_POST = [];
            if ($conn->transDepth > 0) {
                $conn->transRollback();
            }
        }
    }

    public function testCompositeKeyRejectsSameScopeDuplicateInsert(): void
    {
        $conn = $this->mysqlConnectionOrSkip();

        $conn->transBegin();
        try {
            // (213, item 351) already exists as the fixture row; the composite
            // key must reject a same-scope duplicate while the item remains
            // valid for another scope.
            $duplicate = $conn->table('sd_result_time')->insert([
                'result_no'         => 2,
                'sub_district_code' => self::KEEP_SD,
                'district_code'     => 0,
                'item_code'         => self::ITEM,
                'confirm_date'      => date('Y-m-d'),
                'confirm_time'      => date('H:i:s'),
                'is_finalized'      => 'Y',
                'is_reset'          => 0,
            ]);

            $this->assertFalse($duplicate);
            $this->assertSame('1062', (string) $conn->error()['code']);
            $this->assertSame('23000', (string) $this->rawMySqli($conn)->sqlstate);

            $otherScope = $conn->table('sd_result_time')->insert([
                'result_no'         => 1,
                'sub_district_code' => self::SD,
                'district_code'     => 0,
                'item_code'         => self::ITEM,
                'confirm_date'      => date('Y-m-d'),
                'confirm_time'      => date('H:i:s'),
                'is_finalized'      => 'Y',
                'is_reset'          => 0,
            ]);
            $this->assertTrue($otherScope);
        } finally {
            if ($conn->transDepth > 0) {
                $conn->transRollback();
            }
        }
    }

    public function testRepeatConfirmationUpdatesSameScopedRow(): void
    {
        $conn = $this->mysqlConnectionOrSkip();
        $ciDb = new CiDatabase($conn);

        $model = $this->modelWithConnection($ciDb);
        session()->set('SUB_DISTRICT', (string) self::KEEP_SD);
        session()->set('USER_TYPE', '3');
        $_POST['hidItemId'] = (string) self::ITEM;

        $conn->transBegin();
        try {
            $confirmed = $model->set_confirm_result(self::ITEM);
            $this->assertTrue($confirmed);

            $rows = $conn->table('sd_result_time')
                ->where('sub_district_code', self::KEEP_SD)
                ->where('item_code', self::ITEM)
                ->get()->getResultArray();
            $this->assertCount(1, $rows);
            $this->assertSame('Y', (string) $rows[0]['is_finalized']);

            $otherScope = $conn->table('sd_result_time')
                ->where('sub_district_code', self::SD)
                ->where('item_code', self::ITEM)
                ->get()->getRowArray();
            $this->assertNull($otherScope, 'Another scope must not be touched by re-confirmation');
        } finally {
            $_POST = [];
            if ($conn->transDepth > 0) {
                $conn->transRollback();
            }
        }
    }

    public function testIndependentPersistenceAcrossConnections(): void
    {
        // Two independent connections (single-process emulation of concurrent
        // requests). One re-confirms the owning sub-district (update path), the
        // other confirms the second sub-district (insert path). Both must
        // succeed on the composite key without aborting each other.
        $owner = $this->mysqlConnectionOrSkip();
        $second = $this->mysqlConnectionOrSkip();

        $ownerModel = $this->modelWithConnection(new CiDatabase($owner));
        $secondModel = $this->modelWithConnection(new CiDatabase($second));

        session()->set('USER_TYPE', '3');
        $_POST['hidItemId'] = (string) self::ITEM;

        $second->transBegin();
        try {
            $this->seedResultRows($second);
            session()->set('SUB_DISTRICT', (string) self::SD);
            $this->assertTrue($secondModel->set_confirm_result(self::ITEM));
            $secondRow = $second->table('sd_result_time')
                ->where('sub_district_code', self::SD)
                ->where('item_code', self::ITEM)
                ->get()->getRowArray();
            $this->assertNotEmpty($secondRow);
        } finally {
            if ($second->transDepth > 0) {
                $second->transRollback();
            }
        }

        $owner->transBegin();
        try {
            session()->set('SUB_DISTRICT', (string) self::KEEP_SD);
            $this->assertTrue($ownerModel->set_confirm_result(self::ITEM));
            $ownerRow = $owner->table('sd_result_time')
                ->where('sub_district_code', self::KEEP_SD)
                ->where('item_code', self::ITEM)
                ->get()->getRowArray();
            $this->assertNotEmpty($ownerRow);
            $this->assertSame('Y', (string) $ownerRow['is_finalized']);
        } finally {
            $_POST = [];
            if ($owner->transDepth > 0) {
                $owner->transRollback();
            }
        }
    }

    private function modelWithConnection(CiDatabase $ciDb): Result_Model
    {
        $model = new Result_Model();
        $dbRef = new \ReflectionProperty(Result_Model::class, 'db');
        $db1Ref = new \ReflectionProperty(Result_Model::class, 'db1');
        try {
            $dbRef->setValue($model, $ciDb);
            $db1Ref->setValue($model, $ciDb);
        } catch (\ReflectionException $e) {
            $this->markTestSkipped('Cannot inspect model DB property: ' . $e->getMessage());
        }
        return $model;
    }

    private function rawMySqli($conn)
    {
        $ref = new \ReflectionProperty($conn, 'connID');
        return $ref->getValue($conn);
    }

    private function seedResultRows($conn): void
    {
        $grades = [
            ['A', 5, 1, '28#$#29#$#30', 87, '101', 'A'],
            ['B', 4, 2, '26#$#27#$#28', 81, '102', 'B'],
            ['C', 3, 3, '24#$#25#$#26', 75, '103', 'C'],
        ];
        foreach ($grades as [$grade, $point, $rank, $marks, $total, $code, $ignored]) {
            unset($ignored);
            $conn->table('sd_result_master')->insert([
                'sub_district_code'          => self::SD,
                'district_code'              => 0,
                'item_code'                  => self::ITEM,
                'participant_id'             => 1017,
                'school_code'                => 21089,
                'admn_no'                    => '3514',
                'code_no'                    => $code,
                'marks'                      => $marks,
                'total_mark'                 => $total,
                'percentage'                 => (float) $total,
                'grade'                      => $grade,
                'point'                      => $point,
                'rank'                       => 0,
                'is_taken'                   => 'Y',
                'is_finish'                  => 'N',
                'is_printed'                 => 0,
                'is_certificate_printed'     => 0,
                'spo_id'                     => 0,
            ]);
        }
    }

    private function primaryKeyColumns($conn, string $table): array
    {
        $result = $conn->query('SELECT COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ' . $conn->escape($table) . "
              AND CONSTRAINT_NAME = 'PRIMARY'
            ORDER BY ORDINAL_POSITION");
        return array_column($result->getResultArray(), 'COLUMN_NAME');
    }

    private function mysqlConnectionOrSkip()
    {
        $database = getenv('CI_TEST_MYSQL_DATABASE') ?: 'tk2025_test';

        $disposable = false;
        foreach (self::DISPOSABLE_SUFFIXES as $suffix) {
            if (str_ends_with($database, $suffix)) {
                $disposable = true;
                break;
            }
        }
        if ($database === 'tk2025' || !$disposable) {
            $this->fail(
                "Refusing to run MySQL confirmation tests against non-disposable database '{$database}'. "
                . 'Set CI_TEST_MYSQL_DATABASE to a dedicated disposable schema (*_test / *_preflight / *_conc / *_rb / *_fail / *_stage).'
            );
        }

        $password = getenv('CI_TEST_MYSQL_PASSWORD');

        $config = [
            'DSN'          => '',
            'hostname'     => getenv('CI_TEST_MYSQL_HOST') ?: '127.0.0.1',
            'username'     => getenv('CI_TEST_MYSQL_USER') ?: 'root',
            'password'     => $password === false ? '' : $password,
            'database'     => $database,
            'DBDriver'     => 'MySQLi',
            'DBPrefix'     => '',
            'pConnect'     => false,
            'DBDebug'      => true,
            'charset'      => 'utf8mb4',
            'DBCollat'     => 'utf8mb4_unicode_ci',
            'swapPre'      => '',
            'encrypt'      => false,
            'compress'     => false,
            'strictOn'     => false,
            'failover'     => [],
            'port'         => (int) (getenv('CI_TEST_MYSQL_PORT') ?: 3306),
            'numberNative' => false,
            'foundRows'    => false,
            'dateFormat'   => [
                'date'     => 'Y-m-d',
                'datetime' => 'Y-m-d H:i:s',
                'time'     => 'H:i:s',
            ],
        ];

        try {
            $conn = Database::connect($config, false);
        } catch (\Throwable $e) {
            $this->markTestSkipped('Isolated MySQL test connection unavailable: ' . $e->getMessage());
        }

        if ($conn->getDatabase() !== $database) {
            $this->fail(
                "Connected database '{$conn->getDatabase()}' does not match the configured test database '{$database}'."
            );
        }

        if (!$conn->tableExists('sd_result_time') || !$conn->tableExists('d_result_time')) {
            $this->markTestSkipped('sd_result_time/d_result_time are not present in the isolated MySQL test database');
        }

        return $conn;
    }
}