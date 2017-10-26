<?php 

class DB {
    
    var $conn;

    function __construct(){
        $this->conn = $this->getMySqlConnector();
    }

    function __destruct(){
        if(isset($this->conn)){
            $this->conn->close();
        }
    }
    
    function SanitizeString($var){
        $var = strip_tags($var);
        $var = htmlentities($var, ENT_QUOTES ,"UTF-8");
        return stripslashes($var);
    }    

    function getMySqlConnector(){
        
        $server = 'localhost';
        $user = 'valera';
        $pass = 'valerap';
        $db = 'mano';
        $conn = new mysqli($server, $user, $pass, $db);
        if ($conn->connect_error) {
            print(build_template(
                'error', 
                ['error_desc' => $conn->connect_error]));
            exit();
        }
        return $conn;

    }
    /**
     * Производит запрос на поиск данных к MySql и возвращает двумерный массив данных
     *
     * @param $conn mysqli указатель на объект mysqli
     * @param $sql string строка запроса с плейсхолдерами
     * @param $data array массив с данными на места плейсхолдеров
     * 
     * @return array двумерный массив с данными, либо пустой массив в случае их отсутствия
    **/
    function select_data($sql, $data = []) {
        $safe_data = []; 
        foreach($data as $key => $value) {
            $key = $this->conn->real_escape_string($key);
            $value = $this->conn->real_escape_string($value);
            $safe_data[$key] = $value;
        }
        $stmt = $this->db_get_prepare_stmt($sql, $data);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    /**
     * Производит запрос на вставку данных к MySql
     *
     * @param $conn mysqli указатель на объект mysqli
     * @param $table string имя таблицы в базе данных
     * @param $data array ассоциативный массив с данными на места плейсхолдеров, 
     *              ключи которого - имена полей в таблице,
     *              значения - значения на место плеййсхолдеров
     * 
     * @return int|bool id добавленного элемента в случае успеха,
    *                   false в случае неудачи
    **/
    function insert_data($table, $data) {   
        $safe_keys = [];
        $safe_data = [];
        foreach($data as $key => $value) {
            $key = $this->conn->real_escape_string($key);
            $value = $this->conn->real_escape_string($value);
            $safe_keys[] = $key;
            $safe_data[] = $value;
        }
        $sql = "INSERT INTO $table(";
        foreach($safe_keys as $value) {
            $sql .= "$value, ";
        }
        $sql = substr($sql, 0, -2) . ') VALUES(';

        for($i=0; $i < count($safe_data); $i++) {
            $sql .= '?,';
        }
        $sql = substr($sql, 0, -1) . ')';
        
        $stmt = $this->db_get_prepare_stmt($sql, $safe_data);
        $res = $stmt->execute();
        $insert_result = false;
        if ($res) {
            $insert_result = $this->conn->insert_id;
        }
        return $insert_result;
    }
    /**
     * Производит произвольный запрос к MySql и возвращает двумерный массив данных
     *
     * @param $conn mysqli указатель на объект mysqli
     * @param $sql string строка запроса с плейсхолдерами
     * @param $data array массив с данными на места плейсхолдеров
     * 
     * @return bool true в случае успешного выполнения запроса, иначе false
    **/
    function exec_query($sql, $data = []) {
        $stmt = $this->db_get_prepare_stmt($sql, $data);
        return $stmt->execute();   
    }
    /**
     * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
     *
     * @param $conn mysqli-объект соединения
     * @param $sql string SQL запрос с плейсхолдерами вместо значений
     * @param array $data Данные для вставки на место плейсхолдеров
     *
     * @return stmt Подготовленное выражение
     */
    function db_get_prepare_stmt($sql, $data = []) {
        $stmt = $this->conn->prepare($sql);
        if ($data) {
            $types = '';
            $stmt_data = [];
            foreach ($data as $value) {
                $type = null;
                if (is_int($value)) {
                    $type = 'i';
                }
                else if (is_string($value)) {
                    $type = 's';
                }
                else if (is_double($value)) {
                    $type = 'd';
                }
                if ($type) {
                    $types .= $type;
                    $stmt_data[] = $value;
                }
            }
            $values = array_merge([$types], $stmt_data);
            $stmt->bind_param(...$values);
        }
        return $stmt;
    }

}

?>