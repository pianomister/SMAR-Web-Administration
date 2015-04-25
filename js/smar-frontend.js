
////////////////////////////////////
// NAVIGATION FUNCTIONALITY       //
////////////////////////////////////

var pageTitle = 'SMAR Web Administration';

// for navigating back, track the onpopstate event
window.onpopstate = function(event) {
	if(event.state.page) {
		loadPage(event.state.page, true, false, true);
		console.log(event.state.navId);
		if(event.state.navId)
			setMainNav(event.state.navId);
	} else {
		// TODO: load first url
	}
}

function loadPage(url, loadFull, navMainId, skipHistory) {
	
	loadFull = loadFull || false;
	navMainId = navMainId || false;
	skipHistory = skipHistory || false;

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
		
		if(!skipHistory)
			history.pushState({page:url,navId:navMainId}, document.title, url);
		
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
									linkHandler();
								});
							});
						});
					}
				});
			});
		});
	}
}

function setMainNav(target) {
	
	var $target = $('#'+target);
	
	$('nav#nav-main a.smar-active').removeClass('smar-active');
	$target.addClass('smar-active');
}



////////////////////////////////////
// EVENT HANDLER                  //
////////////////////////////////////

// handler for main navigation
function mainNavHandler() {
	$('nav#nav-main a').on('click', function(e) {

		e.preventDefault();
		$target = $(e.delegateTarget);

		// only change page if not current page is selected
		if( !$target.hasClass('smar-active') ) {

			var newTarget = $target.attr('href');

			// toggle active status
			setMainNav($target.attr('id'));

			loadPage(newTarget, true, $target.attr('id'));	
		}
	});
}


// handler for page navigation
function subNavHandler() {
	$('nav#nav-page a').on('click', function(e) {

		e.preventDefault();
		$target = $(e.delegateTarget);

		var newTarget = $target.attr('href');

		// toggle active status
		$('nav#nav-page a.smar-active').removeClass('smar-active');
		$target.addClass('smar-active');

		loadPage(newTarget, false, $('nav#nav-main a.smar-active').attr('id'));
	});
}


// handler for AJAX links in page content
function linkHandler() {
	$('.ajax').on('click', function(e) {
		e.preventDefault();
		$target = $(e.delegateTarget);

		var newTarget = $target.attr('href');
		var loadFull = $target.hasClass('ajax-full');
		
		loadPage(newTarget, loadFull, $('nav#nav-main a.smar-active').attr('id'));
	});
}


// handler for forms with POST request type
function setFormHandler(cssSelector) {
	
	$( cssSelector ).on( 'submit', function( event ) {
		
		event.preventDefault();
		$target = $( this );
		
		var $loadOverlay = $('#smar-loading');
		var methodType = $target.attr('method').toUpperCase();
		var formData = $target.serialize();
		
		attr = $target.attr('data-target');
		if(typeof attr !== typeof undefined && attr !== false)
			var $targetContainer = $(attr);
		else
			var $targetContainer = $('#smar-content-inner');
		
		var $formSubmit = $target.find('input[type="submit"]');
		if($formSubmit.attr('name') != 'undefined') {
			formData += '&' + $formSubmit.attr('name') + '=' + $formSubmit.attr('value');
		}
		
		$loadOverlay.fadeIn(100, function() {
			$targetContainer.fadeOut(100, function() {
				console.log($target.attr('action').split('page=')[1] + methodType);//TODO
				
				postUrl = decodeURIComponent( $target.attr('action').split('page=')[1] );
				
				if(methodType == "POST")
					postUrl += '&smar_include=true';
				else if(methodType == "GET")
					formData += '&smar_include=true';
				
				$targetContainer.hide().empty();
				window.scrollTo(0,0);

				$.ajax({
					method: methodType,
					url: postUrl,
					data: formData
				}).done(function (data) {
					$loadOverlay.fadeOut(100, function() {
						$targetContainer.html( data );
						$targetContainer.fadeIn(100, function() {
							linkHandler();
						});
					});
				}).fail(function(e) {
					console.error(e);
				});
			});
		});
	});
}


////////////////////////////////////
// STARTUP                        //
////////////////////////////////////

$(document).ready(function() {
	
	history.pushState({page:window.location.href}, document.title, window.location.href);

	mainNavHandler();
	subNavHandler();
	linkHandler();

});