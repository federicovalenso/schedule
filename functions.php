<?php
require_once('db.php');
require_once('core/Schedule.php');
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
 * id должно соотвествовать перечислению [1...8]
 * @param $id int идентификатор экрана
 *
 * @return $result boolean если идентификтор - положительное число, меньшее 9, то он корректен, иначе нет
 */
function check_screen_id($id) {
    $result = FALSE;

    if (is_int($id) && $id > 0 && $id < 9) {
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
 * @throws Строка не может быть перемещена на позицию $номер_позиции
 * @throws Неверный идентификатор строки расписания
 */
function change_screen_position($id, $direction) {
    $db = new DB();
    $sched_rows_by_id = $db->select_data(
        "SELECT screen_position,
        screen_id
        FROM sched
        WHERE sched_id=?",
        [$id]);

    if (count($sched_rows_by_id) == 0) {
        throw new Exception("Неверный идентификатор строки расписания");
    }

    $cur_sched = $sched_rows_by_id[0];
    $old_position = intval($cur_sched['screen_position']);
    $new_position = $old_position + $direction;
    $sched_rows_on_screen = count($db->select_data(
        "SELECT sched_id
        FROM sched
        WHERE screen_id=?",
        [$cur_sched['screen_id']]));

    if ($new_position < 1 || $new_position > Schedule::ROWS_COUNT || $new_position > $sched_rows_on_screen) {
        throw new Exception('Строка не может быть перемещена на позицию ' . $new_position);
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
 * Ищет на каком экране находится строка расписания
 * @param $sched_id int идентификатор строки расписания
 *
 * @return int идентификатор экрана
 * 
 */
function find_screen_by_sched($sched_id) {
    $out_screen_id = NULL;
    $db = new DB();
    $sched_rows = $db->select_data(
        "SELECT sched_id,
        screen_id
        FROM sched
        WHERE sched_id=?",
        [$sched_id]
    );

    if (count($sched_rows) > 0) {
        $out_screen_id = $sched_rows[0]['screen_id'];
    }

    return $out_screen_id;
}
/**
 * Обновляет позиции строк расписания на выбранном экране
 * @param $screen_id int идентификатор экрана
 *
 * @return nothing
 * 
 *  * @throws Неверный идентификатор экрана
 */
function refresh_screen_positions($screen_id) {
    $db = new DB();

    if (check_screen_id($screen_id) == FALSE) {
        throw new Exception('Неверный идентификатор экрана');
    }

    $scheds = $db->select_data(
        "SELECT sched_id,
        screen_position
        FROM sched
        WHERE screen_id=?
        ORDER BY screen_position ASC",
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