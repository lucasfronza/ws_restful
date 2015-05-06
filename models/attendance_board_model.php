<?php

class Attendance_board_model {

    public $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function insertActivity($data)
    {
        return $this->db->insert('attendance_activities', $data);
    }

    public function insertAttendance($data)
    {
        return $this->db->insert('attendances', $data);
    }

    public function get($key)
    {
        $attendances = $this->db->select('attendances', "*", ['key' => $key]);
        
        $count = count($attendances);
        for ($i = 0; $i < $count; $i++) {
            $attendances[$i]['title'] = $this->db->get('attendance_activities', "*", ['activity_id' => $attendances[$i]['activity_id']])['title'];
            unset($attendances[$i]['key']);
        }
        $ret = array('key' => $key, 'attendances' => $attendances);

        return $ret;
    }

    public function delete($key)
    {
        return ($this->db->delete('attendances', ['key' => $key]) && $this->db->delete('attendance_activities', ['key' => $key]));
    }

    public function updateAttendance($data)
    {
        return $this->db->update('attendances', $data, ["AND" => ['key' => $data['key'], 'attendance_id' => $data['attendance_id']]]);
    }

    public function getUserAttendances($key, $user_id)
    {
        if (!$this->db->has('attendances', ["AND" => ['key' => $key, 'user_id' => $user_id]])) {
            return false;
        }

        $attendances = $this->db->select('attendances', "*", ["AND" => ['key' => $key, 'user_id' => $user_id]]);

        $count = count($attendances);
        for ($i = 0; $i < $count; $i++) {
            $attendances[$i]['title'] = $this->db->get('attendance_activities', "*", ['activity_id' => $attendances[$i]['activity_id']])['title'];
            unset($attendances[$i]['key']);
        }

        return array('key' => $key, 'attendances' => $attendances);
    }

    public function deleteUser($key, $user_id)
    {
        return $this->db->delete('attendances', ["AND" => ['key' => $key, 'user_id' => $user_id]]);
    }

    public function insertUser($key, $user_id)
    {
        $activities = $this->db->select('attendance_activities', "*", ['key' => $key]);

        if (!$activities) {
            return false;
        }

        foreach ($activities as $activity) {
            $data                = array();
            $data['key']         = $key;
            $data['attended']    = 1;
            $data['user_id']     = $user_id;
            $data['activity_id'] = $activity['activity_id'];

            $this->db->insert('attendances', $data);
        }

        return true;
    }

    public function getUsers($key)
    {
        $attendances = $this->db->query(
            "SELECT DISTINCT `user_id` FROM `attendances` WHERE `key` = ".$this->db->quote($key).""
            )->fetchAll();

        $users = array();
        $count = count($attendances);
        for ($i = 0; $i < $count; $i++) {
            $users[$i]['user_id'] = $attendances[$i]['user_id'];
        }

        return array('key' => $key, 'users' => $users);
    }

}