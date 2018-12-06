<?

namespace Rayonnant\PropTypes\TextAndImage;

use \Bitrix\Main\EventManager;
use \Bitrix\Main\UI\FileInput;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Page\Asset;

/**
 * Кастомный тип свойства инфоблока - Фотогалерея
 * Class TextAndImageProp
 */
EventManager::getInstance()->addEventHandler("iblock", "OnIBlockPropertyBuildList", array("TextAndImageProp", "GetIBlockPropertyDescription"));
EventManager::getInstance()->addEventHandler("main", "OnUserTypeBuildList", array("TextAndImageProp", "GetUserTypeDescription"));

class TextAndImageProp extends \CUserTypeString
{
    public static $stylesSet;
    public static $scriptsSet;
    public static $processData = [];

    const USER_TYPE_ID = 'textimage';

    /**
     * Инициализация пользовательского свойства для главного модуля
     * @return array
     */
    function GetUserTypeDescription()
    {
        return array(
            "USER_TYPE_ID" => static::USER_TYPE_ID,
            "CLASS_NAME" => __CLASS__,
            "DESCRIPTION" => "Текст с картинкой",
            "BASE_TYPE" => \CUserTypeManager::BASE_TYPE_STRING
        );
    }

    /**
     * GetUserTypeDescription
     * @return array
     */
    function GetIBlockPropertyDescription()
    {
        return array(
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => static::USER_TYPE_ID,
            'DESCRIPTION' => 'Текст с картинкой',
            'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
            'GetAdminListViewHTML' => array(__CLASS__, 'GetAdminListViewHTML'),
            'GetPublicEditHTML' => array(__CLASS__, 'GetPublicEditHTML'),
            'GetPublicViewHTML' => array(__CLASS__, 'GetPublicViewHTML'),
            'OnBeforeSave' => array(__CLASS__, 'ConvertToDB'),
            'ConvertToDB' => array(__CLASS__, 'ConvertToDB'),
            'ConvertFromDB' => array(__CLASS__, 'ConvertFromDB'),
            "CheckFields" => array(__CLASS__, "CheckFields"),
            'PrepareSettings' => array(__CLASS__, 'PrepareSettings'),
            'GetSettingsHTML' => array(__CLASS__, 'GetSettingsHTML'),
        );
    }

    /**
     * Эта функция вызывается перед сохранением метаданных свойства в БД.
     * @param array $arUserField
     * @return array
     * @static
     */
    function PrepareSettings($arUserField)
    {
        return array(
            "SHOW_HEADER" => $arUserField["USER_TYPE_SETTINGS"]["SHOW_HEADER"],
            "TEXT_1_REQUIRED" => $arUserField["USER_TYPE_SETTINGS"]["TEXT_1_REQUIRED"]
        );
    }

