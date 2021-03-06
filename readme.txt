=== Affiliate ===
Contributors: itthinx
Donate link: http://www.itthinx.com/plugins/affiliate/
Tags: ads, advertising, affiliate, affiliate marketing, affiliate plugin, affiliate tool, affiliates, amazon, auto, automatic, banner, banners, bucks, contact form, crm, earn money, e-commerce, hover, itthinx, keyword, lead, link, linker, marketing, money, online sale, order, partner, referral, referral links, referrer, shopping cart, sales, site, track, transaction, wordpress, contact form, contact form 7, digital downloads, eshop, jigoshop, magento, paypal, stripe, woocommerce, woothemes, wp e-commerce
Requires at least: 4.0
Tested up to: 4.2
Stable tag: 1.4.0
License: GPLv3

The Affiliate plugin is a toolbox for Affiliate Marketers.

== Description ==

The Affiliate plugin is a toolbox for Affiliate Marketers.

It provides a set of tools that affiliate marketers can use to include marketing resources on their site.

**Current Features**

- Keyword Linker : It's purpose is to define keywords that are substituted automatically with your affiliate links, anywhere they appear in the content of your site.

  For example, you can define a keyword 'dog food' and have it substituted by a link to http://www.example.com/?affiliates=123 anywhere it appears in your site's content.

Additional features that are currently being developed:

- Affiliate Link Index
- Link Converter
- Link Stats

### Documentation and Support ###

- Please refer to the on-screen documentation and the [Documentation](http://docs.itthinx.com/document/affiliate/) pages.
- If you need help or want to ask a question, please leave a comment on the [Affiliate plugin page](http://www.itthinx.com/plugins/affiliate/).

__Feedback__ is welcome!
If you need help, have problems, want to leave feedback or want to provide constructive criticism, please do so here at the [Affiliate](http://www.itthinx.com/plugins/affiliate/) plugin page.

Please try to solve problems there before you rate this plugin or say it doesn't work. There goes a _lot_ of work into providing you with free quality plugins! Please appreciate that and help with your feedback. Thanks!


== Installation ==

Please also refer to the [Documentation](http://docs.itthinx.com/document/affiliate/) pages.

1. Upload or extract the `affiliate` folder to your site's `/wp-content/plugins/` directory. Or you could use the *Add new* option found in the *Plugins* menu in WordPress.  
2. Enable the plugin from the *Plugins* menu in WordPress.
3. A new *Affiliate* menu will appear in WordPress, this is where you manage the plugin settings.

== Frequently Asked Questions ==

= Where do I start? =

The plugin provides a new Affiliate menu when it is activated. Click on the menu in your WordPress Dashboard.

= Where is the documentation? =

Here: [Documentation](http://docs.itthinx.com/document/affiliate/)

= Where can I ask a question? =

Here: [Affiliate plugin page](http://www.itthinx.com/plugins/affiliate/) 

== Screenshots ==

1. Affiliate - main menu.
2. Explaining keywords.
3. List of defined keywords.
4. Editing a keyword.
5. Example text containing keywords.
6. Example text with keywords automatically substituted by links.

== Changelog ==

= 1.4.0 =
* Added the settings section to enable keyword substitution by post type.
* Fixed administrator role is checked before assigning capabilities of the keyword custom post type.
* Added a Settings link to the plugin entry.
* Improved section headings.
* Updated the translation template.
* Corrected the path used to load translations.
* WordPress 4.2 compatibility checked

= 1.3.0 =
* Added help sections and documentation links.
* WordPress 4.1.1 compatible.
* Removed currently inactive Settings link in plugin links.
* Added ABSPATH check to plugin main file.

= 1.2.0 =
* WordPress 4.0 compatible.

= 1.1.3 =
* Improved the Affiliate menu so that the Keywords menu item is shown active
  when adding or editing a keyword.

= 1.1.2 =
* Fixed the edit_form_after_title filter to limit it to the affiliate_keyword post type.

= 1.1.1 =
* Checking if the required Multibyte String PHP extension is enabled.
* Translation template added.

= 1.1.0 =
* Improved the keyword replacement algorithm, using a parser to make correct replacements.
* Added excerpt filter.

= 1.0.1 =
* WordPress 3.9 compatibility checked

= 1.0.0 =
Initial release.

== Upgrade Notice ==

= 1.4.0 =
* This release adds the option to enable keyword substitution by post type (now only enabled for Post and Page by default), fixes a potential conflict with setups that do not have the Administrator role and other minor fixes and improvements.
