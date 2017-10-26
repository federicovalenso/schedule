<?php 

error_reporting(E_ALL^E_NOTICE);
session_start();
require_once('db.php');
require_once('functions.php');

$cur_screen = 1;
$prev_screen = NULL;
$fwd_screen = NULL;
if (isset($_GET['screen']) == TRUE) {
    $cur_screen = $_GET['screen'];
    if ($cur_screen < 1 || $cur_screen > 8) {
        $cur_screen = 1;
    }
}
if ($cur_screen > 1) {
    $prev_screen = $cur_screen - 1;
}
if ($cur_screen < 8) {
    $fwd_screen = $cur_screen + 1;
}
$db = new DB();
$docs = $db->select_data(
    "SELECT cab,
    concat_ws(' ', surname, name, patronymic) as snp,
    (SELECT name FROM posts WHERE posts.id = post_id) as post,
    sched.*
    FROM docs 
    INNER JOIN sched ON docs.doc_id = sched.doc_id 
    WHERE screen_id=$cur_screen", 
    []);
$template_main = build_template('main',
    ['docs' => $docs,
    'cur_screen' => $cur_screen,
    'prev_screen' => $prev_screen,
    'fwd_screen' => $fwd_screen]);
print(build_template('layout', 
    ['title' => 'Редактор расписания',
    'content' => $template_main]))

?>
