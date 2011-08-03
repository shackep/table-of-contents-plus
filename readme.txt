=== Table of Contents Plus ===
Contributors: conjur3r
Donate link: 
Tags: table of contents, indexes, toc, sitemap, cms, options, list, page listing, category listing
Requires at least: 3.0
Tested up to: 3.3
Stable tag: 1108.1

A powerful yet user friendly plugin that automatically creates a table of contents. Can also output a sitemap listing all pages and categories.


== Description ==

A powerful yet user friendly plugin that automatically creates a context specific index or table of contents for long pages (and custom post types).  More than just a table of contents plugin, this plugin can also output a sitemap listing pages and/or categories.

Built from the ground up and with Wikipedia in mind, the table of contents by default appears before the first heading on a page.  This allows the author to insert lead-in content that may summarise or introduce the rest of the page.  It also uses a unique numbering scheme that doesn't get lost through CSS differences across themes.

This plugin is a great companion for content rich sites such as content management system oriented configurations.  That said, bloggers also have the same benefits when writing long structured articles.

Includes an administration options panel where you can customise settings like display position, define the minimum number of headings before an index is displayed, appearance, etc.  Using shortcodes, you can override default behaviour such as special exclusions on a specific page or even to hide the table of contents altogether.

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

That's it!  The table of contents will appear on pages with at least four or more headings.

You can change the default settings and more under Settings > TOC+

This plugin requires PHP 5.


== Shortcodes ==

* `[toc]` lets you generate the table of contents at the preferred position.  Useful for sites that only require a TOC on a small handful of pages.  Supports the following parameters:
** "label": text, title of the table of contents
** "no_label": true/false, shows or hides the title
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

= 1108.1 =
* Released: 3 August 2011
* Anchor targets (eg anything after #) are now limited to ASCII characters as some mobile user agents do not accept internationalised characters.  This is also a recommendation in the [HTML spec](http://www.w3.org/TR/html4/struct/links.html#h-12.2.1).  A new advanced admin option has been added to specify the default prefix when no characters qualify.
* Make TOC, Pages and Category labels compatible with UTF-8 characters.
* Support ' " \ characters in labels as it was being escaped by WordPress before saving.

= 1108 =
* Released: 1 August 2011
* New: option to hide the title on top of the table of contents.  Thanks to [Andrew](http://dublue.com/plugins/toc/#comment-82) for the suggestion.
* New: option to preserve existing theme specified bullet images for unordered list elements.
* New: option to set the width of the table of contents.  You can select from a number of common widths, or define your own.
* Allow 3 to be set as the minimum number of headings for auto insertion.  The default stays at 4.
* Now accepts heading 1s (h1) within the body of a post, page or custom post type.
* Now creates new span tags for the target rather than the id of the heading.
* Now uses the heading as the anchor target rather than toc_index.
* Adjusted CSS styles for lists to be a little more consistent across themes (eg list-style, margins & paddings).
* Fixed: typo 'heirarchy' should be 'hierarchy'.  Also thanks to Andrew.
* Fixed: addressed an issue while saving on networked installs using sub directories.  Thanks to [Aubrey](http://dublue.com/plugins/toc/#comment-79).
* Fixed: closing of the last list item when deeply nested.

= 1107.1 =
* Released: 10 July 2011
* New: added `[toc]` shortcode to generate the table of contents at the preferred position.  Also useful for sites that only require a TOC on a small handful of pages.
* New: smooth scroll effect added to animate to anchor rather than jump.  It's off by default.
* New: appearance options to match your theme a little bit more.

= 1107 =
* Released: 1 July 2011
* First world release (functional & feature packed)


== Frequently Asked Questions ==

Check out the FAQs / Scenarios at [http://dublue.com/plugins/toc/](http://dublue.com/plugins/toc/)


== Upgrade Notice ==

Update folder with the latest files.  Any previous options will be saved.