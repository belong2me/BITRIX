<?
global $MESS; 
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class benequire_customsettings extends CModule
{
	var $MODULE_ID = "benequire.customsettings";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $PARTNER_NAME;
	var $PARTNER_URI;
	var $MODULE_GROUP_RIGHTS = "Y";

	function benequire_customsettings()
	{

		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");


		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("GCUSTOMSETTINGS_MODULE_NAME"); 
		$this->MODULE_DESCRIPTION = GetMessage("GCUSTOMSETTINGS_MODULE_DESC"); 
		$this->PARTNER_URI = GetMessage("GCUSTOMSETTINGS_PARTNER_URL");
		$this->PARTNER_NAME = GetMessage("GCUSTOMSETTINGS_PARTNER_NAME");
	}

	function DoInstall() 
	{
		
		$this->InstallFiles();

		RegisterModule("benequire.customsettings");
		
	}

	function DoUninstall()
	{

		global $APPLICATION;

		UnRegisterModule("benequire.customsettings");

		$this->UnInstallFiles();
			
		$GLOBALS["errors"] = $this->errors;

		COption::RemoveOption("benequire.customsettings");

		$APPLICATION->IncludeAdminFile(GetMessage("GCUSTOMSETTINGS_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/benequire.customsettings/install/unstep2.php");

	}


	function InstallFiles()
	{

		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/benequire.customsettings/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/benequire.customsettings/install/images",  $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/benequire.customsettings", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/benequire.customsettings/install/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);

		// copy settings files, because of marketplace no converting charset in files not in lang directory 
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/benequire.customsettings/lang/ru/default_settings", $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/benequire.customsettings/admin", true, true);

		return true;
	}	

	function UnInstallFiles()
	{

		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/benequire.customsettings/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");

		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/benequire.customsettings/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");//css
		DeleteDirFilesEx("/bitrix/themes/.default/icons/benequire.customsettings/");//icons
		DeleteDirFilesEx("/bitrix/themes/.default/start_menu/benequire.customsettings/");//start menu icons
		DeleteDirFilesEx("/bitrix/images/benequire.customsettings/");//images

		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/benequire.customsettings/lang/ru/default_settings/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/benequire.customsettings/admin"); // default settings files

		return true;
	}


	function GetModuleRightList()
	{
		$arr = Array(
			"reference_id" => array("D","R","S","W"),
			"reference" => array(
				"[D] ".GetMessage("GCUSTOMSETTINGS_RIGHTS_D"),
				"[R] ".GetMessage("GCUSTOMSETTINGS_RIGHTS_R"),
				"[S] ".GetMessage("GCUSTOMSETTINGS_RIGHTS_S"),
				"[W] ".GetMessage("GCUSTOMSETTINGS_RIGHTS_W"),
			)
		);
		return $arr;
	}

} 

?>