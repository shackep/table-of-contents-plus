<?php
/*
Plugin Name:	Table of Contents Plus
Plugin URI: 	http://dublue.com/plugins/toc/
Description: 	A powerful yet user friendly plugin that automatically creates a table of contents. Can also output a sitemap listing all pages and categories.
Author: 		Michael Tran
Author URI: 	http://dublue.com/
Version: 		1107
License:		GPL2
*/

/*  Copyright 2011  Michael Tran  (michael@dublue.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
FOR CONSIDERATION:
- shortcode (custom position)
- support headings already with an id
- back to top links
- sitemap
	- exclude pages/categories
	- support other taxonomies
- advanced options
	- width
	- colour variations
	- jquery smooth scroll
	- highlight target css
*/

 
define( 'TOC_ANCHOR_PREFIX', 'toc_index_' );
define( 'TOC_POSITION_BEFORE_FIRST_HEADING', 1 );
define( 'TOC_POSITION_TOP', 2 );
define( 'TOC_POSITION_BOTTOM', 3 );
define( 'TOC_MIN_START', 4 );
define( 'TOC_MAX_START', 10 );
define( 'TOC_WRAPPING_NONE', 0 );
define( 'TOC_WRAPPING_LEFT', 1 );
define( 'TOC_WRAPPING_RIGHT', 2 );


