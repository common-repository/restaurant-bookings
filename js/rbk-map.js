(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
(function($) {
	$('.map-container').each( function ( index ) {
		var el = $(this);
		var lat = el.data('lat');
		var lng = el.data('lng');
		var tit = $('.entry-title').html();
		var myLatLng = new google.maps.LatLng(lat, lng);
		
		var mapOptions = {
			zoom: el.data('zoom'),
			center: myLatLng,
			mapTypeControl: false,
			streetViewControl: false,
			zoomControl: true,
			zoomControlOptions: {
				style: google.maps.ZoomControlStyle.SMALL,
				position: google.maps.ControlPosition.RIGHT_TOP
			},
			styles: [{
				featureType: "poi.business",
				elementType: "labels",
				stylers: [{ 
					visibility: "off" 
				}]
			}]
		};
		
		var map = new google.maps.Map(this, mapOptions);
		
		var marker = new google.maps.Marker({
			position: myLatLng,
			map: map,
			title: el.data('title')
		});
		
		/* Esto deberia funcionar pero no se ha probado aqui
		if (el.data("viewport")) {
			var vp = $map.data("viewport");
			map.fitBounds(new google.maps.LatLngBounds(vp.southwest, vp.northeast));
		}
		*/
	});
})(jQuery);

},{}]},{},[1])

//# sourceMappingURL=rbk-map.js.map
