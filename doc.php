<?php

error_reporting(E_ALL^E_NOTICE);
session_start();
require_once('db.php');
require_once('functions.php');

if (isset($_GET['action']) == FALSE) {
    print('Empty action');
    exit();
}
if (isset($_GET['id']) == FALSE && $_GET['action'] = 'edit') {
    print('Empty id...');
    exit();
}
$doc_id = intval($_GET['id']);
if (is_int($doc_id) == FALSE) {
    print('Incorrect id type...');
    exit();
}
$db = new DB();
$docs = $db->select_data(
    "SELECT *,
    (SELECT name FROM posts WHERE posts.id = post_id) as post
    FROM docs 
    WHERE docs.doc_id=?", 
    [$doc_id]);
$cur_doc = $docs[0];
$template_doc = build_template('doc',
    ['doc' => $cur_doc]);
print(build_template('layout',
    ['title' => 'Данные врача '.$cur_doc['surname'].' '.$cur_doc['name'].' '.$cur_doc['patronymic'],
    'content' => $template_doc
    ]));