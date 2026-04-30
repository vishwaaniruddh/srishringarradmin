<?php
namespace Core;

class Model {
    protected $db;
    protected $db3;

    public function __construct() {
        $this->db = Database::getConnection('con');
        $this->db3 = Database::getConnection('con3');
    }

    protected function query($db, $sql) {
        return mysqli_query($db, $sql);
    }

    protected function fetchAll($result) {
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    protected function fetchOne($result) {
        return mysqli_fetch_assoc($result);
    }
}
