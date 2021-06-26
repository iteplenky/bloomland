<?php
/**
 *  Copyright (C) BloomLand, Inc - All Rights Reserved
 *  Unauthorized copying of this file, via any medium is strictly prohibited
 *  Proprietary and confidential
 *  Written by Eren Ahmet Akyol <ahmederen123@gmail.com>, July 2019
 */
declare(strict_types=1);

namespace BloomLand\Core\utils;

use pocketmine\utils\TextFormat;

class TextUtils{

    public const RAINBOW_COLORS = [
        TextFormat::RED,
        TextFormat::GOLD,
        TextFormat::YELLOW,
        TextFormat::GREEN,
        TextFormat::AQUA,
        TextFormat::BLUE,
        TextFormat::LIGHT_PURPLE
    ];

    public const NUMBER_TO_COLOR = [
        TextFormat::DARK_GRAY,
        TextFormat::GRAY,
        TextFormat::RED,
        TextFormat::YELLOW,
        TextFormat::DARK_GREEN,
        TextFormat::GREEN,
        TextFormat::BLUE,
        TextFormat::DARK_PURPLE,
        TextFormat::LIGHT_PURPLE,
        TextFormat::DARK_RED,
        TextFormat::GOLD
    ];

    public const CharWidths = [
        '!' => 2,
        '"' => 5,
        '\'' => 3,
        '(' => 5,
        ')' => 5,
        '*' => 5,
        ',' => 2,
        '.' => 2,
        ':' => 2,
        ';' => 2,
        '<' => 5,
        '>' => 5,
        '@' => 7,
        'I' => 4,
        'İ' => 4,
        '[' => 4,
        ']' => 4,
        'f' => 5,
        'i' => 2,
        'ı' => 2,
        'k' => 5,
        'l' => 3,
        't' => 4,
        '' => 5,
        '|' => 2,
        '~' => 7,
        '█' => 9,
        '░' => 8,
        '▒' => 9,
        '▓' => 9,
        '▌' => 5,
        '─' => 9
        //'-' => 9,
    ];

    /** @var string[] */
    private static $numberToColor = [];

    public static function init() : void{
        // number to color
        self::$numberToColor[0] = self::$numberToColor[1] = self::NUMBER_TO_COLOR[0];
        for($number = 2, $index = 0; $index < count(self::NUMBER_TO_COLOR); $index++){
            while($number % 10 !== 0){
                self::$numberToColor[$number++] = self::NUMBER_TO_COLOR[$index];
            }
            self::$numberToColor[$number++] = self::NUMBER_TO_COLOR[$index];
        }
    }

    public static function convert(int $number, array $after): string {
        $cases = [2, 0, 1, 1, 1, 2];

        if (count($after) < 3) {
            throw new InvalidArgumentException("3 - min");
        }

        return $number . ' ' . $after[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }
    
    public static function rainbow(string $str) : string{
        if(empty($str)){
            return $str;
        }

        $str = trim($str);
        $text = '';
        $max = count(self::RAINBOW_COLORS);
        for($i = 0; $i < strlen($str); ++$i){
            $char = $str[$i];
            if($char !== ' '){
                $char = self::RAINBOW_COLORS[$max - ($max - $i % $max)] . $char;
            }
            $text .= $char;
        }

        return $text;
    }

    public static function numberToColor(int $number) : string{
        return self::$numberToColor[$number] ?? TextFormat::GOLD;
    }

    /**
     * @param $text
     * @return int|string
     */
    public static function toNumber($text){
        return is_numeric($text) ? $text + 0 : $text;
    }

    public static function toArray(string $text, bool $useKeys = false, string $separator = '&') : array{
        if(empty($text)){
            return [];
        }

        if($useKeys){
            parse_str($text, $output);
            $output = array_map("self::toNumber", $output);
        }else{
            $output = explode($separator, $text);
        }

        return $output;
    }

    public static function fromArray(array $array, bool $useKeys = false, string $separator = '&') : string{
        return empty($array) ? '' : ($useKeys ? http_build_query($array, '', $separator, PHP_QUERY_RFC3986) : implode($separator, $array));
    }

    public static function classStringToName(string $class) : string{
        return ($pos = strrpos($class, '\\')) !== false ? substr($class, $pos + 1) : $class;
    }

    public static function center(string $input, int $maxLength = 0) : string{
        $lines = explode(TextFormat::EOL, trim($input));

        $sortedLines = $lines;
        usort($sortedLines, function(string $a, string $b){
            return self::getPixelLength($b) <=> self::getPixelLength($a);
        });

        $longest = $sortedLines[0];

        if($maxLength === 0){
            $maxLength = self::getPixelLength($longest);
        }

        $result = '';
        foreach($lines as $sortedLine){
            $result .= str_repeat(' ', (int) round(max($maxLength - self::getPixelLength($sortedLine), 0) / 8)) . $sortedLine . TextFormat::EOL;
        }

        return rtrim($result, TextFormat::EOL);
    }

    public static function getPixelLength(string $line) : int{
        $length = 0;
        foreach(preg_split('//u', TextFormat::clean($line), -1, PREG_SPLIT_NO_EMPTY) as $c){
            $length += self::CharWidths[$c] ?? 6;
        }

        // +1 for each bold character
        $length += substr_count($line, TextFormat::BOLD);
        return $length;
    }

    public static function inText(string $needle, string $haystack) : bool{
    	$needle = preg_quote($needle);
    	return preg_match("/\b$needle\b/i", $haystack) === 1;
    }
}