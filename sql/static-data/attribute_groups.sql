-- Table attribute_groups
SET NAMES 'utf8';
TRUNCATE TABLE `attribute_groups`;
INSERT INTO `attribute_groups` (`id`, `category_id`, `name`, `trans_id`) VALUES
('1', '1', 'Dangers', '1331'),
('2', '1', 'Infrastructure', '1332'),
('3', '1', 'Route', '1658'),
('4', '1', 'Waypoints', '1333'),
('5', '2', 'General', '1334'),
('6', '2', 'Time', '1335'),
('7', '2', 'Seasonal', '1336'),
('8', '2', 'Listing', '1337'),
('9', '2', 'Tools needed', '1338'),
('10', '2', 'Preparation needed', '1339'),
('11', '3', 'Persons', '1340'),
('12', '3', 'Transportation', '1341'),
('13', '3', 'Infrastructure', '1332');
