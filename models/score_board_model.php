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

    public function updateScore($data)
    {
        return $this->db->update('scores', $data, ["AND" => ['key' => $data['key'], 'score_id' => $data['score_id']]]);
    }

    public function getUserScores($key, $user_id)
    {
        if (!$this->db->has('scores', ["AND" => ['key' => $key, 'user_id' => $user_id]])) {
            return false;
        }

        $scores = $this->db->select('scores', "*", ["AND" => ['key' => $key, 'user_id' => $user_id]]);

        $count = count($scores);
        for ($i = 0; $i < $count; $i++) {
            $scores[$i]['title'] = $this->db->get('score_activities', "*", ['activity_id' => $scores[$i]['activity_id']])['title'];
            unset($scores[$i]['key']);
        }

        return array('key' => $key, 'scores' => $scores);
    }

    public function deleteUser($key, $user_id)
    {
        return $this->db->delete('scores', ["AND" => ['key' => $key, 'user_id' => $user_id]]);
    }

}