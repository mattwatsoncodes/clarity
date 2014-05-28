jQuery(document).ready(function() {
	
	jQuery('input[type=button].change').click(function() {
		var imageName = '#' + jQuery(this).attr('id').replace('_button', '');
		formfield = jQuery(imageName).attr('name');
		tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
	 
		window.send_to_editor = function(html) {
			imgUrl = jQuery('img',html).attr('src');
			jQuery(imageName).val(imgUrl);
			jQuery(imageName+'_preview').attr('src',imgUrl);
			tb_remove();
		}
		 
	 return false;
	});
	 
	jQuery('input[type=button].clear').click(function() {
		var imageName = '#' + jQuery(this).attr('id').replace('_button', '').replace('clear_', 'upload_');
		var imgUrl = jQuery(imageName+'_default').val();
		jQuery(imageName).val(imgUrl);
		jQuery(imageName+'_preview').attr('src',imgUrl);

		 
		return false;
	});
 
});