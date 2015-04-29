<?php

class Score_board_model {

    public $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function insertActivity($data)
    {
        return $this->db->insert('score_activities', $data);
    }

    public function insertScore($data)
    {
        return $this->db->insert('scores', $data);
    }

}