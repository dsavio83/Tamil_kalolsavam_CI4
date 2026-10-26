<?php

declare(strict_types=1);

/**
 * MySQL access + state helpers for the multi-scope scale audit tests.
 *
 * Everything here targets the disposable "tk2025_scale" database (see
 * .automation/scripts/mysql_scale_setup.ps1) and refuses to run against any
 * non-disposable schema. Connections and model wiring mirror the established
 * pattern in tests/unit/ResultTimeConfirmTest.php.
 */

namespace Tests\Support\Scale;

use App\Libraries\CiDatabase;
use App\Models\District\Result_Model as DistrictResult_Model;
use App\Models\Result\Result_Model as SubDistrictResult_Model;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Database;

final class ScaleDb
{
    /** Only disposable schemas (never tk2025 / production) may be targeted. */
    public const DISPOSABLE_SUFFIXES = ['_test', '_scale', '_preflight', '_conc', '_rb', '_fail', '_stage', '_fresh'];

    /** Confirmation artifact tables for the sd scope (all carry item_code + sub_district_code). */
    public const SD_CONFIRM_TABLES = [
        'sd_result_master', 'sd_result_time', 'sd_result_publish',
        'sd_certificate_master', 'sd_item_absentee_master',
        'sd_school_point_details', 'sd_participant_item_details',
    ];

    /** Confirmation artifact tables for the district scope (carry item_code; district_code optional). */
    public const D_CONFIRM_TABLES = [
        'd_result_master', 'd_result_time', 'd_result_publish',
        'd_certificate_master', 'd_item_absentee_master',
        'd_school_point_details', 'd_participant_item_details',
    ];

    public static function databaseName(): string
    {
        return getenv('CI_SCALE_MYSQL_DATABASE') ?: 'tk2025_scale';
    }

