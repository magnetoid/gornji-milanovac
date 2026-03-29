<?php
/**
 * Astra Child Theme - Gornji Milanovac Portal
 *
 * @package GM_Portal
 * @version 2.0.0
 */

defined('ABSPATH') || exit;

/**
 * ==========================================================================
 * THEME SETUP
 * ==========================================================================
 */

/**
 * Enqueue parent and child theme styles
 */
function gm_enqueue_styles() {
    $theme_version = wp_get_theme()->get('Version');

    // Parent theme style
    wp_enqueue_style(
        'astra-parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        $theme_version
    );

    // Child theme style
    wp_enqueue_style(
        'gm-portal-style',
        get_stylesheet_uri(),
        array('astra-parent-style'),
        $theme_version
    );

    // Google Fonts - Inter & Playfair Display
    wp_enqueue_style(
        'gm-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@400;700&display=swap',
        array(),
        null
    );

    // Portal JavaScript
    wp_enqueue_script(
        'gm-portal-js',
        get_stylesheet_directory_uri() . '/js/portal.js',
        array('jquery'),
        $theme_version,
        true
    );

    // Localize script for AJAX
    wp_localize_script('gm-portal-js', 'gmPortal', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('gm_portal_nonce'),
        'strings' => array(
            'loading' => __('Učitavanje...', 'gm-portal'),
            'error' => __('Došlo je do greške.', 'gm-portal'),
        )
    ));
}
add_action('wp_enqueue_scripts', 'gm_enqueue_styles');

/**
 * Theme setup
 */
function gm_theme_setup() {
    // Load text domain for translations
    load_child_theme_textdomain('gm-portal', get_stylesheet_directory() . '/languages');

    // Add theme supports
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script'
    ));
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // Register nav menus
    register_nav_menus(array(
        'gm-primary'  => __('Glavni meni', 'gm-portal'),
        'gm-mobile'   => __('Mobilni meni', 'gm-portal'),
        'gm-footer'   => __('Footer meni', 'gm-portal'),
    ));

    // Image sizes
    add_image_size('gm-hero', 1200, 600, true);
    add_image_size('gm-card', 600, 400, true);
    add_image_size('gm-thumbnail', 150, 150, true);
}
add_action('after_setup_theme', 'gm_theme_setup');

/**
 * ==========================================================================
 * WIDGET AREAS
 * ==========================================================================
 */

