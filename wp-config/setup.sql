-- ============================================================================
-- GM Portal - SQL Setup Script
-- Gornji Milanovac WordPress Portal
-- ============================================================================
-- Run this script on the MySQL server to configure initial settings
-- Database prefix: y0H2MHc8_
-- ============================================================================

-- Set Serbian language
UPDATE y0H2MHc8_options
SET option_value = 'sr_RS'
WHERE option_name = 'WPLANG';

-- Update blog name to Serbian
UPDATE y0H2MHc8_options
SET option_value = 'Gornji Milanovac - Digitalni Portal'
WHERE option_name = 'blogname';

-- Update blog description
UPDATE y0H2MHc8_options
SET option_value = 'Sve vesti, oglasi i informacije o Gornjem Milanovcu'
WHERE option_name = 'blogdescription';

-- Fix permalink structure (clean URLs)
UPDATE y0H2MHc8_options
SET option_value = '/%postname%/'
WHERE option_name = 'permalink_structure';

-- Set timezone to Belgrade
UPDATE y0H2MHc8_options
SET option_value = 'Europe/Belgrade'
WHERE option_name = 'timezone_string';

-- Set date format to Serbian style
UPDATE y0H2MHc8_options
SET option_value = 'j. F Y.'
WHERE option_name = 'date_format';

-- Set time format to 24h
UPDATE y0H2MHc8_options
SET option_value = 'H:i'
WHERE option_name = 'time_format';

-- Set week start to Monday
UPDATE y0H2MHc8_options
SET option_value = '1'
WHERE option_name = 'start_of_week';

-- Create /blog page (for editorial content) if it doesn't exist
INSERT INTO y0H2MHc8_posts (
    post_author,
    post_date,
    post_date_gmt,
    post_content,
    post_title,
    post_excerpt,
    post_status,
    comment_status,
    ping_status,
    post_name,
    post_type,
    to_ping,
    pinged,
    post_modified,
    post_modified_gmt,
    post_content_filtered,
    post_parent,
    menu_order
)
SELECT
    1,
    NOW(),
    UTC_TIMESTAMP(),
    '',
    'Blog',
    'Autorski tekstovi, komentari i analize o životu u Gornjem Milanovcu.',
    'publish',
    'closed',
    'closed',
    'blog',
    'page',
    '',
    '',
    NOW(),
    UTC_TIMESTAMP(),
    '',
    0,
    0
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM y0H2MHc8_posts WHERE post_name = 'blog' AND post_type = 'page'
);

-- Set page template for blog page
INSERT INTO y0H2MHc8_postmeta (post_id, meta_key, meta_value)
SELECT p.ID, '_wp_page_template', 'page-blog.php'
FROM y0H2MHc8_posts p
WHERE p.post_name = 'blog' AND p.post_type = 'page'
AND NOT EXISTS (
    SELECT 1 FROM y0H2MHc8_postmeta pm
    WHERE pm.post_id = p.ID AND pm.meta_key = '_wp_page_template'
);

-- Create main news category if it doesn't exist
INSERT INTO y0H2MHc8_terms (name, slug, term_group)
SELECT 'Vesti iz Gornjeg Milanovca', 'vesti-gornji-milanovac', 0
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM y0H2MHc8_terms WHERE slug = 'vesti-gornji-milanovac'
);

-- Link news category to taxonomy
INSERT INTO y0H2MHc8_term_taxonomy (term_id, taxonomy, description, parent, count)
SELECT t.term_id, 'category', 'Sve vesti vezane za Gornji Milanovac i okolinu', 0, 0
FROM y0H2MHc8_terms t
WHERE t.slug = 'vesti-gornji-milanovac'
AND NOT EXISTS (
    SELECT 1 FROM y0H2MHc8_term_taxonomy tt
    WHERE tt.term_id = t.term_id AND tt.taxonomy = 'category'
);

-- Create subcategories for news
INSERT INTO y0H2MHc8_terms (name, slug, term_group) VALUES
('Sport', 'sport', 0),
('Kultura', 'kultura', 0),
('Ekonomija', 'ekonomija', 0),
('Društvo', 'drustvo', 0),
('Hronika', 'hronika', 0),
('Politika', 'politika', 0)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Disable comments on pages by default
UPDATE y0H2MHc8_options
SET option_value = ''
WHERE option_name = 'default_comment_status';

-- Set posts per page
UPDATE y0H2MHc8_options
SET option_value = '12'
WHERE option_name = 'posts_per_page';

-- Disable pingbacks
UPDATE y0H2MHc8_options
SET option_value = ''
WHERE option_name = 'default_pingback_flag';

-- ============================================================================
-- Privacy and Security settings
-- ============================================================================

-- Discourage search engines from indexing during development (set to 1)
-- Change to 0 for production
UPDATE y0H2MHc8_options
SET option_value = '0'
WHERE option_name = 'blog_public';

-- ============================================================================
-- Clean up
-- ============================================================================

-- Remove sample post if exists
DELETE FROM y0H2MHc8_posts
WHERE post_title = 'Hello world!'
AND post_type = 'post'
AND post_status = 'publish';

-- Remove sample page if exists
DELETE FROM y0H2MHc8_posts
WHERE post_title = 'Sample Page'
AND post_type = 'page'
AND post_status = 'publish';

-- Remove sample comment
DELETE FROM y0H2MHc8_comments
WHERE comment_author = 'A WordPress Commenter';

-- ============================================================================
-- Done!
-- ============================================================================
-- After running this script:
-- 1. Flush permalinks in WP Admin → Settings → Permalinks → Save Changes
-- 2. Activate the child theme in Appearance → Themes
-- 3. Activate GM Portal plugin in Plugins
-- 4. Run activation script (activate-plugins.php)
-- ============================================================================
