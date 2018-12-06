<?php

use \Bitrix\Main\Page\Asset;

$customCss = '/local/templates/.default/css/custom.admin.css';

if (file_exists($_SERVER["DOCUMENT_ROOT"] . $customCss)) {
    global $APPLICATION;
    $APPLICATION->SetAdditionalCSS($customCss);
}