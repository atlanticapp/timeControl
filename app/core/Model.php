<?php
namespace App\Core;

class Model {
    protected $db;
    protected $table;
    
    public function __construct() {
        global $conn;
        $this->db = $conn;
    }
}