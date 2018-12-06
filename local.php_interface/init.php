<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/vendor/autoload.php");

new \Rayonnant\Site();

// FormValidators
new \Rayonnant\FormValidators\AntiSpam();
new \Rayonnant\FormValidators\Email();

// PropTypes
if (stristr($GLOBALS['APPLICATION']->GetCurDir(), '/bitrix', 0) ) {

    new \Rayonnant\PropTypes\Services\ServicesListProp();
    new \Rayonnant\PropTypes\PriceList\PriceListProp();
    new \Rayonnant\PropTypes\IblockSection\IblockSectionProp();
}