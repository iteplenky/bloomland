<?php


namespace BloomLand\Core\lang;


    use BloomLand\Core\Core;

    class Language
    {
        public const DEFAULT_LANGUAGE = 'ru_RU';

        protected static $lang = [];

        public static function init() : void
        {
            self::$lang[self::DEFAULT_LANGUAGE] = self::loadLang(__DIR__ . '/locale/' . self::DEFAULT_LANGUAGE . '.ini');

            // LOAD LANGS
            foreach (glob(__DIR__ . '/locale/*.ini') as $locale) {

                $localeName = substr(basename($locale), 0, -4);
                if ($localeName == self::DEFAULT_LANGUAGE) continue;

                $loadLang = self::loadLang($locale);

                if (empty($loadLang)) continue;
                
                self::$lang[$localeName] = array_merge(self::$lang[self::DEFAULT_LANGUAGE], $loadLang);
            
            }

            Core::getAPI()->getLogger()->info('LanguageManager > §b'  . count(self::$lang) . '§r языков загружено!');
        }

        public static function getLanguages() : array 
        {
            return array_keys(self::$lang);
        }

        public static function getTranslatedLanguageNames() : array 
        {
            return array_map(function(string $locale) : string {
                return Language::translate($locale, 'language.name');
            }, self::getLanguages());
        }

        public static function loadLang(string $path) : array 
        {
            return file_exists($path) ? array_map('stripcslashes', parse_ini_file($path, false, INI_SCANNER_RAW)) : [];
        }

        public static function mergeLanguage(string $locale, array $langFile) : void 
        {
            self::$lang[$locale] = array_merge((self::$lang[$locale] ?? []), $langFile);
        }

        public static function translate(string $locale, string $text, array $args = []) : string 
        {
            $translatedText = self::$lang[$locale][$text] ?? null;
            
            if ($translatedText === null) return $text;

            elseif (!empty($args)) $translatedText = sprintf ($translatedText, ...$args);
            
            return $translatedText;
        }

        public static function translateExtended(string $locale, string $text, array $args = [], string $separator = '%') : string 
        {
            $isEmpty = empty($args);
            
            if (!$isEmpty) array_unshift($args, -1);

            return preg_replace_callback('/' . $separator . '[a-zA-Z._]+/mi', function(array $str) use($locale, $isEmpty, $args) {
                $str = $str[0];
                return self::translate($locale, substr($str, 1), $isEmpty ? []: next($args));
            }, $text);
        }

        public static function translateOrExtended(string $locale, string $text, array $parameters = [], string $separator = '%') : string 
        {
            return isset(self::$lang[$locale][$text]) ? self::translate($locale, $text, $parameters) : self::translateExtended($locale, $text, $parameters, $separator);
        }

        public static function getLang(string $locale) : string 
        {
            return isset(self::$lang[$locale]) ? $locale: self::DEFAULT_LANGUAGE;
        }

    }

?>
