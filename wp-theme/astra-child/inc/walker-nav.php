<?php
/**
 * Custom Nav Walker for Gornji Milanovac Portal
 *
 * @package GM_Portal
 */

defined('ABSPATH') || exit;

/**
 * GM Nav Walker Class
 *
 * Custom navigation walker for dropdown menus
 */
class GM_Nav_Walker extends Walker_Nav_Menu {

    /**
     * Starts the list before the elements are added.
     *
     * @param string   $output Used to append additional content (passed by reference).
     * @param int      $depth  Depth of menu item.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     */
    public function start_lvl(&$output, $depth = 0, $args = null) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = str_repeat($t, $depth);

        // Add custom classes for dropdown
        $classes = array('gm-nav-dropdown');

        $class_names = implode(' ', apply_filters('nav_menu_submenu_css_class', $classes, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $output .= "{$n}{$indent}<ul$class_names>{$n}";
    }

    /**
     * Ends the list of after the elements are added.
     *
     * @param string   $output Used to append additional content (passed by reference).
     * @param int      $depth  Depth of menu item.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     */
    public function end_lvl(&$output, $depth = 0, $args = null) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = str_repeat($t, $depth);
        $output .= "$indent</ul>{$n}";
    }

    /**
     * Starts the element output.
     *
     * @param string   $output            Used to append additional content (passed by reference).
     * @param WP_Post  $data_object       Menu item data object.
     * @param int      $depth             Depth of menu item.
     * @param stdClass $args              An object of wp_nav_menu() arguments.
     * @param int      $current_object_id Optional. ID of the current menu item.
     */
    public function start_el(&$output, $data_object, $depth = 0, $args = null, $current_object_id = 0) {
        // Restores the more descriptive, specific name for use within this method.
        $menu_item = $data_object;

        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = ($depth) ? str_repeat($t, $depth) : '';

        $classes = empty($menu_item->classes) ? array() : (array) $menu_item->classes;
        $classes[] = 'menu-item-' . $menu_item->ID;

        // Add custom class based on depth
        if ($depth === 0) {
            $classes[] = 'gm-nav-item';
        } else {
            $classes[] = 'gm-nav-dropdown-item';
        }

        // Check if this item has children
        if (in_array('menu-item-has-children', $classes)) {
            $classes[] = 'has-dropdown';
        }

        // Current item
        if (in_array('current-menu-item', $classes)) {
            $classes[] = 'active';
        }

        /**
         * Filters the arguments for a single nav menu item.
         */
        $args = apply_filters('nav_menu_item_args', $args, $menu_item, $depth);

        /**
         * Filters the CSS classes applied to a menu item's list item element.
         */
        $class_names = implode(' ', apply_filters('nav_menu_css_class', array_filter($classes), $menu_item, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        /**
         * Filters the ID applied to a menu item's list item element.
         */
        $id = apply_filters('nav_menu_item_id', 'menu-item-' . $menu_item->ID, $menu_item, $args, $depth);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names . '>';

        $atts = array();
        $atts['title']  = !empty($menu_item->attr_title) ? $menu_item->attr_title : '';
        $atts['target'] = !empty($menu_item->target) ? $menu_item->target : '';
        if ('_blank' === $menu_item->target && empty($menu_item->xfn)) {
            $atts['rel'] = 'noopener';
        } else {
            $atts['rel'] = $menu_item->xfn;
        }
        $atts['href']         = !empty($menu_item->url) ? $menu_item->url : '';
        $atts['aria-current'] = $menu_item->current ? 'page' : '';

        // Add custom classes to links
        if ($depth === 0) {
            $atts['class'] = 'gm-nav-link';
        } else {
            $atts['class'] = 'gm-nav-dropdown-link';
        }

        /**
         * Filters the HTML attributes applied to a menu item's anchor element.
         */
        $atts = apply_filters('nav_menu_link_attributes', $atts, $menu_item, $args, $depth);

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (is_scalar($value) && '' !== $value && false !== $value) {
                $value       = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        /** This filter is documented in wp-includes/post-template.php */
        $title = apply_filters('the_title', $menu_item->title, $menu_item->ID);

        /**
         * Filters a menu item's title.
         */
        $title = apply_filters('nav_menu_item_title', $title, $menu_item, $args, $depth);

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . $title . $args->link_after;

        // Add dropdown arrow for items with children (depth 0 only)
        if ($depth === 0 && in_array('menu-item-has-children', $classes)) {
            $item_output .= '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>';
        }

        $item_output .= '</a>';
        $item_output .= $args->after;

        /**
         * Filters a menu item's starting output.
         */
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $menu_item, $depth, $args);
    }

    /**
     * Ends the element output, if needed.
     *
     * @param string   $output      Used to append additional content (passed by reference).
     * @param WP_Post  $data_object Menu item data object.
     * @param int      $depth       Depth of page.
     * @param stdClass $args        An object of wp_nav_menu() arguments.
     */
    public function end_el(&$output, $data_object, $depth = 0, $args = null) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $output .= "</li>{$n}";
    }
}

/**
 * Mobile Nav Walker (simplified for mobile menu)
 */
class GM_Mobile_Nav_Walker extends Walker_Nav_Menu {

    /**
     * Starts the element output for mobile.
     */
    public function start_el(&$output, $data_object, $depth = 0, $args = null, $current_object_id = 0) {
        $menu_item = $data_object;
        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $classes = empty($menu_item->classes) ? array() : (array) $menu_item->classes;
        $classes[] = 'gm-mobile-nav-item';

        if ($depth > 0) {
            $classes[] = 'gm-mobile-nav-subitem';
        }

        $class_names = implode(' ', array_filter($classes));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $output .= $indent . '<li' . $class_names . '>';

        $atts = array(
            'href'  => !empty($menu_item->url) ? $menu_item->url : '',
            'class' => 'gm-mobile-nav-link',
        );

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title = apply_filters('the_title', $menu_item->title, $menu_item->ID);

        $item_output = '<a' . $attributes . '>' . $title . '</a>';

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $menu_item, $depth, $args);
    }
}
