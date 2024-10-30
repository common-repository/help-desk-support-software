<?php
/**
 *      Helpdesk shortcodes
 */
/**
 *      Sohrtcode : [swift_searchbox]
 *      Search Form
 */
add_shortcode("swift_searchbox", "shd_search_box_callback");

if (!function_exists('shd_search_box_callback')) {

    function shd_search_box_callback() {
        $output = "";
        $output = '<div class="search_box">
                        <form action="' . home_url("/") . '" method="get" class="form-inline">
                            <div class="input-group">
                                <input type="text" name="s" id="search" placeholder="Search" value="' . get_search_query() . '" class="form-control" />
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-default"><i class="fa fa-search fa-lg"></i></button>
                                </span>
                            </div>
                        </form>
                   </div>';

        return $output;
    }

}


/*
 *      [swiftcloud_lead_scoring name="homebuyer" value="+50"]
 *      - This shortcode set cookie.
 *          - name  = cookie name
 *          - value = cookie value
 */
add_shortcode('swiftcloud_lead_scoring', 'shortcode_shd_lead_scoring');

function shortcode_shd_lead_scoring($ls_atts) {
    $op = '';
    extract(shortcode_atts(array('name' => '', 'value' => ''), $ls_atts));
    $cookie_name = "sc_lead_scoring";
    $cookie_value_arr = array();
    if (!empty($name) && !empty($value)) {
        if (isset($_COOKIE['sc_lead_scoring']) && !empty($_COOKIE['sc_lead_scoring'])) {
            $aa = stripslashes($_COOKIE['sc_lead_scoring']);
            $bb = unserialize($aa);
            $cookie_value_arr[$name] = $value;
            $cc = array_merge($bb, $cookie_value_arr);

            $final_val = serialize($cc);
            setcookie($cookie_name, $final_val, time() + 31556926, '/');
        } else {
            $cookie_value_arr[$name] = $value;

            $final_val = serialize($cookie_value_arr);
            setcookie($cookie_name, $final_val, time() + 31556926, '/');
        }
    }
}

/*
 *      New Shortcode: [swift_helpdesk_support]
 */
