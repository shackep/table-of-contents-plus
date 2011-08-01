jQuery(document).ready(function($) {

	if ( $.smoothScroll ) {
		var target = hostname = hash = null;

		$('body a').click(function(event) {
			// 1.6 moved some attributes to the .prop method
			if ( minVersion('1.6') ) {
				hostname = $(this).prop('hostname');
				hash = $(this).prop('hash');
			}
			else {
				hostname = $(this).attr('hostname');
				hash = $(this).attr('hash');
			}

			if ( (window.location.hostname == hostname) && (hash !== '') ) {
				// escape jquery selector chars, but keep the #
				var hash_selector = hash.replace(/([ !"$%&'()*+,.\/:;<=>?@[\]^`{|}~])/g, '\\$1');
				// check if element exists with id=__
				if ( $( hash_selector ).length > 0 )
					target = hash;
				else {
					// must be an anchor (a name=__)
					anchor = hash;
					anchor = anchor.replace('#', '');
					target = 'a[name="' + anchor  + '"]';
				}
				event.preventDefault();
				$.smoothScroll({
					scrollTarget: target,
					offset: -30
				});

			}
		});
	}
	
	function minVersion(version) {
		var $vrs = window.jQuery.fn.jquery.split('.'),
			min = version.split('.');
		for (var i=0, len=$vrs.length; i<len; i++) {
			console.log($vrs[i], min[i]);
			if (min[i] && $vrs[i] < min[i]) {
				return false;
			}
		}
		return true;
	}

});