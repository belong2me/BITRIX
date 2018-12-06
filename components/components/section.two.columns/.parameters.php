<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

$arComponentParameters = array(
    "GROUPS" => array(),
    "PARAMETERS" => array(
        "HEADER" => array(
            "NAME" => Loc::getMessage('RAYONNANT_TWO_COLUMNS_PARAM_HEADER'),
            "TYPE" => "STRING",
        ),
        "LEFT_COLUMN" => Array(
            "NAME" => Loc::getMessage('RAYONNANT_TWO_COLUMNS_PARAM_LEFT_COLUMN'),
            "TYPE" => "CUSTOM",
            "JS_FILE" => $componentPath . "/script.js",
            "JS_EVENT" => "OnTextAreaConstruct",
            "DEFAULT" => null,
        ),
        "RIGHT_COLUMN" => array(
            "NAME" => Loc::getMessage('RAYONNANT_TWO_COLUMNS_PARAM_RIGHT_COLUMN'),
            "TYPE" => "CUSTOM",
            "JS_FILE" => $componentPath . "/script.js",
            "JS_EVENT" => "OnTextAreaConstruct",
            "DEFAULT" => null,
        ),
    )
);
?>