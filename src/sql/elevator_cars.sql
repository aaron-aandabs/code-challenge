CREATE TABLE IF NOT EXISTS `elevator_cars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cart_number` int(11),
  `floor_position` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
)
