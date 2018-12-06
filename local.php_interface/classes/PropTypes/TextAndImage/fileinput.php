<?php

use \Bitrix\Main\UI\FileInput;

include($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (!defined('AJAX')) {
    define('AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
}

if (!AJAX) {
    die();
}
$name = htmlspecialcharsbx($_REQUEST['name']);
$prefix = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10);

ob_start();
echo FileInput::createInstance(array(
    "name" => "{$name}",
    "description" => false,
    "upload" => true,
    "allowUpload" => "I",
    "medialib" => true,
    "fileDialog" => true,
    "cloud" => true,
    "delete" => true,
    "maxCount" => 1,
    "allowSort" => "N"
))->show(["gal_img_" . $prefix => '']);
$output = ob_get_contents();
ob_end_clean();
die($output);