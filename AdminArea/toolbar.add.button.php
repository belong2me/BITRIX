<?
/**
 * Добавлени своей кнопки на панель
 */
use Bitrix\Main\EventManager;

EventManager::getInstance()->addEventHandler("main", "OnAdminContextMenuShow", ['MyClass', "MyOnAdminContextMenuShow"]);

class MyClass
{
    function MyOnAdminContextMenuShow(&$items)
    {
        //add custom button to the index page toolbar
        if ($GLOBALS["APPLICATION"]->GetCurPage(true) == "/bitrix/admin/iblock_element_edit.php") {
            $items[] = [
                "TEXT" => "TEST",
                "ICON" => "",
                "TITLE" => "TEST",
                "LINK" => "settings.php",
                "CLASS" => "asdas"
            ];
        }
    }
}
