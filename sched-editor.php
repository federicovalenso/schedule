<?php

error_reporting(E_ALL);
session_start();
require_once('db.php');
require_once('functions.php');
require_once('vendor/autoload.php');

use Respect\Validation\Validator as v;
$errors = [];
$cur_sched = [];
$template_editor = NULL;
$db = new DB();
$docs = $db->select_data(
    "SELECT cab,
    doc_id,
    (SELECT name FROM posts WHERE posts.id = post_id) as post,
    concat_ws(' ', surname, name, patronymic) as snp
    FROM docs
    ORDER BY snp",
    []);
$screens = $db->select_data("SELECT * FROM screens");
$fl_display = 0;
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rules = [
        'sched_id' => v::optional(v::intVal()),
        'doc_id' => v::optional(v::intVal()),
        'mon' => v::optional(v::date('H:i')->min('08:00')->max('20:00')),
        'mon_end' => v::optional(v::date('H:i')->min('08:00')->max('20:00')),
        'tue' => v::optional(v::date('H:i')->min('08:00')->max('20:00')),
        'tue_end' => v::optional(v::date('H:i')->min('08:00')->max('20:00')),
        'wed' => v::optional(v::date('H:i')->min('08:00')->max('20:00')),
        'wed_end' => v::optional(v::date('H:i')->min('08:00')->max('20:00')),
        'thu' => v::optional(v::date('H:i')->min('08:00')->max('20:00')),
        'thu_end' => v::optional(v::date('H:i')->min('08:00')->max('20:00')),
        'fri' => v::optional(v::date('H:i')->min('08:00')->max('20:00')),
        'fri_end' => v::optional(v::date('H:i')->min('08:00')->max('20:00')),
        'screen_id' => v::intVal()->min(1)->max(count($screens)),
    ];
    $errors_desc = [
        'sched_id' => 'Некорректный id расписания',
        'doc_id' => 'Введён некорректный id врача!',
        'mon' => 'Некорректно введено время!',
        'mon_end' => 'Некорректно введено время!',
        'tue' => 'Некорректно введено время!',
        'tue_end' => 'Некорректно введено время!',
        'wed' => 'Некорректно введено время!',
        'wed_end' => 'Некорректно введено время!',
        'thu' => 'Некорректно введено время!',
        'thu_end' => 'Некорректно введено время!',
        'fri' => 'Некорректно введено время!',
        'fri_end' => 'Некорректно введено время!',
        'screen_id' => 'Неверный номер экрана!',
    ];
    foreach ($_POST as $key => $value) {
        $cur_sched[$key] = trim(htmlspecialchars($value));
        if ($key == 'fl_display') {
            continue;
        }
        if ($rules[$key]->validate($value) == FALSE) {
            $errors[$key] = $errors_desc[$key];
        }
    }
    $fl_display = $cur_sched['fl_display'] = (isset($_POST['fl_display']) == TRUE) ? 1 : 0;
    if (isset($errors['screen_id']) == FALSE && $cur_sched['fl_display'] == 1) {
        $scheds_on_screen = $db->select_data(
            "SELECT sched_id
            FROM sched 
            WHERE 
            screen_id=? AND
            sched_id<>? AND
            fl_display=1",
            [$cur_sched['screen_id'],
            $cur_sched['sched_id']]);            
        $sched_rows = count($scheds_on_screen);
        if ($sched_rows >= 9) {
            $errors['fl_display'] = "На экране уже имеется 9 записей, отобразить больше нельзя!";
        }
        else {
            $cur_sched['screen_position'] = $sched_rows + 1;
        }
    }
    if ($cur_sched['fl_display'] == 1) {
        list($exist_sched_id, $exist_screen_id) = chk_dupl_doc_sched($cur_sched['doc_id']);
        if ($exist_sched_id != NULL && $cur_sched['sched_id'] != $exist_sched_id) {
            $errors['doc_id'] = "Для данного доктора уже существует расписание на экране $exist_screen_id, просто отредактируйте его при необходимости!";
        }
    }
    if (count($errors) == 0) {
        if ($cur_sched['sched_id'] != NULL) {
            $db->exec_query(
                "UPDATE sched
                SET doc_id=?,
                mon=?,
                mon_end=?,
                tue=?,
                tue_end=?,
                wed=?,
                wed_end=?,
                thu=?,
                thu_end=?,
                fri=?,
                fri_end=?,
                screen_id=?,
                fl_display=?,
                screen_position=?
                WHERE sched_id=?",
                [$cur_sched['doc_id'],
                $cur_sched['mon'],
                $cur_sched['mon_end'],
                $cur_sched['tue'],
                $cur_sched['tue_end'],
                $cur_sched['wed'],
                $cur_sched['wed_end'],
                $cur_sched['thu'],
                $cur_sched['thu_end'],
                $cur_sched['fri'],
                $cur_sched['fri_end'],
                $cur_sched['screen_id'],
                $cur_sched['fl_display'],
                $cur_sched['screen_position'],
                $cur_sched['sched_id']
                ]
            );
        }
        else {
            $db->insert_data(
                'sched',
                ['doc_id' => $cur_sched['doc_id'],
                'mon' => $cur_sched['mon'],
                'mon_end' => $cur_sched['mon_end'],
                'tue' => $cur_sched['tue'],
                'tue_end' => $cur_sched['tue_end'],
                'wed' => $cur_sched['wed'],
                'wed_end' => $cur_sched['wed_end'],
                'thu' => $cur_sched['thu'],
                'thu_end' => $cur_sched['thu_end'],
                'fri' => $cur_sched['fri'],
                'fri_end' => $cur_sched['fri_end'],
                'screen_id' => $cur_sched['screen_id'],
                'fl_display' => $cur_sched['fl_display'],
                'screen_position' => $cur_sched['screen_position']
                ]
            );
        }
        header('Location: index.php?screen='.$cur_sched['screen_id']);
    }
}
if($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id']) == TRUE) {
        $sched_id = intval($_GET['id']);
        if (is_int($sched_id) == FALSE) {
            print('Incorrect id type...');
            exit();
        }
        $scheds = $db->select_data(
            "SELECT *
            FROM sched
            WHERE sched_id=?",
            [$sched_id]);
        $cur_sched = $scheds[0];
        $fl_display = $cur_sched['fl_display'];
    }
    else if (isset($_GET['screen_id']) == TRUE) {
        $screen_id = intval($_GET['screen_id']);
        if (check_screen_id($screen_id) == FALSE) {
            print('Incorrect screen id...');
            exit();
        }
        $cur_sched['screen_id'] = $screen_id;
    }
}
$template_editor = build_template('sched-editor',
    ['errors' => $errors,
    'sched' => $cur_sched,
    'docs' => $docs,
    'screens' => $screens,
    'fl_display' => $fl_display]);
print(build_template('layout',
    ['title' => 'Редактор расписания',
    'screen' => $cur_sched['screen_id'],
    'content' => $template_editor
    ]));