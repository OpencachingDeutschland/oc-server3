-- Table profile_options
SET NAMES 'utf8';
TRUNCATE TABLE `profile_options`;
INSERT INTO `profile_options` (`id`, `name`, `trans_id`, `internal_use`, `default_value`, `check_regex`, `option_order`, `option_input`, `optionset`) VALUES
('1', 'MiniMap-Zoom', '748', '1', '11', '^[1-9][0-9]{0,1}$', '0', 'text', '1'),
('2', 'Location', '747', '0', '', '^.*$', '10', 'text', '1'),
('3', 'Description', '114', '0', '', NULL, '100', 'textarea', '3'),
('4', 'Age', '745', '0', '', '^[[0-9]+$', '80', 'text', '1'),
('5', 'Show statistics', '744', '1', '1', '^[0-1]$', '50', 'checkbox', '1'),
('6', 'Menu option \'Map\' shows:', '1867', '1', '1', '^[0-1]$', '110', 'select:0=small map,1', '2'),
('7', 'Show overview map:', '1870', '1', '0', '^[0-1]$', '120', 'checkbox', '2'),
('8', 'Maximum caches on map<br />(%1-%2, 0=automatic):', '1871', '1', '0', '^[0-9]{1,4}$', '130', 'text', '2'),
('9', 'Cache icons:', '1872', '1', '1', '^[1-9]$', '140', 'select:1=classic OC,', '2'),
('10', 'Show %1preview pictures</a><br />(% of map area, 0=off):', '1928', '1', '7', '^[0-5]?[0-9]$', '150', 'text', '2'),
('11', 'Show picture stats and gallery', '1944', '1', '1', '^[0-1]$', '60', 'checkbox', '1'),
('13', 'Show OConly-81 stats', '2135', '1', '0', '^[0-1]$', '65', 'checkbox', '1'),
('14', 'Auto-load log entries', '2153', '1', '1', '^[0-1]$', '15', 'checkbox', '1');
