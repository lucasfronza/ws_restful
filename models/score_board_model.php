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

    public function insertUser($key, $user_id)
    {
        $activities = $this->db->select('score_activities', "*", ['key' => $key]);

        if (!$activities) {
            return false;
        }

        foreach ($activities as $activity) {
            $data                = array();
            $data['key']         = $key;
            $data['score']       = 0;
            $data['user_id']     = $user_id;
            $data['activity_id'] = $activity['activity_id'];

            $this->db->insert('scores', $data);
        }

        return true;
    }

    public function getUsers($key)
    {
        $scores = $this->db->query(
            "SELECT DISTINCT `user_id` FROM `scores` WHERE `key` = ".$this->db->quote($key).""
            )->fetchAll();

        $users = array();
        $count = count($scores);
        for ($i = 0; $i < $count; $i++) {
            $users[$i]['user_id'] = $scores[$i]['user_id'];
        }

        return array('key' => $key, 'users' => $users);
    }

}