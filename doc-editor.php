<?php

error_reporting(E_ALL^E_NOTICE);
session_start();
require_once('db.php');
require_once('functions.php');
require_once('vendor/autoload.php');

use Respect\Validation\Validator as v;
$errors = [];
$cur_doc = [];
$template_doc = NULL;
$db = new DB();
$posts = $db->select_data(
    "SELECT *
    FROM posts
    ORDER BY name", 
    []);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rules = [
        'doc_id' => v::optional(v::intVal()),
        'cab' => v::stringType()->length(1,4)->notEmpty(),
        'surname' => v::stringType()->length(1,50)->notEmpty(),
        'name' => v::stringType()->length(1,50)->notEmpty(),
        'patronymic' => v::stringType()->length(1,50)->notEmpty(),
        'post_id' => v::intVal()->Positive()->notEmpty()
    ];
    $errors_desc = [
        'doc_id' => 'Введён некорректный id врача!',
        'cab' => 'Неверно введён кабинет!',
        'surname' => 'Неверно введена фамилия!',
        'name' => 'Неверно введено имя!',
        'patronymic' => 'Неверно введено отчество!',
        'post_id' => 'Неверный id должности!'
    ];    
    foreach ($_POST as $key => $value) {
        $cur_doc[$key] = trim(htmlspecialchars($value));
        if ($rules[$key]->validate($value) == FALSE)  {
            $errors[$key] = $errors_desc[$key];
        }
    }
    if (count($errors) == 0) {
        if ($cur_doc['doc_id'] != NULL) {
            $db->exec_query(
                "UPDATE docs
                SET cab=?,
                surname=?,
                name=?,
                patronymic=?,
                post_id=?
                WHERE doc_id=?",
                [$cur_doc['cab'],
                $cur_doc['surname'],
                $cur_doc['name'],
                $cur_doc['patronymic'],
                $cur_doc['post_id'],
                $cur_doc['doc_id']
                ]
            );
        }
        else {
            $db->insert_data(
                'docs',
                ['cab' => $cur_doc['cab'],
                'surname' => $cur_doc['surname'],
                'name' => $cur_doc['name'],
                'patronymic' => $cur_doc['patronymic'],
                'post_id' => $cur_doc['post_id']]);
        }
        header('Location: docs.php');
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if(isset($_GET['id']) == TRUE) {
        $doc_id = intval($_GET['id']);
        if (is_int($doc_id) == FALSE) {
            print('Incorrect id type...');
            exit();
        }
        $docs = $db->select_data(
            "SELECT *,
            (SELECT id FROM posts WHERE posts.id = post_id) as post_id
            FROM docs 
            WHERE docs.doc_id=?", 
            [$doc_id]);
        $cur_doc = $docs[0];
    }
}
$template_doc = build_template('doc-editor',
    ['doc' => $cur_doc,
    'posts' => $posts,
    'errors' => $errors]);
print(build_template('layout',
    ['title' => 'Данные врача '.$cur_doc['surname'].' '.$cur_doc['name'].' '.$cur_doc['patronymic'],
    'content' => $template_doc
    ]));