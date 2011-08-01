jQuery(document).ready(function($) {
	$('.tab_content, #toc_advanced_usage, #sitemap_advanced_usage, div.more_toc_options.disabled').hide();
	$('ul#tabbed-nav li:first').addClass('active').show(); // show first tab
	$('.tab_content:first').show(); // show first tab content

	$('ul#tabbed-nav li').click(function(event) {
		event.preventDefault();
		$('ul#tabbed-nav li').removeClass('active'); //Remove any "active" class
		$(this).addClass('active'); //Add "active" class to selected tab
		$('.tab_content').hide(); //Hide all tab content

		var activeTab = $(this).find('a').attr('href'); //Find the href attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active ID content
	});
	
	$('h3 span.show_hide a').click(function(event) {
		event.preventDefault();
		$( $(this).attr('href') ).toggle('fast');
		if ( $(this).text() == 'show' )
			$(this).text('hide');
		else
			$(this).text('show');
	});
	
	$('input#show_heading_text').click(function() {
		$(this).siblings('div.more_toc_options').toggle('fast');
	});
	
	/* width drop down */
	$('select#width').change(function() {
		if ( $(this).find('option:selected').val() == 'User defined' ) {
			$(this).siblings('div.more_toc_options').show('fast');
			$('input#width_custom').focus();
		}
		else
			$(this).siblings('div.more_toc_options').hide('fast');
	});
	$('input#width_custom').keyup(function() {
		var width = $(this).val();
		width = width.replace(/[^0-9]/, '');
		$('input#width_custom').val(width);
	});
	
	if ( $.farbtastic ) {
		$('#background_colour_wheel').hide();
		$('#background_colour_wheel').farbtastic('#background_colour');
		$("#background_colour").click(function(){$('#background_colour_wheel').slideToggle()});
	}
});