<?php

error_reporting(E_ALL^E_NOTICE);
session_start();
require_once('db.php');
require_once('functions.php');

if (isset($_GET['id']) == FALSE) {
    print('Empty get...');
    exit();
}
$doc_id = intval($_GET['id']);
if (is_int($doc_id) == FALSE) {
    print('Incorrect id type...');
    exit();
}
$db = new DB();
$docs = $db->select_data(
    "SELECT cab,
    concat_ws(' ', surname, name, patronymic) as snp,
    (SELECT name FROM posts WHERE posts.id = post_id) as post,
    sched.*
    FROM docs 
    INNER JOIN sched ON docs.doc_id = sched.doc_id 
    WHERE docs.doc_id=?", 
    [$doc_id]);
$template_editor = build_template('editor',
    ['doc' => $docs[0]]);
print(build_template('layout',
    ['title' => 'Расписание врача '.$cur_doc['snp'],
    'content' => $template_editor
    ]));