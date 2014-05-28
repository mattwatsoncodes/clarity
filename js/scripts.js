jQuery(document).ready(function($){

	jQuery.post(
		ajaxurl,
		{
			action 		: 'clarity_ajax_location',
			'TB_iframe' : 1,
			'width' 	: 800,
			'height' 	: 550
		},
		function(response) {

			tb_show('Select Location', clarity_object.ajax_url );
		}
	);
});