<?
/**
 * Создание подзадач в битрикс24 коробка в бизнес-процессе с помощью активити php-код
 * 
 * В первую очередь нужно добавить стандартное активити "Задача", после него добавить активити php-код.
 * В массив добавляем строки в следующем формате Название задачи; Описание; Постановщик ID; Ответственный ID.
 * Каждая строка отдельная подзадача главной задачи.
 */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

//в начале объявляем текущий бизнес-процесс
$rootActivity = $this->GetRootActivity();


$curTask = '{=A67377_82432_96523_9007:TaskId}'; //id созданной ранее задачи с помощью активити "Задача"

$TASKS = '
    Название задачи; Описание; Постановщик ID; Ответственный ID
    Задача 1; Выполнить задачу 1; 1546; 25
    Задача 2; Выполнить задачу 2; 1546; 25
    Задача 3; Выполнить задачу 3; 1546; 25
';

$TASKS = explode(PHP_EOL, $TASKS);
foreach( $TASKS as $task ){
    if( trim($task) ){
        $arTask = explode(";", trim($task));
        $arTask['TITLE'] = $arTask[0];
        $arTask['DESC'] = $arTask[1];
        $arTask['CREATED_BY'] = $arTask[2];
        $arTask['RESPONSIBLE_ID'] = $arTask[3];

        $arFields = Array(
            "TITLE" => $arTask['TITLE'],
            "DESCRIPTION" => $arTask['DESC'],
            "RESPONSIBLE_ID" => $arTask['RESPONSIBLE_ID'],
            "CREATED_DATE" => date('d.m.Y H:i:s'),
            "CHANGED_DATE" => date('d.m.Y H:i:s'),
            "STATUS_CHANGED_DATE" => date('d.m.Y H:i:s'),
            "START_DATE_PLAN" => date('d.m.Y H:i:s'),
            "VIEWED_DATE" => date('d.m.Y H:i:s'),
            // "DEADLINE" => date("d.m.Y H:i:s",$sevenup),
            "ALLOW_TIME_TRACKING" => 'Y',
            "CREATED_BY" => $arTask['CREATED_BY'],
            "STATUS" => 2,
            "REAL_STATUS" => 2,
            "PRIORITY" => 2,
            "PARENT_ID" => $curTask,
        );
        $obTask = new CTasks;
        $ID = $obTask->Add($arFields);
        $success = ($ID>0);

    }
}