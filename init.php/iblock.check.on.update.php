<?
/**
 * Проверка правильности заполнения значения элемента инфоблока
 */

use \Bitrix\Main\EventManager;

EventManager::getInstance()->addEventHandler("iblock", "OnBeforeIBlockElementAdd", ['MyClass', "check_dzn_tgb"]);
EventManager::getInstance()->addEventHandler("iblock", "OnBeforeIBlockElementUpdate", ['MyClass', "check_dzn_tgb"]);

class MyClass
{
    function check_dzn_tgb(&$arFields)
    {
        if ($arFields['IBLOCK_ID'] == 666) {

            $err = false;
            $e = new \CAdminException([]);

            // Массив с ошибками
            $arErrors = [
                "NAME" => 40,
            ];

            // Проверка названия
            if (strlen(trim($arFields["NAME"])) > $arErrors["NAME"]) {
                $e->AddMessage(array("text" => "Название должно быть не более {$arErrors['NAME']} символов",));
                $err = true;
            }

            if ($err) {
                global $APPLICATION;
                $APPLICATION->ThrowException($e);
                return false;
            }
        }
        return true;
    }
}