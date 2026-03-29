<?php
/**
 * Plugin Name: GM Portal
 * Plugin URI: https://gornji-milanovac.com
 * Description: Core functionality for Gornji Milanovac portal - shortcodes, CRM, REST API, category management
 * Version: 1.0.0
 * Author: Gornji Milanovac Dev Team
 * Author URI: https://gornji-milanovac.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: gm-portal
 * Domain Path: /languages
 *
 * @package GM_Portal
 */

defined('ABSPATH') || exit;

// Plugin constants
define('GM_PORTAL_VERSION', '1.0.0');
define('GM_PORTAL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GM_PORTAL_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main GM Portal Plugin Class
 */
class GM_Portal_Plugin {

    /**
     * Instance
     */
    private static $instance = null;

    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // Initialize
        add_action('init', array($this, 'init'));
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        add_action('admin_init', array($this, 'admin_init'));

        // Shortcodes
        add_action('init', array($this, 'register_shortcodes'));

        // WP Content Crawler hooks
        add_action('wpcc_post_saved', array($this, 'add_crawler_tag'), 10, 2);

        // AJAX handlers
        add_action('wp_ajax_gm_load_category_posts', array($this, 'ajax_load_category_posts'));
        add_action('wp_ajax_nopriv_gm_load_category_posts', array($this, 'ajax_load_category_posts'));
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Set Serbian language
        update_option('WPLANG', 'sr_RS');

        // Create blog page
        $this->create_blog_page();

        // Create listing categories
        $this->create_listing_categories();

        // Create news parent category
        $this->create_news_category();

        // Create CRM Lead post type
        $this->register_lead_cpt();

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }

    /**
     * Initialize plugin
     */
    public function init() {
        // Load text domain
        load_plugin_textdomain('gm-portal', false, dirname(plugin_basename(__FILE__)) . '/languages');

        // Register Lead CPT
        $this->register_lead_cpt();
    }

    /**
     * Admin initialization
     */
    public function admin_init() {
        // Add admin notices if needed plugins are not installed
        add_action('admin_notices', array($this, 'admin_notices'));
    }

    /**
     * Admin notices
     */
    public function admin_notices() {
        // Check for FluentCRM
        if (!is_plugin_active('fluent-crm/fluent-crm.php') && !is_plugin_active('fluent-crm-pro/fluent-crm-pro.php')) {
            echo '<div class="notice notice-info is-dismissible">';
            echo '<p><strong>GM Portal:</strong> Za napredne CRM funkcije, preporučujemo instalaciju FluentCRM plugina.</p>';
            echo '</div>';
        }

        // Check for WooCommerce
        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            echo '<div class="notice notice-info is-dismissible">';
            echo '<p><strong>GM Portal:</strong> Za e-commerce funkcionalnost, instalirajte WooCommerce plugin.</p>';
            echo '</div>';
        }
    }

    /**
     * Create Blog page for gm_blog CPT
     */
    private function create_blog_page() {
        $page_exists = get_page_by_path('blog');

        if (!$page_exists) {
            $page_data = array(
                'post_title'    => 'Blog',
                'post_name'     => 'blog',
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'post_content'  => '',
                'page_template' => 'page-blog.php',
            );

            wp_insert_post($page_data);
        }
    }

    /**
     * Create Classified Listing categories
     */
    private function create_listing_categories() {
        // Check if Classified Listing taxonomy exists
        if (!taxonomy_exists('rtcl_category')) {
            return;
        }

        $categories = array(
            'nekretnine' => array(
                'name' => 'Nekretnine',
                'description' => 'Oglasi za nekretnine - kuće, stanovi, zemljišta',
            ),
            'zaposlenje' => array(
                'name' => 'Zaposlenje',
                'description' => 'Oglasi za posao i zaposlenje',
            ),
            'usluge' => array(
                'name' => 'Usluge',
                'description' => 'Ponuda raznih usluga',
            ),
            'prodaja' => array(
                'name' => 'Prodaja',
                'description' => 'Prodaja raznih artikala',
            ),
            'dogadjaji' => array(
                'name' => 'Događaji',
                'description' => 'Najave događaja i manifestacija',
            ),
            'poslovni-imenik' => array(
                'name' => 'Poslovni Imenik',
                'description' => 'Lokalne firme i preduzeća',
            ),
        );

        foreach ($categories as $slug => $data) {
            if (!term_exists($slug, 'rtcl_category')) {
                wp_insert_term(
                    $data['name'],
                    'rtcl_category',
                    array(
                        'slug' => $slug,
                        'description' => $data['description'],
                    )
                );
            }
        }
    }

    /**
     * Create News parent category
     */
    private function create_news_category() {
        $category_slug = 'vesti-gornji-milanovac';

        if (!term_exists($category_slug, 'category')) {
            wp_insert_term(
                'Vesti iz Gornjeg Milanovca',
                'category',
                array(
                    'slug' => $category_slug,
                    'description' => 'Sve vesti vezane za Gornji Milanovac i okolinu',
                )
            );
        }
    }

    /**
     * Register Lead CPT for CRM
     */
    public function register_lead_cpt() {
        $labels = array(
            'name'               => __('Leadovi', 'gm-portal'),
            'singular_name'      => __('Lead', 'gm-portal'),
            'menu_name'          => __('CRM Leadovi', 'gm-portal'),
            'add_new'            => __('Dodaj novi', 'gm-portal'),
            'add_new_item'       => __('Dodaj novi lead', 'gm-portal'),
            'edit_item'          => __('Uredi lead', 'gm-portal'),
            'new_item'           => __('Novi lead', 'gm-portal'),
            'view_item'          => __('Pogledaj lead', 'gm-portal'),
            'search_items'       => __('Pretraži leadove', 'gm-portal'),
            'not_found'          => __('Nema pronađenih leadova', 'gm-portal'),
            'not_found_in_trash' => __('Nema leadova u korpi', 'gm-portal'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => false,
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 25,
            'menu_icon'          => 'dashicons-groups',
            'supports'           => array('title', 'custom-fields'),
            'show_in_rest'       => false,
        );

        register_post_type('gm_lead', $args);

        // Register meta fields
        register_post_meta('gm_lead', 'gm_lead_ime', array(
            'type' => 'string',
            'single' => true,
            'show_in_rest' => false,
        ));

        register_post_meta('gm_lead', 'gm_lead_email', array(
            'type' => 'string',
            'single' => true,
            'show_in_rest' => false,
        ));

        register_post_meta('gm_lead', 'gm_lead_telefon', array(
            'type' => 'string',
            'single' => true,
            'show_in_rest' => false,
        ));

        register_post_meta('gm_lead', 'gm_lead_grad', array(
            'type' => 'string',
            'single' => true,
            'show_in_rest' => false,
        ));

        register_post_meta('gm_lead', 'gm_lead_tip_upita', array(
            'type' => 'string',
            'single' => true,
            'show_in_rest' => false,
        ));
    }

    /**
     * Add tag to crawler posts
     */
    public function add_crawler_tag($post_id, $post_data) {
        wp_set_post_tags($post_id, 'gornji-milanovac', true);
    }

    /**
     * Register shortcodes
     */
    public function register_shortcodes() {
        add_shortcode('gm_breaking_news', array($this, 'shortcode_breaking_news'));
        add_shortcode('gm_weather', array($this, 'shortcode_weather'));
        add_shortcode('gm_latest_news', array($this, 'shortcode_latest_news'));
        add_shortcode('gm_featured_listings', array($this, 'shortcode_featured_listings'));
    }

    /**
     * Breaking News Shortcode
     */
    public function shortcode_breaking_news($atts) {
        $atts = shortcode_atts(array(
            'count' => 5,
        ), $atts);

        $posts = get_posts(array(
            'post_type'      => 'post',
            'posts_per_page' => intval($atts['count']),
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ));

        if (empty($posts)) {
            return '';
        }

        $output = '<div class="gm-breaking-news-widget">';
        $output .= '<div class="gm-breaking-ticker">';
        $output .= '<div class="gm-breaking-ticker-inner">';

        foreach ($posts as $post) {
            $output .= '<div class="gm-breaking-item">';
            $output .= '<a href="' . get_permalink($post->ID) . '">' . esc_html($post->post_title) . '</a>';
            $output .= '</div>';
        }

        $output .= '</div></div></div>';

        return $output;
    }

    /**
     * Weather Shortcode
     */
    public function shortcode_weather($atts) {
        $atts = shortcode_atts(array(
            'city' => 'Gornji Milanovac',
        ), $atts);

        $output = '<div class="gm-weather-widget">';
        $output .= '<div class="gm-weather-icon">🌤️</div>';
        $output .= '<div class="gm-weather-location">' . esc_html($atts['city']) . '</div>';
        $output .= '<a href="https://www.yr.no/en/forecast/daily-table/2-789128/Serbia/Central%20Serbia/Moravica%20District/Gornji%20Milanovac" target="_blank" rel="noopener" class="gm-weather-link">';
        $output .= 'Pogledaj vremensku prognozu →';
        $output .= '</a>';
        $output .= '</div>';

        return $output;
    }

    /**
     * Latest News Shortcode
     */
    public function shortcode_latest_news($atts) {
        $atts = shortcode_atts(array(
            'count' => 6,
            'category' => '',
        ), $atts);

        $query_args = array(
            'post_type'      => 'post',
            'posts_per_page' => intval($atts['count']),
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        if (!empty($atts['category'])) {
            $query_args['category_name'] = sanitize_text_field($atts['category']);
        }

        $posts = get_posts($query_args);

        if (empty($posts)) {
            return '<p>Nema vesti za prikaz.</p>';
        }

        $output = '<div class="gm-latest-news-grid gm-grid gm-grid-3">';

        foreach ($posts as $post) {
            $thumbnail = get_the_post_thumbnail_url($post->ID, 'gm-card');
            if (!$thumbnail) {
                $thumbnail = get_stylesheet_directory_uri() . '/images/placeholder-card.jpg';
            }

            $categories = get_the_category($post->ID);
            $category_name = !empty($categories) ? $categories[0]->name : '';

            $output .= '<article class="gm-card">';
            $output .= '<div class="gm-card-image">';
            $output .= '<a href="' . get_permalink($post->ID) . '">';
            $output .= '<img src="' . esc_url($thumbnail) . '" alt="' . esc_attr($post->post_title) . '">';
            $output .= '</a>';
            if ($category_name) {
                $output .= '<span class="gm-card-category">' . esc_html($category_name) . '</span>';
            }
            $output .= '</div>';
            $output .= '<div class="gm-card-body">';
            $output .= '<h3 class="gm-card-title"><a href="' . get_permalink($post->ID) . '">' . esc_html($post->post_title) . '</a></h3>';
            $output .= '<div class="gm-card-meta">';
            $output .= '<span class="gm-card-date">' . get_the_date('j. F Y.', $post->ID) . '</span>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</article>';
        }

        $output .= '</div>';

        return $output;
    }

    /**
     * Featured Listings Shortcode
     */
    public function shortcode_featured_listings($atts) {
        $atts = shortcode_atts(array(
            'count' => 3,
        ), $atts);

        if (!post_type_exists('rtcl_listing')) {
            return '<p>Oglasi nisu aktivirani. Aktivirajte Classified Listing plugin.</p>';
        }

        $posts = get_posts(array(
            'post_type'      => 'rtcl_listing',
            'posts_per_page' => intval($atts['count']),
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ));

        if (empty($posts)) {
            return '<p>Nema oglasa za prikaz. <a href="' . home_url('/listing-form/') . '">Postavite prvi oglas!</a></p>';
        }

        $output = '<div class="gm-listings-grid gm-grid gm-grid-3">';

        foreach ($posts as $post) {
            $thumbnail = get_the_post_thumbnail_url($post->ID, 'gm-card');
            if (!$thumbnail) {
                $thumbnail = get_stylesheet_directory_uri() . '/images/placeholder-listing.jpg';
            }

            $terms = get_the_terms($post->ID, 'rtcl_category');
            $category_name = ($terms && !is_wp_error($terms)) ? $terms[0]->name : '';

            $price = get_post_meta($post->ID, '_price', true);

            $output .= '<article class="gm-listing-card">';
            $output .= '<div class="gm-listing-image">';
            $output .= '<a href="' . get_permalink($post->ID) . '">';
            $output .= '<img src="' . esc_url($thumbnail) . '" alt="' . esc_attr($post->post_title) . '">';
            $output .= '</a>';
            $output .= '</div>';
            $output .= '<div class="gm-listing-body">';
            if ($category_name) {
                $output .= '<span class="gm-listing-category">' . esc_html($category_name) . '</span>';
            }
            $output .= '<h3 class="gm-listing-title"><a href="' . get_permalink($post->ID) . '">' . esc_html($post->post_title) . '</a></h3>';
            if ($price) {
                $output .= '<div class="gm-listing-price">' . number_format($price, 0, ',', '.') . ' RSD</div>';
            }
            $output .= '</div>';
            $output .= '</article>';
        }

        $output .= '</div>';

        return $output;
    }

    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        // Stats endpoint
        register_rest_route('gm/v1', '/stats', array(
            'methods'             => 'GET',
            'callback'            => array($this, 'rest_get_stats'),
            'permission_callback' => '__return_true',
        ));

        // Lead capture endpoint
        register_rest_route('gm/v1', '/lead', array(
            'methods'             => 'POST',
            'callback'            => array($this, 'rest_create_lead'),
            'permission_callback' => '__return_true',
        ));
    }

    /**
     * REST: Get stats
     */
    public function rest_get_stats($request) {
        $post_count = wp_count_posts('post');
        $blog_count = wp_count_posts('gm_blog');
        $lead_count = wp_count_posts('gm_lead');

        $listing_count = 0;
        if (post_type_exists('rtcl_listing')) {
            $listing_count_obj = wp_count_posts('rtcl_listing');
            $listing_count = $listing_count_obj->publish;
        }

        return rest_ensure_response(array(
            'success' => true,
            'data' => array(
                'posts' => array(
                    'total' => $post_count->publish,
                    'draft' => $post_count->draft,
                ),
                'blog' => array(
                    'total' => $blog_count->publish,
                ),
                'listings' => array(
                    'total' => $listing_count,
                ),
                'leads' => array(
                    'total' => $lead_count->publish,
                ),
            ),
        ));
    }

    /**
     * REST: Create lead
     */
    public function rest_create_lead($request) {
        $params = $request->get_params();

        // Validate email
        if (empty($params['email']) || !is_email($params['email'])) {
            return new WP_Error('invalid_email', 'Molimo unesite validnu email adresu.', array('status' => 400));
        }

        $email = sanitize_email($params['email']);
        $ime = isset($params['ime']) ? sanitize_text_field($params['ime']) : '';
        $telefon = isset($params['telefon']) ? sanitize_text_field($params['telefon']) : '';
        $grad = isset($params['grad']) ? sanitize_text_field($params['grad']) : 'Gornji Milanovac';
        $tip_upita = isset($params['tip_upita']) ? sanitize_text_field($params['tip_upita']) : 'newsletter';

        // Check for duplicate
        $existing = get_posts(array(
            'post_type' => 'gm_lead',
            'meta_query' => array(
                array(
                    'key' => 'gm_lead_email',
                    'value' => $email,
                )
            ),
            'posts_per_page' => 1,
        ));

        if (!empty($existing)) {
            return rest_ensure_response(array(
                'success' => true,
                'message' => 'Već ste prijavljeni na newsletter.',
            ));
        }

        // Create lead
        $lead_id = wp_insert_post(array(
            'post_type' => 'gm_lead',
            'post_title' => $email,
            'post_status' => 'publish',
        ));

        if (is_wp_error($lead_id)) {
            return new WP_Error('lead_creation_failed', 'Došlo je do greške. Pokušajte ponovo.', array('status' => 500));
        }

        // Save meta
        update_post_meta($lead_id, 'gm_lead_ime', $ime);
        update_post_meta($lead_id, 'gm_lead_email', $email);
        update_post_meta($lead_id, 'gm_lead_telefon', $telefon);
        update_post_meta($lead_id, 'gm_lead_grad', $grad);
        update_post_meta($lead_id, 'gm_lead_tip_upita', $tip_upita);

        return rest_ensure_response(array(
            'success' => true,
            'message' => 'Uspešno ste se prijavili!',
            'lead_id' => $lead_id,
        ));
    }

    /**
     * AJAX: Load category posts
     */
    public function ajax_load_category_posts() {
        check_ajax_referer('gm_portal_nonce', 'nonce');

        $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : 'sport';

        $posts = get_posts(array(
            'post_type'      => 'post',
            'posts_per_page' => 4,
            'post_status'    => 'publish',
            'category_name'  => $category,
        ));

        ob_start();

        foreach ($posts as $post) {
            setup_postdata($post);
            $thumbnail = get_the_post_thumbnail_url($post->ID, 'gm-card');
            if (!$thumbnail) {
                $thumbnail = get_stylesheet_directory_uri() . '/images/placeholder-card.jpg';
            }
            ?>
            <article class="gm-card">
                <div class="gm-card-image">
                    <a href="<?php echo get_permalink($post->ID); ?>">
                        <img src="<?php echo esc_url($thumbnail); ?>" alt="">
                    </a>
                </div>
                <div class="gm-card-body">
                    <h3 class="gm-card-title">
                        <a href="<?php echo get_permalink($post->ID); ?>"><?php echo esc_html($post->post_title); ?></a>
                    </h3>
                    <div class="gm-card-meta">
                        <span class="gm-card-date">
                            <?php echo function_exists('gm_time_ago_serbian') ? gm_time_ago_serbian(get_post_time('U', false, $post->ID)) : get_the_date('', $post->ID); ?>
                        </span>
                    </div>
                </div>
            </article>
            <?php
        }
        wp_reset_postdata();

        $html = ob_get_clean();

        wp_send_json_success($html);
    }
}

