##Принудительная отправка зависшей почты

``
update b_event set SUCCESS_EXEC='N' where SUCCESS_EXEC='F';
``

``
CEvent::CheckEvents();
``

Если не уходит - пересохранить все почтовые события и шаблоны

## Сброс пароля на - 111111

``
update `b_user` set `LOGIN`='admin', `PASSWORD`='G4|k!e5C4905eceb9b4ceca12f393637f1d036ef' where ID=1;
``

## import.from.wordpress.sql

При экспорте использовать "csv" с разделителем ";"