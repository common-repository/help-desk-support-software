<?php
/*
 *  Plugin Name: Swift Help Desk Support Software Ticketing System
 *  Plugin URL: http://SwiftHelpdesk.com?pr=106
 *  Description: Swift Help Desk Support Software Ticketing System - Turn any wordpress into a knowledgebase and support request system. To set up, create a post category called Help / Support, then visit the settings section of the helpdesk plugin. Create 3 pages - Support Central, Support Request, and Thanks Helpdesk. On them, add [helpdesk_knowledgebase] to Support Central, and [helpdesk_form] to Support Request, and write a simple confirmation message on Thanks Helpdesk. Ensure you have a SwiftCloud.AI number for the form, and you're all set up.
 *  Version: 1.3.18
 *  Author: Roger Vaughn, Tejas Hapani
 *  Author URI: https://Swiftcloud.ai/
 *  Text Domain: swift_helpdesk
 */

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    _e('Hi there!  I\'m just a plugin, not much I can do when called directly.', 'swift_helpdesk');
    exit;
}

define('SWIFTHD_VERSION', '1.3.17');
define('SWIFTHD__MINIMUM_WP_VERSION', '4.5');
define('SWIFTHD__PLUGIN_URL', plugin_dir_url(__FILE__));
define('SWIFTHD__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SWIFTHD_PLUGIN_PREFIX', 'shd_');

register_activation_hook(__FILE__, 'shd_install');

