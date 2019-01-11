<?php

use Bitrix\Main\Loader;

if (Loader::IncludeModule('iblock')) {

    $IB_ID = 1;

    $doc = new CIBlockElement;
    $arProps = [
        "IBLOCK_ID" => $IB_ID,
        "IBLOCK_SECTION_ID" => false,
        "ACTIVE" => "Y",
        "MODIFIED_BY" => 1,
        "ACTIVE_FROM" => date('d.m.Y'),
        "NAME" => "Тест",
        "PROPERTY_VALUES" => [],
    ];

    if ($DOC_ID = $doc->Add($arProps)) {
        if (Loader::IncludeModule('bizproc')) {

            $arWorkflowTemplates = \CBPDocument::GetWorkflowTemplatesForDocumentType(["lists", "BizprocDocument", "iblock_" . $IB_ID]);

            foreach ($arWorkflowTemplates as $arTemplate) {
                if ($arTemplate['AUTO_EXECUTE'] == 1) {
                    $CBP_ID = \CBPDocument::StartWorkflow($arTemplate['ID'], ["lists", "BizprocDocument", $DOC_ID], [], $arErrors);
                }
            }
        }
    }
}
