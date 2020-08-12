<?php
namespace Elevator;

class CartController
{
    const TABLE_NAME = 'elevator_cars';

    private $db;

    public function getNearestCart($callFrom, $callTo, $db) {
        $this->db = $db;

        if ($callFrom > $_ENV['FLOOR_COUNT'] ||  $callFrom < 1) {
            return $this->throwErrorMessage('You are starting from a floor that doesn\'t exist. Please choose between 1 and ' . $_ENV['FLOOR_COUNT']);
        }

        if ($callTo > $_ENV['FLOOR_COUNT'] ||  $callTo < 1) {
            return $this->throwErrorMessage('You are going to a floor that doesn\'t exist. Please choose between 1 and ' . $_ENV['FLOOR_COUNT']);
        }

        $nearestCarts = $this->db->prepare('SELECT * FROM ' . self::TABLE_NAME . ' ORDER BY abs(' . $callFrom . ' - floor_position) LIMIT 1');
        $nearestCarts->execute();
        $cartData = $nearestCarts->fetch();
        if ($cartData) {
          $updateCart = $this->db->prepare('UPDATE ' . self::TABLE_NAME . ' SET floor_position = ' . $callTo . ' WHERE id = ' . $cartData['id']);
          $updateCart->execute();
          return $this->throwSuccess();
        }
        return $this->throwErrorMessage('There was an unknown error fetching your cart.');
    }

    private function throwErrorMessage($message) {
        return [
            'success' => false,
            'message' => $message
        ];
    }

    private function throwSuccess() {
        return [
            'success' => true,
            'message' => ''
        ];
    }
}