// Initialize plugin
GM_Portal_Plugin::get_instance();

/**
 * Add custom admin columns for leads
 */
function gm_lead_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = __('Email', 'gm-portal');
    $new_columns['gm_lead_ime'] = __('Ime', 'gm-portal');
    $new_columns['gm_lead_telefon'] = __('Telefon', 'gm-portal');
    $new_columns['gm_lead_tip_upita'] = __('Tip', 'gm-portal');
    $new_columns['date'] = $columns['date'];
    return $new_columns;
}
add_filter('manage_gm_lead_posts_columns', 'gm_lead_columns');

/**
 * Populate lead columns
 */
function gm_lead_column_content($column, $post_id) {
    switch ($column) {
        case 'gm_lead_ime':
            echo esc_html(get_post_meta($post_id, 'gm_lead_ime', true)) ?: '—';
            break;
        case 'gm_lead_telefon':
            echo esc_html(get_post_meta($post_id, 'gm_lead_telefon', true)) ?: '—';
            break;
        case 'gm_lead_tip_upita':
            $tip = get_post_meta($post_id, 'gm_lead_tip_upita', true);
            $labels = array(
                'newsletter' => 'Newsletter',
                'oglas' => 'Oglas',
                'kontakt' => 'Kontakt',
            );
            echo isset($labels[$tip]) ? $labels[$tip] : esc_html($tip);
            break;
    }
}
add_action('manage_gm_lead_posts_custom_column', 'gm_lead_column_content', 10, 2);

/**
 * Make lead columns sortable
 */
function gm_lead_sortable_columns($columns) {
    $columns['gm_lead_tip_upita'] = 'gm_lead_tip_upita';
    return $columns;
}
add_filter('manage_edit-gm_lead_sortable_columns', 'gm_lead_sortable_columns');
