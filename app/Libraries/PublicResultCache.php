<?php

namespace App\Libraries;

/**
 * Scope-aware application cache for public result pages.
 *
 * Keys are intentionally namespaced by district and sub-district IDs.
 * A publish in one scope changes only that scope, its parent district, and
 * the home-page discovery cache. Old in-flight rebuilds remain under the
 * previous version key and cannot overwrite the new version.
 */
class PublicResultCache
{
    public const TTL_SECONDS = 45;
    public const VERSION_TTL_SECONDS = 2592000; // 30 days

    protected static function cache()
    {
        return service('cache');
    }

    protected static function token(): string
    {
        try {
            return dechex((int) (microtime(true) * 1000000)) . bin2hex(random_bytes(3));
        } catch (\Throwable $e) {
            return dechex((int) (microtime(true) * 1000000)) . mt_rand(1000, 9999);
        }
    }

    public static function versionKey(string $scope, ?int $districtCode = null, ?int $subDistrictCode = null): string
    {
        if ($scope === 'subdistrict') {
            return 'tk25_pub_ver_sd_d' . (int) $districtCode . '_sd' . (int) $subDistrictCode;
        }
        if ($scope === 'district') {
            return 'tk25_pub_ver_d' . (int) $districtCode;
        }
        return 'tk25_pub_ver_home';
    }

    public static function version(string $scope = 'home', ?int $districtCode = null, ?int $subDistrictCode = null): string
    {
        $cache = self::cache();
        $key = self::versionKey($scope, $districtCode, $subDistrictCode);
        $value = $cache->get($key);
        return (is_string($value) || is_numeric($value)) && (string) $value !== ''
            ? (string) $value
            : '1';
    }

    public static function pageKey(string $scope, ?int $districtCode = null, ?int $subDistrictCode = null, ?string $version = null): string
    {
        $version = $version ?? self::version($scope, $districtCode, $subDistrictCode);
        if ($scope === 'subdistrict') {
            return 'tk25_pub_result_subdistrict_d' . (int) $districtCode . '_sd' . (int) $subDistrictCode . '_v' . $version;
        }
        if ($scope === 'district') {
            return 'tk25_pub_result_district_d' . (int) $districtCode . '_v' . $version;
        }
        return 'tk25_pub_result_home_v' . $version;
    }

    public static function itemResultsKey(?int $districtCode, ?int $subDistrictCode, string $itemCode): string
    {
        if ($subDistrictCode) {
            $scope = 'subdistrict';
            $version = self::version($scope, $districtCode, $subDistrictCode);
            return 'tk25_pub_item_d' . (int) $districtCode . '_sd' . (int) $subDistrictCode . '_i' . self::safePart($itemCode) . '_v' . $version;
        }
        if ($districtCode) {
            $scope = 'district';
            $version = self::version($scope, $districtCode, null);
            return 'tk25_pub_item_d' . (int) $districtCode . '_i' . self::safePart($itemCode) . '_v' . $version;
        }
        $version = self::version('home');
        return 'tk25_pub_item_all_i' . self::safePart($itemCode) . '_v' . $version;
    }

    protected static function safePart(string $value): string
    {
        $clean = preg_replace('/[^a-zA-Z0-9_-]/', '', $value);
        return substr((string) $clean, 0, 48) ?: 'unknown';
    }

    protected static function bump(string $scope, ?int $districtCode = null, ?int $subDistrictCode = null): void
    {
        $saved = self::cache()->save(
            self::versionKey($scope, $districtCode, $subDistrictCode),
            self::token(),
            self::VERSION_TTL_SECONDS
        );
        if (!$saved) {
            log_message('error', 'PublicResultCache: failed to bump version for scope ' . $scope);
        }
    }

    public static function subdistrictInfo(int $subDistrictCode): ?array
    {
        if ($subDistrictCode <= 0) {
            return null;
        }

        $homeVersion = self::version('home');
        $key = 'tk25_pub_subdistrict_info_' . $subDistrictCode . '_hv' . $homeVersion;
        $cache = self::cache();
        $cached = $cache->get($key);
        if (is_array($cached) && array_key_exists('active', $cached)) {
            return !empty($cached['active']) && is_array($cached['row'] ?? null)
                ? $cached['row']
                : null;
        }

        $row = null;
        try {
            $row = \Config\Database::connect()->table('sub_district_master')
                ->select('sub_district_code, rev_district_code')
                ->where('sub_district_code', $subDistrictCode)
                ->get()->getRowArray();
        } catch (\Throwable $e) {
            log_message('warning', 'PublicResultCache: subdistrict lookup failed for ' . $subDistrictCode);
        }

        $active = is_array($row) && self::isActiveSubdistrict($subDistrictCode);
        $cache->save($key, ['active' => $active, 'row' => $row ?: []], self::TTL_SECONDS);
        return $active ? $row : null;
    }

    public static function districtExists(int $districtCode): bool
    {
        if ($districtCode <= 0) {
            return false;
        }

        $version = self::version('district', $districtCode);
        $key = 'tk25_pub_district_exists_' . $districtCode . '_v' . $version;
        $cache = self::cache();
        $cached = $cache->get($key);
        if (is_array($cached) && array_key_exists('exists', $cached)) {
            return (bool) $cached['exists'];
        }

        $exists = false;
        try {
            $db = \Config\Database::connect();
            $builder = $db->table('rev_district_master')
                ->where('rev_district_code', $districtCode);
            if ($db->fieldExists('status', 'rev_district_master')) {
                $builder->where('status', 'Y');
            }
            $exists = $builder->countAllResults() > 0;
        } catch (\Throwable $e) {
            log_message('warning', 'PublicResultCache: district lookup failed for ' . $districtCode);
        }

        $cache->save($key, ['exists' => $exists], self::TTL_SECONDS);
        return $exists;
    }

