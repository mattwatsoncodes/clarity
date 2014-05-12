<?php

	$file = str_replace("wp-content/plugins/clarity", "", dirname(__FILE__));

	//The inclusion of these files allows full use of all functions of wordpress
	require_once($file.'wp-load.php');
	require_once($file.'wp-admin/includes/admin.php');
	 
	wp_enqueue_style( 'wp-admin' );
	wp_enqueue_style( 'colors-fresh' );
	wp_enqueue_style('clarity_modal_css', plugins_url('/clarity/css/styles.css'));
	 
	//We need to add scripts to the footer, but we dont want to enable the admin bar
	wp_deregister_script( 'admin-bar' );
	wp_deregister_style( 'admin-bar' );
	remove_action('wp_footer','wp_admin_bar_render',1000);
	wp_enqueue_script("jquery");
	wp_enqueue_script('clarity_select_location_js', plugins_url('/clarity/js/select-location.js'));
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

	<form enctype="multipart/form-data" method="post" action="select-template.php">
		
	<div class="tree-wrapper">
	<ul>
		<li class="root">
			<img src="img/folder_page_white.png" alt="folder" width="16" height="16"> <a href="#" class="selected">Root</a>
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
