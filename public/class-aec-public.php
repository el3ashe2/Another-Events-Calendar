<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link          https://github.com/el3ashe2/Another-Events-Calendar
 * @since         1.0.0
 *
 * @package       another-events-calendar
 * @subpackage    another-events-calendar/public
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * AEC_Public Class
 *
 * @since    1.0.0
 */
class AEC_Public {
    
    public function add_rewrites() { 
        $page_settings = get_option('aec_page_settings');
        
        // Check if $page_settings is an array
        if (!is_array($page_settings)) {
            return; // Exit the function if not an array
        }

        $url = network_home_url();
        
        // Define an array of page types and their corresponding settings
        $page_types = [
            'category' => 'aec_category',
            'tag' => 'aec_tag',
            'venue' => 'aec_venue',
            'organizer' => 'aec_organizer'
        ];

        foreach ($page_types as $type => $rewrite_tag) {
            $id = isset($page_settings[$type]) ? $page_settings[$type] : 0;
            if ($id > 0) {
                $link = str_replace($url, '', get_permalink($id));			
                $link = trim($link, '/');		
                add_rewrite_rule("$link/([^/]+)/page/?([0-9]{1,})/?$", 'index.php?page_id=' . $id . '&' . $rewrite_tag . '=$matches[1]&paged=$matches[2]', 'top');
                add_rewrite_rule("$link/([^/]+)/?$", 'index.php?page_id=' . $id . '&' . $rewrite_tag . '=$matches[1]', 'top');
            }
        }

        // Rewrite tags
        foreach ($page_types as $rewrite_tag) {
            add_rewrite_tag('%' . $rewrite_tag . '%', '([^/]+)');
        }
    }

    public function maybe_flush_rules() {
        $rewrite_rules = get_option('rewrite_rules');

        if ($rewrite_rules) {
            global $wp_rewrite;

            // Check for missing rules
            $maybe_missing = $wp_rewrite->rewrite_rules();
            $missing_rules = false;		

            foreach ($maybe_missing as $rule => $rewrite) {
                if (!array_key_exists($rule, $rewrite_rules)) {
                    $missing_rules = true;
                    break;
                }
            }

            if ($missing_rules) {
                flush_rewrite_rules();
            }
        }
    }

    public function enqueue_styles() {
        $settings = get_option('aec_general_settings');

        // Check if $settings is an array
        if (!is_array($settings)) {
            $settings = array(); // Default to an empty array if not set
        }

        wp_register_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

        $deps = array('jquery-ui-css');

        // Safely check for 'bootstrap' key in $settings
        if (isset($settings['bootstrap']) && is_array($settings['bootstrap']) && in_array('css', $settings['bootstrap'])) {
            wp_register_style(AEC_PLUGIN_SLUG . '-bootstrap', AEC_PLUGIN_URL . 'public/css/bootstrap.css', array(), AEC_PLUGIN_VERSION, 'all');
            $deps[] = AEC_PLUGIN_SLUG . '-bootstrap';
        }    

        wp_register_style(AEC_PLUGIN_SLUG, AEC_PLUGIN_URL . 'public/css/aec-public.css', $deps, AEC_PLUGIN_VERSION, 'all');

        if (is_singular('aec_events')) {
            wp_enqueue_style(AEC_PLUGIN_SLUG);
        }
    }

    public function enqueue_scripts() {
        $general_settings = get_option('aec_general_settings');
        $map_settings = get_option('aec_map_settings');

        // Check if settings are arrays
        if (!is_array($general_settings)) {
            $general_settings = array();
        }
        if (!is_array($map_settings)) {
            $map_settings = array();
        }

        wp_register_script(AEC_PLUGIN_SLUG . '-google-map', '//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&key=YOUR_API_KEY_HERE');		

        $deps = array('jquery', 'jquery-ui-datepicker');

        // Safely check for 'bootstrap' key in $general_settings
        if (isset($general_settings['bootstrap']) && is_array($general_settings['bootstrap']) && in_array('javascript', $general_settings['bootstrap'])) {
            wp_register_script(AEC_PLUGIN_SLUG . '-bootstrap', AEC_PLUGIN_URL . 'public/js/bootstrap.min.js', array('jquery'), AEC_PLUGIN_VERSION, true);
            $deps[] = AEC_PLUGIN_SLUG . '-bootstrap';
        }

        wp_register_script(AEC_PLUGIN_SLUG, AEC_PLUGIN_URL . 'public/js/aec-public.js', $deps, AEC_PLUGIN_VERSION, true);

        wp_localize_script(AEC_PLUGIN_SLUG, 'aec', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'zoom' => !empty($map_settings['zoom_level']) ? $map_settings['zoom_level'] : 5
        ));

