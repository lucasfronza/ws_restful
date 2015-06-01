<?php

class Notice_board_model {

    public $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function insert($data)
    {
        return $this->db->insert('notice', $data);
    }

    public function get($key)
    {
        return $this->db->select('notice', "*", ['key' => $key]);
    }

    public function delete($key)
    {
        return $this->db->delete('notice', ['key' => $key]);
    }

    public function update($data)
    {
        return $this->db->update('notice', $data, ["AND" => ['key' => $data['key'], 'notice_id' => $data['notice_id']]]);
    }

}