if ( !class_exists( 'toc' ) ) :
	class toc {
		
		private $path;		// eg /wp-content/plugins/toc
		private $options;
		private $show_toc;	// allows to override the display (eg through [no_toc] shortcode)
		private $exclude_post_types;
		
		function __construct()
		{
			$this->path = dirname( WP_PLUGIN_URL . '/' . plugin_basename( __FILE__ ) );
			$this->show_toc = true;
			$this->exclude_post_types = array( 'attachment', 'revision', 'nav_menu_item', 'safecss' );
		
			// get options
			$defaults = array(		// default options
				'position' => TOC_POSITION_BEFORE_FIRST_HEADING,
				'start' => TOC_MIN_START,
				'heading_text' => 'Contents',
				'auto_insert_post_types' => array('page'),
				'show_heirarchy' => true,
				'ordered_list' => true,
				'wrapping' => TOC_WRAPPING_NONE,
				'sitemap_show_page_listing' => true,
				'sitemap_show_category_listing' => true,
				'sitemap_heading_type' => 3,
				'sitemap_pages' => 'Pages',
				'sitemap_categories' => 'Categories'
			);
			$options = get_option( 'toc-options', $defaults );
			$this->options = wp_parse_args( $options, $defaults );
			
			add_action( 'init', array(&$this, 'init') );
			add_action( 'wp_print_styles', array(&$this, 'public_styles') );
			add_action( 'admin_init', array(&$this, 'admin_init') );
			add_action( 'admin_menu', array(&$this, 'admin_menu') );
			
			add_filter( 'the_content', array(&$this, 'the_content'), 11 );	// run after shortcodes are interpretted (level 10)
			add_filter( 'plugin_action_links', array(&$this, 'plugin_action_links'), 10, 2 );
			add_filter( 'widget_text', 'do_shortcode' );
			
			add_shortcode( 'no_toc', array(&$this, 'shortcode_no_toc') );
			add_shortcode( 'sitemap', array(&$this, 'shortcode_sitemap') );
			add_shortcode( 'sitemap_pages', array(&$this, 'shortcode_sitemap_pages') );
			add_shortcode( 'sitemap_categories', array(&$this, 'shortcode_sitemap_categories') );
		}
		
		
		function __destruct()
		{
		}
		
		
		function plugin_action_links( $links, $file )
		{
			if ( $file == "toc/" . basename(__FILE__) ) {
				$settings_link = '<a href="options-general.php?page=toc">' . __('Settings') . '</a>';
				$links = array_merge( array( $settings_link ), $links );
			}
			return $links;
		}
		
		
		function shortcode_no_toc( $atts )
		{
			$this->show_toc = false;

			return;
		}
		
		
		function shortcode_sitemap( $atts )
		{
			$html = '';
			
			// only do the following if enabled
			if ( $this->options['sitemap_show_page_listing'] || $this->options['sitemap_show_category_listing'] ) {
				$html = '<div class="toc_sitemap">';
				if ( $this->options['sitemap_show_page_listing'] )
					$html .=
						'<h' . $this->options['sitemap_heading_type'] . ' class="toc_sitemap_pages">' . $this->options['sitemap_pages'] . '</h' . $this->options['sitemap_heading_type'] . '>' .
						'<ul class="toc_sitemap_pages_list">' .
							wp_list_pages( array('title_li' => '', 'echo' => false ) ) .
						'</ul>'
					;
				if ( $this->options['sitemap_show_category_listing'] )
					$html .=
						'<h' . $this->options['sitemap_heading_type'] . ' class="toc_sitemap_categories">' . $this->options['sitemap_categories'] . '</h' . $this->options['sitemap_heading_type'] . '>' .
						'<ul class="toc_sitemap_categories_list">' .
							wp_list_categories( array( 'title_li' => '', 'echo' => false ) ) .
						'</ul>'
					;
				$html .= '</div>';
			}
			
			return $html;
		}
		
		
		function shortcode_sitemap_pages( $atts )
		{
			extract( shortcode_atts( array(
				'heading' => $this->options['sitemap_heading_type'],
				'label' => $this->options['sitemap_pages'],
				'no_label' => false,
				'exclude' => ''
				), $atts )
			);
			
			if ( $heading < 1 || $heading > 6 )		// h1 to h6 are valid
				$heading = $this->options['sitemap_heading_type'];
			
			$html = '<div class="toc_sitemap">';
			if ( !$no_label ) $html .= '<h' . $heading . ' class="toc_sitemap_pages">' . $label . '</h' . $heading . '>';
			$html .=
					'<ul class="toc_sitemap_pages_list">' .
						wp_list_pages( array('title_li' => '', 'echo' => false, 'exclude' => $exclude ) ) .
					'</ul>' .
				'</div>'
			;
			
			return $html;
		}
		
		
		function shortcode_sitemap_categories( $atts )
		{
			extract( shortcode_atts( array(
				'heading' => $this->options['sitemap_heading_type'],
				'label' => $this->options['sitemap_pages'],
				'no_label' => false,
				'exclude' => ''
				), $atts )
			);
			
			if ( $heading < 1 || $heading > 6 )		// h1 to h6 are valid
				$heading = $this->options['sitemap_heading_type'];
			
			$html = '<div class="toc_sitemap">';
			if ( !$no_label ) $html .= '<h' . $heading . ' class="toc_sitemap_categories">' . $label . '</h' . $heading . '>';
			$html .=
					'<ul class="toc_sitemap_categories_list">' .
						wp_list_categories( array('title_li' => '', 'echo' => false, 'exclude' => $exclude ) ) .
					'</ul>' .
				'</div>'
			;
			
			return $html;
		}
		
		
		function init()
		{
			wp_register_style( 'toc-screen', $this->path . '/screen.css' );
			wp_register_script( 'toc-front', $this->path . '/front.js' );
			
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'toc-front' );
		}
		
		
		function admin_init()
		{
			wp_register_script( 'toc_admin_script', $this->path . '/admin.js' );
			wp_register_style( 'toc_admin_style', $this->path . '/admin.css' );
		}
		
		
		function admin_menu()
		{
			$page = add_submenu_page(
				'options-general.php', 
				__('TOC') . '+', 
				__('TOC') . '+', 
				'manage_options', 
				'toc', 
				array(&$this, 'admin_options')
			);
			
			add_action( 'admin_print_styles-' . $page, array(&$this, 'admin_options_head') );
		}
		
		
		/**
		 * Load needed scripts and styles only on the toc administration interface.
		 */
		function admin_options_head()
		{
			wp_enqueue_style( 'farbtastic' );
			wp_enqueue_script( 'farbtastic' );
			wp_enqueue_script ( 'jquery' );
			wp_enqueue_script( 'toc_admin_script' );
			wp_enqueue_style( 'toc_admin_style' );
		}
		
		
		private function save_admin_options()
		{
			// security check
			if ( !wp_verify_nonce( $_POST['toc-admin-options'], plugin_basename(__FILE__) ) )
				return false;

			// require an administrator level to save
			if ( !current_user_can( 'manage_options', $post_id ) )
				return false;

			$this->options = array(
				'position' => intval($_POST['position']),
				'start' => intval($_POST['start']),
				'heading_text' => trim($_POST['heading_text']),
				'auto_insert_post_types' => (array)$_POST['auto_insert_post_types'],
				'show_heirarchy' => ($_POST['show_heirarchy']) ? true : false,
				'ordered_list' => ($_POST['ordered_list']) ? true : false,
				'wrapping' => intval($_POST['wrapping']),
				'sitemap_show_page_listing' => ($_POST['sitemap_show_page_listing']) ? true : false,
				'sitemap_show_category_listing' => ($_POST['sitemap_show_category_listing']) ? true : false,
				'sitemap_heading_type' => intval($_POST['sitemap_heading_type']),
				'sitemap_pages' => trim($_POST['sitemap_pages']),
				'sitemap_categories' => trim($_POST['sitemap_categories'])
			);
			
			// update_option will return false if no changes were made
			update_option( 'toc-options', $this->options );
			
			return true;
		}
		
		
		function admin_options() 
		{
			$msg = '';
		
			if ( isset( $_GET['update'] ) ) {
				if ( $this->save_admin_options() )
					$msg = "<div id='message' class='updated fade'><p>" . __('Options saved.') . "</p></div>";
				else
					$msg = "<div id='message' class='error fade'><p>" . __('Save failed.') . "</p></div>";
			}

?>
<div id='toc' class='wrap'>
<div id="icon-options-general" class="icon32"><br /></div>
<h2><?php _e('Table of Contents', 'toc+'); ?>+</h2>
<?php echo $msg; ?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?page=' . htmlentities($_GET['page'] . '&update'); ?>">
<?php wp_nonce_field( plugin_basename(__FILE__), 'toc-admin-options' ); ?>

<ul id="tabbed-nav">
	<li><a href="#tab1">Main Options</a></li>
	<li><a href="#tab2">Sitemap</a></li>
	<li><a href="#tab3">Help</a></li>
</ul>
<div class="tab_container">
	<div id="tab1" class="tab_content">
  
<table class="form-table">
<tbody>
<tr>
	<th><label for="position"><?php _e('Position', 'toc+'); ?></label></th>
	<td>
		<select name="position" id="position">
			<option value="<?php echo TOC_POSITION_BEFORE_FIRST_HEADING; ?>"<?php if ( TOC_POSITION_BEFORE_FIRST_HEADING == $this->options['position'] ) echo ' selected="selected"'; ?>>Before first heading (default)</option>
			<option value="<?php echo TOC_POSITION_TOP; ?>"<?php if ( TOC_POSITION_TOP == $this->options['position'] ) echo ' selected="selected"'; ?>>Top</option>
			<option value="<?php echo TOC_POSITION_BOTTOM; ?>"<?php if ( TOC_POSITION_BOTTOM == $this->options['position'] ) echo ' selected="selected"'; ?>>Bottom</option>
		</select>
	</td>
</tr>
<tr>
	<th><label for="start"><?php _e('Show when', 'toc+'); ?></label></th>
	<td>
		<select name="start" id="start">
<?php
			for ($i = TOC_MIN_START; $i <= TOC_MAX_START; $i++) {
				echo '<option value="' . $i . '"';
				if ( $i == $this->options['start'] ) echo ' selected="selected"';
				echo '>' . $i . '</option>' . "\n";
			}
?>
		</select> <?php _e('or more headings are present', 'toc+'); ?>
	</td>
</tr>
<tr>
	<th><label for="heading_text"><?php _e('Heading text', 'toc+'); ?></label></th>
	<td><input type="text" class="regular-text" value="<?php echo htmlentities($this->options['heading_text']); ?>" id="heading_text" name="heading_text" />
		<span class="description"><?php _e('Eg: Contents, Table of Contents, Page Contents', 'toc+'); ?></span>
	</td>
</tr>
<tr>
	<th><?php _e('Auto insert for the following post types', 'toc+'); ?></th>
	<td><?php
			foreach (get_post_types() as $post_type) {
				// make sure the post type isn't on the exclusion list
				if ( !in_array($post_type, $this->exclude_post_types) ) {
					echo '<input type="checkbox" value="' . $post_type . '" id="auto_insert_post_types_' . $post_type .'" name="auto_insert_post_types[]"';
					if ( in_array($post_type, $this->options['auto_insert_post_types']) ) echo ' checked="checked"';
					echo ' /><label for="auto_insert_post_types_' . $post_type .'"> ' . $post_type . '</label><br />';
				}
			}
?>
</tr>
<tr>
	<th><label for="show_heirarchy"><?php _e('Show heirarchy', 'toc+'); ?></label></th>
	<td><input type="checkbox" value="1" id="show_heirarchy" name="show_heirarchy"<?php if ( $this->options['show_heirarchy'] ) echo ' checked="checked"'; ?> /></td>
</tr>
<tr>
	<th><label for="ordered_list"><?php _e('Number list items', 'toc+'); ?></label></th>
	<td><input type="checkbox" value="1" id="ordered_list" name="ordered_list"<?php if ( $this->options['ordered_list'] ) echo ' checked="checked"'; ?> /></td>
</tr>
<tr>
	<th><label for="wrapping"><?php _e('Wrapping', 'toc+'); ?></label></td>
	<td>
		<select name="wrapping" id="wrapping">
			<option value="<?php echo TOC_WRAPPING_NONE; ?>"<?php if ( TOC_WRAPPING_NONE == $this->options['wrapping'] ) echo ' selected="selected"'; ?>>None (default)</option>
			<option value="<?php echo TOC_WRAPPING_LEFT; ?>"<?php if ( TOC_WRAPPING_LEFT == $this->options['wrapping'] ) echo ' selected="selected"'; ?>>Left</option>
			<option value="<?php echo TOC_WRAPPING_RIGHT; ?>"<?php if ( TOC_WRAPPING_RIGHT == $this->options['wrapping'] ) echo ' selected="selected"'; ?>>Right</option>
		</select>
	</td>
</tr>
</tbody>
</table>
<!--
<h3>Appearance</h3>
Background Colour
<input type="text" name="background_colour" id="background_colour" value="#00ffff" /><div id="background_colour_wheel"></div>
-->

	</div>
	<div id="tab2" class="tab_content">
	

<h3 class="title"><?php _e('Sitemap', 'toc+'); ?></h3>
<p><?php _e('At its simplest, placing', 'toc+'); ?> <code>[sitemap]</code> <?php _e('into a page will automatically create a sitemap of all pages and categories. This also works in a text widget.', 'toc+'); ?></p>
<table class="form-table">
<tbody>
<tr>
	<th><label for="sitemap_show_page_listing"><?php _e('Show page listing', 'toc+'); ?></label></th>
	<td><input type="checkbox" value="1" id="sitemap_show_page_listing" name="sitemap_show_page_listing"<?php if ( $this->options['sitemap_show_page_listing'] ) echo ' checked="checked"'; ?> /></td>
</tr>
<tr>
	<th><label for="sitemap_show_category_listing"><?php _e('Show category listing', 'toc+'); ?></label></th>
	<td><input type="checkbox" value="1" id="sitemap_show_category_listing" name="sitemap_show_category_listing"<?php if ( $this->options['sitemap_show_category_listing'] ) echo ' checked="checked"'; ?> /></td>
</tr>
<tr>
	<th><label for="sitemap_heading_type"><?php _e('Heading type', 'toc+'); ?></label></th>
	<td><label for="sitemap_heading_type"><?php _e('Use', 'toc+'); ?> h</label><select name="sitemap_heading_type" id="sitemap_heading_type">
<?php
			// h1 to h6
			for ($i = 1; $i <= 6; $i++) {
				echo '<option value="' . $i . '"';
				if ( $i == $this->options['sitemap_heading_type'] ) echo ' selected="selected"';
				echo '>' . $i . '</option>' . "\n";
			}
?>
		</select> <?php _e('to print out the titles', 'toc+'); ?>
	</td>
</tr>
<tr>
	<th><label for="sitemap_pages"><?php _e('Pages label', 'toc+'); ?></label></th>
	<td><input type="text" class="regular-text" value="<?php echo htmlentities($this->options['sitemap_pages']); ?>" id="sitemap_pages" name="sitemap_pages" />
		<span class="description"><?php _e('Eg: Pages, Page List', 'toc+'); ?></span>
	</td>
</tr>
<tr>
	<th><label for="sitemap_categories"><?php _e('Categories label', 'toc+'); ?></label></th>
	<td><input type="text" class="regular-text" value="<?php echo htmlentities($this->options['sitemap_categories']); ?>" id="sitemap_categories" name="sitemap_categories" />
		<span class="description"><?php _e('Eg: Categories, Category List', 'toc+'); ?></span>
	</td>
</tr>
</tbody>
</table>

<h3>Advanced usage <span class="show_hide">(<a href="#sitemap_advanced_usage">show</a>)</span></h3>
<div id="sitemap_advanced_usage">
	<p><code>[sitemap_pages]</code> lets you print out a listing of only pages. Similarly, <code>[sitemap_categories]</code> can be used to print out a category listing. They both can accept the following parameters for further customisation:</p>
	<ul>
		<li><strong>heading</strong>: number between 1 and 6, defines which html heading to use</li>
		<li><strong>label</strong>: text, title of the list</li>
		<li><strong>no_label</strong>: true/false, shows or hides the list heading</li>
		<li><strong>exclude</strong>: IDs of the pages or categories you wish to exclude</li>
	</ul>
	<p>When parameters are left out, they will fallback to the default settings.</p>
	<p>Examples</p>
	<ol>
		<li><code>[sitemap_categories no_label="true"]</code> hides the heading from a category listing</li>
		<li><code>[sitemap_pages heading="6" label="This is an awesome listing" exclude="1,15"]</code> Uses h6 to display <em>This is an awesome listing</em> on a page listing excluding pages with IDs 1 and 15.</li>
	</ol>
</div>


	</div>
	<div id="tab3" class="tab_content">

<h3>How do I stop the table of contents from appearing on a single page?</h3>
<p>Place the following <code>[no_toc]</code> anywhere on the page to suppress the table of contents. This is known as a shortcode and works for posts and custom post types that make use of the_content().</p>

<h3>I've set wrapping to left or right but the headings don't wrap around the table of contents</h3>
<p>This normally occurs when there is a CSS clear directive in or around the heading. This directive tells the user agent to reset the previous wrapping specifications. You can adjust your theme's CSS or try moving the table of contents position to the top of the page. If you didn't build your theme, I'd highly suggest you try the <a href="http://wordpress.org/extend/plugins/safecss/">Custom CSS plugin</a> if you wish to make CSS changes.</p>

<h3>What's with the version numbers?</h3>
<p>I like Ubuntu, especially the server product and highly recommend it for Linux deployments. I also like their versioning scheme and have adopted it. All versions are in a YYMM format (year month) of when the release was made.</p>

<h3>I have another question...</h3>
<p>Visit the <a href="http://dublue.com/plugins/toc/">plugin homepage</a> to ask your question - who knows, maybe your question has already been answered. I'd really like to hear your suggestions if you have any.</p>

	</div>
</div>
	

<p class="submit"><input type="submit" name="submit" class="button-primary" value="<?php _e('Update Options', 'toc+'); ?>" /></p>
</form>
</div>
<?php
		}
		
		
		function public_styles()
		{
			wp_enqueue_style("toc-screen");
		}
		
		
		private function build_heirarchy( &$matches )
		{
			$current_depth = 100;	// headings can't be larger than h6 but 100 as a default to be sure
			$html = '';
			$numbered_items = array();
			$numbered_items_min = null;
			
			// find the minimum heading to establish our baseline
			for ($i = 0; $i < count($matches); $i++) {
				if ( $current_depth > $matches[$i][2] )
					$current_depth = (int)$matches[$i][2];
			}
			
			$numbered_items[$current_depth] = 0;
			$numbered_items_min = $current_depth;

			for ($i = 0; $i < count($matches); $i++) {

				if ( $current_depth == (int)$matches[$i][2] )
					$html .= '<li>';
			
				// start lists
				if ( $current_depth != (int)$matches[$i][2] ) {
					for ($current_depth; $current_depth < (int)$matches[$i][2]; $current_depth++) {
						$html .= '<ul><li>';
					}
				}
				
				// list item
				$html .= '<a href="#' . TOC_ANCHOR_PREFIX . ($i + 1) . '">';
				if ( $this->options['ordered_list'] ) {
					// attach leading numbers when lower in heirarchy
					for ($j = $numbered_items_min; $j < $current_depth; $j++) {
						$number = ($numbered_items[$j]) ? $numbered_items[$j] : 0;
						$html .= $number . '.';
					}
					
					$html .= ($numbered_items[$current_depth] + 1) . ' ';
					$numbered_items[$current_depth]++;
				}
				$html .= strip_tags($matches[$i][0]) . '</a>';
				
				
				// end lists
				if ( $i != count($matches) - 1 ) {
					if ( $current_depth > (int)$matches[$i + 1][2] ) {
						for ($current_depth; $current_depth > (int)$matches[$i + 1][2]; $current_depth--) {
							$html .= '</li></ul>';
							$numbered_items[$current_depth] = 0;
						}
					}
				}
			
				if ( $current_depth == (int)$matches[$i + 1][2] )
					$html .= '</li>';

			}
			
			return $html;
		}
		
		
		function the_content( $content )
		{
			global $post;
			$items = '';
			$matches = $find = $replace = array();

			if ( in_array(get_post_type($post), $this->options['auto_insert_post_types']) && !is_search() && $this->show_toc ) {
				// get all headings
				// the html spec allows for a maximum of 6 heading depths
				if ( preg_match_all('/(<h([2-6]{1})>).*<\/h\2>/', $content, $matches, PREG_SET_ORDER) >= $this->options['start'] ) {
					for ($i = 0; $i < count($matches); $i++) {
						
						// create find and replace arrays
						$find[] = $matches[$i][0];
						$replace[] = str_replace(
							$matches[$i][1], 
							trim($matches[$i][1], '>') . ' id="' . TOC_ANCHOR_PREFIX . ($i + 1) . '">',
							$matches[$i][0]
						);
						
						// assemble flat list
						if ( !$this->options['show_heirarchy'] ) {
							$items .= '<li><a href="#' . TOC_ANCHOR_PREFIX . ($i + 1) . '">';
							if ( $this->options['ordered_list'] ) $items .= ($i + 1) . ' ';
							$items .= strip_tags($matches[$i][0]) . '</a></li>';
						}
					}
			
					// build a heirarchical toc?
					// we could have tested for $items but that var can be quite large in some cases
					if ( $this->options['show_heirarchy'] ) $items = $this->build_heirarchy( &$matches );
					
					// wrapping css classes
					switch( $this->options['wrapping'] ) {
						case TOC_WRAPPING_LEFT:
							$wrapping = 'toc_wrap_left';
							break;
							
						case TOC_WRAPPING_RIGHT:
							$wrapping = 'toc_wrap_right';
							break;

						case TOC_WRAPPING_NONE:
						default:
							$wrapping = ' ';
					}
					
					// add container, toc title and list items
					$html = 
						'<div id="toc_container" class="' . $wrapping . '">' .
							'<p class="toc_title">' . htmlentities($this->options['heading_text']) . '</p>' .
							'<ul>' . $items . '</ul>' .
						'</div>'
					;
					
					switch ( $this->options['position'] ) {
						case TOC_POSITION_TOP:
							$content = $html . str_replace($find, $replace, $content);
							break;
						
						case TOC_POSITION_BOTTOM:
							$content = str_replace($find, $replace, $content) . $html;
							break;
					
						case TOC_POSITION_BEFORE_FIRST_HEADING:
						default:
							$replace[0] = $html . $replace[0];
							$content = str_replace($find, $replace, $content);
					}
				}
			}
		
			return $content;
		}
		
		
	} // end class
endif;


// do the magic
$tic = new toc();

?>