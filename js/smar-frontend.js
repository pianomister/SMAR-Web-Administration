////////////////////////////////////
// NAVIGATION FUNCTIONALITY       //
////////////////////////////////////

var pageTitle = 'SMAR Web Administration';

// for navigating back, track the onpopstate event
window.onpopstate = function(event) {
	if(event.state.page) {
		loadPage(event.state.page);
	}
}

function loadPage(url) {

	var $contentInner = $('#smar-content-inner');
	var $loadOverlay = $('#smar-loading');
	var new_url = url;
	
	if(url.substr(0,4) == 'http') {
		window.location.href = url;
	} else {
		
		history.pushState({page:url}, document.title, new_url);
		
		$contentInner.fadeOut(200, function() {

			$contentInner.hide().empty();
			window.scrollTo(0,0);

			$loadOverlay.fadeIn(200, function() {
				$contentInner.load(url, function(data, textStatus, jqXHR) {

					if(textStatus == 'error') {
						$contentInner.html("<h1>Fehler</h1><p>Die Seite konnte nicht geladen werden (Fehler bei DurchfÃ¼hrung des AJAX-Requests).</p>");
						$loadOverlay.fadeOut(200, function() {
							$contentInner.fadeIn(200, function() {
								document.title = 'Fehler - ' + pageTitle;
							});
						});
					} else {
						$contentInner.waitForImages(function() {
							$loadOverlay.fadeOut(200, function() {
								$contentInner.fadeIn(200, function() {
									var newTitle = '';
									newTitle = $('#smar-content-inner').find('h1').first().text();
									document.title = newTitle + ' - ' + pageTitle;
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
		
		loadPage(newTarget);	
	}
});

// handler for page navigation
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



////////////////////////////////////
// STARTUP                        //
////////////////////////////////////

history.pushState({page:window.location.href}, document.title, window.location.href);
