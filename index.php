<?php 

error_reporting(E_ALL^E_NOTICE);
session_start();
require_once('db.php');
require_once('functions.php');
require_once('core/Schedule.php');

$db = new DB();
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
else if ($cur_screen == 1) {
    $prev_screen = 8;
}
if ($cur_screen < 8) {
    $fwd_screen = $cur_screen + 1;
}
if (isset($_GET['action']) == TRUE) {
    $action = trim(htmlspecialchars($_GET['action']));
    if (isset($_GET['sched_id']) == FALSE) {
        print('Empty sched id');
        exit();
    }
    $sched_id = intval($_GET['sched_id']);
    if (sched_id_exists($sched_id) == FALSE) {
        print('Incorrect sched id');
        exit();
    }
    if ($action != "move_up" && $action != "move_down") {
        print('Incorrect action');
        exit();
    }
    $edit_sched = $db->select_data(
        "SELECT screen_position
        FROM sched
        WHERE sched_id=?",
        [$sched_id]
    )[0];
    try {
        if ($action == 'move_up') {
            change_screen_position($sched_id, Schedule::UP_POSITION);
        }
        else if ($action == 'move_down') {
            change_screen_position($sched_id, Schedule::DOWN_POSITION);
        }
    }
    catch (Exception $e) {
        print($e->getMessage());
        exit();
    }
}
$scheds = $db->select_data(
    "SELECT cab,
    concat_ws(' ', surname, name, patronymic) as snp,
    (SELECT name FROM posts WHERE posts.id = post_id) as post,
    sched.*
    FROM sched 
    INNER JOIN docs ON sched.doc_id = docs.doc_id 
    WHERE 
    screen_id=?
    ORDER BY screen_position ASC", 
    [$cur_screen]);
$template_main = build_template('main',
    ['scheds' => $scheds,
    'cur_screen' => $cur_screen,
    'prev_screen' => $prev_screen,
    'fwd_screen' => $fwd_screen]);
print(build_template('layout', 
    ['title' => 'Редактор расписания',
    'screen' => $cur_screen,
    'content' => $template_main]))

?>
