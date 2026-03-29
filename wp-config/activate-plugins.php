<?php
/**
 * GM Portal - Plugin Activation Script
 *
 * Run this script via WP-CLI or include it directly to activate required plugins
 * and configure initial settings.
 *
 * Usage via WP-CLI:
 * wp eval-file activate-plugins.php
 *
 * Or include in functions.php temporarily:
 * require_once '/path/to/activate-plugins.php';
 *
 * @package GM_Portal
 */

// Prevent direct access without WordPress
if (!defined('ABSPATH')) {
    // Try to load WordPress
    $wp_load_paths = array(
        dirname(__FILE__) . '/../../../wp-load.php',
        dirname(__FILE__) . '/../../../../wp-load.php',
        '/var/www/vhosts/gornji-milanovac.com/httpdocs/wp-load.php',
    );

    $wp_loaded = false;
    foreach ($wp_load_paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            $wp_loaded = true;
            break;
        }
    }

    if (!$wp_loaded) {
        die('WordPress not found. Run this script via WP-CLI: wp eval-file activate-plugins.php');
    }
}

// Check if running from CLI
$is_cli = (php_sapi_name() === 'cli');

/**
 * Output message
 */
function gm_output($message, $type = 'info') {
    global $is_cli;

    if ($is_cli) {
        $prefix = '';
        switch ($type) {
            case 'success':
                $prefix = "\033[32m✓\033[0m ";
                break;
            case 'error':
                $prefix = "\033[31m✗\033[0m ";
                break;
            case 'warning':
                $prefix = "\033[33m!\033[0m ";
                break;
            default:
                $prefix = "→ ";
        }
        echo $prefix . $message . "\n";
    } else {
        $color = 'inherit';
        switch ($type) {
            case 'success':
                $color = 'green';
                break;
            case 'error':
                $color = 'red';
                break;
            case 'warning':
                $color = 'orange';
                break;
        }
        echo "<p style='color: {$color};'>{$message}</p>";
    }
}

// Start output
if (!$is_cli) {
    echo '<!DOCTYPE html><html><head><title>GM Portal Setup</title></head><body>';
    echo '<h1>GM Portal - Activation Script</h1>';
}

gm_output('Starting GM Portal setup...', 'info');

// ============================================================================
// 1. Set Serbian language
// ============================================================================
gm_output('Setting language to Serbian (sr_RS)...', 'info');
update_option('WPLANG', 'sr_RS');
gm_output('Language set to Serbian', 'success');

// ============================================================================
// 2. Activate plugins
// ============================================================================
gm_output('Activating plugins...', 'info');

// Include plugin functions
if (!function_exists('activate_plugin')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

$plugins_to_activate = array(
    'classified-listing/classified-listing.php' => 'Classified Listing',
    'gm-portal/gm-portal.php' => 'GM Portal',
);

foreach ($plugins_to_activate as $plugin_file => $plugin_name) {
    if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_file)) {
        if (!is_plugin_active($plugin_file)) {
            $result = activate_plugin($plugin_file);
            if (is_wp_error($result)) {
                gm_output("Failed to activate {$plugin_name}: " . $result->get_error_message(), 'error');
            } else {
                gm_output("Activated: {$plugin_name}", 'success');
            }
        } else {
            gm_output("{$plugin_name} is already active", 'info');
        }
    } else {
        gm_output("{$plugin_name} not found at {$plugin_file}", 'warning');
    }
}

// Plugins that need to be installed (not included)
$plugins_to_install = array(
    'fluent-crm/fluent-crm.php' => array(
        'name' => 'FluentCRM',
        'url' => 'https://wordpress.org/plugins/fluent-crm/',
    ),
    'woocommerce/woocommerce.php' => array(
        'name' => 'WooCommerce',
        'url' => 'https://wordpress.org/plugins/woocommerce/',
    ),
);

foreach ($plugins_to_install as $plugin_file => $plugin_info) {
    if (!file_exists(WP_PLUGIN_DIR . '/' . $plugin_file)) {
        gm_output("{$plugin_info['name']} not installed. Install from: {$plugin_info['url']}", 'warning');
    } elseif (!is_plugin_active($plugin_file)) {
        gm_output("{$plugin_info['name']} installed but not active", 'warning');
    }
}

// ============================================================================
// 3. Activate child theme
// ============================================================================
gm_output('Checking theme...', 'info');

$current_theme = wp_get_theme();
if ($current_theme->get_stylesheet() !== 'astra-child') {
    $astra_child = wp_get_theme('astra-child');
    if ($astra_child->exists()) {
        switch_theme('astra-child');
        gm_output('Activated Astra Child theme', 'success');
    } else {
        gm_output('Astra Child theme not found. Copy theme files to wp-content/themes/astra-child/', 'warning');
    }
} else {
    gm_output('Astra Child theme is already active', 'info');
}

