<?php
/**
 *      Setting page
 */
add_action('admin_init', 'restrict_admin_with_redirect');

function restrict_admin_with_redirect() {
    if (isset($_POST['save_help_support']) && wp_verify_nonce($_POST['save_help_support'], 'save_help_support')) {
        $swift_settings['help_form_id'] = sanitize_text_field($_POST['swift_settings']['help_form_id']);
        $swift_settings['help_form_cat'] = sanitize_text_field($_POST['swift_settings']['help_form_cat']);
        $swift_settings['enable_help_show_articles'] = sanitize_text_field($_POST['swift_settings']['enable_help_show_articles']);
        $swift_settings['help_form_enable_auto_search'] = sanitize_text_field($_POST['swift_settings']['help_form_enable_auto_search']);
        $swift_settings['helpdesk_thank_you_url'] = sanitize_text_field($_POST['swift_settings']['helpdesk_thank_you_url']);
        $swift_settings['enable_membership_site_only'] = sanitize_text_field($_POST['swift_settings']['enable_membership_site_only']);
        $swift_settings['help_form_captcha_flag'] = sanitize_text_field($_POST['swift_settings']['help_form_captcha_flag']);
        $swift_settings['help_form_was_this_helpful_flag'] = sanitize_text_field($_POST['swift_settings']['help_form_was_this_helpful_flag']);
        $update = update_option('swift_helpdesk_settings', $swift_settings);

        if ($update) {
            wp_redirect(admin_url("admin.php?page=shd_settings&update=1"));
            exit;
        }
    }
}