        if (is_singular('aec_events')) {
            wp_enqueue_script(AEC_PLUGIN_SLUG . '-google-map');
            wp_enqueue_script(AEC_PLUGIN_SLUG);
        }
    }

    public function og_metatags() {
        global $post;

        if (empty($post)) return;

        $page_settings = get_option('aec_page_settings');
        $socialshare_settings = get_option('aec_socialshare_settings');

        // Check if settings are arrays
        if (!is_array($page_settings)) {
            $page_settings = array();
        }
        if (!is_array($socialshare_settings)) {
            $socialshare_settings = array();
        }

        $page = '';
        if (is_singular('aec_events')) {
            $page = 'event_detail';
        } else {
            if (isset($page_settings['categories']) && $page_settings['categories'] == $post->ID) {
                $page = 'categories';
            }

            if (in_array($post->ID, array_filter(array(
                $page_settings['calendar'] ?? null,
                $page_settings['events'] ?? null,
                $page_settings['category'] ?? null,
                $page_settings['tag'] ?? null,
                $page_settings['venue'] ?? null,
                $page_settings['organizer'] ?? null,
                $page_settings['search'] ?? null
            )))) {
                $page = 'event_archives';
            }
        }

        if (isset($socialshare_settings['pages']) && in_array($page, $socialshare_settings['pages'])) {
            $permalink = aec_get_current_url();
            $title = get_the_title();
            $content = get_the_content();
            $post_thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');

            // Define logic for category, tag, venue, and organizer pages...
            // Similar checks as above with appropriate handling

            // Generate Open Graph meta tags
            $meta = array();
            $meta[] = '<meta property="og:url" content="' . esc_url($permalink) . '" />';
            $meta[] = '<meta property="og:type" content="article" />';
            $meta[] = '<meta property="og:title" content="' . esc_html($title) . '" />';
            if (!empty($content)) {
                $meta[] = '<meta property="og:description" content="' . esc_html(wp_trim_words($content, 150)) . '" />';
            }
            if (!empty($post_thumbnail)) {
                $meta[] = '<meta property="og:image" content="' . esc_url($post_thumbnail[0]) . '" />';
            }
            $meta[] = '<meta property="og:site_name" content="' . esc_html(get_bloginfo('name')) . '" />';
            $meta[] = '<meta name="twitter:card" content="summary">';

            echo "\n" . implode("\n", $meta) . "\n";
        }
    }

    public function the_title($title) {
        global $id, $post;

        if (is_singular('aec_events') && 'aec_events' == $post->post_type) return $title;

        if (is_page() && in_the_loop()) {
            $page_settings = get_option('aec_page_settings');

            // Check if $page_settings is an array
            if (!is_array($page_settings)) {
                return $title; // Exit if not an array
            }

            // Change Category page title
            if ($id == $page_settings['category']) {
                $slug = get_query_var('aec_category');
                if ($slug && $term = get_term_by('slug', $slug, 'aec_categories')) {
                    $title = $term->name;			
                }
            }

            // Change Tag page title
            if ($id == $page_settings['tag']) {
                $slug = get_query_var('aec_tag');
                if ($slug && $term = get_term_by('slug', $slug, 'aec_tags')) {
                    $title = $term->name;	
                }
            }

            // Change Venue page title
            if ($id == $page_settings['venue']) {
                $slug = get_query_var('aec_venue');
                if ($slug && $page = get_page_by_path($slug, OBJECT, 'aec_venues')) {
                    $title = $page->post_title;			
                }
            }

            // Change Organizer page title
            if ($id == $page_settings['organizer']) {
                $slug = get_query_var('aec_organizer');
                if ($slug && $page = get_page_by_path($slug, OBJECT, 'aec_organizers')) {
                    $title = $page->post_title;			
                }
            }
        }

        return $title;
    }
}