// ============================================================================
// 4. Create blog page
// ============================================================================
gm_output('Creating blog page...', 'info');

$blog_page = get_page_by_path('blog');
if (!$blog_page) {
    $blog_page_id = wp_insert_post(array(
        'post_title'    => 'Blog',
        'post_name'     => 'blog',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'post_content'  => '',
    ));

    if ($blog_page_id && !is_wp_error($blog_page_id)) {
        update_post_meta($blog_page_id, '_wp_page_template', 'page-blog.php');
        gm_output('Created /blog page', 'success');
    } else {
        gm_output('Failed to create blog page', 'error');
    }
} else {
    update_post_meta($blog_page->ID, '_wp_page_template', 'page-blog.php');
    gm_output('Blog page already exists', 'info');
}

// ============================================================================
// 5. Configure permalinks
// ============================================================================
gm_output('Configuring permalinks...', 'info');

update_option('permalink_structure', '/%postname%/');
flush_rewrite_rules();
gm_output('Permalinks set to /%postname%/', 'success');

// ============================================================================
// 6. Create main news category
// ============================================================================
gm_output('Creating news category...', 'info');

$news_cat = get_term_by('slug', 'vesti-gornji-milanovac', 'category');
if (!$news_cat) {
    $result = wp_insert_term(
        'Vesti iz Gornjeg Milanovca',
        'category',
        array(
            'slug' => 'vesti-gornji-milanovac',
            'description' => 'Sve vesti vezane za Gornji Milanovac i okolinu',
        )
    );
    if (!is_wp_error($result)) {
        gm_output('Created news category', 'success');
    }
} else {
    gm_output('News category already exists', 'info');
}

// Create subcategories
$subcategories = array(
    'sport' => 'Sport',
    'kultura' => 'Kultura',
    'ekonomija' => 'Ekonomija',
    'drustvo' => 'Društvo',
    'hronika' => 'Hronika',
    'politika' => 'Politika',
);

foreach ($subcategories as $slug => $name) {
    if (!get_term_by('slug', $slug, 'category')) {
        wp_insert_term($name, 'category', array('slug' => $slug));
    }
}
gm_output('Created news subcategories', 'success');

// ============================================================================
// 7. Set timezone and locale
// ============================================================================
gm_output('Setting timezone and locale...', 'info');

update_option('timezone_string', 'Europe/Belgrade');
update_option('date_format', 'j. F Y.');
update_option('time_format', 'H:i');
update_option('start_of_week', '1'); // Monday
gm_output('Timezone set to Europe/Belgrade', 'success');

// ============================================================================
// 8. Update site title and description
// ============================================================================
gm_output('Updating site info...', 'info');

update_option('blogname', 'Gornji Milanovac - Digitalni Portal');
update_option('blogdescription', 'Sve vesti, oglasi i informacije o Gornjem Milanovcu');
gm_output('Site title and description updated', 'success');

// ============================================================================
// 9. Create navigation menus
// ============================================================================
gm_output('Creating navigation menus...', 'info');

$menu_locations = get_theme_mod('nav_menu_locations');

// Primary menu
$primary_menu = wp_get_nav_menu_object('Glavni meni');
if (!$primary_menu) {
    $menu_id = wp_create_nav_menu('Glavni meni');

    // Add menu items
    $menu_items = array(
        array('title' => 'Vesti', 'url' => home_url('/vesti/')),
        array('title' => 'Blog', 'url' => home_url('/blog/')),
        array('title' => 'Oglasi', 'url' => home_url('/oglasi/')),
        array('title' => 'Poslovni Imenik', 'url' => home_url('/poslovni-imenik/')),
        array('title' => 'O Gradu', 'url' => home_url('/o-gradu/')),
        array('title' => 'Kontakt', 'url' => home_url('/kontakt/')),
    );

    foreach ($menu_items as $item) {
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-title' => $item['title'],
            'menu-item-url' => $item['url'],
            'menu-item-status' => 'publish',
            'menu-item-type' => 'custom',
        ));
    }

    // Assign to location
    $menu_locations['gm-primary'] = $menu_id;
    set_theme_mod('nav_menu_locations', $menu_locations);

    gm_output('Created primary navigation menu', 'success');
} else {
    gm_output('Primary menu already exists', 'info');
}

// ============================================================================
// Done!
// ============================================================================
gm_output('', 'info');
gm_output('========================================', 'info');
gm_output('GM Portal setup completed!', 'success');
gm_output('========================================', 'info');
gm_output('', 'info');
gm_output('Next steps:', 'info');
gm_output('1. Install FluentCRM for CRM functionality', 'info');
gm_output('2. Install WooCommerce for e-commerce', 'info');
gm_output('3. Configure WP Content Crawler settings', 'info');
gm_output('4. Add logo in Appearance → Customize', 'info');
gm_output('5. Visit Settings → Permalinks and click Save', 'info');

if (!$is_cli) {
    echo '</body></html>';
}
