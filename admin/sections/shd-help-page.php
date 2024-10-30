<?php
/**
 *      Help page
 */
if (!function_exists('shd_use_help_cb')) {

    function shd_use_help_cb() {
        ?>
        <div class="wrap">
            <h2>Swift Helpdesk Use/Help</h2><hr/>
            <div class="inner_content swift-help-setup">
                <div class="swift-help-blue-div">
                    <h2>Setup Instructions are at</h2>
                    <a href="https://SwiftCloud.AI/support/helpdesk" target="_blank">https://SwiftCloud.AI/support/helpdesk</a>
                </div>
                <p><?php _e('We recommend setting up the basics first before adding more complex systems.', 'swift_helpdesk'); ?></p>
                <p><?php _e('Further help can be seen at', 'swift_helpdesk'); ?><br/>
                    <a href="https://SwiftCloud.AI/support/tag/helpdesk" target="_blank">https://SwiftCloud.AI/support/tag/helpdesk</a>
                </p>
                <p><?php _e('A full list of shortcodes can be found at', 'swift_helpdesk'); ?><br/>
                    <a href="https://SwiftCloud.AI/support/wordpress-helpdesk-plugin" target="_blank">https://SwiftCloud.AI/support/wordpress-helpdesk-plugin</a>
                </p>
            </div>
        </div>
        <?php
    }

}
?>
