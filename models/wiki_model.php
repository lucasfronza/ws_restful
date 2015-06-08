<?php

class Wiki_model {

    public $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function insert($data)
    {
        return $this->db->insert('wiki', $data);
    }

    public function get($key)
    {
        return $this->db->select('wiki', "*", ['key' => $key]);
    }

    public function delete($key)
    {
        return $this->db->delete('wiki', ['key' => $key]);
    }

    public function update($data)
    {
        return $this->db->update('wiki', $data, ['key' => $data['key']]);
    }

}