    public static function isDisposable(string $database): bool
    {
        if ($database === 'tk2025') {
            return false;
        }
        foreach (self::DISPOSABLE_SUFFIXES as $suffix) {
            if (str_ends_with($database, $suffix)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Build a MySQL connection to the disposable scale database, refusing any
     * non-disposable target and skipping when it is not provisioned.
     */
    public static function connect(CIUnitTestCase $case, ?string $database = null, bool $requireProvision = true)
    {
        $database = $database ?? self::databaseName();

        if (!self::isDisposable($database)) {
            $case->fail(
                "Refusing to run scale tests against non-disposable database '{$database}'. "
                . 'Set CI_SCALE_MYSQL_DATABASE to a dedicated disposable schema (*_scale / *_test / *_preflight / *_conc / *_rb / *_fail / *_stage).'
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
            $conn->connect();
        } catch (\Throwable $e) {
            $case->markTestSkipped('Isolated scale MySQL connection unavailable: ' . $e->getMessage());
        }

        if ($conn->getDatabase() !== $database) {
            $case->fail(
                "Connected database '{$conn->getDatabase()}' does not match the configured scale database '{$database}'."
            );
        }

        if ($requireProvision && !self::isProvisioned($conn)) {
            $case->markTestSkipped('tk2025_scale is not provisioned; run pwsh .automation/scripts/mysql_scale_setup.ps1 first');
        }

        return $conn;
    }

    public static function isProvisioned($conn): bool
    {
        if (!$conn->tableExists('sd_result_master') || !$conn->tableExists('d_result_master')) {
            return false;
        }
        if ((int) $conn->table('sub_district_master')->where('sub_district_code', 9101)->countAllResults() !== 1) {
            return false;
        }
        return (int) $conn->table('item_master')->whereIn('item_code', [9901, 9902, 9903, 9904, 9905])->countAllResults() === 5;
    }

    public static function wireSdModel($conn): SubDistrictResult_Model
    {
        return self::wireModel(new SubDistrictResult_Model(), SubDistrictResult_Model::class, $conn);
    }

    public static function wireDistrictModel($conn): DistrictResult_Model
    {
        return self::wireModel(new DistrictResult_Model(), DistrictResult_Model::class, $conn);
    }

    private static function wireModel($model, string $class, $conn)
    {
        $ciDb = new CiDatabase($conn);
        foreach (['db', 'db1'] as $prop) {
            $ref = new \ReflectionProperty($class, $prop);
            $ref->setValue($model, $ciDb);
        }
        return $model;
    }

    public static function setSdSession(int $subDistrict, int $item): void
    {
        session()->set('SUB_DISTRICT', (string) $subDistrict);
        session()->set('USER_TYPE', '3');
        $_POST['hidItemId'] = (string) $item;
    }

    public static function setDistrictSession(int $district, int $item): void
    {
        session()->set('DISTRICT', (string) $district);
        session()->set('USER_TYPE', '2');
        $_POST['hidItemId'] = (string) $item;
    }

    // ---------------------------------------------------------------------
    // Grading scheme (must match update_point_grade in both result models)
    // ---------------------------------------------------------------------

    /**
     * @return array{0:string,1:int} [grade, point]
     */
    public static function gradeAndPoint(float $percentage): array
    {
        if ($percentage >= 80) {
            return ['A', 5];
        }
        if ($percentage >= 70) {
            return ['B', 3];
        }
        if ($percentage >= 60) {
            return ['C', 1];
        }
        return ['', 0];
    }

    // ---------------------------------------------------------------------
    // Seeding
    // ---------------------------------------------------------------------

    /**
     * Ensure the sub-district participant, registration and pending result
     * rows exist for an item, then return the participant ids created.
     *
     * @param list<float> $percentages
     *
     * @return list<int> participant ids, one per percentage (seed order)
     */
    public static function seedSdItem($conn, int $subDistrict, int $item, array $percentages = [85, 75, 65]): array
    {
        $ids = [];
        $schools = ScaleTopology::schools($subDistrict);

        foreach ($percentages as $index => $percentage) {
            $position = $index + 1;
            $pid = ScaleTopology::participantId($subDistrict, $position);
            $school = $schools[$index % count($schools)];
            $admn = ScaleTopology::admnNo($subDistrict, $position);

            if ((int) $conn->table('sd_participant_details')->where('participant_id', $pid)->countAllResults() === 0) {
                $conn->table('sd_participant_details')->insert([
                    'participant_id'     => $pid,
                    'school_code'        => $school,
                    'sub_district_code'  => $subDistrict,
                    'admn_no'            => $admn,
                    'participant_name'   => 'Scale Sd ' . $subDistrict . ' P' . $position,
                    'class'              => abs($item) % 5 + 6,
                    'gender'             => 'M',
                    'bank_id'            => 0,
                    'branch_name'        => '',
                    'account_no'         => '',
                    'ifsc_code'          => '',
                ]);
            }

            if (
                (int) $conn->table('sd_participant_item_details')
                    ->where('participant_id', $pid)
                    ->where('item_code', $item)
                    ->where('sub_district_code', $subDistrict)
                    ->countAllResults() === 0
            ) {
                $conn->table('sd_participant_item_details')->insert([
                    'sub_district_code' => $subDistrict,
                    'participant_id'    => $pid,
                    'school_code'       => $school,
                    'admn_no'           => $admn,
                    'parent_admn_no'    => '0',
                    'item_code'         => $item,
                    'item_type'         => 'S',
                    'spo_id'            => 0,
                    'spo_remarks'       => null,
                    'is_captain'        => 'N',
                ]);
            }

            if (
                (int) $conn->table('sd_result_master')
                    ->where('participant_id', $pid)
                    ->where('item_code', $item)
                    ->where('sub_district_code', $subDistrict)
                    ->countAllResults() === 0
            ) {
                [$grade, $point] = self::gradeAndPoint((float) $percentage);
                $conn->table('sd_result_master')->insert([
                    'sub_district_code'      => $subDistrict,
                    'district_code'          => 0,
                    'item_code'              => $item,
                    'participant_id'         => $pid,
                    'school_code'            => $school,
                    'admn_no'                => $admn,
                    'code_no'                => $pid,
                    'marks'                  => (string) $percentage,
                    'total_mark'             => $percentage,
                    'percentage'             => $percentage,
                    'grade'                  => $grade,
                    'point'                  => $point,
                    'rank'                   => 0,
                    'is_taken'               => 'Y',
                    'is_finish'              => 'N',
                    'is_printed'             => 0,
                    'is_certificate_printed' => 0,
                    'spo_id'                 => 0,
                ]);
            }

            $ids[] = $pid;
        }

        return $ids;
    }

    /**
     * Ensure district participant / registration / pending result rows exist.
     *
     * @param list<float> $percentages
     *
     * @return list<int> participant ids
     */
    public static function seedDistrictItem($conn, int $district, int $item, array $percentages = [85, 75, 65]): array
    {
        $ids = [];
        $sub = ScaleTopology::subDistricts($district)[0];
        $school = ScaleTopology::schools($sub)[0];

        foreach ($percentages as $index => $percentage) {
            $position = $index + 1;
            $pid = ScaleTopology::districtParticipantId($district, $position);
            $admn = ScaleTopology::districtAdmnNo($district, $position);

            if ((int) $conn->table('d_participant_details')->where('d_participant_id', $pid)->countAllResults() === 0) {
                $conn->table('d_participant_details')->insert([
                    'd_participant_id'         => $pid,
                    'participant_id'           => $pid,
                    'district_code'            => $district,
                    'sub_district_code'        => $sub,
                    'school_code'              => $school,
                    'admn_no'                  => $admn,
                    'participant_name'         => 'Scale Dist ' . $district . ' D' . $position,
                    'class'                    => abs($item) % 5 + 6,
                    'gender'                   => 'M',
                    'bank_id'                  => 0,
                    'branch_name'              => '',
                    'account_no'               => '',
                    'ifsc_code'                => '',
                    'photo_filename'           => null,
                    'photo_status'             => 'NO_PHOTO',
                    'source_sub_district_code' => $sub,
                    'source_participant_id'    => null,
                    'promoted_at'              => date('Y-m-d H:i:s'),
                    'promoted_by_user_id'      => 1,
                    'promotion_type'           => 'DIRECT',
                    'order_reference_no'       => null,
                    'export_id'                => null,
                    'import_id'                => null,
                ]);
            }

            if (
                (int) $conn->table('d_participant_item_details')
                    ->where('d_participant_id', $pid)
                    ->where('item_code', $item)
                    ->where('district_code', $district)
                    ->countAllResults() === 0
            ) {
                $conn->table('d_participant_item_details')->insert([
                    'd_participant_id'   => $pid,
                    'district_code'      => $district,
                    'sub_district_code'  => $sub,
                    'school_code'        => $school,
                    'item_code'          => $item,
                    'item_type'          => 'S',
                    'admn_no'            => $admn,
                    'parent_admn_no'     => '0',
                    'is_captain'         => 'N',
                    'spo_id'             => 0,
                    'spo_remarks'        => null,
                    'district_chest_no'  => null,
                    'source_pi_id'       => 0,
                    'source_rm_id'       => null,
                    'source_rp_id'       => null,
                    'source_rank'        => 1,
                    'source_grade'       => 'A',
                ]);
            }

            if (
                (int) $conn->table('d_result_master')
                    ->where('participant_id', $pid)
                    ->where('item_code', $item)
                    ->where('district_code', $district)
                    ->countAllResults() === 0
            ) {
                [$grade, $point] = self::gradeAndPoint((float) $percentage);
                $conn->table('d_result_master')->insert([
                    'sub_district_code'      => $sub,
                    'district_code'          => $district,
                    'item_code'              => $item,
                    'participant_id'         => $pid,
                    'school_code'            => $school,
                    'admn_no'                => $admn,
                    'code_no'                => $pid,
                    'marks'                  => (string) $percentage,
                    'total_mark'             => $percentage,
                    'percentage'             => $percentage,
                    'grade'                  => $grade,
                    'point'                  => $point,
                    'rank'                   => 0,
                    'is_taken'               => 'Y',
                    'is_finish'              => 'N',
                    'is_printed'             => 0,
                    'is_certificate_printed' => 0,
                    'spo_id'                 => 0,
                ]);
            }

            $ids[] = $pid;
        }

        return $ids;
    }

    // ---------------------------------------------------------------------
    // Cleanup / reset
    // ---------------------------------------------------------------------

    /** Remove every confirmation artifact + seeded rows for one sd scope/item. */
    public static function cleanupSdItem($conn, int $subDistrict, int $item): void
    {
        foreach (self::SD_CONFIRM_TABLES as $table) {
            $conn->table($table)
                ->where('sub_district_code', $subDistrict)
                ->where('item_code', $item)
                ->delete();
        }
    }

    /** Remove every confirmation artifact + seeded rows for one district scope/item. */
    public static function cleanupDistrictItem($conn, int $district, int $item): void
    {
        foreach (self::D_CONFIRM_TABLES as $table) {
            $builder = $conn->table($table)
                ->where('item_code', $item);
            if ($conn->fieldExists('district_code', $table)) {
                $builder->where('district_code', $district);
            }
            $builder->delete();
        }
    }

    /**
     * Un-confirm a previously confirmed sd scope/item, restoring the seeded
     * pending state (is_finish N, rank 0, seeded grade/point, no artifacts).
     */
    public static function restoreSdItemToPending($conn, int $subDistrict, int $item): void
    {
        foreach (['sd_result_time', 'sd_result_publish', 'sd_certificate_master', 'sd_item_absentee_master', 'sd_school_point_details'] as $table) {
            $conn->table($table)
                ->where('sub_district_code', $subDistrict)
                ->where('item_code', $item)
                ->delete();
        }
        $rows = $conn->table('sd_result_master')
            ->where('sub_district_code', $subDistrict)
            ->where('item_code', $item)
            ->get()->getResultArray();
        foreach ($rows as $row) {
            [$grade, $point] = self::gradeAndPoint((float) $row['percentage']);
            $conn->table('sd_result_master')
                ->where('rm_id', $row['rm_id'])
                ->set([
                    'is_finish' => 'N',
                    'is_taken'  => 'Y',
                    'rank'      => 0,
                    'grade'     => $grade,
                    'point'     => $point,
                ])
                ->update();
        }
    }

    // ---------------------------------------------------------------------
    // State readers
    // ---------------------------------------------------------------------

    /**
     * Lifetime count of ER_LOCK_DEADLOCK (1213) from performance_schema, or -1
     * if the counter is unavailable. MySQL 8.x has no Innodb_deadlocks status
     * variable (that is MariaDB-only), so this is the authoritative source.
     */
    public static function deadlockCount($conn): int
    {
        try {
            $result = $conn->query(
                'SELECT COALESCE(SUM(SUM_ERROR_RAISED), 0) AS c
                   FROM performance_schema.events_errors_summary_global_by_error
                  WHERE ERROR_NUMBER = 1213'
            );
        } catch (\Throwable $e) {
            return -1;
        }

        if ($result === false) {
            return -1;
        }

        $row = $result->getRowArray();
        return $row === null ? -1 : (int) $row['c'];
    }

    public static function sdResultTime($conn, int $subDistrict, int $item): ?array
    {
        return $conn->table('sd_result_time')
            ->where('sub_district_code', $subDistrict)
            ->where('item_code', $item)
            ->get()->getRowArray() ?: null;
    }

    public static function districtResultTime($conn, int $district, int $item): ?array
    {
        return $conn->table('d_result_time')
            ->where('district_code', $district)
            ->where('item_code', $item)
            ->get()->getRowArray() ?: null;
    }

    public static function sdFinishedCount($conn, int $subDistrict, int $item): int
    {
        return (int) $conn->table('sd_result_master')
            ->where('sub_district_code', $subDistrict)
            ->where('item_code', $item)
            ->where('is_finish', 'Y')
            ->countAllResults();
    }

    public static function districtFinishedCount($conn, int $district, int $item): int
    {
        return (int) $conn->table('d_result_master')
            ->where('district_code', $district)
            ->where('item_code', $item)
            ->where('is_finish', 'Y')
            ->countAllResults();
    }

    /** @return list<int> result_no per scope in ascending order */
    public static function sdResultNos($conn, int $subDistrict): array
    {
        return array_map(
            static fn ($row) => (int) $row['result_no'],
            $conn->table('sd_result_time')->select('result_no')->where('sub_district_code', $subDistrict)
                ->orderBy('result_no', 'ASC')->get()->getResultArray()
        );
    }

    /** @return list<int> result_no per district scope in ascending order */
    public static function districtResultNos($conn, int $district): array
    {
        return array_map(
            static fn ($row) => (int) $row['result_no'],
            $conn->table('d_result_time')->select('result_no')->where('district_code', $district)
                ->orderBy('result_no', 'ASC')->get()->getResultArray()
        );
    }

    /** Total rows left behind by a confirmation for a scope/item (rollback probe). */
    public static function sdConfirmationRowCount($conn, int $subDistrict, int $item): int
    {
        $count = 0;
        foreach (['sd_result_time', 'sd_result_publish', 'sd_certificate_master', 'sd_item_absentee_master', 'sd_school_point_details'] as $table) {
            $count += (int) $conn->table($table)
                ->where('sub_district_code', $subDistrict)
                ->where('item_code', $item)
                ->countAllResults();
        }
        return $count;
    }

    // ---------------------------------------------------------------------
    // Provisioning (client binaries: mysql / mysqldump)
    // ---------------------------------------------------------------------

    /**
     * Locate the mysql and mysqldump client binaries. Returns null when they are
     * not installed, so migration tests can skip instead of failing.
     *
     * @return array{mysql:string,mysqldump:string}|null
     */
    public static function mysqlBinaries(): ?array
    {
        $ext = stripos(PHP_OS_FAMILY, 'Windows') === 0 ? '.exe' : '';

        $dirs = [];
        $env = getenv('CI_MYSQL_BIN_DIR');
        if ($env !== false && $env !== '') {
            $dirs[] = rtrim($env, '\\/');
        }
        $dirs[] = 'c:\\mysql84\\bin';
        $dirs[] = 'C:\\xampp\\mysql\\bin';

        foreach ($dirs as $dir) {
            $mysql = $dir . DIRECTORY_SEPARATOR . 'mysql' . $ext;
            $dump = $dir . DIRECTORY_SEPARATOR . 'mysqldump' . $ext;
            if (is_file($mysql) && is_file($dump)) {
                return ['mysql' => $mysql, 'mysqldump' => $dump];
            }
        }

        return null;
    }

    /**
     * Run a client binary (mysql / mysqldump) to completion.
     *
     * @param list<string> $cmd
     *
     * @return array{exit:int,out:string,err:string}
     */
    public static function runClient(array $cmd): array
    {
        $spec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $pipes = [];
        $process = proc_open($cmd, $spec, $pipes);
        if (!is_resource($process)) {
            throw new \RuntimeException('proc_open failed: ' . implode(' ', $cmd));
        }
        fclose($pipes[0]);
        $out = (string) stream_get_contents($pipes[1]);
        $err = (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return ['exit' => proc_close($process), 'out' => $out, 'err' => $err];
    }

    /**
     * @return list<string> client auth/connection arguments
     */
    private static function clientAuth(): array
    {
        $args = [
            '--host=' . (getenv('CI_TEST_MYSQL_HOST') ?: '127.0.0.1'),
            '--port=' . (string) (getenv('CI_TEST_MYSQL_PORT') ?: 3306),
            '-u' . (getenv('CI_TEST_MYSQL_USER') ?: 'root'),
        ];

        $password = getenv('CI_TEST_MYSQL_PASSWORD');
        $password = $password === false ? '' : $password;
        if ($password !== '') {
            $args[] = '-p' . $password;
        }

        return $args;
    }

    public static function databaseExists(string $database): bool
    {
        $bins = self::mysqlBinaries();
        if ($bins === null) {
            return false;
        }
        $r = self::runClient(array_merge(
            [$bins['mysql']],
            self::clientAuth(),
            ['-N', '-B', '-e', "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '" . $database . "'"]
        ));

        return $r['exit'] === 0 && trim($r['out']) === $database;
    }

    /** Drop + recreate a disposable database (empty schema). */
    public static function recreateDatabase(string $database): void
    {
        self::assertDisposable($database);
        $bins = self::mysqlBinaries();
        if ($bins === null) {
            throw new \RuntimeException('mysql client binary not found (set CI_MYSQL_BIN_DIR)');
        }
        $r = self::runClient(array_merge(
            [$bins['mysql']],
            self::clientAuth(),
            ['-e', 'DROP DATABASE IF EXISTS `' . $database . '`; CREATE DATABASE `' . $database . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;']
        ));
        if ($r['exit'] !== 0) {
            throw new \RuntimeException("create database '{$database}' failed: {$r['err']}");
        }
    }

    public static function dropDatabase(string $database): void
    {
        self::assertDisposable($database);
        $bins = self::mysqlBinaries();
        if ($bins === null) {
            return;
        }
        self::runClient(array_merge(
            [$bins['mysql']],
            self::clientAuth(),
            ['-e', 'DROP DATABASE IF EXISTS `' . $database . '`']
        ));
    }

    /**
     * Provision a disposable database from the documented base schema: a
     * schema-only clone (mysqldump --no-data) of the given source. The source is
     * only read, never altered, and must differ from the target.
     */
    public static function provisionSchemaOnlyClone(string $source, string $target): void
    {
        self::assertDisposable($target);
        if ($source === $target) {
            throw new \InvalidArgumentException('clone source and target must differ');
        }
        $bins = self::mysqlBinaries();
        if ($bins === null) {
            throw new \RuntimeException('mysql/mysqldump client binaries not found (set CI_MYSQL_BIN_DIR)');
        }

        $tmp = tempnam(sys_get_temp_dir(), 'migclone_') . '.sql';

        try {
            $dump = self::runClient(array_merge(
                [$bins['mysqldump']],
                self::clientAuth(),
                ['--single-transaction', '--skip-opt', '--skip-comments', '--no-data', '--result-file=' . $tmp, $source]
            ));
            if ($dump['exit'] !== 0) {
                throw new \RuntimeException("mysqldump of '{$source}' failed: {$dump['err']}");
            }

            self::recreateDatabase($target);

            $import = self::runClient(array_merge(
                [$bins['mysql']],
                self::clientAuth(),
                ['--database=' . $target, '-e', 'source ' . str_replace('\\', '/', $tmp)]
            ));
            if ($import['exit'] !== 0) {
                throw new \RuntimeException("import of '{$source}' into '{$target}' failed: {$import['err']}");
            }
        } finally {
            @unlink($tmp);
        }
    }

    /**
     * Provision a disposable database as a full (schema + data) clone of the
     * given source, using a single consistent snapshot. The source is only read.
     * Used by the lock-contention audit so the global destructive import/reset
     * paths can be exercised without disturbing the shared tk2025_scale fixtures.
     */
    public static function provisionDataClone(string $source, string $target): void
    {
        self::assertDisposable($target);
        if ($source === $target) {
            throw new \InvalidArgumentException('clone source and target must differ');
        }
        $bins = self::mysqlBinaries();
        if ($bins === null) {
            throw new \RuntimeException('mysql/mysqldump client binaries not found (set CI_MYSQL_BIN_DIR)');
        }

        $tmp = tempnam(sys_get_temp_dir(), 'dataclone_') . '.sql';

        try {
            $dump = self::runClient(array_merge(
                [$bins['mysqldump']],
                self::clientAuth(),
                ['--single-transaction', '--skip-opt', '--skip-comments', '--add-drop-table', '--result-file=' . $tmp, $source]
            ));
            if ($dump['exit'] !== 0) {
                throw new \RuntimeException("mysqldump of '{$source}' failed: {$dump['err']}");
            }

            self::recreateDatabase($target);

            $import = self::runClient(array_merge(
                [$bins['mysql']],
                self::clientAuth(),
                ['--database=' . $target, '-e', 'source ' . str_replace('\\', '/', $tmp)]
            ));
            if ($import['exit'] !== 0) {
                throw new \RuntimeException("import of '{$source}' into '{$target}' failed: {$import['err']}");
            }
        } finally {
            @unlink($tmp);
        }
    }

    private static function assertDisposable(string $database): void
    {
        if (!self::isDisposable($database)) {
            throw new \InvalidArgumentException("Refusing to manage non-disposable database '{$database}'.");
        }
    }

    // ---------------------------------------------------------------------
    // Schema introspection
    // ---------------------------------------------------------------------

    /**
     * @return array<string, list<string>> index name => column list (in key order)
     */
    public static function indexColumns($conn, string $table): array
    {
        $rows = $conn->query(
            "SELECT INDEX_NAME, COLUMN_NAME, SEQ_IN_INDEX
               FROM information_schema.STATISTICS
              WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = " . $conn->escape($table) . '
              ORDER BY INDEX_NAME, SEQ_IN_INDEX'
        )->getResultArray();

        $indexes = [];
        foreach ($rows as $row) {
            $indexes[$row['INDEX_NAME']][] = $row['COLUMN_NAME'];
        }
        return $indexes;
    }

    /** @return list<string> primary key columns in key order */
    public static function primaryKeyColumns($conn, string $table): array
    {
        return self::indexColumns($conn, $table)['PRIMARY'] ?? [];
    }
}