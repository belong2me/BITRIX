<?php

namespace Rayonnant;

use \Bitrix\Main\Application;
use \Bitrix\Main\Page\Asset;
use \Bitrix\Main\SiteTable;
use \Bitrix\Main\SystemException;
use \Bitrix\Main\EventManager;
use \Bitrix\Main\Service\GeoIp\Manager as Geo;

/**
 * Class Site
 * @package Rayonnant
 */
class Site
{
    /**
     * Main page or not
     * @var bool
     */
    private static $home;

    /**
     * @var string
     * site name
     */
    private static $name;

    /**
     * @var string
     * main template path
     */
    public static $tpl;

    /**
     * Site constructor.
     */
    public function __construct()
    {
        $this->defineVariables();
        $this->setRefCookie();
        $this->setRegionCookie();
        $this->setCanonical();

        $inst = EventManager::getInstance();
        $inst->addEventHandler("main", "OnAfterEpilog", [__CLASS__, "Error404"]); //404
        $inst->addEventHandler("main", "OnEndBufferContent", [__CLASS__, "AddDeferToJs"]); //Отложить загрузку скриптов
    }

    /**
     * Define variables
     */
    private function defineVariables()
    {
        try {
            $request = Application::getInstance()->getContext()->getRequest();
            $arSite = SiteTable::getById('s1')->fetchAll()[0];

            self::$home = $request->getPhpSelf() == '/index.php';
            self::$name = $arSite["SITE_NAME"];

        } catch (SystemException $e) {
        }
    }

    /**
     * Set Referral Source cookie
     */
    private function setRefCookie()
    {
        if (empty($_COOKIE['ref'])) {
            $r_ref_f = $_SERVER['HTTP_REFERER'];
            $r_ref_full = $_SERVER['HTTP_REFERER'];

            if (!empty($r_ref_f)) {
                $r_self_f = $_SERVER['SERVER_NAME'];
                preg_match("/([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}/", $r_ref_f, $m);
                $r_ref_f = $m[0];
                $r_ref = $r_ref_f;
                $r_self = $r_self_f;
                while (substr_count($r_ref, ".") > 1) {
                    $r_ref = substr($r_ref, strpos($r_ref, ".") + 1);
                }
                while (substr_count($r_self, ".") > 1) {
                    $r_self = substr($r_self, strpos($r_self, ".") + 1);
                }
                if ($r_ref != $r_self) {
                    setcookie("ref", str_replace(array(""), array(""), $r_ref_full), time() + 86400, "/");
                }
            }
        }
    }

    /**
     * Set Region Code cookie
     */
    private function setRegionCookie()
    {
        if (!isset($_COOKIE["GEO_REGION"]) || empty($_SESSION["GEO_REGION"])) {

            $geoResult = Geo::getDataResult(Geo::getRealIp());

            if ($geoResult && $geoResult->isSuccess()) {
                $region = $geoResult->getGeoData()->regionCode;
            } else {
                $region = 'RU-MOW';
            }

            setcookie("GEO_REGION", $region, time() + 60*60*24*365, "/");
        }
    }

    /**
     * Set Canonical
     */
    private function setCanonical()
    {
        global $APPLICATION;
        if (!empty($_REQUEST['PAGEN_1']) || !empty($_GET['q']) || !empty($_GET['tag'])) {
            Asset::getInstance()->addString('<link rel="canonical" href="' . $APPLICATION->GetCurDir() . '" />');
        }
    }

    /**
     * @return bool
     */
    public static function isHome()
    {
        return self::$home;
    }

    /**
     * @return string
     */
    public static function getName()
    {
        return self::$name;
    }

    /**
     * @return string
     */
    public static function getEmail()
    {
        $rsSites = \CSite::GetByID(SITE_ID);
        $arSite = $rsSites->Fetch();
        return $arSite['EMAIL'];
    }

    /**
     * 404 Error
     */
    public static function Error404()
    {
        global $APPLICATION;

        if (!defined('ERROR_404') || ERROR_404 != 'Y') {
            return;
        }
        if ($APPLICATION->GetCurPage() != "/404.php") {
            header('X-Accel-Redirect: ' . "/404.php");
            exit();
        }
    }

