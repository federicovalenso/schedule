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
 * Проверяет факт существования расписания с переданным id
 * @param $id int идентификатор расписания
 *
 * @return $result boolean если расписание с текущим идентификатором существует
 */
function sched_id_exists($id) {
    $result = FALSE;
    $db = new DB();

    $sched = $db->select_data(
        "SELECT sched_id
        FROM sched
        WHERE sched_id=?",
        [$id]
    );

    if (count($sched) > 0) {
        $result = TRUE;
    }

    return $result;
}
/**
 * Проверяет существует ли у врача с указанным id расписание
 * @param $id int идентификатор врача
 *
 * @return array int состоит из двух элементов:
 *              идентификатор строки расписания и
 *              идентификатор экрана, на котором расположена строка
 */
function chk_dupl_doc_sched($id) {
    $out_sched_id = NULL;
    $out_screen_id = NULL;
    $db = new DB();

    $sched = $db->select_data(
        "SELECT sched_id,
        screen_id
        FROM sched
        WHERE doc_id=? AND
        fl_display=1",
        [$id]);

    if (count($sched) > 0) {
        $out_sched_id = $sched[0]['sched_id'];
        $out_screen_id = $sched[0]['screen_id'];
    }

    return [$out_sched_id, $out_screen_id];
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
/**
 * Изменяет положение строки расписания на экране в соответствии с направлением
 * @param $id int идентификатор строки расписания
 * @param $direction int направление и длина перемещения
 * @return Nothing
 * 
 * @throws Exception Incorrect screen position
 */
function change_screen_position($id, $direction) {
    $db = new DB();

    $cur_sched = $db->select_data(
        "SELECT screen_position,
        screen_id
        FROM sched
        WHERE sched_id=?",
        [$id])[0];
    $old_position = intval($cur_sched['screen_position']);
    $new_position = $old_position + $direction;

    if ($new_position < 1 || $new_position > 9) {
        throw new Exception('Incorrect screen position');
    }

    $db->exec_query(
        "UPDATE sched
        SET screen_position=?
        WHERE 
        screen_id=? AND
        screen_position=?",
        [$old_position,
        $cur_sched['screen_id'],
        $new_position]);
    $db->exec_query(
        "UPDATE sched
        SET screen_position=?
        WHERE
        sched_id=?",
        [$new_position,
        $id]);
}
/**
 * Обновляет позиции строк расписания на выбранном экране
 * @param $screen_id int идентификатор экрана
 *
 * @return nothing
 * 
 *  * @throws Exception Incorrect screen id
 */
function refresh_screen_positions($screen_id) {
    $db = new DB();

    if ($screen_id < 1 || $screen_id > 8) {
        throw new Exception('Incorrect screen id');
    }

    $scheds = $db->select_data(
        "SELECT sched_id
        FROM sched
        WHERE screen_id=?",
        [$screen_id]
    );
    $scheds_size = count($scheds);
    for ($i = 1; $i <= $scheds_size; $i++) {
        $db->exec_query(
            "UPDATE sched
            SET screen_position=?
            WHERE sched_id=?",
            [$i,
            $scheds[$i-1]['sched_id']]);
    }
}