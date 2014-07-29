<?php
/**
 * @package Clarity
 * @version 1.0.2
 */

/*
Plugin Name:  Clarity
Plugin URI:   http://makedo.in/products/
Description:  Choose heirachy and template visually in WordPress
Author:       Make Do
Version:      1.0.2
Author URI:   http://makedo.in
Licence:      GPLv2 or later
License URI:  http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * 
 * @since  		1.0.0
 * 
 * Fire the thickbox if we are adding a new post
 * 
 */
function clarity() {

	global $pagenow;

	if(isset($_GET['post_type']))
	{
		if (( $pagenow == 'post-new.php' ) && ($_GET['post_type'] == 'page')) {

			$ajax_url = add_query_arg(
				array( 
					'action' 	=> 'clarity_ajax_location',
					'TB_iframe' => 1,
					'width' 	=> 800,
					'height' 	=> 550
				), 
				admin_url( 'admin-ajax.php' ) 
			); 
			// creating a new page!
			wp_register_script('clarity_js', plugins_url('js/scripts.js', __FILE__));
			$clarity_object = array( 'path' => plugins_url('', __FILE__), 'ajax_url' => $ajax_url);
			wp_localize_script( 'clarity_js', 'clarity_object', $clarity_object );
			wp_enqueue_script( 'clarity_js' );
			
		}
	}
}
add_action( 'admin_footer', 'clarity' );




/**
 * 
 * @since  		1.0.0
 * 
 * Add the clarity menu page
 * 
 */
function clarity_add_page() {
	add_options_page('Clarity', 'Clarity', 'manage_options', 'clarity', 'clarity_do_page');
}
add_action( 'admin_menu', 'clarity_add_page' );



/**
 * 
 * @since  		1.0.0
 * 
 * Register clarity options
 * 
 */
function clarity_init(){
	register_setting( 'clarity_options', 'clarity_group', 'clarity_validate' );
}
add_action( 'admin_init', 'clarity_init' );



/**
 * 
 * @since  		1.0.0
 * 
 * Draw the menu page
 * 
 */
