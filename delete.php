<?php

error_reporting(E_ALL);
session_start();
require_once('db.php');
require_once('functions.php');

if (isset($_POST['sched_id']) == FALSE) {
    print("Не передан идентификатор");
    exit();
}

$sched_id = intval($_POST['sched_id']);
if ($sched_id != $_POST['sched_id'] || $sched_id <= 0) {
    print("Передан неверный идентификатор расписания");
    exit();
}

$screen_id = intval($_POST['screen_id']);
if ($screen_id < 1 || $screen_id > 8) {
    print("Передан неверный идентификатор экрана");
    exit();
}

$db = new DB();
$rows_deleted = $db->exec_query(
    "DELETE FROM 
    sched
    WHERE
    sched_id=?",
    [$sched_id]
);

if ($rows_deleted == 0) {
    $message = "Возникла ошибка при удалении";
}
else {
    $message = 'Успешно удалена строка с экрана ' . $screen_id;
}

refresh_screen_positions($screen_id);

$template_delete = build_template('delete',
    ['message' => $message]);

print(build_template('layout',
    ['title' => 'Удаление строк в расписании',
    'content' => $template_delete]));