<?php

error_reporting(E_ALL^E_NOTICE);
session_start();
require_once('db.php');
require_once('functions.php');

$errors = [];
$cur_post = NULL;
$template_post = NULL;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim(htmlspecialchars($_POST['post-name']));
    if ($name == '') {
        $errors['post-name'] = "Введите наименование должности!";
    }
    $post_id = trim(htmlspecialchars($_POST['post-id']));
    if ($post_id != NULL && is_numeric($post_id) == FALSE) {
        $errors['post-id'] = "Введён некорректный id!";
    }
    if (count($errors) == 0) {
        $db = new DB();
        if ($post_id != NULL) {
            $db->exec_query(
                "UPDATE posts
                SET name=?
                WHERE id=?",
                [$name,
                $post_id]);
        }
        else {
           $db->insert_data('posts', ['name' => $name]);
        }
        header('Location: posts.php');
    }
    else {
        $template_post = build_template('post-editor',
        ['errors' => $errors]);        
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id']) == TRUE) {
        $post_id = intval($_GET['id']);
        if (is_int($post_id) == FALSE) {
            print('Incorrect id type...');
            exit();
        }
        $db = new DB();
        $posts = $db->select_data(
            "SELECT *
            FROM posts
            WHERE id=?", 
            [$post_id]);
        $cur_post = $posts[0];
        $template_post = build_template('post-editor',
        ['post' => $cur_post]);
    }
    else {
        $template_post = build_template('post-editor',
        []);
    }
}
print(build_template('layout',
['title' => 'Должность '.$cur_post['name'],
'content' => $template_post
]));