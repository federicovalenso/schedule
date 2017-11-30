<?php

require_once('db.php');
use Respect\Validation\Validator as v;

final class Schedule {
    const UP_POSITION = -1;
    const DOWN_POSITION = 1;
    const ROWS_COUNT = 9;
    const SCHEDULE_ID_COL = 'sched_id';
    const SCHEDULE_ID_ERROR_DESC = 'Некорректный id расписания';
    const DOC_ID_COL = 'doc_id';
    const DOC_ID_ERROR_DESC = 'Введён некорректный id врача!';
    const SCREEN_ID_COL = 'screen_id';
    const SCREEN_ID_ERROR_DESC = 'Неверный номер экрана!';
    const SCREEN_POSITION_COL = 'screen_position';
    const SCREEN_POSITION_ERROR_DESC = 'Неверная позиция на экране!';
    const SCREENS_COUNT = 9;
    const MONDAY_START = 'mon';
    const MONDAY_END = 'mon_end';
    const TUESDAY_START = 'tue';
    const TUESDAY_END = 'tue_end';
    const WEDNESDAY_START = 'wed';
    const WEDNESDAY_END = 'wed_end';
    const THURSDAY_START = 'thu';
    const THURSDAY_END = 'thu_end';
    const FRIDAY_START = 'fri';
    const FRIDAY_END = 'fri_end';
    const TIME_ERROR_DESC = 'Некорректно введено время';
    const START_TIME_ERROR = 'Необходимо ввести время начала рабочего дня';
    const END_TIME_ERROR = 'Необходимо ввести время окончания рабочего дня';
    const DIFF_TIME_ERROR = 'Время окончания работчего дня должно быть больше времени начала';
    private $time_param_pairs = [
        ['start' => self::MONDAY_START,
        'end' => self::MONDAY_END],
        ['start' => self::TUESDAY_START,
        'end' => self::TUESDAY_END],
        ['start' => self::WEDNESDAY_START,
        'end' => self::WEDNESDAY_END],
        ['start' => self::THURSDAY_START,
        'end' => self::THURSDAY_END],
        ['start' => self::FRIDAY_START,
        'end' => self::FRIDAY_END]
    ];
    private $errors_desc = [
        self::SCHEDULE_ID_COL => self::SCHEDULE_ID_ERROR_DESC,
        self::DOC_ID_COL => self::DOC_ID_ERROR_DESC,
        self::MONDAY_START => self::TIME_ERROR_DESC,
        self::MONDAY_END => self::TIME_ERROR_DESC,
        self::TUESDAY_START => self::TIME_ERROR_DESC,
        self::TUESDAY_END => self::TIME_ERROR_DESC,
        self::WEDNESDAY_START => self::TIME_ERROR_DESC,
        self::WEDNESDAY_END => self::TIME_ERROR_DESC,
        self::THURSDAY_START => self::TIME_ERROR_DESC,
        self::THURSDAY_END => self::TIME_ERROR_DESC,
        self::FRIDAY_START => self::TIME_ERROR_DESC,
        self::FRIDAY_END => self::TIME_ERROR_DESC,
        self::SCREEN_ID_COL => self::SCREEN_ID_ERROR_DESC,
        self::SCREEN_POSITION_COL => self::SCREEN_POSITION_ERROR_DESC
    ];
    private $db;
    private $validate_errors = [];
    private $validate_rules = [];

    public function __construct() {
        $this->db = new DB();
        $this->validate_rules = [
            self::SCHEDULE_ID_COL => v::optional(v::intVal()),
            self::DOC_ID_COL => v::optional(v::intVal()),
            self::MONDAY_START => v::optional(v::date('H:i')->min('08:00')->max('20:00')),
            self::MONDAY_END => v::optional(v::date('H:i')->min('08:00')->max('20:00')),
            self::TUESDAY_START => v::optional(v::date('H:i')->min('08:00')->max('20:00')),
            self::TUESDAY_END => v::optional(v::date('H:i')->min('08:00')->max('20:00')),
            self::WEDNESDAY_START => v::optional(v::date('H:i')->min('08:00')->max('20:00')),
            self::WEDNESDAY_END => v::optional(v::date('H:i')->min('08:00')->max('20:00')),
            self::THURSDAY_START => v::optional(v::date('H:i')->min('08:00')->max('20:00')),
            self::THURSDAY_END => v::optional(v::date('H:i')->min('08:00')->max('20:00')),
            self::FRIDAY_START => v::optional(v::date('H:i')->min('08:00')->max('20:00')),
            self::FRIDAY_END => v::optional(v::date('H:i')->min('08:00')->max('20:00')),
            self::SCREEN_ID_COL => v::intVal()->min(1)->max(self::SCREENS_COUNT),
            self::SCREEN_POSITION_COL => v::optional(v::intVal()->min(1)->max(self::ROWS_COUNT))
        ];
    }

    private function validate_day_worktime($from, $to) {
        if (isset($this->validate_errors[$from['key']]) == TRUE || 
            isset($this->validate_errors[$to['key']]) == TRUE) {
                return;
        }
        if ($from['value'] != NULL && $to['value'] == NULL) {
            $this->validate_errors[$to['key']] = self::END_TIME_ERROR;
        }
        else if ($to['value'] != NULL && $from['value'] == NULL) {
            $this->validate_errors[$from['key']] = self::START_TIME_ERROR;
        }
        else if ($from['value'] != NULL && $to['value'] != NULL) {
            $from_time = strtotime($from['value']);
            $to_time = strtotime($to['value']);
            if ($to_time - $from_time < 0) {
                $this->validate_errors[$to['key']] = self::DIFF_TIME_ERROR;
            }
        }
    }

    public function validate_input_data($input_data) {
        $this->validate_errors = [];
        foreach ($input_data as $key => $value) {
            if ($this->validate_rules[$key]->validate($value) == FALSE) {
                $this->validate_errors[$key] = $this->errors_desc[$key];
            }
        }
        foreach ($this->time_param_pairs as $pair) {
            $from = [];
            $to = [];
            $start_key = $pair['start'];
            $from['key'] = $start_key;
            $from['value'] = $input_data[$start_key];
            $end_key = $pair['end'];
            $to['key'] = $end_key;
            $to['value'] = $input_data[$end_key];
            $this->validate_day_worktime($from, $to);
        }
        return $this->validate_errors;
    }

}