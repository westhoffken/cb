<?php

namespace App\Utils;

/**
 * Static methods omdat deze klein en makkelijk inzetbaar zijn daarnaast vindt ik het code technisch overzichterlijker
 */
class OperatorUtils
{

    /**
     *
     */
    public const ALLOWED_OPERATORS = ['*', '+', '-', '/', '%', '**'];

    /**
     * @param int $key
     * @param array $commands
     * @return bool
     */
    public static function needsOperatorBefore(int $key, array $commands): bool
    {
        return isset($commands[$key - 1]) && !in_array($commands[$key - 1], self::ALLOWED_OPERATORS, true);

    }

    /**
     * @param int $key
     * @param array $commands
     * @return bool
     */
    public static function needsOperatorAfter(int $key, array $commands): bool
    {
        return isset($commands[$key + 1]) && !in_array($commands[$key + 1], self::ALLOWED_OPERATORS, true);
    }

}