    /**
     * deleteKernelJs
     * @param $content
     */
    public static function DeleteKernelJs(&$content)
    {
        global $USER, $APPLICATION;

        if ((is_object($USER) && $USER->IsAuthorized()) || strpos($APPLICATION->GetCurDir(), "/bitrix/") !== false) {
            return;
        }
        if ($APPLICATION->GetProperty("save_kernel") == "Y") {
            return;
        }

        $arPatternsToRemove = Array(
            '/<script.+?src=".+?kernel_main\/kernel_main\.js\?\d+"><\/script\>/',
            '/<script.+?src=".+?bitrix\/js\/main\/core\/core[^"]+"><\/script\>/',
            '/<script.+?>BX\.(setCSSList|setJSList)\(\[.+?\]\).*?<\/script>/',
            '/<script.+?>if\(\!window\.BX\)window\.BX.+?<\/script>/',
            '/<script[^>]+?>\(window\.BX\|\|top\.BX\)\.message[^<]+<\/script>/',
        );

        $content = preg_replace($arPatternsToRemove, "", $content);
        $content = preg_replace("/\n{2,}/", "\n\n", $content);
    }

    /**
     * DeleteKernelCss
     * @param $content
     */
    public static function DeleteKernelCss(&$content)
    {
        global $USER, $APPLICATION;

        if ((is_object($USER) && $USER->IsAuthorized()) || strpos($APPLICATION->GetCurDir(), "/bitrix/") !== false) {
            return;
        }
        if ($APPLICATION->GetProperty("save_kernel") == "Y") {
            return;
        }

        $arPatternsToRemove = Array(
            '/<link.+?href=".+?kernel_main\/kernel_main\.css\?\d+"[^>]+>/',
            '/<link.+?href=".+?bitrix\/js\/main\/core\/css\/core[^"]+"[^>]+>/',
            '/<link.+?href=".+?bitrix\/templates\/[\w\d_-]+\/styles.css[^"]+"[^>]+>/',
            '/<link.+?href=".+?bitrix\/templates\/[\w\d_-]+\/template_styles.css[^"]+"[^>]+>/',
            '/<link.+?href=".+?\/panel\/main\/popup\.min\.css\?\d+"[^>]+>/'
        );

        $content = preg_replace($arPatternsToRemove, "", $content);
        $content = preg_replace("/\n{2,}/", "\n\n", $content);
    }


    /**
     * AddDeferToJs
     * @param $content
     */
    public static function AddDeferToJs(&$content)
    {
        global $USER, $APPLICATION;

        if ((is_object($USER) && $USER->IsAuthorized()) || strpos($APPLICATION->GetCurDir(), "/bitrix/") !== false) {
            return;
        }
        if ($APPLICATION->GetProperty("save_kernel") == "Y") {
            return;
        }

        $patternsToReplace = array(
            '/<script.+?src=".+?template_.+\.js\?\d+"><\/script>/',
            '/<script.+?src=".+?page_.+\.js\?\d+"><\/script>/'
        );

        foreach ($patternsToReplace as $pattern) {
            preg_match($pattern, $content, $matches);
            if (count($matches)) {
                $newStr = str_replace('<script', '<script defer', $matches[0], $count);
            }
            $content = preg_replace($pattern, $newStr, $content);
        }
    }

    /**
     * DeferCssLoading
     * @param $content
     */
    public static function DeferCssLoading(&$content)
    {
        $patternsToReplace = array(
            '/<link.+?href=".+?template_.+\.css\?\d+"[^>]+>/'
        );

        foreach ($patternsToReplace as $pattern) {
            preg_match($pattern, $content, $matches);
            if (count($matches)) {
                $newStr = str_replace('/>', 'media="none" onload="if(media!=\'all\')media=\'all\'"  />', $matches[0], $count);
            }
            $content = preg_replace($pattern, $newStr, $content);
        }
    }
}
