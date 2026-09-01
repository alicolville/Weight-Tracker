-- WordPress test pages required by Playwright shortcode tests.
-- Run once against the local WordPress database before running the test suite.
-- Assumes default table prefix `wp_`. Adjust if your site uses a different prefix.
-- Safe to re-run: each INSERT uses a slug; if the slug already exists the row will be a duplicate
-- (unique index on post_name per post_type is not enforced by default, so duplicates are possible –
-- run the SELECT at the bottom first to check before inserting).

INSERT INTO `wp_posts` (
    `post_author`, `post_date`, `post_date_gmt`,
    `post_content`, `post_title`, `post_excerpt`,
    `post_status`, `comment_status`, `ping_status`,
    `post_name`, `to_ping`, `pinged`,
    `post_modified`, `post_modified_gmt`, `post_content_filtered`,
    `post_parent`, `guid`, `menu_order`,
    `post_type`, `post_mime_type`, `comment_count`
) VALUES

-- [wt-chart] test page  →  http://localhost/test-wt-chart/
(1, NOW(), UTC_TIMESTAMP(),
 '[wt-chart]', 'Test: wt-chart', '',
 'publish', 'closed', 'closed',
 'test-wt-chart', '', '',
 NOW(), UTC_TIMESTAMP(), '',
 0, '', 0, 'page', '', 0),

-- [wt-table] test page (editing enabled)  →  http://localhost/test-wt-table/
(1, NOW(), UTC_TIMESTAMP(),
 '[wt-table enable-add-edit="true"]', 'Test: wt-table', '',
 'publish', 'closed', 'closed',
 'test-wt-table', '', '',
 NOW(), UTC_TIMESTAMP(), '',
 0, '', 0, 'page', '', 0),

-- [wt-form] test page  →  http://localhost/test-wt-form/
(1, NOW(), UTC_TIMESTAMP(),
 '[wt-form]', 'Test: wt-form', '',
 'publish', 'closed', 'closed',
 'test-wt-form', '', '',
 NOW(), UTC_TIMESTAMP(), '',
 0, '', 0, 'page', '', 0),

-- [wt-progress-bar] test page  →  http://localhost/test-wt-progress-bar/
(1, NOW(), UTC_TIMESTAMP(),
 '[wt-progress-bar display-errors="true"]', 'Test: wt-progress-bar', '',
 'publish', 'closed', 'closed',
 'test-wt-progress-bar', '', '',
 NOW(), UTC_TIMESTAMP(), '',
 0, '', 0, 'page', '', 0),

-- [wt-reminder] test page – target reminder only (no date dependency)
--   →  http://localhost/test-wt-reminder/
(1, NOW(), UTC_TIMESTAMP(),
 '[wt-reminder type="target"]', 'Test: wt-reminder', '',
 'publish', 'closed', 'closed',
 'test-wt-reminder', '', '',
 NOW(), UTC_TIMESTAMP(), '',
 0, '', 0, 'page', '', 0),

-- [wt-bmi] test page – two instances wrapped in identifiable spans
--   →  http://localhost/test-wt-bmi/
(1, NOW(), UTC_TIMESTAMP(),
 '<span class="wt-test-bmi-index">[wt-bmi display="index"]</span> <span class="wt-test-bmi-label">[wt-bmi display="label"]</span>',
 'Test: wt-bmi', '',
 'publish', 'closed', 'closed',
 'test-wt-bmi', '', '',
 NOW(), UTC_TIMESTAMP(), '',
 0, '', 0, 'page', '', 0),

-- [wt-message] test page – both gain and loss instances in identifiable divs
--   →  http://localhost/test-wt-message/
(1, NOW(), UTC_TIMESTAMP(),
 '<div class="wt-test-msg-lost">[wt-message type="lost"]Losing weight![/wt-message]</div><div class="wt-test-msg-gained">[wt-message type="gained"]Gaining weight![/wt-message]</div>',
 'Test: wt-message', '',
 'publish', 'closed', 'closed',
 'test-wt-message', '', '',
 NOW(), UTC_TIMESTAMP(), '',
 0, '', 0, 'page', '', 0);

-- Verify pages were created:
-- SELECT ID, post_name, post_status FROM wp_posts
-- WHERE post_name IN ('test-wt-chart','test-wt-table','test-wt-form','test-wt-progress-bar',
--                     'test-wt-reminder','test-wt-bmi','test-wt-message')
-- AND post_type = 'page';