if (!function_exists('shd_install')) {

    function shd_install() {

        if (version_compare($GLOBALS['wp_version'], SWIFTHD__MINIMUM_WP_VERSION, '<')) {
            add_action('admin_notices', create_function('', "
        echo '<div class=\"error\"><p>" . sprintf(esc_html__('Swift Help Desk Support Software Ticketing System %s requires WordPress %s or higher.', 'swift_helpdesk'), SWIFTHD_VERSION, SWIFTHD__MINIMUM_WP_VERSION) . "</p></div>'; "));

            add_action('admin_init', 'shd_deactivate_self');

            function shd_deactivate_self() {
                if (isset($_GET["activate"]))
                    unset($_GET["activate"]);
                deactivate_plugins(plugin_basename(__FILE__));
            }

            return;
        }
        update_option('swift_help_desk_db_version', SWIFTHD_VERSION);
    }

}

if (!function_exists('shd_initial_data')) {

    function shd_initial_data() {
        /**
         *   Auto generate page
         */
        $swift_settings = get_option('swift_helpdesk_settings');
        $pages_array = array(
            "helpsupport" => array("title" => "Support", "content" => "[swift_helpdesk_support]", "slug" => "help-support"),
            "thankssupport" => array("title" => "Thanks Support", "content" => "", "slug" => "thanks-support", "option" => "helpdesk_thank_you_url"),
        );
        $helpdesk_pages_id = '';
        foreach ($pages_array as $key => $page) {
            $page_data = array(
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_title' => $page['title'],
                'post_name' => $page['slug'],
                'post_content' => $page['content'],
                'comment_status' => 'closed'
            );
            $page_id = wp_insert_post($page_data);
            if (!empty($page['option'])) {
                $swift_settings[$page['option']] = $page_id;
                update_option('swift_helpdesk_settings', $swift_settings);
            }
            $helpdesk_pages_id .= $page_id . ",";
        }

        if (!empty($helpdesk_pages_id)) {
            update_option('swifthelpdesk_pages', rtrim($helpdesk_pages_id, ","));
        }

        //Show Popular Articles & Auto Search - default to ON
        $swift_settings['enable_help_show_articles'] = 1;
        $swift_settings['help_form_enable_auto_search'] = 1;
        update_option('swift_helpdesk_settings', $swift_settings);
    }

}


/* Update plugin */
add_action('plugins_loaded', 'shd_update_check');
if (!function_exists('shd_update_check')) {

    function shd_update_check() {
        if (get_option("swift_help_desk_db_version") != SWIFTHD_VERSION) {
            shd_install();
        }
    }

}


/**/
add_action('upgrader_process_complete', 'shd_update_process');
if (!function_exists('shd_update_process')) {

    function shd_update_process($upgrader_object, $options = '') {
        $current_plugin_path_name = plugin_basename(__FILE__);

        if (isset($options) && !empty($options) && $options['action'] == 'update' && $options['type'] == 'plugin' && $options['bulk'] == false && $options['bulk'] == false) {
            foreach ($options['packages'] as $each_plugin) {
                if ($each_plugin == $current_plugin_path_name) {
                    shd_install();
                    shd_initial_data();
                }
            }
        }
    }

}


/**
 *      Deactive plugin
 */
register_deactivation_hook(__FILE__, 'shd_deactive_plugin');
if (!function_exists('shd_deactive_plugin')) {

    function shd_deactive_plugin() {
        flush_rewrite_rules();
    }

}

/**
 *      Uninstall plugin
 */
register_uninstall_hook(__FILE__, 'shd_uninstall_callback');
if (!function_exists('shd_uninstall_callback')) {

    function shd_uninstall_callback() {

        delete_option("swift_help_desk_db_version");
        delete_option("swift_helpdesk_notice");
        delete_option("swift_dashboard_subscribe");

        // delete pages
        $pages = get_option('swifthelpdesk_pages');
        if ($pages) {
            $pages = explode(",", $pages);
            foreach ($pages as $pid) {
                wp_delete_post($pid, true);
            }
        }
        delete_option("swifthelpdesk_pages");
    }

}


/**
 *      front end enqueue styles and scripts
 */
add_action('wp_enqueue_scripts', 'shd_enqueue_scripts_styles');
if (!function_exists('shd_enqueue_scripts_styles')) {

    function shd_enqueue_scripts_styles() {
        wp_enqueue_style('shd-public', plugins_url('/css/shd_public.css', __FILE__), '', '', '');
        wp_enqueue_script('jquery-help', "//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js", '', '', false);
        wp_enqueue_script('shd-front', plugins_url('js/shd-front.js', __FILE__), array('jquery'), '', true);
        wp_localize_script('shd-front', 'shd_ajax_obj', array('ajax_url' => admin_url('admin-ajax.php')));
    }

}

//Load admin modules
require_once('admin/shd_admin.php');
include_once 'sections/shd-shortcodes.php';
include_once 'sections/shd-was-this-helpful.php';
include_once 'sections/swift-form-error-popup.php';





//second step find articales
add_action('wp_ajax_setp_two_helpdesk_find_artical', 'setp_two_helpdesk_find_artical_callback');
add_action('wp_ajax_nopriv_setp_two_helpdesk_find_artical', 'setp_two_helpdesk_find_artical_callback');
if (!function_exists('setp_two_helpdesk_find_artical_callback')) {

    function setp_two_helpdesk_find_artical_callback() {
        // Our variables
        global $wpdb;
        $table_terms = $wpdb->prefix . "terms";
        $table_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $table_term_relationships = $wpdb->prefix . "term_relationships";

        $search_key = (isset($_POST['search_key'])) ? $_POST['search_key'] : "";
        $tags = "";
        $tag_ids = "";
        $tag_query_str = "";
        $tag_result = "";

        $search_key_arr = @explode(" ", $search_key);
        foreach ($search_key_arr as $key) {
            $tag_query[] = " $table_terms.name LIKE '%" . esc_sql($key) . "%' ";
        }
        if (isset($tag_query) && !empty($tag_query)) {
            $tag_query_str = " AND ";
            $tag_query_str .= "(" . @implode(" OR ", $tag_query) . " )";
        }

        $tag_result = $wpdb->get_results("SELECT * FROM $table_terms
                INNER JOIN $table_term_taxonomy ON $table_term_taxonomy.term_id = $table_terms.term_id
                INNER JOIN $table_term_relationships ON $table_term_relationships.term_taxonomy_id = $table_term_taxonomy.term_taxonomy_id
                WHERE taxonomy = 'post_tag' " . $tag_query_str);

        if (isset($tag_result) && !empty($tag_result)) {
            foreach ($tag_result as $tag) {
                $tag_ids[] = $tag->slug;
            }
            $tags = @implode(",", $tag_ids);
        }

        if (!empty($tag)) {
            $search_type = array('tag' => $tags);
        } else {
            $search_type = array('s' => $search_key);
        }

        //set limit
        $limit = (isset($_POST['limit']) && !empty($_POST['limit'])) ? $_POST['limit'] : 10;

        $args = array(
            'posts_per_page' => $limit,
            'post_type' => 'post',
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
        );
        array_merge($args, $search_type);
        $args = $args + $search_type;

        query_posts($args);
        if (have_posts()) {
            ?>
            <label class="list_title">We think this will help...</label>
            <?php
            while (have_posts()) {
                the_post();
                ?>
                <div class="item">
                    <a href="<?php echo get_permalink(get_the_ID()); ?>" target="_blank">
                        <?php if (has_post_thumbnail()) { ?>
                            <div class="item_image">
                                <?php the_post_thumbnail(array(80, 80)); ?>
                            </div>
                        <?php } else { ?>
                            <div class="item_image">
                                <img src="<?php echo SWIFTHD__PLUGIN_URL . "/images/flag.png"; ?>" alt="<?php echo get_the_title(get_the_ID()); ?>" />
                            </div>
                        <?php } ?>
                        <div class="content">
                            <h3><?php the_title(); ?></h3>
                            <?php
                            $post_content = shd_custom_excerpt(100, get_the_ID(), FALSE);
                            echo highlight_key_words($post_content, $_POST['search_key']);
                            ?>
                        </div>
                        <div class="clear"></div>
                    </a>
                </div>
                <?php
            }
        }
        wp_reset_query();
        wp_die();
    }

}

// setp one : find articales
add_action('wp_ajax_helpdesk_find_artical', 'helpdesk_find_artical_callback');
add_action('wp_ajax_nopriv_helpdesk_find_artical', 'helpdesk_find_artical_callback');
if (!function_exists('helpdesk_find_artical_callback')) {

    function helpdesk_find_artical_callback() {
        $search_key = (isset($_POST['search_key'])) ? $_POST['search_key'] : "";

        //set limit
        $limit = (isset($_POST['limit']) && !empty($_POST['limit'])) ? $_POST['limit'] : 10;

        $args = array(
            'posts_per_page' => $limit,
            'post_type' => 'post',
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
            's' => $search_key
        );

        $op = '';
        query_posts($args);
        if (have_posts()) {
            while (have_posts()) {
                the_post();
                $op .= '<div class="item">';
                $op .= '<a href="' . get_permalink(get_the_ID()) . '" target="_blank">';
                if (has_post_thumbnail()) {
                    $op .= '<div class="item_image">' . get_the_post_thumbnail(get_the_ID(), array(80, 80)) . '</div>';
                } else {
                    $op .= '<div class="item_image"><img src="' . SWIFTHD__PLUGIN_URL . '/images/flag.png" alt="' . get_the_title(get_the_ID()) . '" /></div>';
                }
                $op .= '<div class="content"><h3>' . get_the_title() . '</h3> ' . shd_custom_excerpt(10, get_the_ID(), FALSE) . '</div>';
                $op .= '<div class="clear"></div></a></div>';
            }
            wp_reset_postdata();
        }
        if (!empty($op)) {
            echo $op;
        } else {
            echo "<h3>We've got nothing based on these keywords - please open a support ticket for further assistance.</h3>";
        }
        wp_reset_query();
        wp_die();
    }

}

//find tags
add_action('wp_ajax_helpdesk_find_tag', 'helpdesk_find_tag_callback');
add_action('wp_ajax_nopriv_helpdesk_find_tag', 'helpdesk_find_tag_callback');
if (!function_exists('helpdesk_find_tag_callback')) {

    function helpdesk_find_tag_callback() {
        global $wpdb;
        $table_terms = $wpdb->prefix . "terms";
        $table_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $table_term_relationships = $wpdb->prefix . "term_relationships";

        $tag_query_str = "";
        $search_key = (isset($_POST['search_key'])) ? $_POST['search_key'] : "";
        $search_key = preg_replace('/\s\s+/', ' ', $search_key);
        $search_key_arr = @explode(" ", $search_key);
        $search_key_arr = array_unique($search_key_arr);

        if (!empty($search_key_arr)) {
            $tag_query = array();
            foreach ($search_key_arr as $key) {
                $tag_query[] = " $table_terms.name LIKE '%" . esc_sql($key) . "%' ";
            }
            if (isset($tag_query) && !empty($tag_query)) {
                $tag_query_str = " AND ";
                $tag_query_str .= "(" . @implode(" OR ", $tag_query) . " )";
            }
        }
        $tag_result = $wpdb->get_results("SELECT DISTINCT $table_terms.term_id ,$table_terms.name FROM $table_terms
                INNER JOIN $table_term_taxonomy ON $table_term_taxonomy.term_id = $table_terms.term_id
                INNER JOIN $table_term_relationships ON $table_term_relationships.term_taxonomy_id = $table_term_taxonomy.term_taxonomy_id
                WHERE taxonomy = 'post_tag' " . $tag_query_str);

        if (isset($tag_result) && !empty($tag_result)) {
            foreach ($tag_result as $tag) {
                echo "<span><a href='" . get_tag_link($tag->term_id) . "' target='_blank'>#" . $tag->name . "</a></span>";
            }
        }

        wp_reset_query();
        wp_die();
    }

}
/*
 *      custom excerpt
 */
if (!function_exists('shd_excerpt')) {

    function shd_excerpt($excerpt_length = 55, $id = false, $echo = true, $allow_html = false) {
        $text = '';
        if (!empty($id)) {
            $the_post = get_post($id);
            $text = ($the_post->post_excerpt) ? $the_post->post_excerpt : $the_post->post_content;
        } else {
            global $post;
            $text = ($post->post_excerpt) ? $post->post_excerpt : get_the_content('');
        }

        $allowable = ($allow_html) ? '<br>, <br/>, <p>, <b>, <strong>, <h1>,<h2>,<h3>,<h4>,<h5>' : '';
        $text = strip_shortcodes($text);
        $text = apply_filters('the_content', $text);
        $text = str_replace(']]>', ']]&gt;', $text);
        $text = strip_tags($text, $allowable);

        $excerpt_more = ' ' . '...';
        $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
        if (count($words) > $excerpt_length) {
            array_pop($words);
            $text = implode(' ', $words);
            $text = $text . $excerpt_more;
        } else {
            $text = implode(' ', $words);
        }
        if ($echo)
            echo apply_filters('the_content', $text);
        else
            return $text;
    }

}

if (!function_exists('shd_custom_excerpt')) {

    function shd_custom_excerpt($excerpt_length = 55, $id = false, $echo = false, $allow_html = false) {
        return shd_excerpt($excerpt_length, $id, $echo, $allow_html);
    }

}


/*
 *      Highlight key words
 */
if (!function_exists('highlight_key_words')) {

    function highlight_key_words($text, $words) {
        preg_match_all('~\w+~', $words, $m);
        if (!$m)
            return $text;
        $regular_exp = '~\\b(' . implode('|', $m[0]) . ')\\b~';
        return preg_replace($regular_exp, '<span style="background-color:#FFFF66; color:#FF0000;">$0</span>', $text);
    }

}

/*
 *      Global keyword for search
 */
if (!function_exists('shd_global_keywords')) {

    function shd_global_keywords() {
        $global_keywords = array(
            "test",
            "support",
            "webinar",
            "esign",
            "e-sign",
            "swift",
            "wordpress",
            "plugin",
            "help",
            "cancle",
            "trial offer",
            "offer",
            "payment",
            "real estate"
        );
        return $global_keywords;
    }

}
?>