<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);
?>
<section id="description">
    <div class="container">
        <div class="row">
            <div class="col-24">
                <h2>
                    <?= $arParams["HEADER"]?>
                </h2>
                <div class="description-flex">
                    <? if (!empty($arParams["LEFT_COLUMN"])) { ?>
                        <div class="description-flex__column">
                            <?= html_entity_decode($arParams["LEFT_COLUMN"])?>
                        </div>
                    <? } ?>
                    <? if (!empty($arParams["RIGHT_COLUMN"])) { ?>
                        <div class="description-flex__column">
                            <?= html_entity_decode($arParams["RIGHT_COLUMN"])?>
                        </div>
                    <? } ?>
                </div>
            </div>
        </div>
    </div>
</section>