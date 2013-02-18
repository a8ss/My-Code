<?php
class Model {
    public $db;

    public static function getDB(){
        $dsn = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME;
        try {
            $db = new PDO($dsn, DBUSER, DBPWD);
            $db->exec('set names utf8');
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }

        return $db;
    }

    public function __construct(){
        $this->db = self::getDB();
    }

}
