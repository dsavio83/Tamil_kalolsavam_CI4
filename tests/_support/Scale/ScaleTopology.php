<?php

declare(strict_types=1);

/**
 * Fixed multi-scope test topology (see .automation/scripts/mysql_scale_fixtures.sql).
 *
 *  - 4 districts (900..903) x 4 sub-districts each (9101..9116)
 *  - shared items:  9901 (UP single), 9902 (UP group), 9903 (HS single)
 *  - district-only: 9904 (UP single), 9905 (HS single)
 *  - participants:  sub-district p1..p8 -> items 9901/9903/9902
 *                   district j1..j6 -> 9901/9903/9904
 */

namespace Tests\Support\Scale;

final class ScaleTopology
{
    public const DISTRICTS = [900, 901, 902, 903];

    public const SUB_DISTRICTS = [
        900 => [9101, 9102, 9103, 9104],
        901 => [9105, 9106, 9107, 9108],
        902 => [9109, 9110, 9111, 9112],
        903 => [9113, 9114, 9115, 9116],
    ];

    public const SHARED_ITEM       = 9901;
    public const SHARED_GROUP_ITEM = 9902;
    public const SHARED_HS_ITEM    = 9903;
    public const DISTRICT_ONLY_UP  = 9904;
    public const DISTRICT_ONLY_HS  = 9905;

    public static function subDistricts(int $district): array
    {
        return self::SUB_DISTRICTS[$district] ?? [];
    }

    public static function allSubDistricts(): array
    {
        return array_merge(...array_values(self::SUB_DISTRICTS));
    }

    /**
     * The 2 school codes owned by a sub-district.
     */
    public static function schools(int $subDistrict): array
    {
        return [$subDistrict, $subDistrict + 1];
    }

    /**
     * sd participant id for (sub_district, position 1..8).
     */
    public static function participantId(int $subDistrict, int $position): int
    {
        return 911000 + ($subDistrict - 9100) * 100 + $position;
    }

    public static function admnNo(int $subDistrict, int $position): string
    {
        return $subDistrict . 'A' . $position;
    }

    /**
     * d participant id for (district, position 1..6).
     */
    public static function districtParticipantId(int $district, int $position): int
    {
        return 913000 + ($district - 899) * 100 + $position;
    }

    public static function districtAdmnNo(int $district, int $position): string
    {
        return $district . 'D' . $position;
    }
}