function clarity_do_page() {

	$template_link = plugins_url('img/default.png', __FILE__);

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

							if( $options !== false && array_key_exists('clarity_default-image-auto', $options ) && $options['clarity_default-image-auto'] )
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
							<input name="clarity_group[clarity_default-image-auto]" type="checkbox" value="1" <?php $options !== false && checked('1', array_key_exists('clarity_default-image-auto', $options ) && $options['clarity_default-image-auto']); ?> />
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

							if( $options !== false && array_key_exists($template_shortname.'-auto', $options ) && $options[$template_shortname.'-auto'])
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

									<label for="users_can_register"><input name="clarity_group[<?php echo $template_shortname; ?>-auto]" type="checkbox" value="1" <?php $options !== false && checked('1', array_key_exists($template_shortname.'-auto', $options ) &&  $options[$template_shortname.'-auto']); ?> />Auto generate</label>

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


/**
 * 
 * @since  		1.0.0
 * 
 * Add JS
 * 
 */
function clarity_admin_scripts() {
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_register_script('clarity-upload', plugins_url('js/options.js', __FILE__), array('jquery','media-upload','thickbox'));
	wp_enqueue_script('clarity-upload');
	wp_enqueue_style('clarity_modal_css', plugins_url('css/styles.css', __FILE__));

}



/**
 * 
 * @since  		1.0.0
 * 
 * Add CSS
 * 
 */
function clarity_admin_styles() {
	wp_enqueue_style('thickbox');
}


/**
 * 
 * @since  		1.0.0
 * 
 * Register the CSS and JS
 * 
 */
if (isset($_GET['page']) && $_GET['page'] == 'clarity') {
	add_action('admin_print_scripts', 'clarity_admin_scripts');
	add_action('admin_print_styles', 'clarity_admin_styles');
}


/**
 * 
 * @since  		1.0.0
 * 
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 * 
 */
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



/**
 * 
 * @since  		1.0.0
 * 
 * Render the location popup
 * 
 */
function clarity_render_ajax_location()
{
	$ajax_url = add_query_arg(
		array( 
			'action' 	=> 'clarity_ajax_template',
			'TB_iframe' => 1,
			'width' 	=> 800,
			'height' 	=> 550
		), 
		admin_url( 'admin-ajax.php' ) 
	); 
	wp_enqueue_style('clarity_modal_css', plugins_url('css/styles.css', __FILE__));
	wp_enqueue_script("jquery");
	wp_register_script('clarity_select_location_js', plugins_url('js/select-location.js', __FILE__));
	$clarity_object = array( 'path' => plugins_url('', __FILE__), 'ajax_url' => $ajax_url);
	wp_localize_script( 'clarity_select_location_js', 'clarity_object', $clarity_object );
	wp_enqueue_script( 'clarity_select_location_js' );
	?>
	<!DOCTYPE html>
	<html>
	<head>
		<title>Clarity</title>
		<?php wp_head(); ?>
		<style>
			html,body
			{
				margin-top: 0px !important;
			}
		</style>
	</head>
	<body>
		
		<div class="wrap">
		<h1>Select a location</h1>
		<p>Select a location from the content tree below and click &lsquo;next&rsquo; to continue.</p>
		<p>Click on a folder to view the pages within it.</p>

		<form enctype="multipart/form-data" method="post" action="<?php echo $ajax_url; ?>">
			
		<div class="tree-wrapper">
		<ul>
			<li class="root">
				<img src="<?php echo plugins_url('img/folder_page_white.png', __FILE__);?>" alt="folder" width="16" height="16"> <a href="#" class="selected">Root</a>
				<ul class="tree">
					<?php wp_list_pages('title_li='); ?>
				</ul>
			</li>
		</ul>
		</div>
			
			<input type="text" name="parent_id" id="parent_id" value="0" hidden="hidden">
			<input type="submit" name="next" id="next" class="button button-highlighted" value="Next">
		</form>
		</div>
		<?php wp_footer(); ?>
	</body>
	</html>
	<?php
	exit();
}
add_action( 'wp_ajax_'        . 'clarity_ajax_location', 'clarity_render_ajax_location' );
add_action( 'wp_ajax_nopriv_' . 'clarity_ajax_location', 'clarity_render_ajax_location' );



/**
 * 
 * @since  		1.0.0
 * 
 * Render the template popup
 * 
 */
function clarity_render_ajax_template()
{
	$ajax_url = add_query_arg(
		array( 
			'action' 	=> 'clarity_ajax_template',
			'TB_iframe' => 1,
			'width' 	=> 800,
			'height' 	=> 550
		), 
		admin_url( 'admin-ajax.php' ) 
	); 
	if(isset($_POST['template_id']))
	{
		$current_user = wp_get_current_user();

		$post = array(
			'post_author' => $current_user->ID ,
			'post_parent' => $_POST['parent_id'],
			'post_type'   => 'page'
		);

		$page_id = wp_insert_post($post);

		update_post_meta($page_id, "_wp_page_template", $_POST['template_id']);

		echo "<script>parent.location = '".admin_url()."post.php?post=".$page_id."&action=edit';parent.eval('tb_remove()'); </script>";
	}

	wp_enqueue_style('clarity_modal_css', plugins_url('css/styles.css', __FILE__));

	//We need to add scripts to the footer, but we dont want to enable the admin bar
	wp_enqueue_script("jquery");
	wp_enqueue_script('clarity_select_location_js', plugins_url('js/select-template.js?v=2', __FILE__));
	?>
	<!DOCTYPE html>
	<html>
	<head>
		<title>Clarity</title><?php wp_head(); ?>
		<style type="text/css">
			html,body
			{
				margin-top: 0px !important;
			}
		</style>
	</head>
	<body>
		<div class="wrap">
			<h1>Select a template</h1>

			<p>Select a template from the gallery below and click &lsquo;finish&rsquo;.</p>

			<form enctype="multipart/form-data" method="post" action="<?php echo $ajax_url;?>">
				<div class="template-wrapper">
				<?php
					$templates = get_page_templates();

					$options = get_option('clarity_group');

					$template_image = plugins_url('img/default.png', __FILE__);

					if(isset($options['clarity_default-image']) && $options['clarity_default-image'] != '' && $options['clarity_default-image'] != false)
					{
						$template_image = $options['clarity_default-image'];
					}

					if($options['clarity_default-image-auto'])
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

				<div class="screenshot-wrapper">
					<a href="Default" class="selected"><img src="<?php echo $template_image ?>" class="screenshot" width="118" height="95"> Default</a>
				</div>
				<?php

					$template_image = plugins_url('img/default.png', __FILE__);

					foreach ( $templates as $template_name => $template_filename ) 
					{

						$template_shortname = 'clarity_'.str_replace('.php','',$template_filename);
						$template_shortname = str_replace("/", "_", $template_shortname);

						$templatefile = locate_template(array($template_filename));

						if(file_exists($templatefile)) 
						{

							$template_data = implode('', array_slice(file($templatefile), 0, 10));
							$matches = '';
							if (preg_match( '|Template Image:(.*)$|mi', $template_data, $matches)) 
							{
								$multi = explode(',',_cleanup_header_comment($matches[1]));

								$template_image = $multi[0];
							}
						}

						if(isset($options[$template_shortname]) && $options[$template_shortname] != '' && $options[$template_shortname] != false)
						{
							$template_image = $options[$template_shortname];
						}

						if(isset($options[$template_shortname.'-auto']) && $options[$template_shortname.'-auto'])
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
					<div class="screenshot-wrapper">
						<a href="<?php echo $template_filename; ?>"><img src="<?php echo $template_image;?>" class="screenshot" width="118" height="95"> <?php echo $template_name; ?></a>
					</div>
					<?php
					}
				?>
				</div><input type="text" name="template_id" id="template_id" value="Default" hidden="hidden"> <input type="text" name="parent_id" id="parent_id" value="<?php echo $_POST['parent_id']?>" hidden="hidden"> <input type="submit" name="next" id="next" class="button button-primary" value="Finish">
			</form>
		</div>
		<?php wp_footer(); ?>
	</body>
	</html>
	<?php
	exit();
}
add_action( 'wp_ajax_'        . 'clarity_ajax_template', 'clarity_render_ajax_template' );
add_action( 'wp_ajax_nopriv_' . 'clarity_ajax_template', 'clarity_render_ajax_template' );