function gm_register_sidebars() {
    // Sidebar za vesti
    register_sidebar(array(
        'name'          => __('Sidebar - Vesti', 'gm-portal'),
        'id'            => 'sidebar-news',
        'description'   => __('Widgets koji se prikazuju na stranicama vesti.', 'gm-portal'),
        'before_widget' => '<div id="%1$s" class="gm-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="gm-widget-title">',
        'after_title'   => '</h3>',
    ));

    // Sidebar za oglase
    register_sidebar(array(
        'name'          => __('Sidebar - Oglasi', 'gm-portal'),
        'id'            => 'sidebar-listings',
        'description'   => __('Widgets koji se prikazuju na stranicama oglasa.', 'gm-portal'),
        'before_widget' => '<div id="%1$s" class="gm-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="gm-widget-title">',
        'after_title'   => '</h3>',
    ));

    // Footer kolone
    register_sidebar(array(
        'name'          => __('Footer - Kolona 1', 'gm-portal'),
        'id'            => 'footer-1',
        'description'   => __('Prva kolona u footeru (O nama).', 'gm-portal'),
        'before_widget' => '<div id="%1$s" class="gm-footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ));

    register_sidebar(array(
        'name'          => __('Footer - Kolona 2', 'gm-portal'),
        'id'            => 'footer-2',
        'description'   => __('Druga kolona u footeru (Brzi linkovi).', 'gm-portal'),
        'before_widget' => '<div id="%1$s" class="gm-footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ));

    register_sidebar(array(
        'name'          => __('Footer - Kolona 3', 'gm-portal'),
        'id'            => 'footer-3',
        'description'   => __('Treća kolona u footeru (Newsletter).', 'gm-portal'),
        'before_widget' => '<div id="%1$s" class="gm-footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'gm_register_sidebars');

/**
 * ==========================================================================
 * CUSTOM POST TYPE: GM BLOG
 * ==========================================================================
 */

function gm_register_blog_post_type() {
    $labels = array(
        'name'                  => __('Blog', 'gm-portal'),
        'singular_name'         => __('Blog članak', 'gm-portal'),
        'menu_name'             => __('Blog', 'gm-portal'),
        'add_new'               => __('Dodaj novi', 'gm-portal'),
        'add_new_item'          => __('Dodaj novi članak', 'gm-portal'),
        'edit_item'             => __('Uredi članak', 'gm-portal'),
        'new_item'              => __('Novi članak', 'gm-portal'),
        'view_item'             => __('Pogledaj članak', 'gm-portal'),
        'search_items'          => __('Pretraži članke', 'gm-portal'),
        'not_found'             => __('Nema pronađenih članaka', 'gm-portal'),
        'not_found_in_trash'    => __('Nema članaka u korpi', 'gm-portal'),
        'all_items'             => __('Svi članci', 'gm-portal'),
        'archives'              => __('Arhiva bloga', 'gm-portal'),
        'featured_image'        => __('Naslovna slika', 'gm-portal'),
        'set_featured_image'    => __('Postavi naslovnu sliku', 'gm-portal'),
        'remove_featured_image' => __('Ukloni naslovnu sliku', 'gm-portal'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'blog', 'with_front' => false),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-edit-page',
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions'),
        'taxonomies'         => array('gm_blog_category'),
    );

    register_post_type('gm_blog', $args);
}
add_action('init', 'gm_register_blog_post_type');

/**
 * ==========================================================================
 * CUSTOM TAXONOMY: GM BLOG CATEGORY
 * ==========================================================================
 */

function gm_register_blog_taxonomy() {
    $labels = array(
        'name'              => __('Blog kategorije', 'gm-portal'),
        'singular_name'     => __('Blog kategorija', 'gm-portal'),
        'search_items'      => __('Pretraži kategorije', 'gm-portal'),
        'all_items'         => __('Sve kategorije', 'gm-portal'),
        'parent_item'       => __('Roditeljska kategorija', 'gm-portal'),
        'parent_item_colon' => __('Roditeljska kategorija:', 'gm-portal'),
        'edit_item'         => __('Uredi kategoriju', 'gm-portal'),
        'update_item'       => __('Ažuriraj kategoriju', 'gm-portal'),
        'add_new_item'      => __('Dodaj novu kategoriju', 'gm-portal'),
        'new_item_name'     => __('Ime nove kategorije', 'gm-portal'),
        'menu_name'         => __('Kategorije', 'gm-portal'),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'blog-kategorija'),
    );

    register_taxonomy('gm_blog_category', array('gm_blog'), $args);
}
add_action('init', 'gm_register_blog_taxonomy');

/**
 * ==========================================================================
 * REMOVE BROKEN FUNCTION
 * The original update_focus_keywords function queries ALL posts on every page load
 * This is a performance killer - REMOVED
 * ==========================================================================
 */

// Remove any existing hooks that might be loading the broken function
remove_all_actions('wp');
remove_all_actions('init');

// Make sure the broken function doesn't run
if (function_exists('update_focus_keywords')) {
    remove_action('wp', 'update_focus_keywords');
    remove_action('init', 'update_focus_keywords');
    remove_action('admin_init', 'update_focus_keywords');
}

/**
 * ==========================================================================
 * SERBIAN LANGUAGE HELPERS
 * ==========================================================================
 */

/**
 * Serbian month names
 */
function gm_serbian_month($month_number) {
    $months = array(
        1  => 'januar',
        2  => 'februar',
        3  => 'mart',
        4  => 'april',
        5  => 'maj',
        6  => 'jun',
        7  => 'jul',
        8  => 'avgust',
        9  => 'septembar',
        10 => 'oktobar',
        11 => 'novembar',
        12 => 'decembar'
    );

    return isset($months[$month_number]) ? $months[$month_number] : '';
}

/**
 * Serbian day names
 */
function gm_serbian_day($day_number) {
    $days = array(
        0 => 'nedelja',
        1 => 'ponedeljak',
        2 => 'utorak',
        3 => 'sreda',
        4 => 'četvrtak',
        5 => 'petak',
        6 => 'subota'
    );

    return isset($days[$day_number]) ? $days[$day_number] : '';
}

/**
 * Format date in Serbian
 */
function gm_format_date_serbian($timestamp = null) {
    if ($timestamp === null) {
        $timestamp = current_time('timestamp');
    }

    $day_name = gm_serbian_day(date('w', $timestamp));
    $day = date('j', $timestamp);
    $month = gm_serbian_month(date('n', $timestamp));
    $year = date('Y', $timestamp);

    return ucfirst($day_name) . ', ' . $day . '. ' . $month . ' ' . $year;
}

/**
 * Relative time in Serbian
 */
function gm_time_ago_serbian($timestamp) {
    $diff = current_time('timestamp') - $timestamp;

    if ($diff < 60) {
        return 'upravo sada';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return 'pre ' . $minutes . ' min';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return 'pre ' . $hours . ' h';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        if ($days == 1) return 'juče';
        return 'pre ' . $days . ' dana';
    } else {
        return gm_format_date_serbian($timestamp);
    }
}

/**
 * ==========================================================================
 * NAV WALKER
 * ==========================================================================
 */

require_once get_stylesheet_directory() . '/inc/walker-nav.php';

/**
 * ==========================================================================
 * HELPER FUNCTIONS
 * ==========================================================================
 */

/**
 * Get breaking news posts
 */
function gm_get_breaking_news($count = 5) {
    return get_posts(array(
        'post_type'      => 'post',
        'posts_per_page' => $count,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'category_name'  => 'vesti-gornji-milanovac',
        'meta_query'     => array(
            'relation' => 'OR',
            array(
                'key'     => '_gm_breaking',
                'value'   => '1',
                'compare' => '='
            ),
            array(
                'key'     => '_gm_breaking',
                'compare' => 'NOT EXISTS'
            )
        )
    ));
}

/**
 * Get latest news
 */
function gm_get_latest_news($count = 6, $exclude = array()) {
    return get_posts(array(
        'post_type'      => 'post',
        'posts_per_page' => $count,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post__not_in'   => $exclude,
    ));
}

/**
 * Get featured listings
 */
function gm_get_featured_listings($count = 3) {
    // Check if Classified Listing plugin is active
    if (!post_type_exists('rtcl_listing')) {
        return array();
    }

    return get_posts(array(
        'post_type'      => 'rtcl_listing',
        'posts_per_page' => $count,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'meta_query'     => array(
            array(
                'key'     => '_featured',
                'value'   => '1',
                'compare' => '='
            )
        )
    ));
}

/**
 * Get post source (for crawler posts)
 */
function gm_get_post_source($post_id) {
    $source = get_post_meta($post_id, '_wpcc_source_url', true);
    if ($source) {
        $parsed = parse_url($source);
        return isset($parsed['host']) ? str_replace('www.', '', $parsed['host']) : '';
    }
    return '';
}

/**
 * Truncate text
 */
function gm_truncate($text, $length = 150, $suffix = '...') {
    $text = wp_strip_all_tags($text);
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Get category color
 */
function gm_get_category_color($category_slug) {
    $colors = array(
        'sport'     => '#E53935',
        'kultura'   => '#8E24AA',
        'ekonomija' => '#43A047',
        'politika'  => '#1E88E5',
        'hronika'   => '#FB8C00',
        'drustvo'   => '#00897B',
    );

    return isset($colors[$category_slug]) ? $colors[$category_slug] : '#1B5E20';
}

/**
 * ==========================================================================
 * ADMIN CUSTOMIZATIONS
 * ==========================================================================
 */

/**
 * Add custom columns to gm_blog
 */
function gm_blog_columns($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        if ($key == 'date') {
            $new_columns['gm_blog_category'] = __('Kategorija', 'gm-portal');
        }
        $new_columns[$key] = $value;
    }
    return $new_columns;
}
add_filter('manage_gm_blog_posts_columns', 'gm_blog_columns');

/**
 * Populate custom columns
 */
function gm_blog_column_content($column, $post_id) {
    if ($column == 'gm_blog_category') {
        $terms = get_the_terms($post_id, 'gm_blog_category');
        if ($terms && !is_wp_error($terms)) {
            $term_names = wp_list_pluck($terms, 'name');
            echo implode(', ', $term_names);
        } else {
            echo '—';
        }
    }
}
add_action('manage_gm_blog_posts_custom_column', 'gm_blog_column_content', 10, 2);

/**
 * Custom admin footer text
 */
function gm_admin_footer_text() {
    return 'Gornji Milanovac Portal — Powered by WordPress';
}
add_filter('admin_footer_text', 'gm_admin_footer_text');

/**
 * ==========================================================================
 * PERFORMANCE OPTIMIZATIONS
 * ==========================================================================
 */

/**
 * Disable emojis
 */
function gm_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'gm_disable_emojis');

/**
 * Remove query strings from static resources
 */
function gm_remove_cssjs_ver($src) {
    if (strpos($src, '?ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('style_loader_src', 'gm_remove_cssjs_ver', 10, 2);
add_filter('script_loader_src', 'gm_remove_cssjs_ver', 10, 2);

/**
 * ==========================================================================
 * CUSTOM HOOKS FOR THEME
 * ==========================================================================
 */

/**
 * Before header hook
 */
function gm_before_header() {
    do_action('gm_before_header');
}

/**
 * After header hook
 */
function gm_after_header() {
    do_action('gm_after_header');
}

/**
 * Before footer hook
 */
function gm_before_footer() {
    do_action('gm_before_footer');
}

/**
 * After footer hook
 */
function gm_after_footer() {
    do_action('gm_after_footer');
}
