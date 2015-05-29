
////////////////////////////////////
// NAVIGATION FUNCTIONALITY       //
////////////////////////////////////

var pageTitle = 'SMAR Web Administration';
window.path = '/SMAR-Web-Administration/';

function loadPage(url, loadFull, navMainId, skipHistory) {
	
	loadFull = loadFull || false;
	navMainId = navMainId || false;
	skipHistory = skipHistory || false;

	var $content = $('#smar-content'),
		$contentInner = $('#smar-content-inner'),
		$targetContainer = loadFull ? $content : $contentInner,
		$loadOverlay = $('#smar-loading');
	
	if (url.substr(0, 4) === 'http') {
		window.location.href = url;
	} else {
		
		// create parameters for include
		var new_url = url;
		
		if (new_url.indexOf('?') === -1) {
			new_url += '?';
		} else {
			new_url += '&';
		}
		new_url += 'smar_include=true';

		if (loadFull) {
			new_url += '&smar_nav=true';
		}
		
		if (!skipHistory)
			history.pushState({page:url,navId:navMainId}, document.title, url);
		
		$loadOverlay.fadeIn(100, function () {
			$targetContainer.fadeOut(100, function () {
				
				$targetContainer.hide().empty();
				window.scrollTo(0,0);
				
				$targetContainer.load(new_url, function (data, textStatus, jqXHR) {

					if (textStatus === 'error') {
						$targetContainer.html("<h1>Error</h1><p>The page could not be loaded (error during execution of AJAX request).</p>");
						$loadOverlay.fadeOut(100, function () {
							$targetContainer.fadeIn(100, function () {
								document.title = 'Error - ' + pageTitle;
							});
						});
					} else {
						$targetContainer.waitForImages(function () {
							$loadOverlay.fadeOut(200, function () {
								$targetContainer.fadeIn(200, function () {
									var newTitle = '';
									newTitle = $targetContainer.find('h1').first().text();
									document.title = newTitle + ' - ' + pageTitle;
									
									if (loadFull)
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
	
	var $target = $('#' + target);
	$('nav#nav-main a.smar-active').removeClass('smar-active');
	$target.addClass('smar-active');
}


// for navigating back, track the onpopstate event
window.onpopstate = function (event) {
	
	if (event.state && event.state.page) {
		loadPage(event.state.page, true, false, true);
		console.log(event.state.navId);
		if (event.state.navId)
			setMainNav(event.state.navId);
	} else {
		// TODO: load first url
	}
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
	$('nav#nav-page a').on('click', function (e) {

		e.preventDefault();
		var $target = $(e.delegateTarget),
			newTarget = $target.attr('href');
		
		// toggle active status
		$('nav#nav-page a.smar-active').removeClass('smar-active');
		$target.addClass('smar-active');

		loadPage(newTarget, false, $('nav#nav-main a.smar-active').attr('id'));
	});
}


// handler for AJAX links in page content
function linkHandler() {
	$('.ajax').on('click', function (e) {
		
		e.preventDefault();
		var $target = $(e.delegateTarget),
			newTarget = $target.attr('href'),
			loadFull = $target.hasClass('ajax-full');
		
		loadPage(newTarget, loadFull, $('nav#nav-main a.smar-active').attr('id'));
	});
}


// handler for forms with POST and GET request type
function setFormHandler(cssSelector) {
	
	$( cssSelector ).on( 'submit', function( event ) {
		
		event.preventDefault();
		
		var $target = $(this),
			$loadOverlay = $('#smar-loading'),
			methodType = $target.attr('method').toUpperCase(),
			formData = $target.serialize(),
			attr = $target.attr('data-target'),
			$targetContainer;
		
		if (typeof attr !== typeof undefined && attr !== false)
			$targetContainer = $(attr);
		else
			$targetContainer = $('#smar-content-inner');
		
		var $formSubmit = $target.find('input[type="submit"]');
		if ($formSubmit.attr('name') != 'undefined') {
			formData += '&' + $formSubmit.attr('name') + '=' + $formSubmit.attr('value');
		}
		
		$loadOverlay.fadeIn(100, function() {
			$targetContainer.fadeOut(100, function() {
				
				postUrl = decodeURIComponent( $target.attr('action').split('page=')[1] );
				
				if (methodType == "POST")
					postUrl += '&smar_include=true';
				else if (methodType == "GET")
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
				}).fail(function (e) {
					$loadOverlay.fadeOut(100, function() {
						$targetContainer.fadeIn(100, function() {
							console.error(e);
						});
					});
				});
			});
		});
	});
}


// handler for inputs using autocomplete service api
function setAutocompleteHandler(target, table, resultTarget, resultFunction) {
	
	resultTarget = resultTarget || false;
	resultFunction = resultFunction || false; // function expects object parameter with id and name attributes
	var $target = $(target);
	
	$target.autocomplete({
		showNoSuggestionNotice: true,
		lookup: function (search, done) {
			
			searchUrl = 'api/search/' + table + '/' + encodeURI(search) + '?jwt=' + window.loginJWTToken;
			
			if(window.autocomplete) {
				window.autocomplete.abort();
			}
			
			window.autocomplete = $.get(
				searchUrl
			).done(function(data) {
				
				var result = {
					suggestions: $.map(data, function(dataItem) {
						if(typeof dataItem.name !== typeof undefined && dataItem.name !== false) {
							var artnr = '';
							if(typeof dataItem.article_nr !== typeof undefined && dataItem.article_nr !== false)
								artnr = dataItem.article_nr +': ';
							return { value: artnr + dataItem.name, data: dataItem[table + '_id'] };
						}
						return {};
					})
				};
				
				if(result.length == 0 || typeof result.suggestions[0].value === typeof undefined)
					result = {suggestions: []}
				done(result);

			}).fail(function() {
				console.error('Error on autocomplete request');
			});
    },
		onSearchStart: function (query) {
			$(this).css('background-image', 'url(img/ajax-loader.gif)');
		},
		onSearchComplete: function(query, suggestions) {
			$(this).css('background-image', 'none');
		},
    onSelect: function (suggestion) {
			if(resultTarget)
				$(resultTarget).val(suggestion.data);
			if(resultFunction)
				resultFunction({name: suggestion.value, id: suggestion.data});
    }
	});
}


// handler to save unit mappings
function setMappingsSaveHandler(link, type, itemID) {

	$(link).on('click', function(event) {

		event.preventDefault();
		var resultSet = mappings,
			formData = 'data=' + JSON.stringify(resultSet) + '&type=' + type + '&id=' + itemID + '&jwt=' + window.loginJWTToken,
			$loadOverlay = $('#smar-loading');

		$loadOverlay.fadeIn(100, function() {

			postUrl = window.path + 'api/mappings/update/';

			$.ajax({
				method: 'POST',
				url: postUrl,
				data: formData
			}).done(function (data) {
				$loadOverlay.fadeOut(100, function() {
					mappings.forEach(function(m) {
						m.action = 'none';
					});
				});
			}).fail(function(e) {
				$loadOverlay.fadeOut(100, function() {
					console.error(e);
				});
			});
		});
	});
}

// handler for saving changes on shelf designer canvas
function setDesignerSaveHandler(link, canvas, container) {

	$(link).on('click', function(event) {

		event.preventDefault();
		var resultSet = [];

		$containers = $(canvas).find(container);
		$.each($containers, function(index, value) {
			value = $(value);
			resultSet.push({
				section_id: value.attr('data-sectionid'),
				size_x: value.attr('data-sizex'),
				size_y: value.attr('data-sizey'),
				position_x: value.attr('data-x'),
				position_y: value.attr('data-y')
			});
		});

		var formData = 'data=' + JSON.stringify(resultSet) + '&jwt=' + window.loginJWTToken,
			$loadOverlay = $('#smar-loading');

		$loadOverlay.fadeIn(100, function() {

			postUrl = window.path + 'api/designer/update/' + $(canvas).attr('data-shelfid');

			$.ajax({
				method: 'POST',
				url: postUrl,
				data: formData
			}).done(function (data) {
				$loadOverlay.fadeOut(100, function() {
					// TODO evaluate result
				});
			}).fail(function(e) {
				$loadOverlay.fadeOut(100, function() {
					console.error(e);
				});
			});
		});
	});
}


// handler for shelf designer: drag & drop, resize
function setDesignerHandler(canvasID, containerSelector) {
	
	var canvasC = document.getElementById(canvasID);
	
	// target elements
	interact(containerSelector)
	.origin('parent')
  .draggable({
		snap: {
      targets: [
        interact.createSnapGrid({ x: 10, y: 10 })
      ],
      range: Infinity,
      relativePoints: [ { x: 0, y: 0 } ]
    },
    // enable inertial throwing
    inertia: false,
    // keep the element within the area of it's parent
    restrict: {
      restriction: "parent",
      endOnly: true,
      elementRect: { top: 0, left: 0, bottom: 1, right: 1 }
    },

		onstart: function (event) {
			var target = event.target;
			target.style.borderColor = '#107861';
			target.style.backgroundColor = '#16A082';
		},
    // call this function on every dragmove event
    onmove: function (event) {
			var target = event.target,
					// keep the dragged position in the data-x/data-y attributes
					x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx,
					y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

			// translate the element
			target.style.webkitTransform =
			target.style.transform =
				'translate(' + x + 'px, ' + y + 'px)';

			// update the posiion attributes
			target.setAttribute('data-x', x);
			target.setAttribute('data-y', y);
		},
		onend: function(event) {
			var target = event.target;
			target.style.borderColor = '';
			target.style.backgroundColor = '';
		}
  })
	.resizable({
		snap: {
      targets: [
        interact.createSnapGrid({ x: 10, y: 10 })
      ],
      range: Infinity,
      relativePoints: [ { x: 0, y: 0 } ]
    },
    edges: { left: true, right: true, bottom: true, top: true },
		onstart: function (event) {
			var target = event.target;
			target.style.borderColor = '#107861';
			target.style.backgroundColor = '#16A082';
		},
  	onmove: function (event) {
			var target = event.target,
					x = (parseFloat(target.getAttribute('data-x')) || 0),
					y = (parseFloat(target.getAttribute('data-y')) || 0);

			// update the element's style
			target.style.width  = event.rect.width + 'px';
			target.style.height = event.rect.height + 'px';

			// translate when resizing from top or left edges
			x += event.deltaRect.left;
			y += event.deltaRect.top;

			target.style.webkitTransform = target.style.transform =
					'translate(' + x + 'px,' + y + 'px)';

			target.setAttribute('data-x', x);
			target.setAttribute('data-y', y);
			target.setAttribute('data-sizex', event.rect.width);
			target.setAttribute('data-sizey', event.rect.height);
			target.textContent = event.rect.width + '×' + event.rect.height;
		},
		onend: function(event) {
			var target = event.target;
			target.style.borderColor = '';
			target.style.backgroundColor = '';
			target.textContent = event.target.getAttribute('data-sectionid');
		}
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