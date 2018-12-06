<?php

namespace Rayonnant\PropTypes\Services;

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Iblock;
use \Bitrix\Main\Page\Asset;
use \Bitrix\Main\EventManager;

Loc::loadMessages(__FILE__);

/**
 * Class ServicesListProp
 * @package Rayonnant\FormValidators
 */
class ServicesListProp
{
    const USER_TYPE = 'ServicesList';

    public static $stylesSet;
    public static $scriptsSet;

    /**
     * AntiSpam constructor.
     */
    public function __construct()
    {
        EventManager::getInstance()->addEventHandler("iblock", "OnIBlockPropertyBuildList", [__CLASS__, "GetUserTypeDescription"]);
    }

    public static function GetUserTypeDescription()
    {
        return array(
            "PROPERTY_TYPE" => Iblock\PropertyTable::TYPE_ELEMENT,
            "USER_TYPE" => self::USER_TYPE,
            "DESCRIPTION" => Loc::getMessage('CUSTOM_PROP_SERVICE_DESC'),
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

    public static function PrepareSettings($arProperty)
    {
        $size = 0;
        if (is_array($arProperty["USER_TYPE_SETTINGS"])) {
            $size = intval($arProperty["USER_TYPE_SETTINGS"]["size"]);
        }
        if ($size <= 0) {
            $size = 1;
        }

        $width = 0;
        if (is_array($arProperty["USER_TYPE_SETTINGS"])) {
            $width = intval($arProperty["USER_TYPE_SETTINGS"]["width"]);
        }
        if ($width <= 0) {
            $width = 0;
        }

        if (is_array($arProperty["USER_TYPE_SETTINGS"]) && $arProperty["USER_TYPE_SETTINGS"]["group"] === "Y") {
            $group = "Y";
        } else {
            $group = "N";
        }

        if (is_array($arProperty["USER_TYPE_SETTINGS"]) && $arProperty["USER_TYPE_SETTINGS"]["multiple"] === "Y") {
            $multiple = "Y";
        } else {
            $multiple = "N";
        }

        return array(
            "size" => $size,
            "width" => $width,
            "group" => $group,
            "multiple" => $multiple,
        );
    }

    public static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
    {
        $settings = self::PrepareSettings($arProperty);

        $arPropertyFields = array(
            "HIDE" => array("ROW_COUNT", "COL_COUNT", "MULTIPLE_CNT"),
        );

        return '
		<tr valign="top">
			<td>' . Loc::getMessage("CUSTOM_PROP_SERVICE_SETTING_SIZE") . ':</td>
			<td><input type="text" size="5" name="' . $strHTMLControlName["NAME"] . '[size]" value="' . $settings["size"] . '"></td>
		</tr>
		<tr valign="top">
			<td>' . Loc::getMessage("CUSTOM_PROP_SERVICE_SETTING_WIDTH") . ':</td>
			<td><input type="text" size="5" name="' . $strHTMLControlName["NAME"] . '[width]" value="' . $settings["width"] . '">px</td>
		</tr>
		<tr valign="top">
			<td>' . Loc::getMessage("CUSTOM_PROP_SERVICE_SETTING_SECTION_GROUP") . ':</td>
			<td><input type="checkbox" name="' . $strHTMLControlName["NAME"] . '[group]" value="Y" ' . ($settings["group"] == "Y" ? 'checked' : '') . '></td>
		</tr>
		<tr valign="top">
			<td>' . Loc::getMessage("CUSTOM_PROP_SERVICE_SETTING_MULTIPLE") . ':</td>
			<td><input type="checkbox" name="' . $strHTMLControlName["NAME"] . '[multiple]" value="Y" ' . ($settings["multiple"] == "Y" ? 'checked' : '') . '></td>
		</tr>
		';
    }

    //PARAMETERS:
    //$arProperty - b_iblock_property.*
    //$value - array("VALUE","DESCRIPTION") -- here comes HTML form value
    //strHTMLControlName - array("VALUE","DESCRIPTION")
    //return:
    //safe html
    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        $settings = self::PrepareSettings($arProperty);
        if ($settings["size"] > 1) {
            $size = ' size="' . $settings["size"] . '"';
        } else {
            $size = '';
        }

        if ($settings["width"] > 0) {
            $width = ' style="width:' . $settings["width"] . 'px"';
        } else {
            $width = '';
        }

        $bWasSelect = false;
        $options = self::GetOptionsHtml($arProperty, array($value["VALUE"]), $bWasSelect);

        $html = '<select name="' . $strHTMLControlName["VALUE"] . '"' . $size . $width . '>';
        if ($arProperty["IS_REQUIRED"] != "Y") {
            $html .= '<option value=""' . (!$bWasSelect ? ' selected' : '') . '>' . Loc::getMessage("CUSTOM_PROP_SERVICE_NO_VALUE") . '</option>';
        }
        $html .= $options;
        $html .= '</select>';
        return $html;
    }

    public static function GetPropertyFieldHtmlMulty($arProperty, $value, $strHTMLControlName)
    {
        $max_n = 0;
        $values = [];
        $properties = [];

        if (is_array($value)) {
            foreach ($value as $property_value_id => $arValue) {
                if (is_array($arValue)) {
                    $values[$property_value_id] = $arValue["VALUE"];
                    $properties[$property_value_id] = unserialize($arValue["DESCRIPTION"]);
                } else {
                    $values[$property_value_id] = $arValue;
                }

                if (preg_match("/^n(\\d+)$/", $property_value_id, $match)) {
                    if ($match[1] > $max_n) {
                        $max_n = intval($match[1]);
                    }
                }
            }
        }

        $settings = self::PrepareSettings($arProperty);
        if ($settings["size"] > 1) {
            $size = ' size="' . $settings["size"] . '"';
        } else {
            $size = '';
        }

        if ($settings["width"] > 0) {
            $width = ' style="width:' . $settings["width"] . 'px"';
        } else {
            $width = '';
        }

        if (end($values) != "" || substr(key($values), 0, 1) != "n") {
            $values["n" . ($max_n + 1)] = "";
        }
        $name = $strHTMLControlName["VALUE"] . "VALUE";
        ob_start();
        ?>
        <div class="custom-prop-services">
            <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%" id="tb<?= md5($name) ?>">
                <? foreach ($values as $property_value_id => $value) {
                    $bWasSelect = false;
                    $options = self::GetOptionsHtml($arProperty, array($value), $bWasSelect);
                    ?>
                    <tr>
                        <td>
                            <div>
                                <select name="<?= $strHTMLControlName["VALUE"] ?>[<?= $property_value_id ?>][VALUE]" <?= $size . $width ?>>
                                    <option <?= (!$bWasSelect ? ' selected' : '') ?>><?= Loc::getMessage("CUSTOM_PROP_SERVICE_NO_VALUE") ?></option>
                                    <?= $options ?>
                                </select>
                            </div>
                            <div>
                                <input type="hidden"
                                       name="<?= $strHTMLControlName["VALUE"] ?>[<?= $property_value_id ?>][DESCRIPTION][VALUE]"
                                       value="<?= $values[$property_value_id] ?>"
                                >
                                <input type="number"
                                       placeholder="<?= Loc::getMessage('CUSTOM_PROP_SERVICE_VAL_PRICE') ?>"
                                       id="<?= $strHTMLControlName["VALUE"] ?>[<?= $property_value_id ?>][DESCRIPTION][PRICE]"
                                       name="<?= $strHTMLControlName["VALUE"] ?>[<?= $property_value_id ?>][DESCRIPTION][PRICE]"
                                       value="<?= $properties[$property_value_id]['PRICE'] ?>"
                                > руб.
                            </div>
                        </td>
                    </tr>
                <? } ?>
            </table>
            <input type="button"
                   value="<?= Loc::getMessage("CUSTOM_PROP_SERVICE_ADD") ?>"
                   onClick="addNewRow('tb<?= md5($name) ?>', -1)"
            >
        </div>
        <?= self::getStyles() ?>
        <?
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    public static function GetAdminFilterHTML($arProperty, $strHTMLControlName)
    {
        $lAdmin = new \CAdminList($strHTMLControlName["TABLE_ID"]);
        $lAdmin->InitFilter(array($strHTMLControlName["VALUE"]));
        $filterValue = $GLOBALS[$strHTMLControlName["VALUE"]];

        if (isset($filterValue) && is_array($filterValue)) {
            $values = $filterValue;
        } else {
            $values = array();
        }

        $settings = self::PrepareSettings($arProperty);
        if ($settings["size"] > 1) {
            $size = ' size="' . $settings["size"] . '"';
        } else {
            $size = '';
        }

        if ($settings["width"] > 0) {
            $width = ' style="width:' . $settings["width"] . 'px"';
        } else {
            $width = '';
        }

        $bWasSelect = false;
        $options = self::GetOptionsHtml($arProperty, $values, $bWasSelect);

        $html = '<select multiple name="' . $strHTMLControlName["VALUE"] . '[]"' . $size . $width . '>';
        $html .= '<option value="" ' . (!$bWasSelect ? 'selected' : '') . '>' . Loc::getMessage("CUSTOM_PROP_SERVICE_ANY_VALUE") . '</option>';
        $html .= $options;
        $html .= '</select>';
        return $html;
    }

    public static function GetPublicViewHTML($arProperty, $arValue, $strHTMLControlName)
    {
        static $cache = array();

        $strResult = '';
        $arValue['VALUE'] = intval($arValue['VALUE']);
        if (0 < $arValue['VALUE']) {
            $viewMode = '';
            $resultKey = '';
            if (!empty($strHTMLControlName['MODE'])) {
                switch ($strHTMLControlName['MODE']) {
                    case 'CSV_EXPORT':
                        $viewMode = 'CSV_EXPORT';
                        $resultKey = 'ID';
                        break;
                    case 'EXTERNAL_ID':
                        $viewMode = 'EXTERNAL_ID';
                        $resultKey = '~XML_ID';
                        break;
                    case 'SIMPLE_TEXT':
                        $viewMode = 'SIMPLE_TEXT';
                        $resultKey = '~NAME';
                        break;
                    case 'ELEMENT_TEMPLATE':
                        $viewMode = 'ELEMENT_TEMPLATE';
                        $resultKey = '~NAME';
                        break;
                }
            }

            if (!isset($cache[$arValue['VALUE']])) {
                $arFilter = [];
                $intIBlockID = (int)$arProperty['LINK_IBLOCK_ID'];
                if ($intIBlockID > 0) {
                    $arFilter['IBLOCK_ID'] = $intIBlockID;
                }
                $arFilter['ID'] = $arValue['VALUE'];
                if ($viewMode === '') {
                    $arFilter['ACTIVE'] = 'Y';
                    $arFilter['ACTIVE_DATE'] = 'Y';
                    $arFilter['CHECK_PERMISSIONS'] = 'Y';
                    $arFilter['MIN_PERMISSION'] = 'R';
                }
                $rsElements = \CIBlockElement::GetList(array(), $arFilter, false, false, array("ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL"));
                $cache[$arValue['VALUE']] = $rsElements->GetNext(true, true);
                unset($rsElements);
            }
            if (!empty($cache[$arValue['VALUE']]) && is_array($cache[$arValue['VALUE']])) {
                if ($viewMode !== '' && $resultKey !== '') {
                    $strResult = $cache[$arValue['VALUE']][$resultKey];
                } else {
                    $strResult = '<a href="' . $cache[$arValue['VALUE']]['DETAIL_PAGE_URL'] . '">' . $cache[$arValue['VALUE']]['NAME'] . '</a>';
                }
            }
        }
        return $strResult;
    }

    public static function GetOptionsHtml($arProperty, $values, &$bWasSelect)
    {
        $options = "";
        $settings = self::PrepareSettings($arProperty);
        $bWasSelect = false;

        if ($settings["group"] === "Y") {
            $arElements = self::GetElements($arProperty["LINK_IBLOCK_ID"]);
            $arTree = self::GetSections($arProperty["LINK_IBLOCK_ID"]);
            foreach ($arElements as $i => $arElement) {
                if (
                    $arElement["IN_SECTIONS"] == "Y"
                    && array_key_exists($arElement["IBLOCK_SECTION_ID"], $arTree)
                ) {
                    $arTree[$arElement["IBLOCK_SECTION_ID"]]["E"][] = $arElement;
                    unset($arElements[$i]);
                }
            }

            foreach ($arTree as $arSection) {
                $options .= '<optgroup label="' . str_repeat(" . ", $arSection["DEPTH_LEVEL"] - 1) . $arSection["NAME"] . '">';
                if (isset($arSection["E"])) {
                    foreach ($arSection["E"] as $arItem) {
                        $options .= '<option value="' . $arItem["ID"] . '"';
                        if (in_array($arItem["~ID"], $values)) {
                            $options .= ' selected';
                            $bWasSelect = true;
                        }
                        $options .= '>' . $arItem["NAME"] . '</option>';
                    }
                }
                $options .= '</optgroup>';
            }
            foreach ($arElements as $arItem) {
                $options .= '<option value="' . $arItem["ID"] . '"';
                if (in_array($arItem["~ID"], $values)) {
                    $options .= ' selected';
                    $bWasSelect = true;
                }
                $options .= '>' . $arItem["NAME"] . '</option>';
            }

        } else {
            foreach (self::GetElements($arProperty["LINK_IBLOCK_ID"]) as $arItem) {
                $options .= '<option value="' . $arItem["ID"] . '"';
                if (in_array($arItem["~ID"], $values)) {
                    $options .= ' selected';
                    $bWasSelect = true;
                }
                $options .= '>' . $arItem["NAME"] . '</option>';
            }
        }

        return $options;
    }

    /**
     * Returns data for smart filter.
     *
     * @param array $arProperty Property description.
     * @param array $value Current value.
     * @return false|array
     */
    public static function GetExtendedValue($arProperty, $value)
    {
        $html = self::GetPublicViewHTML($arProperty, $value, array('MODE' => 'SIMPLE_TEXT'));
        if (strlen($html)) {
            $text = htmlspecialcharsback($html);
            return array(
                'VALUE' => $text,
                'UF_XML_ID' => $text,
            );
        }
        return false;
    }

    public static function GetElements($IBLOCK_ID)
    {
        static $cache = array();
        $IBLOCK_ID = intval($IBLOCK_ID);

        if (!array_key_exists($IBLOCK_ID, $cache)) {
            $cache[$IBLOCK_ID] = array();
            if ($IBLOCK_ID > 0) {
                $arSelect = array(
                    "ID",
                    "NAME",
                    "IN_SECTIONS",
                    "IBLOCK_SECTION_ID",
                );
                $arFilter = array(
                    "IBLOCK_ID" => $IBLOCK_ID,
                    //"ACTIVE" => "Y",
                    "CHECK_PERMISSIONS" => "Y",
                );
                $arOrder = array(
                    "NAME" => "ASC",
                    "ID" => "ASC",
                );
                $rsItems = \CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
                while ($arItem = $rsItems->GetNext()) {
                    $cache[$IBLOCK_ID][] = $arItem;
                }
            }
        }
        return $cache[$IBLOCK_ID];
    }

    public static function GetSections($IBLOCK_ID)
    {
        static $cache = array();
        $IBLOCK_ID = intval($IBLOCK_ID);

        if (!array_key_exists($IBLOCK_ID, $cache)) {
            $cache[$IBLOCK_ID] = array();
            if ($IBLOCK_ID > 0) {
                $arSelect = array(
                    "ID",
                    "NAME",
                    "DEPTH_LEVEL",
                );
                $arFilter = array(
                    "IBLOCK_ID" => $IBLOCK_ID,
                    //"ACTIVE" => "Y",
                    "CHECK_PERMISSIONS" => "Y",
                );
                $arOrder = array(
                    "LEFT_MARGIN" => "ASC",
                );
                $rsItems = \CIBlockSection::GetList($arOrder, $arFilter, false, $arSelect);
                while ($arItem = $rsItems->GetNext()) {
                    $cache[$IBLOCK_ID][$arItem["ID"]] = $arItem;
                }
            }
        }
        return $cache[$IBLOCK_ID];
    }

    public static function ConvertToDB($arProperty, $value)
    {
        if (isset($value['VALUE']) && $value['VALUE'] > 0) {
            $value['VALUE'] = intval($value['VALUE']);
            $value['DESCRIPTION'] = serialize($value['DESCRIPTION']);
        }
        return $value;
    }

    public static function ConvertFromDB($arProperty, $value)
    {
        if (isset($value['VALUE'])) {
            $value['VALUE'] = intval($value['VALUE']);
        }
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
}