-- Table log_types
SET NAMES 'utf8';
TRUNCATE TABLE `log_types`;
INSERT INTO `log_types` (`id`, `name`, `trans_id`, `permission`, `cache_status`, `de`, `en`, `icon_small`, `allow_rating`, `require_password`, `maintenance_logs`) VALUES
('1', 'Found', '56', 'C', '0', 'gefunden', 'found', 'log/16x16-found.png', '1', '1', '1'),
('2', 'Not found', '497', 'C', '0', 'nicht gefunden', 'not found', 'log/16x16-dnf.png', '0', '0', '1'),
('3', 'Note', '20', 'A', '0', 'Bemerkung', 'note', 'log/16x16-note.png', '0', '0', '1'),
('7', 'Attended', '498', 'C', '0', 'teilgenommen', 'attended', 'log/16x16-attended.png', '1', '1', '0'),
('8', 'Will attend', '499', 'C', '0', 'möchte teilnehmen', 'will attend', 'log/16x16-will_attend.png', '0', '0', '0'),
('9', 'Archived', '496', 'C', '3', 'archiviert', 'Archived', 'log/16x16-archived.png', '0', '0', '0'),
('10', 'Available', '531', 'C', '1', 'kann gesucht werden', 'Available', 'log/16x16-active.png', '0', '0', '1'),
('11', 'Temporarily not available', '532', 'C', '2', 'momentan nicht verfügbar', 'Temporarily not available', 'log/16x16-disabled.png', '0', '0', '1'),
('13', 'Locked', '2023', 'C', '6', 'gesperrt', 'Locked', 'log/16x16-locked.png', '0', '0', '0'),
('14', 'Locked, invisible', '822', 'C', '7', 'gesperrt, versteckt', 'Locked, invisible', 'log/16x16-locked-invisible.png', '0', '0', '0');
