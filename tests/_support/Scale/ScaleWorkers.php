<?php

declare(strict_types=1);

/**
 * Launch many confirmation workers as real, concurrent OS processes so the
 * scale tests exercise actual parallel MySQL transactions (locking, unique-key
 * contention, retry) rather than an in-process approximation.
 *
 * @internal audit tooling
 */

namespace Tests\Support\Scale;

final class ScaleWorkers
{
    private const WORKER = __DIR__ . '/../../../.automation/scripts/confirm_worker.php';

    private const CONTENTION_WORKER = __DIR__ . '/../../../.automation/scripts/contention_worker.php';

    /**
     * Launch a single lock-contention worker without waiting.
     *
     * @param array{role:string,scope?:int,item?:int,scopeType?:string,serialize?:string,isolation?:string,holdMs?:int,failAfter?:int} $job
     *
     * @return array{process:resource,pipes:array<int,resource>}
     */
    public static function startContention(array $job, ?string $database = null): array
    {
        $cmd = array_merge([PHP_BINARY, self::CONTENTION_WORKER], self::contentionArgs($job, $database));
        $spec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $pipes = [];
        $process = proc_open($cmd, $spec, $pipes, realpath(__DIR__ . '/../../../'), null);
        if (!is_resource($process)) {
            throw new \RuntimeException('proc_open failed for contention worker');
        }
        fclose($pipes[0]);

        return ['process' => $process, 'pipes' => $pipes];
    }

    /**
     * Run many lock-contention workers concurrently.
     *
     * @param list<array{role:string,scope?:int,item?:int,scopeType?:string,serialize?:string,isolation?:string,holdMs?:int,failAfter?:int}> $jobs
     *
     * @return list<array{exit:int,out:string,err:string,json:?array}>
     */
    public static function contentionParallel(array $jobs, ?string $database = null): array
    {
        $handles = [];
        foreach ($jobs as $key => $job) {
            $handles[$key] = self::startContention($job, $database);
        }

        $results = [];
        foreach ($handles as $key => $handle) {
            $results[$key] = self::finish($handle);
        }

        return $results;
    }

    /** @return list<string> */
    private static function contentionArgs(array $job, ?string $database): array
    {
        $db = $database ?? (getenv('CI_SCALE_MYSQL_DATABASE') ?: 'tk2025_scale');
        $cmd = [
            '--db', $db,
            '--host', getenv('CI_TEST_MYSQL_HOST') ?: '127.0.0.1',
            '--user', getenv('CI_TEST_MYSQL_USER') ?: 'root',
            '--pass', getenv('CI_TEST_MYSQL_PASSWORD') ?: '',
            '--port', (string) (getenv('CI_TEST_MYSQL_PORT') ?: 3306),
            '--role', $job['role'],
        ];
        if (isset($job['scope'])) {
            $cmd[] = '--scope';
            $cmd[] = (string) $job['scope'];
        }
        if (isset($job['item'])) {
            $cmd[] = '--item';
            $cmd[] = (string) $job['item'];
        }
        if (isset($job['scopeType'])) {
            $cmd[] = '--scope-type';
            $cmd[] = (string) $job['scopeType'];
        }
        if (isset($job['serialize'])) {
            $cmd[] = '--serialize';
            $cmd[] = (string) $job['serialize'];
        }
        if (isset($job['isolation'])) {
            $cmd[] = '--isolation';
            $cmd[] = (string) $job['isolation'];
        }
        if (isset($job['holdMs'])) {
            $cmd[] = '--hold-ms';
            $cmd[] = (string) $job['holdMs'];
        }
        if (isset($job['failAfter'])) {
            $cmd[] = '--fail-after';
            $cmd[] = (string) $job['failAfter'];
        }

        return $cmd;
    }

