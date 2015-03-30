////////////////////////////////////
// NAVIGATION FUNCTIONALITY       //
////////////////////////////////////

var pageTitle = 'SMAR Web Administration';

// for navigating back, track the onpopstate event
window.onpopstate = function(event) {
	if(event.state.page) {
		loadPage(event.state.page, true);
	}
}

function loadPage(url, loadFull) {
	
	loadFull = loadFull || false;

	var $content = $('#smar-content');
	var $contentInner = $('#smar-content-inner');
	var $targetContainer = loadFull ? $content : $contentInner;
	var $loadOverlay = $('#smar-loading');	
	
	if(url.substr(0,4) == 'http') {
		window.location.href = url;
	} else {
		
		// create parameters for include
		var new_url = url;
		
		if(new_url.indexOf('?') == -1) {
			new_url += '?';
		} else {
			new_url += '&';
		}
		new_url += 'smar_include=true';

		if(loadFull) {
			new_url += '&smar_nav=true';
		}
		
		history.pushState({page:url}, document.title, url);
		
		$loadOverlay.fadeIn(100, function() {
			$targetContainer.fadeOut(100, function() {
				
				$targetContainer.hide().empty();
				window.scrollTo(0,0);
				
				$targetContainer.load(new_url, function(data, textStatus, jqXHR) {

					if(textStatus == 'error') {
						$targetContainer.html("<h1>Error</h1><p>The page could not be loaded (error during execution of AJAX request).</p>");
						$loadOverlay.fadeOut(100, function() {
							$targetContainer.fadeIn(100, function() {
								document.title = 'Error - ' + pageTitle;
							});
						});
					} else {
						$targetContainer.waitForImages(function() {
							$loadOverlay.fadeOut(200, function() {
								$targetContainer.fadeIn(200, function() {
									var newTitle = '';
									newTitle = $targetContainer.find('h1').first().text();
									document.title = newTitle + ' - ' + pageTitle;
									
									if(loadFull)
										subNavHandler();
								});
							});
						});
					}
				});
			});
		});
	}
}



////////////////////////////////////
// EVENT HANDLER                  //
////////////////////////////////////

// handler for main navigation
$('nav#nav-main a').on('click', function(e) {

	e.preventDefault();
	$target = $(e.delegateTarget);

	// only change page if not current page is selected
	if( !$target.hasClass('smar-active') ) {
	
		var newTarget = $target.attr('href');
	
		// toggle active status
		$('nav#nav-main a.smar-active').removeClass('smar-active');
		$target.addClass('smar-active');
		
		loadPage(newTarget, true);	
	}
});

// handler for page navigation
function subNavHandler() {
	$('nav#nav-page a').on('click', function(e) {

		e.preventDefault();
		$target = $(e.delegateTarget);

		// only change page if not current page is selected
		if( !$target.hasClass('smar-active') ) {

			var newTarget = $target.attr('href');

			// toggle active status
			$('nav#nav-page a.smar-active').removeClass('smar-active');
			$target.addClass('smar-active');

			loadPage(newTarget);
		}
	});
}



////////////////////////////////////
// STARTUP                        //
////////////////////////////////////

history.pushState({page:window.location.href}, document.title, window.location.href);
