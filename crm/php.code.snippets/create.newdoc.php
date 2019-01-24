<?php

\Bitrix\Main\Loader::IncludeModule('iblock');
\Bitrix\Main\Loader::includeModule('disk');

// Получаем согласованные УТВ из переменной
$arUTV = $this->GetVariable('UTV');

// Пишем значения в документ
$arValues = [];
foreach ($arUTV as $val) {
    $arValues[] = ["VALUE" => $val, "DESCRIPTION" => ""];
}
CIBlockElement::SetPropertyValuesEx({=Document:ID}, {=Document:IBLOCK_ID}, ['UTV' => $arValues]);

// Для согласованных УТВ  меняем статус и создаем документы

$arUtvValues = [];
$arUTVs = [];
$arNewDocs = [];

foreach ($arUTV as $utv) {

    if ($utv['STATUS'] == 1 && isset($utv['UTV'])) {

        $utv['STATUS'] = '2'; // Меняем статус УТВ

        // Создаем файл на Диске
        try {
            $driver = \Bitrix\Disk\Driver::getInstance();
            $storage = $driver->getStorageByGroupId(50);
            $fileArray = \CFile::MakeFileArray(\CFile::GetPath(intval($utv['UTV'])));
            $root = $storage->getRootObject();
            $file = $root->uploadFile($fileArray, ['CREATED_BY' => 1]);

            if ($file) {
                $file = $file->getFileId(); // id файла в таблице b_file
                $disk = \Bitrix\Disk\File::load(['FILE_ID' => $file])->getId(); // id файла в таблице b_disk_object
            }

        } catch (Exception $e) {
        }

        $arNewDocs[] = [
            'THEME' => $utv['THEME'],
            'UTV' => $file,
            'DISK' => $disk
        ];
    }

    $arUTVs[] = $utv;
    $arUtvValues[] = ["VALUE" => $utv, "DESCRIPTION" => ""];
}

// Обновляем статусы УТВ в переменной БП
$this->SetVariables(['UTV' => $arUTVs]);

// Обновляем статусы УТВ в свойстве документа
CIBlockElement::SetPropertyValuesEx({=Document:ID}, {=Document:IBLOCK_ID}, ['UTV' => $arUtvValues]);

// Создаем документы в Разработке РК
foreach ($arNewDocs as $utv) {

    $doc = new CIBlockElement;
    $arProps = [
        "IBLOCK_ID" => 54,
        "IBLOCK_SECTION_ID" => false,
        "ACTIVE" => "Y",
        "MODIFIED_BY" => 1,
        "ACTIVE_FROM" => date('d.m.Y'),
        "NAME" => "{=Document:NAME} - " . $utv['THEME'],
        "PROPERTY_VALUES" => [
            'KLIENT' => '{=Document:PROPERTY_KLIENT}',
            'AKKAUNT_V_DYNAADS' => '{=Document:PROPERTY_AKKAUNT_V_DYNAADS}',
            'PODOBRANNYE_TEMY' => $utv['THEME'],
            'BRIF' => ['n' . {=Document:PROPERTY_BRIF}],
            'UTV1' => ['n' . intval($utv['DISK'])]
        ],
    ];

    $doc->Add($arProps);
}