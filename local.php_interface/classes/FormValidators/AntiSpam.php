<?

namespace Rayonnant\FormValidators;

use \Rayonnant\StopSpam;
use \Bitrix\Main\EventManager;

/**
 * Class AntiSpam
 */
class AntiSpam
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
            "NAME" => "custom_antispam",
            "DESCRIPTION" => "Антиспам",
            "TYPES" => ["hidden"],
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
            if (StopSpam::check($_POST, $value)) {
                return true;
            }
        }
        $APPLICATION->ThrowException("Поздравляем, ты бот!");
        return false;
    }
}