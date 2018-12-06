<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
    "NAME" => Loc::getMessage('RAYONNANT_TWO_COLUMNS_NAME'),
    "DESCRIPTION" => Loc::getMessage('RAYONNANT_TWO_COLUMNS_DESC'),
    "ICON" => '/images/icon.gif',
    "SORT" => 160,
    "PATH" => array(
        "ID" => 'rayonnant',
        "NAME" => Loc::getMessage('RAYONNANT_TWO_COLUMNS_GROUP'),
        "SORT" => 1,
    ),
);