if (!function_exists('shd_settings_cb')) {

    function shd_settings_cb() {
        $args = array(
            'sort_order' => 'ASC',
            'sort_column' => 'post_title',
            'hierarchical' => 1,
            'exclude' => '',
            'include' => '',
            'meta_value' => '',
            'child_of' => 0,
            'parent' => -1,
            'offset' => 0,
            'post_type' => 'page',
            'post_status' => 'publish'
        );
        $pages = get_pages($args);
        ?>
        <div class="wrap">
            <div class="inner_content">
                <h2>Swift Helpdesk</h2><hr/>
                <?php
                /* Save settings */
                $swift_settings = get_option('swift_helpdesk_settings');
                if (isset($_GET['update']) && !empty($_GET['update']) && $_GET['update'] == 1) {
                    ?>
                    <div id="message" class="notice notice-success is-dismissible below-h2">
                        <p>Setting updated successfully.</p>
                    </div>
                    <?php
                }
                ?>
                <form method="post" action="" id="frmHelpDesk">
                    <table class="form-table">
                        <tr>
                            <th><label for="help_form_id">My <a href="https://swiftcloud.ai/is/drive/" target="_blank">SwiftCloud.AI help support form</a> is number</label></th>
                            <td><input type="text" id="help_form_id" value="<?php echo (isset($swift_settings['help_form_id'])) ? $swift_settings['help_form_id'] : ""; ?>" class="" name="swift_settings[help_form_id]"/></td>
                        </tr>
                        <tr>
                            <th><label for="helpdesk_thank_you_url">Ticket confirmation URL <span class="dashicons dashicons-editor-help shd-tootltip" title="This is the URL the user will see after submitting a ticket via the helpdesk form."></span></label></th>
                            <!--<td><input type="text" value="<?php echo (!empty($swift_settings['helpdesk_thank_you_url']) ? $swift_settings['helpdesk_thank_you_url'] : home_url() . '/thanks-helpdesk'); ?>" class="regular-text"/></td>-->
                            <td><select id="helpdesk_thank_you_url" name="swift_settings[helpdesk_thank_you_url]" >
                                    <option value="0">--Select Page--</option>
                                    <?php
                                    if ($pages) {
                                        $helpdesk_thank_you_url = (isset($swift_settings['helpdesk_thank_you_url'])) ? $swift_settings['helpdesk_thank_you_url'] : 0;
                                        foreach ($pages as $page) {
                                            ?>
                                            <option <?php selected($helpdesk_thank_you_url, $page->ID); ?> value="<?php echo $page->ID ?>"><?php echo $page->post_title ?></option>
                                            <?php
                                        }//First if
                                    }// First loop
                                    ?>
                                </select></td>

                        </tr>
                        <tr>
                            <th><label for="popup-scroll">My help / support category is</label></th>
                            <td>
                                <?php
                                $cat_args = array(
                                    'name' => 'swift_settings[help_form_cat]',
                                    'show_option_all' => '--Select--',
                                    'show_option_none' => '0',
                                    'option_none_value' => '0',
                                    'selected' => (isset($swift_settings['help_form_cat']) ? $swift_settings['help_form_cat'] : ""),
                                );
                                wp_dropdown_categories($cat_args);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="">Show Popular Articles</label></th>
                            <td>
                                <?php $showArtical = (isset($swift_settings['enable_help_show_articles']) && $swift_settings['enable_help_show_articles'] == 1 ? 'checked="checked"' : ""); ?>
                                <input type="checkbox" value="1" data-ontext="ON" data-offtext="OFF" name="swift_settings[enable_help_show_articles]" id="enable_help_show_articles" class="enable_help_show_articles" <?php echo $showArtical; ?>>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="">Auto Search</label></th>
                            <td>
                                <?php $autoSearch = (isset($swift_settings['help_form_enable_auto_search']) && $swift_settings['help_form_enable_auto_search'] == 1 ? 'checked="checked"' : ""); ?>
                                <input type="checkbox" value="1" data-ontext="ON" data-offtext="OFF" name="swift_settings[help_form_enable_auto_search]" id="help_form_enable_auto_search" class="help_form_enable_auto_search" <?php echo $autoSearch; ?>>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="">Auto Password Reset Offer*<br/><span class="smalltxt">* Membership Sites Only - will trigger a wordpress password reset if user clicks reset button.</span></label>
                            </th>
                            <td>
                                <?php $membershipSiteOnly = (isset($swift_settings['enable_membership_site_only']) && !empty($swift_settings['enable_membership_site_only']) && $swift_settings['enable_membership_site_only'] == 1 ? 'checked="checked"' : ""); ?>
                                <input type="checkbox" value="1" data-ontext="ON" data-offtext="OFF" name="swift_settings[enable_membership_site_only]" id="enable_membership_site_only" class="enable_membership_site_only" <?php echo $membershipSiteOnly; ?>>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="">Enable Captcha</label></th>
                            <td>
                                <?php $captchaFlag = (isset($swift_settings['help_form_captcha_flag']) && $swift_settings['help_form_captcha_flag'] == 1 ? 'checked="checked"' : ""); ?>
                                <input type="checkbox" value="1" data-ontext="ON" data-offtext="OFF" name="swift_settings[help_form_captcha_flag]" id="help_form_captcha_flag" class="help_form_captcha_flag" <?php echo $captchaFlag; ?>>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="">Enable Was this helpful?</label></th>
                            <td>
                                <?php $wasThisHelpfulFlag = (isset($swift_settings['help_form_was_this_helpful_flag']) && $swift_settings['help_form_was_this_helpful_flag'] == 1 ? 'checked="checked"' : ""); ?>
                                <input type="checkbox" value="1" data-ontext="ON" data-offtext="OFF" name="swift_settings[help_form_was_this_helpful_flag]" id="help_form_was_this_helpful_flag" class="help_form_was_this_helpful_flag" <?php echo $wasThisHelpfulFlag; ?>>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php wp_nonce_field('save_help_support', 'save_help_support') ?>
                                <input type="submit" name="submit" value="Save" class="button-orange" />
                            </th>
                        </tr>
                    </table>
                </form>
            </div>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery('.enable_help_show_articles').rcSwitcher();
                    jQuery('.help_form_enable_auto_search').rcSwitcher();
                    jQuery('.enable_membership_site_only').rcSwitcher();
                    jQuery('#help_form_captcha_flag').rcSwitcher();
                    jQuery('#help_form_was_this_helpful_flag').rcSwitcher();

                    jQuery(".helpdeskError").hide();
                    jQuery("#frmHelpDesk").submit(function (e) {
                        jQuery(".helpdeskError").hide();
                        if (jQuery.trim(jQuery("#help_form_id").val()) === '') {
                            jQuery("#frmHelpDesk").before('<div id="" class="error helpdeskError"><p>Form ID is Required to Enable This Function. Please visit <a href="https://SwiftCloud.AI?pr=92">SwiftCloud.AI</a> (free or paid accounts will work) to generate this form.</p></div>');
                            jQuery("#help_form_id").focus();
                            e.preventDefault();
                        }
                    });
                });
            </script>
        </div>
        <?php
    }

}
?>
