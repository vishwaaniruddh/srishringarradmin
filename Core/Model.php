<?php
namespace Core;

class Model {
    protected $db;
    protected $db3;

    public function __construct() {
        $this->db = Database::getConnection('con');
        $this->db3 = Database::getConnection('con3');
    }

    public function getDb() {
        if (!$this->db || !@mysqli_ping($this->db)) {
            $this->db = Database::getConnection('con');
        }
        return $this->db;
    }

    public function getDb3() {
        if (!$this->db3 || !@mysqli_ping($this->db3)) {
            $this->db3 = Database::getConnection('con3');
        }
        return $this->db3;
    }

    public function query($db, $sql) {
        if (!$db || !@mysqli_ping($db)) {
            $db = Database::getConnection('con');
        }
        return mysqli_query($db, $sql);
    }

    public function fetchAll($result) {
        $rows = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function fetchOne($result) {
        return $result ? mysqli_fetch_assoc($result) : null;
    }
}
