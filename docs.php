<?php

error_reporting(E_ALL^E_NOTICE);
session_start();
require_once('db.php');
require_once('functions.php');

$db = new DB();
$docs = $db->select_data(
    "SELECT doc_id,
    cab,
    concat_ws(' ', surname, name, patronymic) as snp,
    (SELECT name FROM posts WHERE posts.id = post_id) as post
    FROM docs", 
    []);
$template_docs = build_template('docs',
    ['docs' => $docs]);
print(build_template('layout',
    ['title' => 'Врачи МАНО ЛДЦ',
    'content' => $template_docs
    ]));