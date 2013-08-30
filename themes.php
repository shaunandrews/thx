<?php
/*
Plugin Name: THX
Plugin URI: n/a
Description: A better way to browse themes.
Version: 0.1
Author: Shaun Andrews
Author URI: http://automattic.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// Enqueue some new styles and drop in a few lines of js
add_action( 'admin_print_styles', 'thx_add_style' );
function thx_add_style() {
	if ( get_current_screen()->id != 'appearance_page_thx' )
		return;
	wp_enqueue_style( 'thx', plugins_url( 'style.css', __FILE__ ) );
}

add_action( 'admin_enqueue_scripts', 'thx_add_scripts' );
function thx_add_scripts() {
	if ( get_current_screen()->id != 'appearance_page_thx_themes' )
		return;
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-draggable' );
	wp_enqueue_script( 'jquery-ui-droppable' );
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'jquery-effects-highlight' );
	wp_enqueue_script( 'thx', plugins_url( 'scripts.js', __FILE__ ), array( 'jquery' ) );
}

add_action( 'admin_menu', 'thx_settings_page' );
function thx_settings_page() {
	// create admin page
	$settings_page = add_submenu_page( 'themes.php', 'Themes', 'Themes Prototype', 'edit_theme_options', 'thx', 'thx_replace_core' );
}

function thx_replace_core() {
	if ( ! current_user_can('edit_theme_options') )
		wp_die( __( 'Cheatin&#8217; uh?' ));

	echo '<div class="wrap thx-prototype">';
	screen_icon();?>
	<h2>Your Themes</h2>
	<?php thx_new_themes();
	echo '</div><!-- .wrap -->';
}

function thx_new_themes() {
	$themes = wp_get_themes( array( 'allowed' => true ) );
?>
<ol id="your-themes" class="theme-list">
	<?php foreach( $themes as $theme ): ?>
		<?php thx_theme_block( $theme ); ?>
	<?php endforeach; ?>
</ol>

<h3 class="thx-title">Popular Themes</h3>
<ol id="popular-themes" class="theme-grid">
	<?php $featured_themes = themes_api( 'query_themes', array( 'page' => 1, 'per_page' => 4, 'browse' => 'featured' ) ); ?>
	<?php foreach( $featured_themes->themes as $theme ): ?>
		<?php thx_theme_block( $theme, 'popular' ); ?>
	<?php endforeach; ?>
	<li class="more-themes">More Themes</li>
</ol>

<h3 class="thx-title">Newest Themes</h3>
<ol id="newest-themes" class="theme-grid">
	<?php $newest_themes = themes_api( 'query_themes', array( 'page' => 1, 'per_page' => 4, 'browse' => 'new' ) ); ?>
	<?php foreach( $newest_themes->themes as $theme ): ?>
		<?php thx_theme_block( $theme, 'new' ); ?>
	<?php endforeach; ?>
	<li class="more-themes">More Themes</li>
</ol>
<?php
}

function thx_theme_block( $theme, $type = 'installed' ) {
	$is_active = false;
	if( $type == 'installed' ) {
		$screenshot_url = $theme->get_screenshot();
		if ( $theme->template == thx_get_current_theme() )
			$is_active = true;
	} else {
		$screenshot_url = esc_url( $theme->screenshot_url );
	}
	?>
	<li class="theme<?php if( $is_active ) echo ' active';?>" id="theme-<?php echo $theme->template; ?>">
		<img class="theme-screenshot" src="<?php echo $screenshot_url; ?>">
		<h4 class="theme-name"><?php echo $theme->name; ?></h4>
		<?php if( $is_active ): ?>
			<p class="theme-active">Activated</p>
		<?php else: ?>
			<ul class="theme-actions">
			<?php if($type == 'installed'): ?>
				<li><a class="button button-secondary" href="#">Activate</a></li>
				<li><a href="/wp-admin/customize.php?theme=<?php echo $theme->template; ?>">Preview</a></li>
				<li><a href="#">x</a></li>
			<?php else: ?>
				<li><a href="#">Details</a></li>
			<?php endif; ?>
			</ul>
		<?php endif; ?>
	</li>
	<?php
}

function thx_get_themes() {
	$themes = wp_get_themes( array( 'allowed' => true ) );

	$data = array();

	foreach( $themes as $slug => $theme ) {
		$data[] = array(
			'slug' => $slug,
			'name' => $theme->get( 'Name' ),
			'screenshot_url' => $theme->get_screenshot(),
			'description' => $theme->get( 'Description' ),
			'author' => $theme->get( 'Author' ),
			'version' => $theme->Version,
			'active' => ( $slug == thx_get_current_theme() ) ? true : NULL,
		);
	}

	$themes = $data;
	return $themes;
}

function thx_get_current_theme() {
	$theme = wp_get_theme();
	return $theme->template;
}