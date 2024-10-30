<?php
/*
 *  Swift Helpdesk Admin Modul.
 */

// check is plugin active and not empty sf formId
if (version_compare($GLOBALS['wp_version'], SWIFTHD__MINIMUM_WP_VERSION, '>=')) {
    add_action('admin_notices', 'shd_set_setting_notices');
}
if (!function_exists('shd_set_setting_notices')) {

    function shd_set_setting_notices() {
        $swift_settings = get_option('swift_helpdesk_settings');
        $HelpFormId = $swift_settings['help_form_id'];
        if (is_plugin_active('help-desk-support-software/swift_helpdesk.php') && empty($HelpFormId)) {
            echo "<div class='error notice'><p>Heads up! Your form will not display until you add a form ID number in the <a href='admin.php?page=shd_settings'>control panel</a>.</p></div>";
        }
        //Autogenerates pages
        if (!get_option('swift_helpdesk_notice') && !get_option('swifthelpdesk_pages')) {
            ?>
            <div class="notice notice-success is-dismissible shd-notice">
                <p><b>SwiftCloud Helpdesk Plugin</b></p>
                <form method="post">
                    <p class="sr-notice-msg"><?php _e('Want to auto-create the following pages to quickly get you set up? ', 'swift-review'); ?></p>
                    <ul>
                        <li>Support</li>
                        <li>Thanks Support</li>
                    </ul>
                    <?php wp_nonce_field('shd_autogen_pages', 'shd_autogen_pages'); ?>
                    <button type="submit" value="yes" name="sr_autogen_yes" class="button button-green"><i class="fa fa-check"></i> Yes</button>  <button type="submit" name="sr_autogen_no" value="no" class="button button-default button-red"><i class="fa fa-ban"></i> No</button>
                </form>
            </div>
            <?php
        }
    }

}


add_action('admin_menu', 'shd_control_panel');
if (!function_exists('shd_control_panel')) {

    function shd_control_panel() {
        global $submenu;

        $shd_icon_url = plugins_url('/images/swiftcloud.png', __FILE__);
        $shd_menu_slug = 'shd_settings';
        add_menu_page('Swift Helpdesk', 'Swift Helpdesk', 'manage_options', 'shd_settings', 'shd_settings_cb', $shd_icon_url);
        //Add sub menus.
        add_submenu_page($shd_menu_slug, "Settings", "Settings", 'manage_options', $shd_menu_slug, null);
        add_submenu_page($shd_menu_slug, "Feedback", "Feedback", 'manage_options', "shd_feedback", 'shd_feedback_cb');
        add_submenu_page($shd_menu_slug, "Use / Help", "Use / Help", 'manage_options', "shd_use_help", 'shd_use_help_cb');
        add_submenu_page($shd_menu_slug, "Updates & Tips", "Updates & Tips", 'manage_options', "shd_dashboard", 'shd_dashboard_cb');
        //$submenu['shd_control_panel'][0][0] = 'Settings';
    }

}


/*
 *      Admin enqueue styles and scripts
 */
add_action('admin_enqueue_scripts', 'shd_admin_enqueue');
if (!function_exists('shd_admin_enqueue')) {

    function shd_admin_enqueue($hook) {
        wp_enqueue_style('shd-admin-style', plugins_url('/css/shd_admin.css', __FILE__), '', '', '');
        wp_enqueue_style('swift-toggle-style', plugins_url('/css/shd_rcswitcher.css', __FILE__), '', '', '');
        wp_enqueue_script('swift-toggle', plugins_url('/js/shd_rcswitcher.js', __FILE__), array('jquery'), '', true);
        wp_enqueue_script('jquery-ui-tooltip');
        wp_enqueue_style('swift-cloud-jquery-ui', plugins_url('/css/jquery-ui.min.css', __FILE__), '', '', '');

        wp_enqueue_script('shd-toggle-custom', plugins_url('/js/shd_admin.js', __FILE__), array('jquery'), '', true);
        wp_localize_script('shd-toggle-custom', 'shd_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    }

}


include_once 'sections/shd-setting-page.php';
include_once 'sections/shd-feedback-page.php';
include_once 'sections/shd-help-page.php';
include_once 'sections/swift_dashboard.php';


add_action("init", "shd_admin_init");
if (!function_exists('shd_admin_init')) {

    function shd_admin_init() {
        /* update old swift cal. settings to new option */
        $swift_helpdesk_settings = get_option('swift_helpdesk_settings');
        if (empty($swift_helpdesk_settings)) {
            $swift_helpdesk_settings = get_option('swift_settings');
            update_option('swift_helpdesk_settings', $swift_helpdesk_settings);
        }

        /* on plugin active auto generate pages and options */
        if (isset($_POST['shd_autogen_pages']) && wp_verify_nonce($_POST['shd_autogen_pages'], 'shd_autogen_pages')) {
            if ($_POST['sr_autogen_yes'] == 'yes') {
                shd_initial_data();
            }
            update_option('swift_helpdesk_notice', true);
        }
    }

}


/**
 *  Dismiss notice callback
 */
add_action('wp_ajax_shd_dismiss_notice', 'shd_dismiss_notice_callback');
add_action('wp_ajax_nopriv_shd_dismiss_notice', 'shd_dismiss_notice_callback');
if (!function_exists('shd_dismiss_notice_callback')) {

    function shd_dismiss_notice_callback() {
        update_option('swift_helpdesk_notice', true);
    }

}
?>