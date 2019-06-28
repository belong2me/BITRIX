<?php

include($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

\Bitrix\Main\Loader::includeModule('form');

$FORM_ID = isset($_GET['form']) ? intval($_GET['form']) : 2;

$arDates = [];
$rsResults = CFormResult::GetList($FORM_ID, ($by = "s_timestamp"), ($order = "desc"), [], $is_filtered, "N");
while ($arResult = $rsResults->Fetch()) {
    $arDates[$arResult["ID"]] = $arResult["DATE_CREATE"];
}

CForm::GetResultAnswerArray($FORM_ID,
    $arrColumns,
    $arrAnswers,
    $arrAnswersVarname
);

$arrAddColumns = [
        ['SID' => 'ID', 'TITLE' => 'ID'],
        ['SID' => 'DATE', 'TITLE' => 'Дата'],
    ] + $arrColumns;
$result = '';

foreach ($arrAddColumns as $arrColumn) {
    if (!in_array($arrColumn['SID'], ['CHECK'])) {
        $arColumns[] = $arrColumn['SID'];
        $result .= "\"" . $arrColumn['TITLE'] . "\";";
    }
}
$result .= "\r\n";

foreach ($arrAnswersVarname as $arFields) {

    foreach ($arColumns as $arColumn) {

        if ($arColumn == 'ID') {
            $code = $arrColumns[array_keys($arrColumns)[0]]['SID'];
            $result .= "\"" . $arFields[$code][0]['RESULT_ID'] . "\";";
            continue;
        }
        if ($arColumn == 'DATE') {
            $code = $arrColumns[array_keys($arrColumns)[0]]['SID'];
            $result .= "\"" . $arDates[$arFields[$code][0]['RESULT_ID']]. "\";";
            continue;
        }

        if (isset($arFields[$arColumn])) {

            if ($arFields[$arColumn][0]['USER_FILE_ID']) {
                $file = CFile::GetByID($arFields[$arColumn][0]['USER_FILE_ID'])->Fetch();
                if ($file) {
                    $arFields[$arColumn][0]['USER_TEXT'] = $_SERVER["SERVER_NAME"] . "/upload/" . $file['SUBDIR'] . "/" . $file['FILE_NAME'];
                }
            }

            $text = trim(str_replace(["\r\n", "\r", "\n"], "", $arFields[$arColumn][0]['USER_TEXT']));
            $result .= "\"$text\";";
        } else {
            $result .= "\"\";";
        }
    }

    $result .= "\r\n";
}

header("Content-type: text/csv; charset=windows-1251");
header("Content-Disposition: attachment; filename=file.csv");
header("Pragma: no-cache");
header("Expires: 0");

$result = mb_convert_encoding($result, 'windows-1251', 'UTF-8');

die($result);