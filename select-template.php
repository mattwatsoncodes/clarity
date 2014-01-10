<?php

define("VP","b6_wp_content_flow");
define("ABSPATH", str_replace("wp-content/plugins/".VP, "", dirname(__FILE__)));

//The inclusion of these files allows full use of all functions of wordpress
require_once(ABSPATH.'wp-load.php');
require_once(ABSPATH.'wp-admin/includes/admin.php');

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
wp_enqueue_style('b6_content_flow_modal_css', plugins_url('/'.VP.'/css/styles.css'));

//We need to add scripts to the footer, but we dont want to enable the admin bar
wp_deregister_script( 'admin-bar' );
wp_deregister_style( 'admin-bar' );
remove_action('wp_footer','wp_admin_bar_render',1000);
wp_enqueue_script("jquery");
wp_enqueue_script('b6_content_flow_select_location_js', plugins_url('/'.VP.'/js/select-template.js?v=2'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
    <title>WP Content Flow</title><?php wp_head(); ?>
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

$options = get_option('b6_wp_content_flow_group');

$template_image = plugins_url('/'.VP.'/img/default.png');

if(isset($options['b6_wp_content_default-image']) && $options['b6_wp_content_default-image'] != '' && $options['b6_wp_content_default-image'] != false)
{
	$template_image = $options['b6_wp_content_default-image'];
}

if($options['b6_wp_content_default-image-auto'])
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
                </div><?php

$template_image = plugins_url('/'.VP.'/img/default.png');

foreach ( $templates as $template_name => $template_filename ) {

	$template_shortname = 'b6_wp_content_'.str_replace('.php','',$template_filename);

	$templatefile = locate_template(array($template_filename));

	if(file_exists($templatefile)) {

		$template_data = implode('', array_slice(file($templatefile), 0, 10));
		$matches = '';
		if (preg_match( '|Template Image:(.*)$|mi', $template_data, $matches)) {
			$multi = explode(',',_cleanup_header_comment($matches[1]));

			$template_image = $multi[0];
		}
	}

	if(isset($options[$template_shortname]) && $options[$template_shortname] != '' && $options[$template_shortname] != false)
	{
		$template_image = $options[$template_shortname];
	}

	if($options[$template_shortname.'-auto'])
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
                </div><?php

}
?>
            </div><input type="text" name="template_id" id="template_id" value="Default" hidden="hidden"> <input type="text" name="parent_id" id="parent_id" value="<?php echo $_POST['parent_id']?>" hidden="hidden"> <input type="submit" name="next" id="next" class="button button-primary" value="Finish">
        </form>
    </div><?php wp_footer(); ?>
</body>
</html>