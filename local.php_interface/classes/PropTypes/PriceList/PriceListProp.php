<?php

namespace Rayonnant\PropTypes\PriceList;

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\EventManager;
use \Bitrix\Main\Page\Asset;
use \Bitrix\Iblock;

Loc::loadMessages(__FILE__);

/**
 * Тип свойства Цены
 * Class PriceListProp
 */
class PriceListProp
{
    const USER_TYPE = 'pricelist';

    public static $stylesSet;
    public static $scriptsSet;
    public static $services;
    public static $segments;

    /**
     * AntiSpam constructor.
     */
    public function __construct()
    {
        EventManager::getInstance()->addEventHandler("iblock", "OnIBlockPropertyBuildList", [__CLASS__, "GetUserTypeDescription"]);
    }

    /**
     * Инициализация пользовательского свойства для инфоблока
     * @return array
     */
    public static function GetUserTypeDescription()
    {
        return array(
            "PROPERTY_TYPE" => Iblock\PropertyTable::TYPE_STRING,
            "USER_TYPE" => self::USER_TYPE,
            "DESCRIPTION" => Loc::getMessage('CUSTOM_PROP_PRICE_NAME'),
            'CheckFields' => array(__CLASS__, 'CheckFields'),
            "GetPropertyFieldHtml" => array(__CLASS__, "GetPropertyFieldHtml"),
            "GetPropertyFieldHtmlMulty" => array(__CLASS__, "GetPropertyFieldHtmlMulty"),
            "GetPublicEditHTML" => array(__CLASS__, "GetPropertyFieldHtml"),
            "GetPublicEditHTMLMulty" => array(__CLASS__, "GetPropertyFieldHtmlMulty"),
            "GetPublicViewHTML" => array(__CLASS__, "GetPublicViewHTML"),
            "GetAdminFilterHTML" => array(__CLASS__, "GetAdminFilterHTML"),
            "PrepareSettings" => array(__CLASS__, "PrepareSettings"),
            "GetSettingsHTML" => array(__CLASS__, "GetSettingsHTML"),
            "GetExtendedValue" => array(__CLASS__, "GetExtendedValue"),
            'ConvertToDB' => array(__CLASS__, 'ConvertToDB'),
            'ConvertFromDB' => array(__CLASS__, 'ConvertFromDB'),
        );
    }

    /**
     * Валидация
     * @param array $arUserField
     * @param string $value
     * @return array
     */
    function CheckFields($arUserField, $value)
    {
        if (empty($value)) {
            return [];
        }

        $aMsg = [];
        return $aMsg;
    }

