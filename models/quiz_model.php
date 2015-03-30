<?php

class Quiz_model {

    public $db;

    public function __construct($db) {
        $this->db = $db;
    }

    # Insere um Quiz
    public function insert($data)
    {
        return $this->db->insert('quiz', $data);
    }

    # Atualiza um Quiz
    public function update($data)
    {
        return $this->db->update('quiz', $data, ['key' => $data['key']]);
    }

    # Retorna um Quiz
    public function get($key)
    {
        return $this->db->get('quiz', "*", ['key' => $key]);
    }

    # Deleta um Quiz
    public function delete($key)
    {
        return $this->db->delete('quiz', ['key' => $key]);
    }

}