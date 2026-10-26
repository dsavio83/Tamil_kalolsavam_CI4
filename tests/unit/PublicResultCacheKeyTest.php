<?php

namespace Tests\Unit;

use App\Libraries\PublicResultCache;
use CodeIgniter\Test\CIUnitTestCase;

final class PublicResultCacheKeyTest extends CIUnitTestCase
{
    public function testSubdistrictKeyContainsBothDistrictAndSubdistrictIds(): void
    {
        $key = PublicResultCache::pageKey('subdistrict', 9, 213, 'abc123');

        $this->assertSame('tk25_pub_result_subdistrict_d9_sd213_vabc123', $key);
    }

    public function testDifferentSubdistrictsHaveDifferentCacheKeys(): void
    {
        $first = PublicResultCache::pageKey('subdistrict', 9, 213, '1');
        $second = PublicResultCache::pageKey('subdistrict', 9, 214, '1');

        $this->assertNotSame($first, $second);
    }

    public function testDistrictAndSubdistrictCachesAreSeparated(): void
    {
        $district = PublicResultCache::pageKey('district', 9, null, '1');
        $subdistrict = PublicResultCache::pageKey('subdistrict', 9, 213, '1');

        $this->assertNotSame($district, $subdistrict);
    }

    public function testHomeCacheIsSeparateFromEveryDistrict(): void
    {
        $home = PublicResultCache::pageKey('home', null, null, '1');
        $district = PublicResultCache::pageKey('district', 9, null, '1');

        $this->assertNotSame($home, $district);
    }
}
