<?php

namespace App\Libraries;

/**
 * Central resolver for a competition item's live status.
 *
 * The single source of truth is the stage manager's actions (start / finish)
 * combined with result publication. Only four states are exposed:
 *
 *   not_started       - stage manager has not started the item yet.
 *   ongoing           - stage manager started (STARTED / PAUSED / IN_PROGRESS).
 *   completed         - item finished, results are not published yet.
 *   result_published  - results have been published.
 */
class CompetitionStatus
{
    public const NOT_STARTED = 'not_started';
    public const ONGOING = 'ongoing';
    public const COMPLETED = 'completed';
    public const RESULT_PUBLISHED = 'result_published';

    private const ONGOING_STATES = ['STARTED', 'PAUSED', 'IN_PROGRESS', 'ONGOING', 'LIVE'];
    private const COMPLETED_STATES = ['COMPLETED', 'FINISHED', 'DONE'];

    private const DEFINITIONS = [
        self::NOT_STARTED => [
            'label'    => 'போட்டி துவங்கவில்லை',
            'badge'    => 'badge-scheduled',
            'icon'     => 'bi-calendar2-check',
            'priority' => 1,
        ],
        self::ONGOING => [
            'label'    => 'நடைபெறுகிறது',
            'badge'    => 'badge-live',
            'icon'     => 'bi-broadcast',
            'priority' => 2,
        ],
        self::COMPLETED => [
            'label'    => 'முடிவு வெளியிடவில்லை',
            'badge'    => 'badge-completed',
            'icon'     => 'bi-flag-fill',
            'priority' => 3,
        ],
        self::RESULT_PUBLISHED => [
            'label'    => 'முடிவு வெளியானது',
            'badge'    => 'badge-declared',
            'icon'     => 'bi-check-circle-fill',
            'priority' => 4,
        ],
    ];

    /**
     * Resolve the status for a single row/record.
     *
     * Accepted keys (aliases supported):
     *   stage_status / status, actual_start_time, actual_end_time,
     *   is_finish, is_published / published.
     *
     * @param array|object $row
     * @return array{state:string,label:string,badge:string,icon:string,priority:int}
     */
    public static function resolve($row): array
    {
        $state = self::state($row);

        return ['state' => $state] + self::DEFINITIONS[$state];
    }

    /**
     * Return only the canonical state key.
     */
    public static function state($row): string
    {
        $data = is_object($row) ? get_object_vars($row) : (array) $row;

        $isPublished = self::flag($data, ['is_published', 'published', 'has_published_result']);
        $isFinished  = self::flag($data, ['is_finish', 'result_finished', 'is_completed']);
        $status      = strtoupper(trim((string) self::first($data, ['stage_status', 'status', 'stage_state'])));
        $startedAt   = self::first($data, ['actual_start_time', 'started_at']);
        $endedAt     = self::first($data, ['actual_end_time', 'ended_at', 'completed_at']);

        if ($isPublished) {
            return self::RESULT_PUBLISHED;
        }

        if ($isFinished || in_array($status, self::COMPLETED_STATES, true) || self::hasTimestamp($endedAt)) {
            return self::COMPLETED;
        }

        if (in_array($status, self::ONGOING_STATES, true) || self::hasTimestamp($startedAt)) {
            return self::ONGOING;
        }

        return self::NOT_STARTED;
    }

    public static function badgeClass(string $state): string
    {
        return self::DEFINITIONS[$state]['badge'] ?? self::DEFINITIONS[self::NOT_STARTED]['badge'];
    }

    public static function label(string $state): string
    {
        return self::DEFINITIONS[$state]['label'] ?? self::DEFINITIONS[self::NOT_STARTED]['label'];
    }

    /**
     * Sort comparator: later stages of the lifecycle first.
     */
    public static function compare(string $a, string $b): int
    {
        return (self::DEFINITIONS[$b]['priority'] ?? 0) <=> (self::DEFINITIONS[$a]['priority'] ?? 0);
    }

    private static function flag(array $data, array $keys): bool
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $data)) {
                continue;
            }

            $value = $data[$key];
            if (is_bool($value)) {
                if ($value) {
                    return true;
                }
                continue;
            }

            if (in_array(strtoupper(trim((string) $value)), ['Y', 'YES', '1', 'TRUE', 'PUBLISHED'], true)) {
                return true;
            }
        }

        return false;
    }

    private static function first(array $data, array $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $data) && $data[$key] !== null && $data[$key] !== '') {
                return $data[$key];
            }
        }

        return null;
    }

    private static function hasTimestamp($value): bool
    {
        if (empty($value)) {
            return false;
        }

        $value = trim((string) $value);
        if ($value === '' || strpos($value, '0000-00-00') === 0) {
            return false;
        }

        return strtotime($value) !== false;
    }
}
