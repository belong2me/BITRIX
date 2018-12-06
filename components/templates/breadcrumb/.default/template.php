<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

global $APPLICATION;
if (empty($arResult)) {
    return "";
}
$strReturn = <<<HTML
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-24">
                    <ul class='breadcrumb-items' itemscope itemtype='http://schema.org/BreadcrumbList'>
HTML;

$itemSize = count($arResult);
if ($itemSize <= 1) {
    return false;
};
for ($index = 0; $index < $itemSize; $index++) {
    $title = htmlspecialcharsex($arResult[$index]["TITLE"]);

    if ($arResult[$index]["LINK"] <> "" && $index != $itemSize - 1) {
        $strReturn .= "<li class='breadcrumb-item' itemprop='itemListElement' itemscope itemtype='http://schema.org/ListItem'>"
            . "<a class='breadcrumb-link' itemprop='item' href='" . $arResult[$index]['LINK'] . "'><span itemprop='name'>" . $title . "</span></a>"
            . "<meta itemprop='position' content='" . ($index + 1) . "' />"
            . "</li>\r\n";
    } else {
        if ($itemSize - 1 == $index) {

            $strReturn .= "<li class='breadcrumb-item current' itemprop='itemListElement' itemscope itemtype='http://schema.org/ListItem'>"
                . "<meta itemprop='position' content='" . ($index + 1) . "' />"
                . "<span itemprop='name'>" . $title . "</span>"
                . "</li>\r\n";
        } else {
            $strReturn .= "<li class='breadcrumb-item' itemprop='itemListElement' itemscope itemtype='http://schema.org/ListItem'>"
                . "<a class='breadcrumb-link' itemprop='item' href='" . $arResult[$index]['LINK'] . "'><span itemprop='name'>" . $title . "</span></a>"
                . "<meta itemprop='position' content='" . ($index + 1) . "' />"
                . "</li>\r\n";
        }
    }
}

$strReturn .= <<<HTML
                    </ul>
                </div>
            </div>
        </div>
    </div>
HTML;

return $strReturn;