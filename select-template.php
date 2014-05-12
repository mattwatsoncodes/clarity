<?php

	$file = str_replace("wp-content/plugins/clarity", "", dirname(__FILE__));

	//The inclusion of these files allows full use of all functions of wordpress
	require_once($file.'wp-load.php');
	require_once($file.'wp-admin/includes/admin.php');

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

	wp_enqueue_style( 'wp-admin' );
	wp_enqueue_style( 'colors-fresh' );
	wp_enqueue_style('clarity_modal_css', plugins_url('/clarity/css/styles.css'));

	//We need to add scripts to the footer, but we dont want to enable the admin bar
	wp_deregister_script( 'admin-bar' );
	wp_deregister_style( 'admin-bar' );
	remove_action('wp_footer','wp_admin_bar_render',1000);
	wp_enqueue_script("jquery");
	wp_enqueue_script('clarity_select_location_js', plugins_url('/clarity/js/select-template.js?v=2'));
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

		<form enctype="multipart/form-data" method="post" action="select-template.php">
			<div class="template-wrapper">
			<?php
				$templates = get_page_templates();

				$options = get_option('clarity_group');

				$template_image = plugins_url('/clarity/img/default.png');

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

				$template_image = plugins_url('/clarity/img/default.png');

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