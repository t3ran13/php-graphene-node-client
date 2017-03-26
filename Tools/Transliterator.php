<?php


namespace GrapheneNodeClient\Tools;

class Transliterator
{
    const LANG_RU = 'ru';

    /** @var array  */
    private static $langs = [
        //order is impotent
        self::LANG_RU => [
            'ые' => 'yie',
            'щ' => 'shch',
            'ш' => 'sh',
            'ч' => 'ch',
            'ц' => 'cz',
            'й' => 'ij',
            'ё' => 'yo',
            'э' => 'ye',
            'ю' => 'yu',
            'я' => 'ya',
            'х' => 'kh',
            'ж' => 'zh',
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'з' => 'z',
            'и' => 'i',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'ъ' => 'xx',
            'ы' => 'y',
            'ь' => 'x',
            'ґ' => 'g',
            'є' => 'e',
            'і' => 'i',
            'ї' => 'i'
        ]
    ];

    /**
     * @param string $str
     * @param string $lang
     * @param bool $reverse
     * @return string
     */
    private static function transliterate($str, $lang, $reverse = false)
    {
        foreach (self::$langs[$lang] as $lFrom => $lTo) {
            if ($reverse) {
                $from = $lTo;
                $to = $lFrom;
            } else {
                $from = $lFrom;
                $to = $lTo;
            }
            $str = str_replace($from, $to, $str);
            $str = str_replace(mb_strtoupper($from, 'utf-8'), mb_strtoupper($to, 'utf-8'), $str);
        }

        return $str;
    }

    /**
     * @param string $str
     * @param string $pattern
     * @param array $original
     * @return string
     */
    private static function restoreTechnicalData($str, $pattern, $original)
    {
        preg_match_all($pattern, $str, $damaged, PREG_PATTERN_ORDER);

        foreach ($damaged[0] as $key => $el) {
            $str = str_replace($el, $original[0][$key], $str);
        }

        return $str;
    }

    /**
     * @param string $str
     * @param string $fromLang
     * @return string
     */
    public static function encode($str, $fromLang)
    {
        if (!$str) {
            return $str;
        }

        $s = '/[^[\]]+(?=])/';
        $t = '/<(.|\n)*?>/';
        preg_match_all($s, $str, $orig, PREG_PATTERN_ORDER);
        preg_match_all($t, $str, $tags, PREG_PATTERN_ORDER);

        $str = self::transliterate($str, $fromLang);

        if (!empty($orig[0])) {
            self::restoreTechnicalData($str, $s, $orig);
        }

        if (!empty($tags[0])) {
            self::restoreTechnicalData($str, $t, $tags);
        }

        return $str;
    }

    /**
     * @param string $str
     * @param string $toLang
     * @return string
     */
    public static function decode($str, $toLang)
    {
        if (!$str || substr($str, 0, 4) !== ($toLang . '--')) {
            return $str;
        }
        $str = substr($str, 4);

        $s = '/[^[\]]+(?=])/';
        $t = '/<(.|\n)*?>/';
        preg_match_all($s, $str, $orig, PREG_PATTERN_ORDER);
        preg_match_all($t, $str, $tags, PREG_PATTERN_ORDER);

        $str = self::transliterate($str, $toLang, true);

        if (!empty($orig[0])) {
            self::restoreTechnicalData($str, $s, $orig);
        }

        if (!empty($tags[0])) {
            self::restoreTechnicalData($str, $t, $tags);
        }

        return $str;
    }
}