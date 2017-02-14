AddEventHandler("main", "OnAdminContextMenuShow", "MyOnAdminContextMenuShow");
function MyOnAdminContextMenuShow(&$items)
{
    //add custom button to the index page toolbar
    if($GLOBALS["APPLICATION"]->GetCurPage(true) == "/bitrix/admin/iblock_element_edit.php")
    {
        $items[] = array("TEXT"=>"TEST", "ICON"=>"", "TITLE"=>"TEST", "LINK"=>"javascript:alert('asd');", "CLASS" =>"asdas");
        // init jQuery from core
        CJSCore::Init(array("jquery"));
    }
}
