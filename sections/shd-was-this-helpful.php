<?php
$swift_settings = get_option('swift_helpdesk_settings');
if (isset($swift_settings['help_form_was_this_helpful_flag']) && $swift_settings['help_form_was_this_helpful_flag'] == 1) {
    add_filter('the_content', 'shd_the_content_filter');

    function shd_the_content_filter($content) {
        global $post;
        $shd_pID = get_the_ID();
        wp_enqueue_script('shd-jquery-cookie', plugins_url('../js/jquery.cookie.js', __FILE__), array('jquery'), '', true);
        $shd_vote_cookie = (isset($_COOKIE['shd_was_this_helpful_' . $shd_pID]) && !empty($_COOKIE['shd_was_this_helpful_' . $shd_pID])) ? $_COOKIE['shd_was_this_helpful_' . $shd_pID] : "";
        if (is_single() && $post->post_type=='post') {
            $content .= '<div class="shd-was-this-helpful-container">';
            $content .= '<div class="shd-wth-vote-controls">';
            $content .= '<strong>Was this article helpful?</strong>';
            $content .= '<a href="javascript:;" class="shd-vote shd-vote-up ' . ($shd_vote_cookie == 'Up' ? "shd-active-vote" : "") . '" data-post-id="' . $shd_pID . '" data-vote="Up">Yes</a>';
            $content .= '<a href="javascript:;" class="shd-vote shd-vote-down ' . ($shd_vote_cookie == 'Down' ? "shd-active-vote" : "") . '" data-post-id="' . $shd_pID . '" data-vote="Down">No</a>';
            $content .= '</div>';
            $content .= '<div class="shd-wth-vote-down-reason">';
            $content .= 'Ah, sorry to hear this. We\'ll look into updating this item. <br>What could we do to improve this?<br />';
            $content .= '';
            $content .= '<textarea class="shd-wth-vote-down-reason-textarea"></textarea>';
            $content .= '<button type="button" class="shd-wth-vote-down-reason-btn" data-post-id="' . $shd_pID . '"><i class="fa fa-check"></i> Send Form Contents <i class="fa fa-paper-plane"></i></button>';
            $content .= '</div>';
            $content .= '</div>';
        }
        return $content;
    }

    // add meta box for was this helpful
    add_action('add_meta_boxes', 'shd_was_this_helpful_metabox');

    function shd_was_this_helpful_metabox() {
        add_meta_box('shd_was_this_helpful', 'HelpDesk - Was this helpful?', 'shd_was_this_helpful_callback', 'post', 'normal', 'default');
    }

    function shd_was_this_helpful_callback($post) {
        $post_id = $post->ID;
        $vote_up = get_post_meta($post_id, 'shd_vote_up', true);
        $vote_down = get_post_meta($post_id, 'shd_vote_down', true);
        $reason_arr = get_post_meta($post_id, 'shd_vote_down_reason', true);
        $reason_arr = unserialize($reason_arr);
        ?>
        <table class="form-table">
            <tr>
                <th><label>Up vote:  </label></th>
                <td><?php echo $vote_up; ?></td>
            </tr>
            <tr>
                <th><label>Down vote:</label></th>
                <td><?php echo $vote_down; ?></td>
            </tr>
            <tr>
                <td colspan="2" class="p-0">
                    <strong>Feedback:</strong><br />
                    <?php
                    if (isset($reason_arr) && !empty($reason_arr)) {
                        echo '<ul class="shd_reason_list">';
                        foreach ($reason_arr as $reason) {
                            echo '<li>' . stripslashes($reason) . '</li>';
                        }
                        echo '</ul>';
                    }
                    ?>
                </td>
            </tr>
        </table>
        <?php
    }

}

// save was this helpul vote
add_action('wp_ajax_helpdesk_save_was_this_helpful', 'helpdesk_save_was_this_helpful_callback');
add_action('wp_ajax_nopriv_helpdesk_save_was_this_helpful', 'helpdesk_save_was_this_helpful_callback');
if (!function_exists('helpdesk_save_was_this_helpful_callback')) {

    function helpdesk_save_was_this_helpful_callback() {
        $post_id = esc_attr($_POST['shd_post_id']);
        $vote = esc_attr($_POST['vote']);
        if (isset($post_id) && get_post($post_id)) {
            if ($vote == 'Up') {
                $vote = get_post_meta($post_id, 'shd_vote_up', true);
                $vote = (int) $vote + 1;
                update_post_meta($post_id, 'shd_vote_up', $vote);
            } else {
                $vote = get_post_meta($post_id, 'shd_vote_down', true);
                $vote = (int) $vote + 1;
                update_post_meta($post_id, 'shd_vote_down', $vote);
            }
        }
        echo 'success';
        wp_die();
    }

}

// save down reason
add_action('wp_ajax_helpdesk_save_down_reason', 'helpdesk_save_down_reason_callback');
add_action('wp_ajax_nopriv_helpdesk_save_down_reason', 'helpdesk_save_down_reason_callback');
if (!function_exists('helpdesk_save_down_reason_callback')) {

    function helpdesk_save_down_reason_callback() {
        $post_id = esc_attr($_POST['shd_post_id']);
        $reason = esc_attr($_POST['reason']);
        $vote = esc_attr($_POST['vote']);
        if (isset($post_id) && get_post($post_id)) {
            if ($vote == 'Down') {
                $vote = get_post_meta($post_id, 'shd_vote_down', true);
                $vote = (int) $vote + 1;
                update_post_meta($post_id, 'shd_vote_down', $vote);
            }

            $reason_arr = get_post_meta($post_id, 'shd_vote_down_reason', true);
            $reason_arr = unserialize($reason_arr);
            $reason_arr[] = $reason;
            $reason = serialize($reason_arr);
            update_post_meta($post_id, 'shd_vote_down_reason', esc_sql($reason));
        }
        echo 'success';
        wp_die();
    }

}