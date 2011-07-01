jQuery(document).ready(function($) {
	$(".tab_content, #sitemap_advanced_usage").hide();
	$("ul#tabbed-nav li:first").addClass("active").show(); // show first tab
	$(".tab_content:first").show(); // show first tab content

	$("ul#tabbed-nav li").click(function() {
		$("ul#tabbed-nav li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".tab_content").hide(); //Hide all tab content

		var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active ID content
		return false;
	});
	
	$('h3 span.show_hide a').click(function() {
		$( $(this).attr('href') ).toggle('fast');
		if ( $(this).text() == 'show' )
			$(this).text('hide');
		else
			$(this).text('show');
		return false;
	});
	
    $('#background_colour_wheel').hide();
    $('#background_colour_wheel').farbtastic("#background_colour");
    $("#background_colour").click(function(){$('#background_colour_wheel').slideToggle()});
});