-- Table coordinates_type
SET NAMES 'utf8';
TRUNCATE TABLE `coordinates_type`;
INSERT INTO `coordinates_type` (`id`, `name`, `trans_id`, `image`, `preposition`, `pp_trans_id`) VALUES
('1', 'Parking', '1788', 'resource2/ocstyle/images/misc/wp_parking.png', 'for', '1923'),
('2', 'Stage or reference point', '1789', 'resource2/ocstyle/images/misc/wp_reference.png', 'of', '894'),
('3', 'Path', '1926', 'resource2/ocstyle/images/misc/wp_path.png', 'to ', '1961'),
('4', 'Final', '1927', 'resource2/ocstyle/images/misc/wp_final.png', 'of', '894'),
('5', 'Point of interest', '1570', 'resource2/ocstyle/images/misc/wp_poi.png', 'at', '1962');
