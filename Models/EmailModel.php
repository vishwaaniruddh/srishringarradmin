<?php
namespace Models;

use Core\Model;

class EmailModel extends Model {
    public function getSettings() {
        $sql = "SELECT * FROM email_settings";
        $result = $this->query($this->db, $sql);
        $settings = [];
        if ($result) {
            while ($row = $this->fetchOne($result)) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        }
        return $settings;
    }

    public function updateSettings($data) {
        foreach ($data as $key => $value) {
            $safeKey = mysqli_real_escape_string($this->db, $key);
            $safeValue = mysqli_real_escape_string($this->db, $value);
            
            $checkSql = "SELECT id FROM email_settings WHERE setting_key = '$safeKey'";
            $checkRes = $this->query($this->db, $checkSql);
            
            if (mysqli_num_rows($checkRes) > 0) {
                $sql = "UPDATE email_settings SET setting_value = '$safeValue' WHERE setting_key = '$safeKey'";
            } else {
                $sql = "INSERT INTO email_settings (setting_key, setting_value) VALUES ('$safeKey', '$safeValue')";
            }
            $this->query($this->db, $sql);
        }
        return true;
    }

    public function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS email_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        return $this->query($this->db, $sql);
    }
}
