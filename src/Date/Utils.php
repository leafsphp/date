<?php

namespace Leaf\Date;

class Utils
{
    /**
     * Parse JavaScript date format to PHP date format
     */
    public static function formatToPHP(string $format): string
    {
        $matches = [
            'YY' => 'y',
            'YYYY' => 'Y',
            'M' => 'n',
            'MM' => 'm',
            'MMM' => 'M',
            'MMMM' => 'F',
            'D' => 'j',
            'DD' => 'd',
            'd' => 'w',
            'dd' => 'D',
            'ddd' => 'D',
            'dddd' => 'l',
            'H' => 'G',
            'HH' => 'H',
            'h' => 'g',
            'hh' => 'h',
            'a' => 'a',
            'A' => 'A',
            'm' => 'i',
            'mm' => 'i',
            's' => 's',
            'ss' => 's',
            'SSS' => 'u',
            'Z' => 'Z',
            'T' => '\T',
        ];

        return preg_replace_callback('/\[([^\]]+)]|Y{1,4}|T|M{1,4}|D{1,2}|d{1,4}|H{1,2}|h{1,2}|a|A|m{1,2}|s{1,2}|Z{1,2}|SSS/', function ($match) use ($matches) {
            if (strpos($match[0], '[') === 0) {
                return preg_replace_callback('/\[(.*?)\]/', function ($matched) {
                    return preg_replace("/(.)/", "\\\\$1", $matched[1]);
                }, $match[0]);
            }

            return $matches[$match[0]] ?? $match[0];
        }, $format);
    }
}
