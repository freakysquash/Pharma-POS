/*
 * CSS UI Notify v1 (Aug 29 2011)
 * http://css-ui.com
 *
 * Copyright 2011, http://codeeverywhere.ca
 * Licensed under the MIT license.
 *
 */
(function($)
{
  $.uinotify = function(options)
  {
    var settings = {
      'text'	: 'Notify',
      'duration': 2000
    };
    
    if(options){ $.extend(settings, options); }
	
	$('body').append('<div class="ui-notify">' + settings.text + '</div>');
	$('.ui-notify').hide().slideDown(500, "easeInQuad").delay(settings.duration).slideUp(500, function()
	{
		$(this).remove();
	});	
  };
})(jQuery);
