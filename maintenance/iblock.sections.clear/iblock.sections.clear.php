#!/usr/bin/env php
<?php set_time_limit(0);

use \Bitrix\Main\Loader;
use \Bitrix\Main\LoaderException;

if (count($argv) < 2) {
    echo <<< USAGE

Usage: php clear_section.php /path/to/document/root section_id

Скрипт для удаления секции инфоблока

Примеры использования:
    
    php clear_section.php /var/www/example.com 666

USAGE;
    exit(0);
}

////////////////////////////////////////////////////////////////////////////////////////////

$command='';
$commandvalue='';

// Проверяем аргументы
if (!isset($argv[1]) || !isset($argv[2])) {
    die("Не указаны необходимые параметры" . PHP_EOL);
}

$SECTION_IDS = explode(',', $argv[2]);

$_SERVER['DOCUMENT_ROOT'] = $DOCUMENT_ROOT = $argv[1];
#define("LANG", "ru");
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

$prolog = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
if (file_exists($prolog)) include_once($prolog); else die("Указанный катало не является корневой директорией сайта на 1С-Битрикс" . PHP_EOL);

try {
    Loader::includeModule('iblock');
} catch (LoaderException $e) {
    die;
}

foreach ($SECTION_IDS as $SECTION_ID) {

    $DB->StartTransaction();

    if (!CIBlockSection::Delete($SECTION_ID)) {
        $DB->Rollback();
        echo ("Ошибка удаления секции " . $SECTION_ID . PHP_EOL);
    } else {
        $DB->Commit();
        echo ("Секция " . $SECTION_ID . "удалена" . PHP_EOL);
    }
}
