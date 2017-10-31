<?php

error_reporting(E_ALL^E_NOTICE);
session_start();
require_once('db.php');
require_once('functions.php');

$db = new DB();
$posts = $db->select_data(
    "SELECT *
    FROM posts
    ORDER BY name", 
    []);
$template_posts = build_template('posts',
    ['posts' => $posts]);
print(build_template('layout',
    ['title' => 'Должности врачей',
    'content' => $template_posts
    ]));