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
        // Determine which connection type this is for proper reconnection
        $connType = 'con';
        if ($db === $this->db3) {
            $connType = 'con3';
        }

        // Reconnect if connection is dead or null
        if (!$db || !($db instanceof \mysqli) || !@mysqli_ping($db)) {
            $db = Database::getConnection($connType);
            // Update the instance property so subsequent calls use the fresh connection
            if ($connType === 'con3') {
                $this->db3 = $db;
            } else {
                $this->db = $db;
            }
        }

        if (!$db) {
            error_log("Model::query() - Failed to get DB connection ($connType) for query: " . substr($sql, 0, 80));
            return false;
        }

        $result = @mysqli_query($db, $sql);

        // Retry once on connection-level failure (server has gone away, etc.)
        if ($result === false && mysqli_errno($db) >= 2000) {
            $db = Database::getConnection($connType);
            if ($connType === 'con3') {
                $this->db3 = $db;
            } else {
                $this->db = $db;
            }
            if ($db) {
                $result = @mysqli_query($db, $sql);
            }
        }

        return $result;
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
