<?php


namespace BloomLand\Core\utils;


use InvalidArgumentException;

class Utils
{

    /**
     * @param int $number
     * @param array $after
     * @param string $tag
     * @return string
     */
    public static function convertCase(int $number, array $after, string $tag = '') : string
    {
        $cases = [2, 0, 1, 1, 1, 2];

        if (count($after) < 3) {
            throw new InvalidArgumentException('Массив должен содержать 3 ключа, а у вас ' . count($after));
        }

        return $number . ' ' . $tag . $after[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }

    /**
     * @param int $ping
     * @return string
     */
    public static function pingToStatus(int $ping) : string
    {
        if ($ping < 60) {
            $status = ' §aОтличный';
        }
        elseif ($ping < 140) {
            $status = ' §cХороший';
        }
        elseif ($ping < 250) {
            $status = ' §eНестабильный';
        }
        elseif ($ping < 400) {
            $status = ' §cПлохой';
        }
        /** @var $status */
        return $status;
    }
}