    /**
     * A subdistrict is public/active when at least one school record exists in
     * either the subdistrict school master or imported district school master.
     * The cached answer is versioned by the home scope so school changes can
     * make a newly-active subdistrict visible immediately after invalidation.
     */
    public static function isActiveSubdistrict(int $subDistrictCode): bool
    {
        if ($subDistrictCode <= 0) {
            return false;
        }

        $homeVersion = self::version('home');
        $key = 'tk25_pub_active_sd_' . $subDistrictCode . '_hv' . $homeVersion;
        $cache = self::cache();
        $cached = $cache->get($key);
        if (is_array($cached) && array_key_exists('active', $cached)) {
            return (bool) $cached['active'];
        }

        $db = \Config\Database::connect();
        $exists = false;
        try {
            $exists = $db->table('sub_district_master')
                ->where('sub_district_code', $subDistrictCode)
                ->countAllResults() > 0;
        } catch (\Throwable $e) {
            log_message('warning', 'PublicResultCache: subdistrict existence check failed for ' . $subDistrictCode . ': ' . $e->getMessage());
        }

        $cache->save($key, ['active' => $exists], self::TTL_SECONDS);
        return $exists;
    }

    /**
     * Remember a public result payload and serialize concurrent cache rebuilds
     * for the same key using a filesystem lock (works with shared hosting).
     *
     * @return array{data:mixed,hit:bool}
     */
    public static function remember(string $key, int $ttl, callable $builder): array
    {
        $cache = self::cache();
        $cached = $cache->get($key);
        if (is_array($cached)) {
            return ['data' => $cached, 'hit' => true];
        }

        $lockDir = defined('WRITEPATH') ? WRITEPATH . 'cache/tk25-public-locks' : null;
        $lockHandle = null;
        if ($lockDir) {
            try {
                if (!is_dir($lockDir)) {
                    @mkdir($lockDir, 0775, true);
                }
                if (is_dir($lockDir) && is_writable($lockDir)) {
                    $lockIdentity = preg_replace('/_v[^_]+$/', '', $key) ?: $key;
                    $lockHandle = @fopen($lockDir . '/' . hash('sha256', $lockIdentity) . '.lock', 'c');
                    if ($lockHandle) {
                        @flock($lockHandle, LOCK_EX);
                    }
                }
            } catch (\Throwable $e) {
                $lockHandle = null;
            }
        }

        try {
            // Another request may have populated the cache while we waited.
            $cached = $cache->get($key);
            if (is_array($cached)) {
                return ['data' => $cached, 'hit' => true];
            }

            $data = $builder();
            if (is_array($data) && !$cache->save($key, $data, $ttl)) {
                log_message('warning', 'PublicResultCache: failed to save cache key ' . $key);
            }
            return ['data' => $data, 'hit' => false];
        } finally {
            if (is_resource($lockHandle)) {
                @flock($lockHandle, LOCK_UN);
                @fclose($lockHandle);
            }
        }
    }

    /**
     * Invalidate only the affected subdistrict, its parent district, and home.
     * For a district-wide change pass $subDistrictCode = null.
     */
    public static function invalidate(?int $districtCode = null, ?int $subDistrictCode = null): void
    {
        $districtCode = $districtCode ? (int) $districtCode : null;
        $subDistrictCode = $subDistrictCode ? (int) $subDistrictCode : null;

        if ($subDistrictCode && !$districtCode) {
            try {
                $row = \Config\Database::connect()
                    ->table('sub_district_master')
                    ->select('rev_district_code')
                    ->where('sub_district_code', $subDistrictCode)
                    ->get()->getRowArray();
                if ($row && !empty($row['rev_district_code'])) {
                    $districtCode = (int) $row['rev_district_code'];
                }
            } catch (\Throwable $e) {
                log_message('warning', 'PublicResultCache: could not resolve parent district for subdistrict ' . $subDistrictCode);
            }
        }

        // The home page contains active district/subdistrict discovery and highlights.
        self::bump('home');

        if ($districtCode) {
            self::bump('district', $districtCode);
        }

        if ($districtCode && $subDistrictCode) {
            self::bump('subdistrict', $districtCode, $subDistrictCode);
        }
    }

    /**
     * Invalidate all currently known public scopes after a full dataset import/
     * restore. Use only for truly global replacement operations.
     */
    public static function invalidateAll(): void
    {
        self::bump('home');
        try {
            $db = \Config\Database::connect();
            if ($db->tableExists('rev_district_master')) {
                foreach ($db->table('rev_district_master')->select('rev_district_code')->get()->getResultArray() as $row) {
                    self::bump('district', (int) $row['rev_district_code']);
                }
            }
            if ($db->tableExists('sub_district_master')) {
                foreach ($db->table('sub_district_master')->select('sub_district_code, rev_district_code')->get()->getResultArray() as $row) {
                    self::bump('subdistrict', (int) $row['rev_district_code'], (int) $row['sub_district_code']);
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'PublicResultCache: global invalidation partially failed: ' . $e->getMessage());
        }
    }
}
