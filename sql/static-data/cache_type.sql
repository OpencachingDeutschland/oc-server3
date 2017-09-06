-- Table cache_type
SET NAMES 'utf8';
TRUNCATE TABLE `cache_type`;
INSERT INTO `cache_type` (`id`, `name`, `trans_id`, `ordinal`, `short`, `de`, `en`, `icon_large`, `short2`, `short2_trans_id`, `kml_name`) VALUES
('1', 'Unknown cache type', '535', '10', 'Other', 'unbekannter Cachetyp', 'Unknown cache type', 'unknown.gif', 'Unknown', '862', 'unknown'),
('2', 'Traditional Cache', '536', '1', 'Trad.', 'normaler Cache', 'Traditional Cache', 'traditional.gif', 'Traditional', '1855', 'tradi'),
('3', 'Multicache', '514', '3', 'Multi', 'Multicache', 'Multicache', 'multi.gif', 'Multicache', '514', 'multi'),
('4', 'Virtual Cache', '537', '7', 'Virt.', 'virtueller Cache', 'Virtual Cache', 'virtual.gif', 'Virtual', '1857', 'virtual'),
('5', 'Webcam Cache', '538', '8', 'ICam.', 'Webcam-Cache', 'Webcam Cache', 'webcam.gif', 'Webcam', '1572', 'webcam'),
('6', 'Event Cache', '539', '9', 'Event', 'Event-Cache', 'Event Cache', 'event.gif', 'Event', '1859', 'event'),
('7', 'Quiz Cache', '518', '4', 'Quiz', 'Rätselcache', 'Quiz Cache', 'mystery.gif', 'Quiz', '1860', 'mystery'),
('8', 'Math/Physics Cache', '540', '5', 'Math', 'Mathe-/Physikcache', 'Math/Physics Cache', 'mathe.gif', 'Math/Physics', '1861', 'mathe'),
('9', 'Moving Cache', '541', '6', 'Moving', 'beweglicher Cache', 'Moving Cache', 'moving.gif', 'Moving', '1862', 'moving'),
('10', 'Drive-in Cache', '542', '2', 'Driv.', 'Drive-In-Cache', 'Drive-in Cache', 'drivein.gif', 'Drive-in', '1863', 'drivein');
