=== Table of Contents Plus ===
Contributors: conjur3r
Donate link: 
Tags: table of contents, indexes, toc, sitemap, cms, options, list, page listing, category listing
Requires at least: 3.0
Tested up to: 3.2
Stable tag: 1107.1

A powerful yet user friendly plugin that automatically creates a table of contents. Can also output a sitemap listing all pages and categories.


== Description ==

A powerful yet user friendly plugin that automatically creates a context specific index or table of contents index for long pages (and custom post types).  More than just a table of contents plugin, this plugin can also output a sitemap listing pages and/or categories.

Includes an administration options panel where you can customise things like display position, define the minimum number of headings before an index is displayed, appearance, etc.  Using shortcodes, you can override default behaviour such as special exclusions on a specific page or even to hide the table of contents altogether.

This plugin is a great companion for content rich sites such as content management system oriented configurations.  That said, bloggers have the same benefits when writing long structured articles.

Built from the ground up and with Wikipedia in mind, the table of contents by default appears before the first heading on a page.  This allows the author to insert lead-in content that may summarise or introduce the rest of the page.  It also uses a unique numbering scheme that doesn't get lost through CSS differences across themes.

Custom post types are supported, however, auto insertion works only when the_content() has been used by the custom post type. Each post type will appear in the options panel, so enable the ones you want.

If you have questions or suggestions, please place them at [http://dublue.com/plugins/toc/](http://dublue.com/plugins/toc/)


== Screenshots ==

1. An example of the table of contents, positioned at the top and right aligned
2. The main options tab in the administration area
3. The sitemap options tab


== Installation ==

The normal plugin install process applies, that is search for `table of contents plus` from your plugin screen or via the manual method:

1. Upload the `table-of-contents-plus` folder into your `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Customise your options under Settings > TOC+ if needed

Requires PHP 5.

== Shortcodes ==

* `[toc]` lets you generate the table of contents at the preferred position.  Also useful for sites that only require a TOC on a small handful of pages.
* `[no_toc]` allows you to disable the table of contents for the current post, page, or custom post type.
* `[sitemap]` produces a listing of all pages and categories for your site. You can use this on any post, page or even in a text widget.
* `[sitemap_pages]` lets you print out a listing of only pages. The following parameters are accepted:
** "heading": number between 1 and 6, defines which html heading to use
** "label": text, title of the list
** "no_label": true/false, shows or hides the list heading
** "exclude": IDs of the pages or categories you wish to exclude
When parameters are left out, they will fallback to the default settings.
* `[sitemap_categories]` as above but for categories.


== Changelog ==

= 1107.1 (10/July/2011) =
* New: added `[toc]` shortcode to generate the table of contents at the preferred position.  Also useful for sites that only require a TOC on a small handful of pages.
* New: smooth scroll effect added to animate to anchor rather than jump.  It's off by default.
* New: appearance options to match your theme a little bit more.

= 1107 (1/July/2011) =
* First world release (functional & feature packed)


== Frequently Asked Questions ==

None yet.


== Upgrade Notice ==

Update folder with the latest files.  Any previous options will be saved.