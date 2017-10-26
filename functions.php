<?php
/**
 * Создает шаблон html-документа, принимая на вход имя файла-шаблона и 
 * массив параметров, значения которых подставляются в файле-шаблоне
 * @param $file string имя файла-шаблона
 * @param $sql array ассоциативный массив, ключи которого - имена переменных
 *              в файле-шаблоне, значения - значения переменных в файле-шаблоне
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