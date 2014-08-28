jQuery(document).ready(function($){


	$.post(ajaxurl, {

		action: 'mcq_refresh_server'

	});

});