<?php
namespace Models;

use Core\Model;

class NewsletterModel extends Model {
    
    public function getSubscribers($limit = 100, $offset = 0) {
        $sql = "SELECT * FROM newsl ORDER BY id DESC LIMIT $offset, $limit";
        $result = $this->query($this->db, $sql);
        return $this->fetchAll($result);
    }

    public function getTotalSubscribers() {
        $sql = "SELECT COUNT(*) as total FROM newsl";
        $result = $this->query($this->db, $sql);
        $row = $this->fetchOne($result);
        return $row['total'] ?? 0;
    }

    public function deleteSubscriber($id) {
        $id = (int)$id;
        $sql = "DELETE FROM newsl WHERE id = $id";
        return $this->query($this->db, $sql);
    }

    public function searchSubscribers($term) {
        $term = mysqli_real_escape_string($this->db, $term);
        $sql = "SELECT * FROM newsl WHERE email LIKE '%$term%' LIMIT 50";
        $result = $this->query($this->db, $sql);
        return $this->fetchAll($result);
    }
}