    /**
     * Run a single automation script (e.g. apply_migrations.php) to completion.
     *
     * @param list<string> $args
     *
     * @return array{exit:int,out:string,err:string}
     */
    public static function runScript(string $script, array $args, ?string $database = null): array
    {
        $common = [
            '--host', getenv('CI_TEST_MYSQL_HOST') ?: '127.0.0.1',
            '--user', getenv('CI_TEST_MYSQL_USER') ?: 'root',
            '--pass', getenv('CI_TEST_MYSQL_PASSWORD') ?: '',
            '--port', (string) (getenv('CI_TEST_MYSQL_PORT') ?: 3306),
        ];

        $cmd = array_merge([PHP_BINARY], [$script], $common, $args);
        $spec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $pipes = [];
        $process = proc_open($cmd, $spec, $pipes, realpath(__DIR__ . '/../../../'), null);
        if (!is_resource($process)) {
            throw new \RuntimeException('proc_open failed for ' . $script);
        }
        fclose($pipes[0]);
        $out = (string) stream_get_contents($pipes[1]);
        $err = (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exit = proc_close($process);

        return ['exit' => $exit, 'out' => $out, 'err' => $err];
    }

    /**
     * @param list<array{mode:string,scope:int,item:int}> $jobs
     *
     * @return list<array{exit:int,out:string,err:string,json:?array}>
     */
    public static function parallel(array $jobs, ?string $database = null): array
    {
        $handles = [];
        foreach ($jobs as $key => $job) {
            $handles[$key] = self::start($job, $database);
        }

        $results = [];
        foreach ($handles as $key => $handle) {
            $results[$key] = self::finish($handle);
        }

        return $results;
    }

    /**
     * Launch a single worker without waiting, so a caller can drive a competing
     * transaction while the worker is blocked on the scope lock.
     *
     * @param array{mode:string,scope:int,item:int,isolation?:string,serialize?:string} $job
     *
     * @return array{process:resource,pipes:array<int,resource>}
     */
    public static function start(array $job, ?string $database = null): array
    {
        $database = $database ?? ScaleDb::databaseName();

        $spec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $pipes = [];
        $process = proc_open(self::command($job, $database), $spec, $pipes, realpath(__DIR__ . '/../../../'), null);
        if (!is_resource($process)) {
            throw new \RuntimeException('proc_open failed for job ' . json_encode($job));
        }
        fclose($pipes[0]);

        return ['process' => $process, 'pipes' => $pipes];
    }

    /**
     * @param array{process:resource,pipes:array<int,resource>} $handle
     *
     * @return array{exit:int,out:string,err:string,json:?array}
     */
    public static function finish(array $handle): array
    {
        $pipes = $handle['pipes'];
        $out = (string) stream_get_contents($pipes[1]);
        $err = (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exit = proc_close($handle['process']);

        $json = null;
        $line = trim($out);
        if ($line !== '') {
            $decoded = json_decode($line, true);
            if (is_array($decoded)) {
                $json = $decoded;
            }
        }

        return ['exit' => $exit, 'out' => $line, 'err' => $err, 'json' => $json];
    }

    /**
     * @param array{mode:string,scope:int,item:int,isolation?:string,serialize?:string} $job
     *
     * @return list<string>
     */
    private static function command(array $job, string $database): array
    {
        $cmd = array_merge([PHP_BINARY, self::WORKER], [
            '--db', $database,
            '--host', getenv('CI_TEST_MYSQL_HOST') ?: '127.0.0.1',
            '--user', getenv('CI_TEST_MYSQL_USER') ?: 'root',
            '--pass', getenv('CI_TEST_MYSQL_PASSWORD') ?: '',
            '--port', (string) (getenv('CI_TEST_MYSQL_PORT') ?: 3306),
            '--mode', $job['mode'],
            '--scope', (string) $job['scope'],
            '--item', (string) $job['item'],
        ]);
        if (isset($job['isolation'])) {
            $cmd[] = '--isolation';
            $cmd[] = $job['isolation'];
        }
        if (isset($job['serialize'])) {
            $cmd[] = '--serialize';
            $cmd[] = $job['serialize'];
        }

        return $cmd;
    }
}