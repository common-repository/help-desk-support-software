<?php
/*
 *      [swiftcloud_helpdesk_faqs]
 *          [swiftcloud_helpdesk_faq_question question=""]
 *              Answer will go here
 *          [/swiftcloud_helpdesk_faq_question]
 *      [/swiftcloud_helpdesk_faqs]
 *      - This shortcode will show accordion FAQs system.
 */
add_shortcode('swiftcloud_helpdesk_faqs', 'swiftcloud_helpdesk_faqs_callback');

function swiftcloud_helpdesk_faqs_callback($ls_atts) {
    ob_start();
    $hd_faq_output = '';

    return $hd_faq_output;
}