<?php
/**
 * @package Clarity
 * @version 1.0.0
 */

/*
Plugin Name:  Clarity
Plugin URI:   http://makedo.in/products/
Description:  Choose heirachy and template visually in WordPress
Author:       Make Do
Version:      1.0.0
Author URI:   http://makedo.in
Licence:      GPLv2 or later
License URI:  http://www.gnu.org/licenses/gpl-2.0.html
*/

function clarity() {

	global $pagenow;

	if(isset($_GET['post_type']))
	{
		if (( $pagenow == 'post-new.php' ) && ($_GET['post_type'] == 'page')) {

			// creating a new page!
			wp_enqueue_script('clarity_js', plugins_url('/clarity/js/scripts.js'));
		}
	}
}

add_action( 'admin_footer', 'clarity' );
add_action('admin_init', 'clarity_init' );
add_action('admin_menu', 'clarity_add_page');

// Init plugin options to white list our options
function clarity_init(){
	register_setting( 'clarity_options', 'clarity_group', 'clarity_validate' );
}

// Add menu page
function clarity_add_page() {
	add_options_page('Clarity', 'Clarity', 'manage_options', 'clarity', 'clarity_do_page');
}

// Draw the menu page itself
function clarity_do_page() {

	$template_link = plugins_url('/clarity/img/default.png');

	?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br></div>
			<h2>Clarity</h2>
			<form method="post" action="options.php">
				<h3>Template Images</h3>
				<p>You can set the default image for each of your templates below. You need to save your changes for them to take effect.</p>
				<p>Images can also be <strong>set in the template</strong> by adding the meta <em>&lsquo;Template Image:&rsquo;</em> to the template followed by the path to the image.</p>
				<p>If <em>&lsquo;Auto generate&rsquo;</em> is selected, an existing page that implements that template will have a screen shot taken generated (public access to the page required). If the auto generated image is unsuccessful the image will fall back to the selected image.</p>

				<?php settings_fields('clarity_options'); ?>
				<?php $options = get_option('clarity_group'); ?>
				<div class="default-image-wrapper">

					<div class="default-image-options">
						<h3><label for="upload_image">Default Template</label></h3>
						<?php
							$template_image = $template_link;

							if(isset($options['clarity_default-image']) && $options['clarity_default-image'] != '' && $options['clarity_default-image'] != false)
							{
								$template_image = $options['clarity_default-image'];
							}

							if( array_key_exists('clarity_default-image-auto', $options ) && $options['clarity_default-image-auto'] )
							{

								$pages = get_pages(array(
										'meta_key' => '_wp_page_template',
										'meta_value' => $template_filename,
										'hierarchical' => 0
									));

								if(count($pages)>0)
								{

									$headers = get_headers(get_permalink( $pages[0] -> ID), 1);
									if ($headers[0] == 'HTTP/1.1 200 OK') {
										$template_image = get_permalink( $pages[0] -> ID);
									}
								}
							}

						?>
						<input id="upload_image" type="text" size="36" name="clarity_group[clarity_default-image]" value="<?php echo $template_image ?>" hidden="hidden"/><img height="95" width="118" id="upload_image_preview" src="<?php echo $template_image ?>"/>

						<input id="upload_image_button" type="button" class="button-primary change" value="Change Image" /> <input id="clear_image_button" type="button" class="button-secondary clear" value="Clear" />

						<div class="additional-options">

							<label for="users_can_register">
							<input name="clarity_group[clarity_default-image-auto]" type="checkbox" value="1" <?php checked('1', array_key_exists('clarity_default-image-auto', $options ) && $options['clarity_default-image-auto']); ?> />
							Auto generate</label>

						</div>
					</div>

					<?php
						$templates = get_page_templates();

						foreach ( $templates as $template_name => $template_filename ) 
						{

							$template_shortname = 'clarity_'.str_replace('.php','',$template_filename);
							$template_shortname = str_replace("/", "_", $template_shortname);

							$template_image = $template_link;
							$template_default = $template_link;

							$templatefile = locate_template(array($template_filename));

							if(file_exists($templatefile)) {

								$template_data = implode('', array_slice(file($templatefile), 0, 10));
								$matches = '';
								if (preg_match( '|Template Image:(.*)$|mi', $template_data, $matches)) {
									$multi = explode(',',_cleanup_header_comment($matches[1]));

									$template_image = $multi[0];
									$template_default = $template_image;

								}
							}

							if(isset($options[$template_shortname]) && $options[$template_shortname] != '' && $options[$template_shortname] != false)
							{
								$template_image = $options[$template_shortname];
							}

							if(array_key_exists($template_shortname.'-auto', $options ) && $options[$template_shortname.'-auto'])
							{

								$pages = get_pages(array(
										'meta_key' => '_wp_page_template',
										'meta_value' => $template_filename,
										'hierarchical' => 0
									));

								if(count($pages)>0)
								{

									$headers = get_headers(get_permalink( $pages[0] -> ID), 1);
									if ($headers[0] == 'HTTP/1.1 200 OK') {
										$template_image = get_permalink( $pages[0] -> ID);
									}

								}
							}

							?>
							<div class="default-image-options">

								<h3><label for="upload_<?php echo $template_shortname; ?>"><?php echo $template_name; ?> </label></h3>
								<input id="upload_<?php echo $template_shortname; ?>_default" type="text" value="<?php echo $template_default; ?>" hidden="hidden"/>

								<input id="upload_<?php echo $template_shortname; ?>" type="text" size="36" name="clarity_group[<?php echo $template_shortname; ?>]" value="<?php echo $template_image; ?>" hidden="hidden"/>
								<img height="95" width="118" id="upload_<?php echo $template_shortname ?>_preview" src="<?php echo $template_image; ?>"/>

								<input id="upload_<?php echo $template_shortname; ?>_button" type="button" class="button-primary change" value="Change Image" />  <input id="clear_<?php echo $template_shortname; ?>_button" type="button" class="button-secondary clear" value="Clear" />

								<div class="additional-options">

									<label for="users_can_register"><input name="clarity_group[<?php echo $template_shortname; ?>-auto]" type="checkbox" value="1" <?php checked('1', array_key_exists($template_shortname.'-auto', $options ) &&  $options[$template_shortname.'-auto']); ?> />Auto generate</label>

								</div>
							</div>
							<?php

						}
					?>


					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
					</p>
				</div>
			</form>
		</div>
	<?php
}

function my_admin_scripts() {
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_register_script('b6-wp-content-flow-upload', plugins_url('/clarity/js/options.js'), array('jquery','media-upload','thickbox'));
	wp_enqueue_script('b6-wp-content-flow-upload');
	wp_enqueue_style('clarity_modal_css', plugins_url('/clarity/css/styles.css'));
}

function my_admin_styles() {
	wp_enqueue_style('thickbox');
}

if (isset($_GET['page']) && $_GET['page'] == 'clarity') {
	add_action('admin_print_scripts', 'my_admin_scripts');
	add_action('admin_print_styles', 'my_admin_styles');
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function clarity_validate($input) {

	$templates = get_page_templates();

	$input['clarity_default-image-auto'] = ( $input['clarity_default-image-auto'] == 1 ? 1 : 0 );
	foreach ( $templates as $template_name => $template_filename ) {
		$template_shortname = str_replace('.php','',$template_filename);
		$input[$template_shortname.'-auto'] = ( $input[$template_shortname.'-auto'] == 1 ? 1 : 0 );
	}

	$input['clarity_default-image'] =  $input['clarity_default-image'];

	return $input;
}
