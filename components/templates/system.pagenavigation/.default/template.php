<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);

if (isset($_REQUEST["PAGEN_1"]) && $_REQUEST["PAGEN_1"] != $arResult["NavPageNomer"]) {
    define('ERROR_404', "Y");
}

/**
 * CustomPageNav start
 */

$nPageWindow = 5; //количество отображаемых страниц
if ($arResult["NavPageNomer"] > floor($nPageWindow / 2) + 1 && $arResult["NavPageCount"] > $nPageWindow) {
    $nStartPage = $arResult["NavPageNomer"] - floor($nPageWindow / 2);
} else {
    $nStartPage = 1;
}

if ($arResult["NavPageNomer"] <= $arResult["NavPageCount"] - floor($nPageWindow / 2) && $nStartPage + $nPageWindow - 1 <= $arResult["NavPageCount"]) {
    $nEndPage = $nStartPage + $nPageWindow - 1;
} else {
    $nEndPage = $arResult["NavPageCount"];
    if ($nEndPage - $nPageWindow + 1 >= 1) {
        $nStartPage = $nEndPage - $nPageWindow + 1;
    }
}
$arResult["nStartPage"] = $arResult["nStartPage"] = $nStartPage;
$arResult["nEndPage"] = $arResult["nEndPage"] = $nEndPage;

$url = isset($_SERVER['REAL_FILE_PATH']) && strlen($_SERVER['REAL_FILE_PATH']) ? $_SERVER['REAL_FILE_PATH'] : $_SERVER['SCRIPT_NAME'];
$url = str_replace(array('index.php', 'index.html'), '', $url);
$url = $APPLICATION->GetCurDir();

$arPrr = [];
$arRemove = array("PAGEN_1", "PAGEN_2", "PAGEN_3", "PAGEN_4", "page", "AJAX", "bxrand", "clear_cache", "clear_cache_session");

foreach ($_GET as $code => $val) {

    if (!empty($val) && !in_array($code, $arRemove)) {
        if (is_array($val)) {
            $arPrr[] = $code . '=' . implode(',', $val);
        } else {
            $arPrr[] = $code . '=' . $val;
        }
    }
}
$params = urldecode(implode('&', $arPrr));

/**
 * CustomPageNav end
 */

if (!$arResult["NavShowAlways"]) {
    if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false)) {
        return;
    }
}
$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"] . "&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?" . $arResult["NavQueryString"] : "");

$disabled = ($arResult["NavPageNomer"] == intval($arResult["NavPageCount"])) ? " disabled" : "";

?>
<div class="pagination">
    <?
    $newQuery = array();
    $arQuery = explode('&amp;', $arResult["NavQueryString"]);
    foreach ($arQuery as $val) {
        if ($val != 'AJAX=1') {
            $newQuery[] = $val;
        }
    }
    $arResult["NavQueryString"] = implode('&amp;', $newQuery);
    ?>
    <ul>
        <?
        $p = $params ? '?' . $params : '';
        $pr = $params ? '&' . $params : '';

        echo '<li><a href="' . $url . $p . '" class="first">&nbsp;</a></li>';

        while ($arResult["nStartPage"] <= $arResult["nEndPage"]) {

            $active = ($arResult["nStartPage"] == $arResult["NavPageNomer"]) ? " class='active'" : "";

            if ($arResult["nStartPage"] != 1) {
                $iurl = $url . '?page=' . $arResult["nStartPage"] . $pr;
            } else {
                $iurl = $url . $p;
            }

            echo '<li><a' . $active . ' href="' . $iurl . '">' . $arResult["nStartPage"] . '</a></li>';

            $arResult["nStartPage"]++;
        }

        echo '<li><a href="' . $url . '?page=' . $arResult["NavPageCount"] . $pr . '" class="last">&nbsp;</a></li>';

        ?>
    </ul>
</div>