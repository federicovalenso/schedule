<?php

error_reporting(E_ALL);
session_start();
require_once('db.php');
require_once('functions.php');
require_once('vendor/autoload.php');

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $schedule = new Schedule();
    $errors = $schedule->validate_input_data($_POST);
    foreach ($_POST as $key => $value) {
        $cur_sched[$key] = trim(htmlspecialchars($value));
    }
    //Находим существующую (либо нет, если NULL) строку расписания для врача, и
    //номер экрана, где эта строка находится
    list($exist_sched_id, $exist_screen_id) = chk_dupl_doc_sched($cur_sched['doc_id']);
    
    //Если расписание для врача существует и текущий id расписания не равен существующему,
    //то-есть проверяем дубли расписания
    if ($exist_sched_id != NULL && $cur_sched['sched_id'] != $exist_sched_id) {
        $errors['doc_id'] = "Для данного доктора уже существует расписание на экране $exist_screen_id, просто отредактируйте его при необходимости!";
    }
    //Если строка расписания существует, но при этом выбран врач, у которого ранее не было расписания
    else if ($cur_sched['sched_id'] != NULL && $exist_sched_id == NULL) {
        //Ищем на каком экране находится строка расписания
        $exist_screen_id = find_screen_by_sched($cur_sched['sched_id']);
    }

    if (isset($errors['screen_id']) == FALSE) {
        $scheds_on_screen = $db->select_data(
            "SELECT sched_id
            FROM sched 
            WHERE 
            screen_id=? AND
            sched_id<>?",
            [$cur_sched['screen_id'],
            $cur_sched['sched_id']]);            
        $sched_rows = count($scheds_on_screen);
    
        if ($sched_rows >= 9) {
            $errors['screen_id'] = "На экране уже имеется 9 записей, отобразить больше нельзя!";
        }
        //Если меняем экран для существующей строки расписания, либо
        //если вводим новую строку (то-есть идентификатор равен NULL)
        else if ($exist_screen_id != $cur_sched['screen_id'] || $cur_sched['sched_id'] == NULL) {
            $cur_sched['screen_position'] = $sched_rows + 1;
        }
    }
    
    if (count($errors) == 0) {
        //Если редактируем существующую строку
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
                $cur_sched['screen_position'],
                $cur_sched['sched_id']
                ]
            );
            //Обновляем позиции всех строк на экране
            if ($exist_screen_id != NULL) {
                try {
                    refresh_screen_positions($exist_screen_id);
                }
                catch (Exception $e) {
                    print ($e->getMessage());
                    exit();
                }
            }
        }
        //Если вводим новую строку
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
                'screen_position' => $cur_sched['screen_position']
                ]
            );
        }

        header('Location: index.php?screen='.$cur_sched['screen_id']);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if (isset($_GET['id']) == TRUE) {
        $sched_id = intval($_GET['id']);

        if (is_int($sched_id) == FALSE) {
            print('Неверный идентификатор строки расписания...');
            exit();
        }

        $scheds = $db->select_data(
            "SELECT *
            FROM sched
            WHERE sched_id=?",
            [$sched_id]);

        if (count($scheds) > 0) {
            $cur_sched = $scheds[0];
        }
    }
    else if (isset($_GET['screen_id']) == TRUE) {
        $screen_id = intval($_GET['screen_id']);

        if (check_screen_id($screen_id) == FALSE) {
            print('Неверный идентификатор экрана...');
            exit();
        }

        $cur_sched['screen_id'] = $screen_id;
    }
}

$template_editor = build_template('sched-editor',
    ['errors' => $errors,
    'sched' => $cur_sched,
    'docs' => $docs,
    'screens' => $screens]);
print(build_template('layout',
    ['title' => 'Редактор расписания',
    'screen' => $cur_sched['screen_id'] ?? '',
    'content' => $template_editor
    ]));