=== Help Desk & Knowledgebase Software ===
Contributors: SwiftCloud
Donate link: http://SwiftHelpdesk.com?pr=106
Tags: help desk, helpdesk, knowledgebase
Requires at least: 4.5
Tested up to: 5.2
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Wordpress Help Desk Support Software and Knowledgebase

== Description ==
**Wordpress Help Desk Support Software with Optional Automation in <a href="http://SwiftHelpDesk.com?pr=106" target="_new">http://SwiftHelpDesk.com</a>**

This is a very simple program, currently totally free. We'll split off a paid version soon but will always have a free level.

**To setup in about 5 minutes just...**

1. Go create a form on SwiftCloud (free, signup <a href="http://swiftcloud.ai/#s?site_url=http://swifthelpdesk.com&utm_source=swifthelpdesk.com&utm_medium=signup&utm_campaign=30DayTrial&pr=106&button=TR">here</a>, (then click to Web Forms >> Create New at http://swiftcloud.ai/form/create-form). Click "Get Code Install Form" top right and note the number. Ensure the notification settings are correct on the right side (who you want to get the email). Click SwiftTasks in the far bottom right if you want to also create a ticket for each request.
2. Plug that number into your Wordpress Admin >> Swift Helpdesk >> Settings >> My SwiftCloud.AI help support form is..., and select your thank-you page that someone will see after they submit a ticket. Auto Password Reset is only applicable if you have a membership site running our plugin, in which case we try to auto-detect phrases like "locked out" and "reset", etc. then offer a password reset.
3. On your contact page, drop in the shortcode [swift_helpdesk_support] to instantly generate the nice contact form. NOTE this is probably done for you already via the setup wizard
4. Done! Assuming you clicked SwiftTasks in step 2, any submitted tickets will (in addition to emailing you or whomever you set) appear on http://swiftcloud.ai/task/tasks
5. To add customer satisfaction surveys, install the <a href="https://wordpress.org/plugins/advocate-marketing/">Customer Advocate Marketing</a> plugin first and get it set up, then...
6. Add a marketing sequence directing people to your reviews page.

That's it!

This system was born of necessity and we welcome your ideas to make it better.


This plugin requires a <a href="https://SwiftCloud.AI/support" target="_new">https://SwiftCloud.AI/support</a> (free or paid) for the form to work.

Our number 1 goal for this plugin is to reduce your support time and costs, and improve customer experience.

SwiftHelpDesk.com is free, and charges only for automation and other more advanced features.

We want to create the ultimate wordpress help desk support software system and welcome your feedback at <a href="https://SwiftCloud.AI/support" target="_new">https://SwiftCloud.AI/support</a>

**FEATURES**

1. Knowledgebase Homepage with search
2. Rich categories support with CMS-style controls
3. Support ticket form creation, with ajax self-support to try and get the human to help themselves before opening a ticket.
4. Live chat support (coming soon)
5. Optional integration to reset passwords automatically for membership sites

== Installation ==
You probably know the drill by now, but if not, here's a step by step suggestion.

1. Upload the `Swift Helpdesk` folder to the `/wp-content/plugins/` directory, or better yet, use Wordpress' native installer at Plugins >> Add New >> (search Swift Helpdesk)
2. Activate the plugin through the 'Plugins' menu in WordPress
3. To install a webform, login at <a href="https://SwiftCloud.AI?pr=92" target="_new">https://SwiftCloud.AI</a> (free signup) and click 'new form',
drag and drop fields to create a form, click save, and then remember the number it gives you.
4. Create a page (not post) for your support central, and another to open a support ticket, i.e. 'support' and 'support-request'
5. OR go to appearance >> widgets and drag 'SwiftForm' over into a widget location.

For the various popups, just see the settings area. Note the popup contents get created on <a href="https://SwiftCloud.AI?pr=92" target="_new">https://SwiftCloud.AI</a>, then embedded via the plugin.

== Frequently Asked Questions ==

1. How do I use it?

Easy!

* First, signup at [SwiftCloud](https://SwiftCloud.AI) (free), then when logged in, navigate on the left to WebForms.
* Next, go to [SwiftForm >> New Form](https://SwiftCloud.AI/public/create-form). Drag and drop from the fields on the left, choose what you want to happen after capture, and hit save.
* Next, in Wordpress, create 2 pages: a Support Home page and a Support Request page.
* On Support Home, add this shortcode: [helpdesk_knowledgebase]
* On Support Request, add [helpdesk_form]

That's it.

For more help, see our [SwiftForm Support Section](https://SwiftCloud.AI/support) video training.

== Screenshots ==

1. Support Main Form, shortcode [helpdesk_knowledgebase]
2. Request Form Example, shortcode [helpdesk_form]
3. Call To Action for Inbound Marketing example with marketing bait

== Changelog ==

= 1.3.18 =
- Security updates
- Wordpress compatibility for v5.2

= 1.3.17 =
- Updated SwiftCloud form submission url

= 1.3.16 =
- Add 'Was this helpful' feature

= 1.3.15 =
- Updated SwiftCloud form submission url

= 1.3.14 =
- UI improvements

= 1.3.13 =
- UI improvements

= 1.3.12 =
- Added Captcha to helpdesk form

= 1.3.11 =
- UI improvements

= 1.3.10 =
- UI improvements
- Added Shortcode for FAQ

= 1.3.9 =
- Bug fixing

= 1.3.8 =
- Added GMT Timezone in swift form

= 1.3.7 =
- UI changes

= 1.3.6 =
- UI changes

= 1.3.5 =
- Admin UI changes
- Bug fixing

= 1.3.4 =
- Added quick search for Recent Articles

= 1.3.3 =
- Optimize search results

= 1.3.2 =
- Smarter Keyword Auto-Results
- Added updates and tips
- UI updates

= 1.3.1 =
- Bug fixing.

= 1.3.0 =
- UI changes.

= 1.2.9 =
- UI changes.

= 1.2.8 =
- UI changes.

= 1.2.7 =
- Auto generate pages "Support" and "Thanks Support"

= 1.2.6 =
- UI changes.
- Search result improvement

= 1.2.5 =
- Bug fixing

= 1.2.4 =
- Added search box option and shortcode

= 1.2.3 =
- Inclusion of product id

= 1.2.2 =
- Bug fixing

= 1.2.1 =
- Internal changes

= 1.2 =
- UI changes

= 1.1 =
- Alpha Release
- Bug fixing

= 1.0 =
- Basic Setup

== Translations ==

* English - for now, that's all, but if interested we welcome some help! Contact us for a .pot file.