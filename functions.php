<?php
require_once('db.php');
/**
 * Создает шаблон html-документа, принимая на вход имя файла-шаблона и 
 * массив параметров, значения которых подставляются в файле-шаблоне
 * @param $file string имя файла-шаблона
 * @param $sql array ассоциативный массив, ключи которого - имена переменных
 *          в файле-шаблоне, значения - значения переменных в файле-шаблоне
 *
 * @return string строка, содержащая шаблон html-документа
 */
function build_template($file, $params) {
    $template = "";
    $path_to_file = 'templates/' . $file . '.php';
    if (file_exists($path_to_file) == true) {
        ob_start('ob_gzhandler');
        extract($params, EXTR_SKIP);
        require_once($path_to_file);
        $template = ob_get_clean();
    }
    return $template;
}
/**
 * Проверяет корректность id экрана
 * id должно соотвествовать перечислению [1...9]
 * @param $id int идентификатор экрана
 *
 * @return $result boolean если идентификтор - положительное число, меньшее 10, то он корректен, иначе нет
 */
function check_screen_id($id) {
    $result = FALSE;
    if (is_int($id) && $id > 0 && $id < 10) {
        $result = TRUE;
    }
    return $result;
}
/**
 * Проверяет существует ли у врача с указанным id расписание
 * @param $id int идентификатор врача
 *
 * @return $out_sched_id int идентификатор строки расписание, если оно существует,
 *          иначе NULL
 */
function chk_dupl_doc_sched($id) {
    $out_sched_id = NULL;
    $db = new DB();
    $sched = $db->select_data(
        "SELECT sched_id
        FROM sched
        WHERE doc_id=? AND
        fl_display=1",
        [$id]);
    if (count($sched) > 0) {
        $out_sched_id = $sched[0]['sched_id'];
    }
    return $out_sched_id;
}
/**
 * Проверяет существует ли на экране расписание с заданной отображаемой позицией
 * @param $screen_id int идентификатор экрана
 * @param $screen_position int идентификатор позиции на экране
 *
 * @return $out_sched_id int если на экране если строка расписания с искомой позицией,
 *          то вернётся id расписания, иначе NULL
 */
function chk_dupl_screen_position($screen_id, $screen_position) {
    $out_sched_id = NULL;
    $db = new DB();
    $sched = $db->select_data(
        "SELECT sched_id
        FROM sched
        WHERE screen_id=? AND
        screen_position=? AND
        fl_display=1",
        [$screen_id,
        $screen_position]
    );
    if (count($sched) > 0) {
        $out_sched_id = $sched[0]['sched_id'];
    }
    return $out_sched_id;
}