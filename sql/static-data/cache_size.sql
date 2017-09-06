-- Table cache_size
SET NAMES 'utf8';
TRUNCATE TABLE `cache_size`;
INSERT INTO `cache_size` (`id`, `name`, `trans_id`, `ordinal`, `de`, `en`) VALUES
('1', 'other size', '524', '7', 'andere Größe', 'other size'),
('2', 'micro', '525', '2', 'mikro', 'micro'),
('3', 'small', '526', '3', 'klein', 'small'),
('4', 'normal', '527', '4', 'normal', 'normal'),
('5', 'large', '528', '5', 'groß', 'large'),
('6', 'very large', '529', '6', 'extrem groß', 'very large'),
('7', 'no container', '530', '8', 'kein Behälter', 'no container'),
('8', 'nano', '1803', '1', 'nano', 'nano');
