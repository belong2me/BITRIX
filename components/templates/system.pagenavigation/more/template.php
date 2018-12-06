<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->createFrame()->begin("Загрузка навигации");
?>
<? if ($arResult["NavPageCount"] > 1): ?>

    <? if ($arResult["NavPageNomer"] + 1 <= $arResult["nEndPage"]): ?>
        <?
        $plus = $arResult["NavPageNomer"] + 1;
        $url = $arResult["sUrlPathParams"] . "PAGEN_1=" . $plus
        ?>
        <div class="load_more col-24 flex justify-content-center" data-url="<?= $url ?>">
            <span class="next-page">...Еще...</span>
        </div>
    <? else: ?>

    <? endif ?>

<? endif ?>