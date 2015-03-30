<?php

class Key_model {

    public $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function teste() {
        var_dump($this->db->select("keys", "*"));
    }

    public function _generate_key()
    {
        
        do
        {
            $salt = hash("sha256", time().mt_rand());
            $new_key = substr($salt, 0, 40);
        }

        // Already in the DB? Fail. Try again
        while (self::_key_exists($new_key));

        return $new_key;
    }

    // --------------------------------------------------------------------

    /* Private Data Methods */

    public function _get_key($key)
    {
        return $this->db->select("keys", "*", ["key" => $key]);
    }

    // --------------------------------------------------------------------

    public function _key_exists($key)
    {
        return ($this->db->count("keys", ["key" => $key]) != 0);
    }

    // --------------------------------------------------------------------

    public function _insert_key($key)
    {
        
        $data['key'] = $key;
        $data['date_created'] = function_exists('now') ? now() : time();

        return $this->db->insert("keys", $data);
    }

    // --------------------------------------------------------------------

    public function _update_key($key, $data)
    {
        return $this->db->insert("keys", $data, ["key" => $key]);
    }

    // --------------------------------------------------------------------

    public function _delete_key($key)
    {
        return $this->db->delete("keys", ["key" => $key]);
    }
    
}