add_shortcode("swift_helpdesk_support", "swift_helpdesk_support_callback");
if (!function_exists('swift_helpdesk_support_callback')) {

    function swift_helpdesk_support_callback() {
        ob_start();
        $swift_settings = get_option('swift_helpdesk_settings');

        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if (is_plugin_active('help-desk-support-software/swift_helpdesk.php') && empty($swift_settings['help_form_id'])) {
            echo "<div class='shd_error_notice'><p><i class='fa fa-exclamation-triangle'></i> Heads up! Your form will not display until you add a form ID number in the control panel.</p></div>";
            return;
        }
        wp_enqueue_style('swiftcloud-fontawesome', "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css", '', '', '');
        wp_enqueue_style('shd-mscrollbar', plugins_url('../css/jquery.mCustomScrollbar.css', __FILE__), '', '', '');
        wp_enqueue_script('shd-mscrollbar', plugins_url('../js/jquery.mCustomScrollbar.concat.min.js', __FILE__), array('jquery'), '', true);
        wp_enqueue_script('swift-form-jstz', SWIFTHD__PLUGIN_URL . "js/jstz.min.js", '', '', true);

        $HelpFormId = $swift_settings['help_form_id'];
        $HelpFormCat = $swift_settings['help_form_cat'];
        $HelpFormEnableAutoSearch = $swift_settings['help_form_enable_auto_search'];
        $help_form_captcha_flag = $swift_settings['help_form_captcha_flag'];

        $shd_captcha_arr = array("sc1.jpg", "sc2.jpg", "sc3.jpg", "sc4.jpg");
        $rand_keys = array_rand($shd_captcha_arr, 1);
        ?>
        <div class="helpdesk_support">
            <form id="swift_helpdesk_form" method="post" action="https://swiftcloud.ai/is/drive/formHandlingProcess001">
                <div class="hepldesk-row">
                    <div id="shd_helpdesk">
                        <?php if ($swift_settings['enable_help_show_articles']) { ?>
                            <div class="shd-left-part">
                                <div class="shd_artical_list">
                                    <label class="list_title">Recent Articles<span class="subtitle"></span></label>
                                    <div class="article_search_container">
                                        <div class="input-group">
                                            <label for="quick_article_search" style="display: none;">Quick Search</label>
                                            <input type="text" name="quick_article_search" id="quick_article_search" placeholder="Search" class="form-control" />
                                            <span class="input-group-btn">
                                                <button type="button" id="quick_search" class="btn btn-default"><i class="fa fa-search fa-lg"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="shd_loader"></div>
                                    <div id="shd_artical_list" class="mCustomScrollbar">
                                        <?php query_posts('&post_type=post&posts_per_page=10&orderby=date&order=DESC'); ?>
                                        <?php while (have_posts()) : the_post(); ?>
                                            <?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>
                                            <div class="item">
                                                <a href="<?php echo get_permalink(get_the_ID()); ?>" target="_blank">
                                                    <?php if (has_post_thumbnail() && !empty($featured_img_url)) { ?>
                                                        <div class="item_image">
                                                            <?php the_post_thumbnail(array(80, 80)); ?>
                                                        </div>
                                                    <?php } else { ?>
                                                        <div class="item_image">
                                                            <img src="<?php echo SWIFTHD__PLUGIN_URL."/images/flag.png"; ?>" alt="<?php echo get_the_title(get_the_ID()); ?>" />
                                                        </div>
                                                    <?php } ?>
                                                    <div class="content">
                                                        <h3><?php the_title(); ?></h3>
                                                        <?php shd_custom_excerpt(10, get_the_ID(), true, false); ?>
                                                    </div>
                                                    <div class="clear"></div>
                                                </a>
                                            </div>
                                        <?php endwhile; ?>
                                        <?php wp_reset_query(); ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="<?php echo ($swift_settings['enable_help_show_articles']) ? 'shd-right-part' : 'shd-full-width'; ?>">
                            <label class="list_title">Support Request Form</label>
                            <div class="scForm">
                                <div class="field" id="name-container">
                                    <label for="name">Name</label>
                                    <input name="name" class="" id="name" type="text">
                                </div>
                                <div id="phone-container" class="field">
                                    <label for="phone">Phone</label>
                                    <input name="phone" id="phone" required="required" type="text">
                                </div>
                                <div class="field" id="email-container">
                                    <label for="email">Email Address</label>
                                    <input name="email" id="email" required="required" type="email">
                                </div>
                                <div class="field" id="shd_search-container">
                                    <label for="shd_search">What's Up? How can we help?</label>
                                    <textarea name="extra_hd_details" id="shd_search" rows="5" cols="20"></textarea>
                                </div>
                                <?php if (isset($help_form_captcha_flag) && !empty($help_form_captcha_flag) && $help_form_captcha_flag == 1): ?>
                                    <div class="field" id="shp_captcha_code-container">
                                        <label for="shp_captcha_code">Please enter code below</label>
                                        <div class="shp_captcha_img">
                                            <img src="<?php echo SWIFTHD__PLUGIN_URL . 'images/' . $shd_captcha_arr[$rand_keys]; ?>" alt="captcha" />
                                        </div>
                                        <div class="shp_captcha_field">
                                            <input type="text" name="shp_captcha_code" id="shp_captcha_code" />
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <input type="hidden" name="ip_address" id="ip_address" value="<?php echo $_SERVER['SERVER_ADDR'] ?>">
                                <input type="hidden" name="browser" id="SC_browser" value="<?php echo $_SERVER['HTTP_USER_AGENT'] ?>">
                                <input type="hidden" name="trackingvars" class="trackingvars" id="trackingvars" >
                                <input type="hidden" id="SC_fh_timezone" value="" name="timezone">
                                <input type="hidden" id="SC_fh_language" value="" name="language">
                                <input type="hidden" id="SC_fh_capturepage" value="" name="capturepage">
                                <input type="hidden" value="<?php echo $swift_settings['help_form_id']; ?>" id="formid" name="formid">
                                <input type="hidden" name="vTags" id="vTags" value="#support #helpdesk #website_support">
                                <input type="hidden" name="vThanksRedirect" value="<?php echo get_permalink($swift_settings['helpdesk_thank_you_url']); ?>">
                                <input type="hidden" id="sc_lead_referer" value="" name="sc_lead_referer"/>
                                <input type="hidden" value="817" name="iSubscriber">
                                <input type="hidden" id="sc_referer_qstring" value="" name="sc_referer_qstring"/>
                                <?php
                                if (isset($_COOKIE['sc_lead_scoring']) && !empty($_COOKIE['sc_lead_scoring'])) {
                                    $sc_lead_scoring_cookie = unserialize(stripslashes($_COOKIE['sc_lead_scoring']));
                                    if (!empty($sc_lead_scoring_cookie)) {
                                        foreach ($sc_lead_scoring_cookie as $key => $val) {
                                            echo '<input type="hidden" id="' . $key . '" value="' . $val . '" name="extra_' . $key . '">';
                                        }
                                    }
                                }
                                ?>
                            </div>
                            <div class="shd_btn_section" id="btn-section-one">
                                <button type="button" class="shd-btn-gry">Cancel</button>
                                <!--<button type="button" class="shd-btn-orange" id="shd_second_setp_btn">Open Support Request <i class="fa fa-paper-plane"></i></button>-->
                                <div id="btnContainer" style="display: inline-block"></div>
                                <script type="text/javascript">
                                    var button = document.createElement("button");
                                    button.innerHTML = 'Open Support Request <i class="fa fa-paper-plane"></i>';
                                    var body = document.getElementById("btnContainer");
                                    body.appendChild(button);
                                    button.id = "shd_second_setp_btn";
                                    button.name = "shd_second_setp_btn";
                                    button.className = "shd-btn-orange";
                                    button.value = 'send';
                                    button.type = 'button';
                                </script>
                                <noscript>
                                <p style='color:red;font-size:18px;'>JavaScript must be enabled to submit this form. Please check your browser settings and reload this page to continue.</p>
                                </noscript>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hepldesk-row">
                    <div class="shd_tag_list" id="shd_tag_list"><!--Tag list goes here...--></div>
                </div>
                <div class="hepldesk-row">
                    <div class="shd_second_step" style="display: none;">
                        <div class="shd_second_step_content">
                            <div class="shd_second_content_area"></div>
                        </div>
                    </div>
                    <div class="shd_btn_section" id="btn-section-two" style="display: none;">
                        <button type="reset" id="helpdesk_reset" class="shd-btn-gry"><i class="fa fa-check"></i> Abort Request, I found help</button>
                        <button type="button" id="helpdesk_submit" class="shd-btn-orange"><i class="fa fa-life-ring"></i> Open Support Request</button>
                    </div>
                    <!--<input type="hidden" value="<?php echo admin_url('admin-ajax.php'); ?>" id="shd-ajax-url" name="shd-ajax-url"/>-->
                    <?php $enable_artical_and_search = ($swift_settings['enable_help_show_articles'] && $HelpFormEnableAutoSearch == 1) ? 1 : 0; ?>
                    <input type="hidden" value="<?php echo $enable_artical_and_search; ?>" id="shd-enable-artical-search" name="extra_enable_artical_search"/>
                    <input type="hidden" value="<?php echo (isset($help_form_captcha_flag) && !empty($help_form_captcha_flag) && $help_form_captcha_flag == 1) ? 1 : 0; ?>" id="help_form_captcha_flag" name="help_form_captcha_flag"/>
                </div>
            </form>
        </div>
        <?php
        $sm_helpdesk_output = ob_get_clean();
        return $sm_helpdesk_output;
    }

}


/*
 *      Shortcode: [helpdesk_knowledgebase]
 */
add_shortcode('helpdesk_knowledgebase', 'shd_swift_helpdesk_knowledgebase');

if (!function_exists('shd_swift_helpdesk_knowledgebase')) {

    function shd_swift_helpdesk_knowledgebase() {
        ob_start();
        $swift_settings = get_option('swift_helpdesk_settings');

        wp_enqueue_style('swiftcloud-fontawesome', "//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css", '', '', '');
        wp_enqueue_style('shd-mscrollbar', plugins_url('../css/jquery.mCustomScrollbar.css', __FILE__), '', '', '');
        wp_enqueue_script('shd-mscrollbar', plugins_url('../js/jquery.mCustomScrollbar.concat.min.js', __FILE__), array('jquery'), '', true);
        ?>
        <div id="shd_helpdesk">
            <?php if ($swift_settings['enable_help_show_articles']) { ?>
                <div class="shd-left-part">
                    <div class="shd_tag_list" id="shd_tag_list"><!--Tag list goes here...--></div>
                    <div class="shd_artical_list">
                        <label class="list_title">Recent Articles<span class="subtitle"></span></label>
                        <div id="shd_artical_list" class="mCustomScrollbar">
                            <?php query_posts('&post_type=post&posts_per_page=10&orderby=date&order=DESC'); ?>
                            <?php while (have_posts()) : the_post(); ?>
                                <div class="item">
                                    <a href="<?php echo get_permalink(get_the_ID()); ?>" target="_blank">
                                        <?php if (has_post_thumbnail()) { ?>
                                            <div class="item_image">
                                                <?php the_post_thumbnail(array(80, 80)); ?>
                                            </div>
                                        <?php } else { ?>
                                            <div class="item_image">
                                                <img src="<?php echo SWIFTHD__PLUGIN_URL."/images/flag.png"; ?>" alt="<?php echo get_the_title(get_the_ID()); ?>" />
                                            </div>
                                        <?php } ?>
                                        <div class="content">
                                            <h3><?php the_title(); ?></h3>
                                            <?php shd_custom_excerpt(10, get_the_ID(), TRUE, false); ?>
                                        </div>
                                        <div class="clear"></div>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                            <?php wp_reset_query(); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <?php
        $sm_helpdesk_output = ob_get_clean();
        return $sm_helpdesk_output;
    }

}

/*
 *      swift help desk form shortcode
 *      [helpdesk_form]
 */
add_shortcode('helpdesk_form', 'shd_swift_help_desk_form');
if (!function_exists('shd_swift_help_desk_form')) {

    function shd_swift_help_desk_form($attr) {
        ob_start();

        $swift_settings = get_option('swift_helpdesk_settings');
        $HelpFormId = $swift_settings['help_form_id'];
        $HelpFormCat = $swift_settings['help_form_cat'];
        $HelpFormEnableAutoSearch = $swift_settings['help_form_enable_auto_search'];
        $help_form_captcha_flag = $swift_settings['help_form_captcha_flag'];

        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if (is_plugin_active('help-desk-support-software/swift_helpdesk.php') && empty($swift_settings['help_form_id'])) {
            echo "<div class='shd_error_notice'><p><i class='fa fa-exclamation-triangle'></i> Heads up! Your form will not display until you add a form ID number in the control panel.</p></div>";
            return;
        }
        wp_enqueue_script('swift-form-jstz', SWIFTHD__PLUGIN_URL . "js/jstz.min.js", '', '', true);
        $shd_captcha_arr = array("sc1.jpg", "sc2.jpg", "sc3.jpg", "sc4.jpg");
        $rand_keys = array_rand($shd_captcha_arr, 1);
        ?>
        <div id="shd_helpdesk">
            <div class="<?php echo ($swift_settings['enable_help_show_articles']) ? 'shd-right-part' : 'shd-full-width'; ?>">
                <label class="list_title">Support Request Form</label>
                <form id="swift_helpdesk_form" method="post" action="https://swiftcloud.ai/is/drive/formHandlingProcess001">
                    <div class="scForm">
                        <div class="field" id="name-container">
                            <label for="name">Name</label>
                            <input name="name" class="" id="name" type="text">
                        </div>
                        <div id="phone-container" class="field">
                            <label for="phone">Phone</label>
                            <input name="phone" id="phone" type="text">
                        </div>
                        <div class="field" id="email-container">
                            <label for="email">Email Address</label>
                            <input name="email" id="email" required="required" type="email">
                        </div>
                        <div class="field" id="shd_search-container">
                            <label for="shd_search">What's Up? How can we help?</label>
                            <textarea name="extra_hd_details" id="shd_search" rows="5" cols="20"></textarea>
                        </div>
                        <?php if (isset($help_form_captcha_flag) && !empty($help_form_captcha_flag) && $help_form_captcha_flag == 1): ?>
                            <div class="field" id="shp_captcha_code-container">
                                <label for="shp_captcha_code">Please enter code below</label>
                                <div class="shp_captcha_img">
                                    <img src="<?php echo SWIFTHD__PLUGIN_URL . 'images/' . $shd_captcha_arr[$rand_keys]; ?>" alt="captcha" />
                                </div>
                                <div class="shp_captcha_field">
                                    <input type="text" name="shp_captcha_code" id="shp_captcha_code" />
                                </div>
                            </div>
                        <?php endif; ?>
                        <input type="hidden" name="ip_address" id="ip_address" value="<?php echo $_SERVER['SERVER_ADDR'] ?>">
                        <input type="hidden" name="browser" id="SC_browser" value="<?php echo $_SERVER['HTTP_USER_AGENT'] ?>">
                        <input type="hidden" name="trackingvars" class="trackingvars" id="trackingvars" >
                        <input type="hidden" name="timezone" id="SC_fh_timezone" value="">
                        <input type="hidden" name="language" id="SC_fh_language" value="">
                        <input type="hidden" name="capturepage" id="SC_fh_capturepage" value="">
                        <input type="hidden" name="formid"value="<?php echo $swift_settings['help_form_id']; ?>" id="formid">
                        <input type="hidden" name="vTags" id="vTags" value="#support #helpdesk #website_support">
                        <input type="hidden" name="vThanksRedirect" value="<?php echo get_permalink($swift_settings['helpdesk_thank_you_url']); ?>">
                        <input type="hidden" name="sc_lead_referer" id="sc_lead_referer" value=""/>
                        <input type="hidden" name="iSubscriber" value="817">
                        <input type="hidden" name="sc_referer_qstring" id="sc_referer_qstring" value=""/>
                        <?php
                        if (isset($_COOKIE['sc_lead_scoring']) && !empty($_COOKIE['sc_lead_scoring'])) {
                            $sc_lead_scoring_cookie = unserialize(stripslashes($_COOKIE['sc_lead_scoring']));
                            if (!empty($sc_lead_scoring_cookie)) {
                                foreach ($sc_lead_scoring_cookie as $key => $val) {
                                    echo '<input id="' . $key . '" type="hidden" value="' . $val . '" name="extra_' . $key . '">';
                                }
                            }
                        }
                        ?>
                    </div>
                    <div class="shd_btn_section" id="btn-section-one">
                        <button type="button" class="shd-btn-gry">Cancel</button>
                        <!--<button type="button" class="shd-btn-orange" id="shd_second_setp_btn">Send Form Contents <i class="fa fa-paper-plane"></i></button>-->
                        <div id="btnContainer" style="display: inline-block"></div>
                        <script type="text/javascript">
                            var button = document.createElement("button");
                            button.innerHTML = 'Send Form Contents <i class="fa fa-paper-plane"></i>';
                            var body = document.getElementById("btnContainer");
                            body.appendChild(button);
                            button.id = "shd_second_setp_btn";
                            button.name = "shd_second_setp_btn";
                            button.className = "shd-btn-orange";
                            button.value = 'send';
                            button.type = 'button';
                        </script>
                        <noscript>
                        <p style='color:red;font-size:18px;'>JavaScript must be enabled to submit this form. Please check your browser settings and reload this page to continue.</p>
                        </noscript>
                    </div>
                    <div class="shd_second_step" style="display: none;">
                        <div class="shd_second_step_content">
                            <div class="shd_second_content_area"></div>
                        </div>
                    </div>
                    <div class="shd_btn_section" id="btn-section-two" style="display: none;">
                        <button type="reset" id="helpdesk_reset" class="shd-btn-gry"><i class="fa fa-check"></i> Abort Request, I found help</button>
                        <button type="submit" id="helpdesk_submit" class="shd-btn-orange"><i class="fa fa-life-ring"></i> Open Support Request</button>
                    </div>
                </form>
                <input type="hidden" value="<?php echo admin_url('admin-ajax.php'); ?>" id="shd-ajax-url" name="shd-ajax-url"/>
                <input type="hidden" value="<?php echo ($swift_settings['enable_help_show_articles'] && $HelpFormEnableAutoSearch == 1) ? 1 : 0; ?>" id="shd-enable-artical-search" name="shd-ajax-url"/>
                <input type="hidden" value="<?php echo (isset($help_form_captcha_flag) && !empty($help_form_captcha_flag) && $help_form_captcha_flag == 1) ? 1 : 0; ?>" id="help_form_captcha_flag" name="help_form_captcha_flag"/>
            </div>
        </div>
        <?php
        $sm_helpdesk_output = ob_get_clean();
        return $sm_helpdesk_output;
    }

}


/*
 *      [swiftcloud_helpdesk_faq_inline title="FAQ Title" menu=""]
 *      - This shortcode will show FAQs system based on menu.
 *      - title  = FAQ Title
 *      - menu = Menu Id
 */
add_shortcode('swiftcloud_helpdesk_faq_inline', 'swiftcloud_helpdesk_faq_inline_callback');

function swiftcloud_helpdesk_faq_inline_callback($ls_atts) {
    ob_start();
    extract(shortcode_atts(array('title' => '', 'menu' => ''), $ls_atts));
    wp_enqueue_script('shd-accordion', SWIFTHD__PLUGIN_URL.'/js/shd-accordion.js', array('jquery'), '', true);
    $hd_faq_str = '';

    if (isset($menu) && !empty($menu)) {
        $hd_faq_menu = wp_get_nav_menu_items($menu);
        if (isset($hd_faq_menu) && !empty($hd_faq_menu)) {

            wp_enqueue_style('shd-faq', plugins_url('../css/shd-accordion.css', __FILE__), '', '', '');
            wp_enqueue_script('shd-faq', plugins_url('../js/shd-accordion.js', __FILE__), array('jquery'), '', true);

            $hd_faq_str = (isset($title) && !empty($title)) ? '<h2>' . $title . '</h2>' : '';
            foreach ($hd_faq_menu as $hd_faq) {
                if (isset($hd_faq->object_id) && !empty($hd_faq->object_id)) {
                    $faq_info = get_post($hd_faq->object_id);
                    if ($faq_info) {
                        $hd_faq_str .= '<div class="accordion_in">';
                        $hd_faq_str .= '<div class="acc_head">' . $hd_faq->title . '</div>';
                        $hd_faq_str .= '<div class="acc_content">';
                        $hd_faq_str .= nl2br(shd_custom_excerpt(200, $faq_info->ID, false, true));
                        $hd_faq_str .= '<a href="' . get_permalink($faq_info->ID) . '">...More...</a>';
                        $hd_faq_str .= '</div>';
                        $hd_faq_str .= '</div>';
                    }
                }
            }
        }
    }
    $hd_faq_output = '<div class="swift-helpdesk-panel-group" id="swiftcloud-helpdesk-faqs">' . $hd_faq_str . '</div>';
    return $hd_faq_output;
}
?>