<?php

namespace Rayonnant;

/**
 * Class Functions
 * @package Rayonnant
 */
class Functions
{
    /**
     * Trims text to a space then adds ellipses if desired
     * @param string $input text to trim
     * @param int $length in characters to trim to
     * @param bool $ellipses if ellipses (...) are to be added
     * @param bool $strip_html if html tags are to be stripped
     * @return string
     */
    public static function trimText($input, $length, $ellipses = true, $strip_html = true)
    {
        if ($strip_html) {
            $input = strip_tags($input);
        }

        if (strlen($input) <= $length) {
            return $input;
        }

        $last_space = strrpos(substr($input, 0, $length), ' ');
        $trimmed_text = substr($input, 0, $last_space);

        if ($ellipses) {
            $trimmed_text .= '...';
        }

        return $trimmed_text;
    }

    /**
     * truncateString
     *
     * @param string $string
     * @param int $limit
     * @param string $break
     * @param string $pad
     * @return string
     */
    public static function truncateString($string, $limit, $break = ".", $pad = "...")
    {
        if (strlen($string) <= $limit) {
            return $string;
        }

        if (false !== ($breakpoint = strpos($string, $break, $limit))) {
            if ($breakpoint < strlen($string) - 1) {
                $string = substr($string, 0, $breakpoint) . $pad;
            }
        }
        return $string;
    }

    /**
     * Make a String’s First Character Uppercase - Multibyte (UTF-8)
     *
     * @param $str
     * @param string $encoding
     * @param bool $lower_str_end
     * @return string
     */
    public static function mb_ucfirst($str, $encoding = "UTF-8", $lower_str_end = false)
    {
        $first_letter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
        if ($lower_str_end) {
            $str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
        } else {
            $str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
        }
        $str = $first_letter . $str_end;
        return $str;
    }

    /**
     * Make a String’s First Character Lowercase - Multibyte (UTF-8)
     *
     * @param $str
     * @param string $encoding
     * @return string
     */
    public static function mb_lcfirst($str, $encoding = "UTF-8")
    {
        $first_letter = mb_strtolower(mb_substr($str, 0, 1, $encoding), $encoding);
        $str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
        $str = $first_letter . $str_end;
        return $str;
    }

    /**
     * Amount in words
     * @param int $num
     * @param bool $units
     * @uses morph(...)
     * @return mixed|string
     */
    public static function num2str($num, $units = true)
    {
        $nul = 'ноль';
        $ten = array(
            array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
            array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
        );
        $a20 = array(
            'десять',
            'одиннадцать',
            'двенадцать',
            'тринадцать',
            'четырнадцать',
            'пятнадцать',
            'шестнадцать',
            'семнадцать',
            'восемнадцать',
            'девятнадцать'
        );
        $tens = array(2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
        $hundred = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
        $unit = array( // Units
            array('копейка', 'копейки', 'копеек', 1),
            array('рубль', 'рубля', 'рублей', 0),
            array('тысяча', 'тысячи', 'тысяч', 1),
            array('миллион', 'миллиона', 'миллионов', 0),
            array('миллиард', 'милиарда', 'миллиардов', 0),
        );

        list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub) > 0) {
            foreach (str_split($rub, 3) as $uk => $v) { // by 3 symbols
                if (!intval($v)) {
                    continue;
                }
                $uk = sizeof($unit) - $uk - 1; // unit key
                $gender = $unit[$uk][3];
                list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2 > 1) {
                    $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3];
                } # 20-99
                else {
                    $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3];
                } # 10-19 | 1-9
                // units without rub & kop
                if ($uk > 1) {
                    $out[] = self::morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
                }
                if ($units) {
                    return $out[1];
                }
            } //foreach
        } else {
            $out[] = $nul;
        }
        $out[] = self::morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
        $out[] = $kop . ' ' . self::morph($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
        return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
    }

    /**
     * Decline the wordform
     * @param string $n
     * @param string $f1
     * @param string $f2
     * @param string $f5
     * @return string
     */
    public static function morph($n, $f1, $f2, $f5)
    {
        $n = abs(intval($n)) % 100;
        if ($n > 10 && $n < 20) {
            return $f5;
        }
        $n = $n % 10;
        if ($n > 1 && $n < 5) {
            return $f2;
        }
        if ($n == 1) {
            return $f1;
        }
        return $f5;
    }

    /**
     * get phone from grain.customsettings
     *
     * @param string $phone
     * @param string $type
     * @return string
     */
    public static function getPhone($phone, $type = 'link')
    {
        $phone = \COption::GetOptionString("grain.customsettings", $phone);

        if (empty($phone)) {
            return false;
        }
        return self::parcePhone($phone, $type);
    }

    /**
     * Parce phone
     *
     * @param $phone
     * @param string $type
     * @return string
     */
    public static function parcePhone($phone, $type = 'link')
    {
        switch ($type) {
            case 'text':
                return strip_tags($phone);
                break;
            case 'html':
                return $phone;
                break;
            case 'link':
                return "<a href='tel:" . preg_replace('/(\s|-|\(|\))+/', '', strip_tags($phone)) . "' class='tel'>" . $phone . "</a>";
                break;
            case 'whatsapp':
                return "<a href='https://api.whatsapp.com/send?phone=" . preg_replace('/(\+|\s|-|\(|\))+/', '',
                        strip_tags($phone)) . "' class='tel' target='_blank'>" . $phone . "</a>";
                break;
            default:
                return "";
        }
    }

    /**
     * get email from grain.customsettings
     *
     * @param string $mail
     * @param string $type
     * @return string
     */
    public static function getMail($mail, $type = 'link')
    {
        $mail = \COption::GetOptionString("grain.customsettings", $mail);

        if (empty($mail)) {
            return false;
        }
        return self::parceMail($mail, $type);

    }

    /**
     * Parce email
     * @param $mail
     * @param string $type
     * @return string
     */
    public static function parceMail($mail, $type = 'link')
    {
        switch ($type) {
            case 'link':
                return "<a href='mailto:" . $mail . "' class='mail' target='_blank'>" . $mail . "</a>";
                break;
            default:
                return "";
        }
    }

    /**
     * Массив свойств элемента инфоблока для выборки
     * @param $arProps
     * @return array
     */
    public static function getProps($arProps)
    {
        $result = [];

        global $DB;
        $DB->Query('SET SESSION group_concat_max_len = 1000000', true);

        foreach ($arProps as $code) {
            $result[$code . '_PROP'] = [
                'data_type' => 'Bitrix\Iblock\PropertyTable',
                'reference' => ['=this.IBLOCK_ID' => 'ref.IBLOCK_ID'],
                'join_type' => "LEFT"
            ];
            $result[$code] = [
                'data_type' => 'float',
                'expression' => [
                    '(SELECT GROUP_CONCAT(b_iblock_element_property.VALUE SEPARATOR "|||") 
                      FROM b_iblock_element_property 
                      WHERE b_iblock_element_property.IBLOCK_PROPERTY_ID=%s AND b_iblock_element_property.IBLOCK_ELEMENT_ID=%s
                    )',
                    $code . '_PROP.ID',
                    'ID',
                ],
            ];
        }

        return $result;
    }
}