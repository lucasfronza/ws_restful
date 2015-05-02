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

    public function get($key)
    {
        $scores = $this->db->select('scores', "*", ['key' => $key]);
        
        $count = count($scores);
        for ($i = 0; $i < $count; $i++) {
            $scores[$i]['title'] = $this->db->get('score_activities', "*", ['activity_id' => $scores[$i]['activity_id']])['title'];
            unset($scores[$i]['key']);
        }
        $ret = array('key' => $key, 'scores' => $scores);

        return $ret;
    }

    public function delete($key)
    {
        return ($this->db->delete('scores', ['key' => $key]) && $this->db->delete('score_activities', ['key' => $key]));
    }

}