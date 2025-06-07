=== GDPR-DSGVO compliant Embeds for Google Maps ===
Contributors: solutionfirst
Donate link: https://www.paypal.com/donate/?hosted_button_id=CUPZTPGSAHNKY
Tags: google maps, dsgvo, gdpr, iframe, map, privacy, datenschutz, datenschutzgrundverordnung, google
Requires at least: 4.9
Tested up to: 6.8
Stable tag: 1.0.2
Requires PHP: 7.4
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Enables GDPR-DSGVO compliant embedding of multiple Google Maps iframes with user consent. Select light, dark or custom designs, add an optional privacy-policy notice, with unlimited maps.

== Description ==
A flexible WordPress plugin that lets you create fully GDPR-DSGVO compliant Google Maps embeds with per-map customization right in the settings.
Visit our live demonstration at [Live Plugin Demo - Solution First](https://plugin-demo.m00dy.org/live-demonstration/ "Live Plugin Demo")

* GDPR-DSGVO compliant: Your Google Maps iframe embedding only loads after consenting via button click. Hence there are no requests made to Google's server beforehand.
* Iframe Input: Paste your Google Maps iframe code and see the shortcode for easy insertion.
* Consent Button: Define your own button text (e.g. Load Google Maps), choose rounded or square styling, and pick background and text colors.
* Design Modes: Select a light or dark overlay or go “custom” to set your own overlay background, button colors and privacy-text colors via the WordPress color picker.
* Size Control: Specify map width and height in % or px (e.g. 100% or 600px).
* Privacy Notice: Toggle a GDPR-DSGVO notice, enter custom info text and link text, and point it to your privacy-policy URL.
* Unlimited Maps: Free tier by default lets you add unlimited embeds. Have fun!

**Manual Installation**
1. Upload the entire `gdpr-dsgvo-compliant-google-maps-embeds` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to **Maps → Add New** to create your first DSGVO-compliant map.

**Support and Feedback**
If you need assistance or have any suggestions, please don’t hesitate to get in touch <wordpress-support@m00dy.org>. We’re happy to help and value your feedback!

**Disclaimer and Imprint**
This plugin only blocks unwanted requests to Google’s servers made through the configured Google Maps iframe until the user explicitly clicks the consent button in the frontend.
This plugin does not protect your website from any other unwanted (server) requests.
We do not offer legal advice — if you’re in doubt, please consult a qualified attorney.
Please find our Imprint here [Solution First Imprint](https://solutionfirst.m00dy.org/impressum.html "Solution First Imprint")

== Screenshots ==
1. **Frontend** example showing the button and privacy link.
2. **Add/Edit map** screen with custom style settings, custom size settings, and custom privacy-option.
3. **Add/Edit map** screen with iframe input, custom button text, custom style settings and custom size settings.
4. **Frontend** example showing the button and privacy link with different style settings.
5. **Frontend** example showing the final Google Map after click on the consent button.
6. **Main admin screen** showing the list of maps.
7. **Add/Edit map** screen with no input.

== Changelog ==
= 1.0.2 =
* Removed the map limit and license option. Now every user can add unlimited maps without any license key.
* Removed call to load_plugin_textdomain() hence it's not needed anymore (only necessary for WordPress Versions < 4.6).
* Code Refactoring (code structure, code comments).
* Adjusted the screenshots (removed some, added new) due to no longer having a license option.

= 1.0.1 =
* Bug Fix Privacy Info Spacing: Spacing between Privacy Info Text and Privacy Info Link.
* Bug Fix Privacy Info Color: If user selects custom Privacy Info Text Color it now also applies to the link not only to the text.
* Bug Fix JavaScript Integration of sandbox flags for iframe embedding: Allowed Pop-ups and Top-Level-Navigation to prevent COOP-Errors while opening "large map" by clicking a link inside the rendered iframe.

= 1.0.0 =
* Initial release: basic GDPR-DSGVO-compliant Google Maps iframe embed with limit of 3 maps with the free license and unlimited maps with the professional license

== Frequently Asked Questions ==
= How do I add a map to a page? =
Place the shortcode `[dsgvo_map id="123"]` - where 123 is the map ID - directly inside your content.

= Where do I find the iframe code? =
Visit Google Maps via website on your PC or laptop and search for your desired location to open the location listing. Click on the **share icon** inside your location listing and go to the embedding tab to copy the full HTML starting with `<iframe src=`.

= Is this plugin free to use or do I need a license? =
Yes, this plugin is free to use and you can add unlimited maps.

= Where can I find more information? =
Visit us at [GDPR-DSGVO compliant Embeds for Google Maps - Solution First](https://solutionfirst.m00dy.org/wp-plugin/ "GDPR-DSGVO compliant Embeds for Google Maps - Solution First").