    /**
     * @param $arProperty
     * @param $value
     * @param $strHTMLControlName
     * @return string
     * @throws \Exception
     */
    public static function GetPropertyFieldHtmlMulty($arProperty, $value, $strHTMLControlName)
    {
        $max_n = 0;
        $values = [];

        if (!self::getServices() || !self::getSegments()) {
            return '';
        }

        if (is_array($value)) {

            foreach ($value as $id => $arValue) {

                if (!$arValue["VALUE"] || empty($arValue["VALUE"]['SERVICE'])) {
                    continue;
                }

                $values[$id] = is_array($arValue) ? $arValue["VALUE"] : $arValue;

                if (preg_match("/^n(\\d+)$/", $id, $match) && $match[1] > $max_n) {
                    $max_n = intval($match[1]);
                }
            }
        }

        if (!count($values)) {
            $values["n" . (++$max_n)] = "";
        }

        if (end($values) != "" || substr(key($values), 0, 1) != "n" || count($values) == 1) {
            $values["n" . (++$max_n)] = "";
        }

        $name = $strHTMLControlName["VALUE"] . "VALUE";

        ob_start();
        ?>
        <div class="price-list-prop">
            <table cellpadding="0" cellspacing="0" border="0" class="nopadding outer" id="tb<?= md5($name) ?>">
                <?
                $first = true;
                foreach ($values as $id => $value) { ?>
                    <tr>
                        <td>
                            <table cellpadding="0" cellspacing="0" border="0" class="nopadding inner<?= $first ? ' first' : '' ?>">
                                <tr>
                                    <? if ($first) : ?>
                                        <? $first = false; ?>
                                        <td>
                                            <input type="hidden" name="<?= $strHTMLControlName["VALUE"] ?>[<?= $id ?>][VALUE][SERVICE]" value="1">
                                            <span><?= Loc::getMessage('CUSTOM_PROP_PRICE_TABLE_SERVICE')?></span>
                                        </td>
                                        <td>
                                            <span><?= Loc::getMessage('CUSTOM_PROP_PRICE_TABLE_LEVEL')?></span>
                                        </td>
                                        <? if (!isset($value['PRICES'])) {
                                            $value = ['PRICES' => ['']];
                                        }
                                        foreach ($value['PRICES'] as $key => $price) { ?>
                                            <td>
                                                <div>
                                                    <input type="number"
                                                           name="<?= $strHTMLControlName["VALUE"] ?>[<?= $id ?>][VALUE][PRICES][<?= $key ?>][0]"
                                                           value="<?= $value["PRICES"][$key][0] ?>"
                                                    >
                                                    -
                                                    <input type="number"
                                                           name="<?= $strHTMLControlName["VALUE"] ?>[<?= $id ?>][VALUE][PRICES][<?= $key ?>][1]"
                                                           value="<?= $value["PRICES"][$key][1] ?>"
                                                    >
                                                </div>
                                            </td>
                                        <? } ?>
                                        <td class="add-col">
                                            <input type="button" class="adm-btn-add">
                                            <input type="button" class="adm-btn-del">
                                        </td>

                                    <? else: ?>

                                        <td>
                                            <select class="service" name="<?= $strHTMLControlName["VALUE"] ?>[<?= $id ?>][VALUE][SERVICE]">
                                                <option value=""> не выбрано</option>
                                                <? foreach (self::$services as $section => $arItems) { ?>
                                                    <?= ($section && count($arItems)) ? '<optgroup label="' . $arItems[0]["SECTION_NAME"] . '">' : ''; ?>
                                                    <? foreach ($arItems as $arItem) {
                                                        $root = empty($arItem['SECTION_ID']) ? ' class="root"' : '';
                                                        $selected = ($arItem['ID'] == $value['SERVICE']) ? ' selected' : '';
                                                        echo "<option{$root}{$selected} value='{$arItem["ID"]}'>{$arItem["NAME"]}</option>";
                                                    } ?>
                                                    <?= $section && count($arItems) ? '</optgroup>' : ''; ?>
                                                <? } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="level" name="<?= $strHTMLControlName["VALUE"] ?>[<?= $id ?>][VALUE][LEVEL]">
                                                <option value=""> не выбрано</option>
                                                <? foreach (self::$segments as $sid => $sname) {
                                                    $selected = $value["LEVEL"] == $sid ? ' selected' : '';
                                                    echo "<option{$selected} value='{$sid}'>{$sname}</option>";
                                                } ?>
                                            </select>
                                        </td>
                                        <? if (!isset($value['PRICES'])) {
                                            if ($count = count(array_values($values)[0]['PRICES']) ?: 1) {
                                                $value = ['PRICES' => array_fill(0, $count, '')];
                                            }
                                        }
                                        foreach ($value['PRICES'] as $key => $price) { ?>
                                            <td>
                                                <input type="number"
                                                       name="<?= $strHTMLControlName["VALUE"] ?>[<?= $id ?>][VALUE][PRICES][<?= $key ?>]"
                                                       value="<?= $value["PRICES"][$key] ?>"
                                                >
                                            </td>
                                        <? } ?>
                                        <td class="add-row">
                                            <? if (array_search($id, array_keys($values)) > 1) { ?>
                                                <input type="button" class="adm-btn-remove">
                                            <? } else { ?>
                                                <div>&nbsp;</div>
                                            <? } ?>
                                        </td>
                                    <? endif; ?>
                                </tr>
                            </table>
                        </td>
                    </tr>
                <? } ?>
            </table>
            <input type="button" value="<?= Loc::getMessage("CUSTOM_PROP_PRICE_TABLE_SUBMIT") ?>" onClick="addNewRow('tb<?= md5($name) ?>', -1)">
        </div>
        <?= self::getStyles() ?>
        <?= self::getScripts($arProperty['ID']); ?>
        <?
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    public static function ConvertToDB($arProperty, $value)
    {
        if (isset($value['VALUE']['SERVICE']) && $value['VALUE']['SERVICE'] > 0) {
            $value['VALUE'] = serialize($value['VALUE']);
        }
        return $value;
    }

    public static function ConvertFromDB($arProperty, $value)
    {
        $value['VALUE'] = unserialize($value['VALUE']);
        return $value;
    }

    /**
     * @return string
     */
    private static function getStyles()
    {
        if (!self::$stylesSet) {
            self::$stylesSet = true;
            Asset::getInstance()->addString("<style>" . file_get_contents(__DIR__ . '/styles.css') . "</style>");
        }
        return '';
    }

    /**
     * @param $prop_id
     * @return string
     */
    private static function getScripts($prop_id)
    {
        if (!self::$scriptsSet) {

            self::$scriptsSet = true;
            \CJSCore::Init(["jquery"]);
            $url = str_replace($_SERVER["DOCUMENT_ROOT"], '', __FILE__);

            Asset::getInstance()->addString("<script>" . str_replace(
                    ['#PROP_ID#', '#URL#'],
                    [$prop_id, $url],
                    file_get_contents(__DIR__ . '/scripts.js')) . "</script>");
        }
        return '';
    }

    /**
     * @return bool
     * @throws \Exception
     */
    private static function getServices()
    {
        if (empty(self::$services)) {

            try {
                \Bitrix\Main\Loader::includeModule('iblock');
                $obServices = \Bitrix\Iblock\ElementTable::getList([
                    'select' => ['ID', 'IBLOCK_ID', 'NAME', 'SECTION_ID' => 'IBLOCK_SECTION_ID', 'SECTION_NAME' => 'IBLOCK_SECTION.NAME'],
                    'filter' => ['IBLOCK_ID' => 2, 'ACTIVE' => 'Y'],
                    'order' => ['SORT' => 'ASC']
                ]);

                while ($arService = $obServices->fetch()) {
                    self::$services[$arService['SECTION_NAME'] ?: 0][] = $arService;
                }

            } catch (\Exception $e) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    private static function getSegments()
    {
        if (empty(self::$segments)) {

            try {
                \Bitrix\Main\Loader::includeModule('iblock');
                $obSegments = \CIBlockPropertyEnum::GetList(Array("DEF" => "DESC", "SORT" => "ASC"), Array(18, "CODE" => "FILTER_SEGMENT"));
                while ($segment = $obSegments->GetNext()) {
                    self::$segments[$segment['ID']] = $segment['VALUE'];
                }

            } catch (\Exception $e) {
                return false;
            }
        }
        return true;
    }
}