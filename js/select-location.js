jQuery(document).ready(function($){
	
	parent.jQuery('#TB_ajaxWindowTitle').html('Select Location');
	
	var pageHeight = $('html').height();
	var wrapperHeight = pageHeight - 170;
	
	//$('.tree-wrapper').css({"height": wrapperHeight + 'px' });

	$('.tree li:has(ul)').addClass('parent').addClass('closed').prepend('<img src="' + clarity_object.path + '/img/folder.png" alt="closed folder"/>');	
	$('.tree li:not(:has(ul))').prepend('<img src="' + clarity_object.path + '/img/page_white.png" alt="closed folder"/>');	
	
	$('.tree a').click(function(e){
	
		var values = $(this).parent().attr('class');
		var selected_value = values.replace(/[^0-9.]/g, "");
	
		$('li.root a').removeClass('selected');
		$('.tree a').removeClass('selected');
		$(this).addClass('selected');
	
		$('#parent_id').val(selected_value);
	
		e.preventDefault();
		return false;
	});
	
	$('li.root > a').click(function(e){
	
		$('li.root a').removeClass('selected');
		$('.tree a').removeClass('selected');
		$(this).addClass('selected');
	
		$('#parent_id').val('0');
	
		e.preventDefault();
		return false;
	});
	
	$('.tree li ul').hide();
	$('.tree li img').click(function() {
    	$(this).parent().children('ul').slideToggle(200); //Hides if shown, shows if hidden
    	if($(this).parent().hasClass('closed'))
    	{
    		$(this).parent().removeClass('closed');
	    	$(this).parent().addClass('open');
	    	$(this).attr('src','' + clarity_object.path + '/img/folder_page_white.png');
	    	$(this).attr('alt','open folder');
    	}
    	else if($(this).parent().hasClass('open'))
    	{
	    	$(this).parent().removeClass('open');
	    	$(this).parent().addClass('closed');
	    	$(this).attr('src', clarity_object.path + '/img/folder.png');
	    	$(this).attr('alt', 'closed folder');
    	}
    });

	
	$('ul.tree li:last-child').addClass('last');
	
});