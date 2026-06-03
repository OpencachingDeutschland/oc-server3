-- ---------------------------------------------------------------------------
-- Placeholder descriptions for caches that have none.
--
-- Many caches in the development/test database (e.g. coordinate-only imports
-- for map testing) have no row in `cache_desc`. Because viewcache.php uses an
-- INNER JOIN on `cache_desc`, those caches render as "cache does not exist".
-- This script gives every description-less cache a 3-paragraph lorem ipsum
-- listing so it is viewable in the legacy UI.
--
-- Portable + idempotent:
--   * Targets only caches that currently lack a description (INSERT IGNORE on
--     the UNIQUE(cache_id, language) key, plus the IS NULL filter).
--   * Generates a fresh UUID per row via UUID() — no reliance on triggers.
--   * Materialises target IDs in a TEMPORARY table first, so the cache_desc
--     AFTER INSERT trigger (which updates `caches`) does not collide with the
--     statement reading from `caches` (MySQL error 1442).
--
-- Loadable into both the test system (oc3) and the ocde dev database:
--   mysql -u<user> -p<pass> <db> < sql/lorem-descriptions.sql
-- ---------------------------------------------------------------------------

CREATE TEMPORARY TABLE _missing_desc AS
SELECT c.cache_id,
       c.node,
       SUBSTRING_INDEX(c.desc_languages, ',', 1) AS lang
FROM `caches` c
LEFT JOIN `cache_desc` cd ON c.cache_id = cd.cache_id
WHERE cd.cache_id IS NULL;

INSERT IGNORE INTO `cache_desc`
    (`uuid`, `node`, `date_created`, `last_modified`, `cache_id`, `language`,
     `desc`, `desc_html`, `desc_htmledit`, `hint`, `short_desc`, `desc_dark_unsafe`)
SELECT
    UUID(), m.node, NOW(), NOW(), m.cache_id, m.lang,
    '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p><p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p><p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>',
    1, 1, '',
    'Lorem ipsum dolor sit amet (placeholder description)',
    0
FROM `_missing_desc` m;

DROP TEMPORARY TABLE `_missing_desc`;
