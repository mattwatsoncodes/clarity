jQuery(document).ready(function($){
		
	parent.jQuery('#TB_ajaxWindowTitle').html('Select Template');
	
	var pageHeight = $('html').height();
	var wrapperHeight = pageHeight - 170;
	//$('.template-wrapper').css({"height": wrapperHeight + 'px' });
	
	$('.screenshot-wrapper a').click(function(e){
		$('#template_id').val($(this).attr('href'));
		$('.screenshot-wrapper a').removeClass('selected');
		$(this).addClass('selected');
		e.preventDefault();
		return false;
	});
	
	var height = 0;
	$('.screenshot-wrapper a').each(function(){
		if(height < $(this).innerHeight())
		{
			height = $(this).innerHeight();
		}
	});

	$('.screenshot-wrapper a').each(function(){
		$(this).height(height);
	});
});