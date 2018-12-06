<?

namespace Rayonnant\FormValidators;

use \Bitrix\Main\EventManager;

/**
 * Class Email
 */
class Email
{
    /**
     * AntiSpam constructor.
     */
    public function __construct()
    {
        EventManager::getInstance()->addEventHandler("form", "onFormValidatorBuildList", [__CLASS__, "GetDescription"]);
    }

    /**
     * @return array
     */
    function GetDescription()
    {
        return [
            "NAME" => "custom_email",
            "DESCRIPTION" => "Email + кириллица",
            "TYPES" => ["email"],
            "HANDLER" => [__CLASS__, "DoValidate"]
        ];
    }

    /**
     * @param $arParams
     * @param $arQuestion
     * @param $arAnswers
     * @param $arValues
     * @return bool
     */
    function DoValidate($arParams, $arQuestion, $arAnswers, $arValues)
    {
        global $APPLICATION;
        foreach ($arValues as $value) {
            if (empty($value)) continue;
            $isEmail = preg_match('/^[-a-zа-я0-9~!$%^&*_=+}{\'?].*@([a-zа-я0-9_].*\.(рф|aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/', strtolower($value));
            if (!$isEmail) {
                $APPLICATION->ThrowException("#FIELD_NAME#: введенное значение должно быть e-mail адресом");
                return false;
            }
        }
        return true;
    }
}