    /**
     * Эта функция вызывается при выводе формы настройки свойства.
     * @param bool $arUserField
     * @param array $arHtmlControl
     * @param $bVarsFromForm
     * @return string HTML для вывода.
     * @static
     */
    function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm)
    {
        $result = '';

        $checked = $arUserField["USER_TYPE_SETTINGS"]["SHOW_HEADER"] == 'Y' ? ' checked' : '';
        $result .= '
        <tr>
			<td>Выводить поле Заголовок</td>
			<td>
			    <input type="hidden" name="' . $arHtmlControl["NAME"] . '[SHOW_HEADER]" value="N">
				<input type="checkbox"' . $checked . ' name="' . $arHtmlControl["NAME"] . '[SHOW_HEADER]" value="Y">
			</td>
		</tr>
		';

        $checked = $arUserField["USER_TYPE_SETTINGS"]["TEXT_1_REQUIRED"] == 'Y' ? ' checked' : '';
        $result .= '
        <tr>
			<td>Поле "Текст перед картинкой" - обязательное</td>
			<td>
			    <input type="hidden" name="' . $arHtmlControl["NAME"] . '[TEXT_1_REQUIRED]" value="N">
				<input type="checkbox"' . $checked . ' name="' . $arHtmlControl["NAME"] . '[TEXT_1_REQUIRED]" value="Y">
			</td>
		</tr>
		';

        return $result;
    }

    /**
     * Представление свойства
     * @param $arProperty
     * @param $value
     * @return string
     * @throws \Exception
     */
    function getViewHTML($arProperty, $value, $strHTMLControlName)
    {
        return self::getEditHTML($arProperty, $value, $strHTMLControlName);
    }

    /**
     * Редактирование свойства
     * @param $arProperty
     * @param $value
     * @return string
     * @throws Exception
     */
    function getEditHTML($arProperty, $value, $strHTMLControlName)
    {
        return self::GetPublicEditHTML($arProperty, $value, $strHTMLControlName);
    }

    /**
     * Редактирование свойства в форме и списке (инфоблок)
     * @param $arProperty
     * @param $value
     * @param $strHTMLControlName
     * @return string
     * @throws \Exception
     */
    function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        return $strHTMLControlName['MODE'] == 'FORM_FILL'
            ? self::getEditHTML($arProperty, $value, $strHTMLControlName)
            : self::getViewHTML($arProperty, $value, $strHTMLControlName);
    }


    /**
     * GetPropertyFieldHtml
     * @param $arProperty
     * @param $value
     * @param $strHTMLControlName
     * @return string
     * @throws \Exception
     */

    function GetPublicEditHTML($arProperty, $value, $strHTMLControlName)
    {
        $prefix = "{$strHTMLControlName['VALUE']}[IMAGE]";

        $class = static::USER_TYPE_ID;
        Loader::includeModule("fileman");

        echo self::getStyles();
        echo self::getScripts($arProperty['FIELD_ID']);

        $return = "<table class='{$class} bx-edit-table'><tr><td class='bx-field-value'>";
        $return .= "<input type='hidden' name='{$strHTMLControlName['VALUE']}[PREFIX]' value='{$prefix}'>";
        if ($arProperty["USER_TYPE_SETTINGS"]["SHOW_HEADER"] == 'Y') {
            $return .= "<input type='text' name='{$strHTMLControlName['VALUE']}[HEADER]' cols='100' placeholder='Заголовок (обязательное поле)' value='{$value['VALUE']['HEADER']}'/>";
        }
        $required = $arProperty["USER_TYPE_SETTINGS"]["TEXT_1_REQUIRED"] == 'Y' ? ' (обязательное поле)' : '';
        $return .= "<textarea name='{$strHTMLControlName['VALUE']}[TEXT_1]' rows='6' cols='100' placeholder='Текст перед картинкой{$required}'>{$value['VALUE']['TEXT_1']}</textarea>";
        $return .= "<textarea name='{$strHTMLControlName['VALUE']}[TEXT_2]' rows='6' cols='100' placeholder='Текст после картинки'>{$value['VALUE']['TEXT_2']}</textarea>";

        if ($value["VALUE"]["IMAGE"] === null && !empty($value["VALUE"]["IMAGE_"]) && !is_array($value["VALUE"]["IMAGE_"])) {
            $value["VALUE"]["IMAGE"] = $value["VALUE"]["IMAGE_"];
        }

        $return .= "</td><td>" . FileInput::createInstance(array(
                "name" => "{$strHTMLControlName['VALUE']}[IMAGE]",
                "description" => false,
                "upload" => true,
                "allowUpload" => "I",
                "medialib" => true,
                "fileDialog" => true,
                "cloud" => true,
                "delete" => true,
                "maxCount" => 1,
                "allowSort" => "N"
            ))->show(array($prefix => $value["VALUE"]["IMAGE"])) . "</td>";

        $return .= "<input class='shadow_img' type='hidden' name='{$strHTMLControlName['VALUE']}[IMAGE_]' value='{$value["VALUE"]["IMAGE"]}'>";
        $return .= "</tr></table>";

        return $return;
    }


    /**
     * ConvertToDB
     * @param $arProperty
     * @param $value
     * @return bool
     */
    static function ConvertToDB($arProperty, $value)
    {
        $prop_name = 'PROPERTY_' . $arProperty['ID'];

        // Сохранение нновой катинки
        if (!empty($value['VALUE']["IMAGE"]) && is_array($value['VALUE']["IMAGE"])) {
            $value['VALUE']["IMAGE"] = self::makeImage($value['VALUE']["IMAGE"]);
        }
        // Удаление старой картинки
        if (isset($_REQUEST[$prop_name . '_del'])) {
            $matches = null;
            if($returnValue = preg_match('/' . $prop_name . '\[([0-9]+)\].*/', $value['VALUE']['PREFIX'], $matches) && isset($matches[1])) {
                if (isset($_REQUEST[$prop_name . '_del'][$matches[1]]['VALUE']['IMAGE'])) {
                    CFile::Delete($value['VALUE']['IMAGE']);
                }
            }
        }

        if ((
                empty($value['VALUE']["HEADER"]) &&
                empty($value['VALUE']["TEXT_1"]) &&
                empty($value['VALUE']["TEXT_2"]) &&
                empty($value['VALUE']["IMAGE"])
            ) || $value['VALUE']["NAME"] == "_DELETE_"
        ) {
            return false;
        }

        $value['VALUE'] = !empty($value['VALUE']) ? serialize($value['VALUE']) : false;

        return $value;
    }

    /**
     * ConvertFromDB
     * @param $arProperty
     * @param $value
     * @return mixed
     */
    static function ConvertFromDB($arProperty, $value)
    {
        $value['VALUE'] = unserialize($value['VALUE']);
        return $value;
    }

    /**
     * @param array $arUserField
     * @param array|string $value
     * @return array
     */
    function CheckFields($arUserField, $value)
    {
        $errors = [];

        $arSettings = unserialize($arUserField['USER_TYPE_SETTINGS']);

        if ($arSettings["SHOW_HEADER"] == 'Y') {

            if (empty($value['VALUE']['HEADER'])) {
                if (!empty($value['VALUE']['TEXT_1']) || !empty($value['VALUE']['TEXT_2']) || !empty($value['VALUE']['IMAGE'])) {
                    if (!isset($_REQUEST['PROPERTY_' . $arUserField['ID'] . '_del'])) {
                        $errors[] = $arUserField["NAME"] . ' - Неоходимо ввести Заголовок';
                    }
                }
            } else {
                if (empty($value['VALUE']['TEXT_1']) && empty($value['VALUE']['TEXT_2']) && empty($value['VALUE']['IMAGE'])) {
                    //$errors[] = $arUserField["NAME"] . ' - Указан заголовок, но остальные поля пусты';
                }
            }
        }

        if ($arSettings["TEXT_1_REQUIRED"] == 'Y' && empty($value['VALUE']['TEXT_1'])) {
            if (!empty($value['VALUE']['TEXT_2']) || !empty($value['VALUE']['IMAGE'])) {
                $errors[] = $arUserField["NAME"] . ' - Обязательное поле "Текст перед картинкой" не заполнено';
            }
        }

        return $errors;
    }

    /**
     * makeImage
     * @param $arImg
     * @param int $size
     * @param bool $exact
     * @param bool $watermark
     * @return mixed
     */
    private static function makeImage($arImg, $size = 2000, $exact = false, $watermark = false)
    {
        $tmpRoot = CTempFile::GetAbsoluteRoot();

        if ($watermark) {
            $watermark = array(
                array(
                    "name" => "watermark",
                    "position" => "bottomleft",
                    "type" => "image",
                    "file" => $_SERVER["DOCUMENT_ROOT"] . "/upload/watermark/watermark.png",
                    "fill" => "resize",
                    "coefficient" => 0.3
                )
            );

            if ($size < 600) {
                $arWaterMark[0]['fill'] = 'resize';
                $arWaterMark[0]['coefficient'] = 0.5;
            }
        }

        $tmpImage["IMG"] = $arImg;
        $tmpImage["IMG"]["MODULE_ID"] = "cgallery";
        $tmpImage["IMG"]["tmp_name"] = $tmpRoot . $tmpImage["IMG"]["tmp_name"];

        $arImage = $tmpImage["IMG"];

        CFile::SaveForDB($tmpImage, "IMG", "cgallery");

        $arResizeFile = CFile::ResizeImageGet(
            $tmpImage["IMG"],
            array("width" => $size, 'height' => $size),
            $exact ? BX_RESIZE_IMAGE_EXACT : BX_RESIZE_IMAGE_PROPORTIONAL,
            true,
            $watermark
        );

        $arImage["old_file"] = $tmpImage["IMG"];
        $arImage["tmp_name"] = $_SERVER["DOCUMENT_ROOT"] . $arResizeFile["src"];
        $arImage["MODULE_ID"] = "cgallery";

        $arImage["ID"] = CFile::SaveFile($arImage, "cgallery");

        return $arImage["ID"] ? $arImage["ID"] : $tmpImage["IMG"];
    }


    /**
     * @param $prop_id
     * @return string
     */
    private static function getScripts($prop_id)
    {
        //if (!self::$scriptsSet) {
        //    self::$scriptsSet = true;
        \CJSCore::Init(["jquery"]);
        Asset::getInstance()->addString("<script>" . str_replace(['#PROP_ID#'], [$prop_id], file_get_contents(__DIR__ . '/scripts.js')) . "</script>");
        //}
        // return '';
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