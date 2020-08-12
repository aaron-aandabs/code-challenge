<?php
namespace Elevator;

class StartupController
{

    const TABLE_FILE = 'elevator_cars.sql';

    const TABLE_NAME = 'elevator_cars';

    private $db;

    public function checkConnections($db) {
        $this->db = $db;
        if ($this->checkForTable() === true) {
            return $this->checkCartAvailability();
        }
        return false;
    }

    private function checkForTable() {
        try {
            $dbExists = $this->db->prepare('SHOW TABLES LIKE "' . self::TABLE_NAME . '"');
            $dbExists->execute();
            $count = $dbExists->rowCount();
            if ($count == 0) {
                $sql = file_get_contents(__DIR__ . '/../sql/' . self::TABLE_FILE);
                $addTable = $this->db->prepare($sql);
                $addTable->execute();
            }
            return true;
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    private function checkCartAvailability() {
        try {
            $dbExists = $this->db->prepare('SELECT * FROM ' . self::TABLE_NAME);
            $dbExists->execute();
            $count = $dbExists->rowCount();
            if ($count == $_ENV['ELEVATOR_CAR_COUNT']) {
                return true;
            } else {
                $truncate = $this->db->prepare('TRUNCATE TABLE ' . self::TABLE_NAME);
                $truncate->execute();
                $counter = (int) $_ENV['ELEVATOR_CAR_COUNT'];
                for($i = 1; $i <= $counter; $i++) {
                    $truncate = $this->db->prepare('INSERT INTO ' . self::TABLE_NAME . ' SET cart_number = ' . $i);
                    $truncate->execute();
                }
                return true;
            }
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }
}
