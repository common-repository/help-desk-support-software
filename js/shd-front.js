jQuery(document).ready(function () {
    var ajax_url = shd_ajax_obj.ajax_url; //jQuery("#shd-ajax-url").val();
    var enablel_artical = jQuery("#shd-enable-artical-search").val();

    if (jQuery('#SC_fh_timezone').size() > 0) {
        jQuery('#SC_fh_timezone').val(jstz.determine().name());
    }
    if (jQuery('#SC_fh_capturepage').size() > 0) {
        jQuery('#SC_fh_capturepage').val(window.location.origin + window.location.pathname);
    }
    if (jQuery('#SC_fh_language').size() > 0) {
        jQuery('#SC_fh_language').val(window.navigator.userLanguage || window.navigator.language);
    }


    jQuery("#helpdesk_reset").on('click', function () {
        location.reload();
    });

    if (enablel_artical == 1) {
        jQuery("#shd_artical_list").mCustomScrollbar({
            scrollButtons: {enable: true},
        });
    }

    var xhr = null;
    var xhr_tag = null;
    jQuery('#shd_search').keyup(function () {
        var search_key = jQuery.trim(jQuery('#shd_search').val());
        jQuery(".shd-error").remove();
        jQuery("#btn-section-one").fadeIn();
        jQuery(".shd_second_step,#btn-section-two").fadeOut();
        jQuery(".shd_second_content_area").html('');
        jQuery('#shd_tag_list').html("");

        if (enablel_artical == 1) {
            if (search_key.length >= 2) {
                // Find for related articles
                var data = {
                    'action': 'helpdesk_find_artical',
                    'search_key': jQuery("#shd_search").val(),
                };
                xhr = jQuery.ajax({
                    type: "POST",
                    url: ajax_url,
                    data: data,
                    beforeSend: function () {
                        jQuery(".shd_loader").html('<div class="shd-search-loader" style="text-align: center;"><i class="fa fa-spinner fa-pulse fa-lg fa-fw"></i>');
                        jQuery("#shd_artical_list").fadeOut();

                        if (xhr !== null) {
                            xhr.abort();
                        }
                    },
                    success: function (response) {
                        jQuery(".shd_loader").html('');
                        jQuery(".article_search_container").slideDown();
                        jQuery("#field1-container").find(".setp-one-loader").remove();
                        jQuery("#shd_artical_list").fadeIn();
                        jQuery('#shd_artical_list  .mCSB_container').html(response);
                        jQuery("#shd_artical_list").mCustomScrollbar("update");
                    }
                });
            }
        }
    });

    //second step
    jQuery("#shd_second_setp_btn").on("click", function () {
        var search_key = jQuery.trim(jQuery('#shd_search').val());

        jQuery(".shd-error").remove();
        jQuery("#shd_second_setp_btn i").removeClass();
        jQuery("#shd_second_setp_btn i").addClass('fa fa-spinner fa-pulse fa-lg fa-fw');

        if (search_key.length >= 2) {
            // Find for tags
            var data = {
                'action': 'helpdesk_find_tag',
                'search_key': jQuery("#shd_search").val(),
            };
            xhr_tag = jQuery.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                beforeSend: function () {
                    if (xhr_tag !== null) {
                        xhr_tag.abort();
                    }
                },
                success: function (response) {
                    if (response !== '') {
                        jQuery('#shd_tag_list').html("");
                        jQuery('#shd_tag_list').css('margin-top', '25px');
                        jQuery('#shd_tag_list').html(response);
                    }
                }
            });

            // Find for related articles
            var data = {
                'action': 'setp_two_helpdesk_find_artical',
                'search_key': jQuery("#shd_search").val(),
                'limit': 5
            };
            jQuery.post(ajax_url, data, function (response) {
                jQuery("#btn-section-two").fadeIn();
                jQuery("#shd_second_setp_btn i").removeClass();
                jQuery("#shd_second_setp_btn i").addClass('fa fa-paper-plane');

                jQuery(".shd_second_content_area").html('');
                jQuery("#btn-section-one").fadeOut();
                jQuery(".shd_second_content_area").html(response);
                jQuery(".shd_second_step").fadeIn();
                jQuery('html, body').animate({
                    scrollTop: jQuery("#shd_tag_list").offset().top - 85
                }, 1000);
            });

        } else {
            jQuery(".shd-error").remove();
            jQuery("#shd_search-container label").after('<span class="shd-error">Please enter a keyword.</span>');
            jQuery("#shd_second_setp_btn i").removeClass();
            jQuery("#shd_second_setp_btn i").addClass('fa fa-paper-plane');
            jQuery('html, body').animate({
                scrollTop: jQuery("#shd_tag_list").offset().top - 85
            }, 1000);
            return false;
        }
    });

    jQuery("#helpdesk_submit").on("click", function () {
        var err = false;
        jQuery(".shd-error").remove();
        var name = jQuery.trim(jQuery(".scForm #name").val());
        var phone = jQuery.trim(jQuery(".scForm #phone").val());
        var email = jQuery.trim(jQuery(".scForm #email").val());
        var shp_captcha_code = jQuery.trim(jQuery(".scForm #shp_captcha_code").val());

        if (name.length <= 0) {
            jQuery('html, body').animate({
                scrollTop: jQuery(".scForm").offset().top
            }, 1000);
            jQuery(".scForm #name").focus();
            jQuery("#name-container label").after('<span class="shd-error">Name is required.</span>');
            err = true;
            return false;
        }

        if (email.length <= 0) {
            jQuery('html, body').animate({
                scrollTop: jQuery(".scForm").offset().top
            }, 1000);
            jQuery(".scForm #email").focus();
            jQuery("#email-container label").after('<span class="shd-error">Email is required.</span>');
            err = true;
            return false;
        } else if (!ValidateEmail(email)) {
            jQuery('html, body').animate({
                scrollTop: jQuery(".scForm").offset().top
            }, 1000);
            jQuery(".scForm #email").focus();
            jQuery("#email-container label").after('<span class="shd-error">Invalid email address.</span>');
            err = true;
            return false;
        }

        if (jQuery("#help_form_captcha_flag").length > 0 && jQuery("#help_form_captcha_flag").val() == 1) {
            if (shp_captcha_code.length <= 0) {
                jQuery('html, body').animate({
                    scrollTop: jQuery(".scForm").offset().top
                }, 1000);
                jQuery(".scForm #shp_captcha_code").focus();
                jQuery("#shp_captcha_code-container label").after('<span class="shd-error">Code is required.</span>');
                err = true;
                return false;
            } else if (shp_captcha_code.toLowerCase() != 'swiftcloud') {
                jQuery('html, body').animate({
                    scrollTop: jQuery(".scForm").offset().top
                }, 1000);
                jQuery(".scForm #shp_captcha_code").focus();
                jQuery("#shp_captcha_code-container label").after('<span class="shd-error">Please enter correct code.</span>');
                err = true;
                return false;
            }
        }

        if (!err && jQuery('#SC_browser').val() !== "WP Fastest Cache Preload Bot") {
            jQuery("#swift_helpdesk_form").submit();
        }
    });

    // quick search above Recent articles
    jQuery(document).keypress(function (e) {
        if (e.which == 13) {
            jQuery("#quick_search").trigger('click');
        }
    });
    jQuery("#quick_article_search").on('focus', function () {
        jQuery("#quick_search").trigger('click');
    });

    jQuery("#quick_search").click(function () {
        var xhr = null;
        var xhr_tag = null;
        var search_key = jQuery.trim(jQuery('#quick_article_search').val());

        if (search_key.length >= 2) {
            // search for related articles
            var data = {
                'action': 'helpdesk_find_artical',
                'search_key': jQuery("#quick_article_search").val(),
            };
            xhr = jQuery.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                beforeSend: function () {
                    jQuery("#quick_search").html('<i class="fa fa-spinner fa-pulse"></i>');
                    if (xhr !== null) {
                        xhr.abort();
                    }
                },
                success: function (response) {
                    jQuery('#shd_artical_list  .mCSB_container').html(response);
                    jQuery("#shd_artical_list").mCustomScrollbar("update");
                    jQuery("#quick_search").html('<i class="fa fa-search fa-lg"></i>');
                }
            });
        }
    });

    /* FAQ accordian */
    if (jQuery("#swiftcloud-helpdesk-faqs").length > 0) {
        jQuery("#swiftcloud-helpdesk-faqs").smk_Accordion({
            showIcon: true, //boolean
            animation: false, //boolean
            closeAble: true, //boolean
            slideSpeed: 200 //integer, miliseconds
        });
    }

    /* was this helpful? */
    if (jQuery('.shd-vote').length > 0) {
        jQuery('.shd-vote').click(function () {
            var shd_post_id = jQuery(this).attr('data-post-id');
            var vote = jQuery(this).attr('data-vote');
            if (jQuery.cookie('shd_was_this_helpful_' + shd_post_id) === undefined) {
                if (vote == "Down") {
                    jQuery('.shd-wth-vote-down-reason').slideToggle();
                    jQuery(this).addClass('shd-active-vote');
                } else {
                    jQuery.cookie('shd_was_this_helpful_' + shd_post_id, vote);
                    jQuery('.shd-vote').removeClass('shd-active-vote');
                    jQuery(this).addClass('shd-active-vote');

                    var data = {
                        'action': 'helpdesk_save_was_this_helpful',
                        'vote': vote,
                        'shd_post_id': shd_post_id
                    };
                    jQuery.ajax({
                        type: "POST",
                        url: ajax_url,
                        data: data,
                        beforeSend: function () {
                        },
                        success: function (response) {
                            if (response == 'success') {
                                jQuery('.shd-wth-vote-down-reason').slideUp();
                                jQuery('.shd-wth-vote-down-reason').html('<span class="shd-success">Great! Glad we could help this time.</span>').show();
                            } else {
                                jQuery('.shd-wth-vote-down-reason').hide();
                            }
                        }
                    });
                }
            }
        });
    }

    jQuery('.shd-wth-vote-down-reason-btn').on('click', function () {
        var shd_post_id = jQuery(this).attr('data-post-id');
        if (jQuery.cookie('shd_was_this_helpful_' + shd_post_id) === undefined) {
            jQuery.cookie('shd_was_this_helpful_' + shd_post_id, 'Down');
            var shd_down_reason = jQuery.trim(jQuery('.shd-wth-vote-down-reason-textarea').val());
            jQuery('.shd-down-reason-error').remove();
            if (shd_down_reason.length > 0) {
                var data = {
                    'action': 'helpdesk_save_down_reason',
                    'vote': 'Down',
                    'reason': shd_down_reason,
                    'shd_post_id': shd_post_id
                };
                jQuery.ajax({
                    type: "POST",
                    url: ajax_url,
                    data: data,
                    beforeSend: function () {
                        jQuery('.shd-wth-vote-down-reason-btn').html('<i class="fa fa-spinner fa-pulse fa-lg fa-fw"></i> Processing...');
                        jQuery('.shd-wth-vote-down-reason-btn').attr('disabled', 'disabled');
                    },
                    success: function (response) {
                        if (response == 'success') {
                            jQuery('.shd-wth-vote-down-reason').html('<span class="shd-success">Thanks for your feedback!</span>');
                        } else {
                            jQuery('.shd-wth-vote-down-reason-btn').html('Submit');
                            jQuery('.shd-wth-vote-down-reason-btn').removeAttr('disabled');
                            jQuery('.shd-wth-vote-down-reason-textarea').before('<span class="shd-down-reason-error">There was some error while submitting your feedback.</span>');
                        }
                    }
                });
            } else {
                jQuery('.shd-wth-vote-down-reason-textarea').before('<span class="shd-down-reason-error">Required!</span>');
            }
        }
    });

    if (jQuery('.shdAccordion').length > 0) {
        var acc = document.getElementsByClassName("shdAccordion");
        var i;
        for (i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function () {
                this.classList.toggle("active");
                var panel = this.nextElementSibling;
                if (panel.style.maxHeight) {
                    panel.style.maxHeight = null;
                } else {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                }
            });
        }
    }
});
//Email validation
function ValidateEmail(mail)
{
    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail))
    {
        return (true);
    }
    return (false);
}