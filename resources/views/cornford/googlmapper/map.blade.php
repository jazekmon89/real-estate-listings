<div id="map-canvas-{!! $id !!}" style="width: 100%; height: 100%; margin: 0; padding: 0; position: relative; overflow: hidden;"></div>

<script type="text/javascript">

	var maps = [];
	window.overlay_markers = [];
	window.overlay_markers_index = [];
	function showVisibleMarkers() {
		var bounds = window.map_{!! $id !!}.getBounds();
		if(window.polygonFlag){
			for (var i = 0; i < window.markers.length; i++) {
				if(bounds.contains(window.markers[i].getPosition())===true && window.markers[i].getMap() === null && $.inArray(i, window.overlay_markers_index) !== -1)
					window.markers[i].setMap(window.map_{!! $id !!})
				else if(bounds.contains(window.markers[i].getPosition())!==true && $.inArray(i, window.overlay_markers_index) !== -1)
					window.markers[i].setMap(null);
			}
		}else{
			for (var i = 0; i < window.markers.length; i++) {
				if(bounds.contains(window.markers[i].getPosition())===true && window.markers[i].getMap() === null)
					window.markers[i].setMap(window.map_{!! $id !!})
				else if(bounds.contains(window.markers[i].getPosition())!==true)
					window.markers[i].setMap(null);
			}
			updateResultCount(window.markers.length);
		}
	}

	function initInfoWindowDetails(){
		$(".marker-read-more").on("click", function(){
			$('a[href="#details"]').trigger('click');
			//$("#listings-table_filter input").val($(".listing-more-details").val());
			//$("#listings-table").DataTable().search($(".listing-more-details").val()).draw();
			window.search_flag = true;
			$(".filters .infoWindowSearch").val($(".listing-more-details").val());
			$('#listings-table').DataTable().ajax.reload();
			//$("#listings-table").DataTable().columns(0).search($(".listing-more-details").val()).draw();
		});
	}
	function updateResultCount(count){
		$(".results-count").html(count+" results.");
	}
	function restoreMarkers(){
		if(window.overlays.length > 0){
			initOverlays(false);
		}else{
			window.polygonFlag = false;
			showVisibleMarkers();
			window.overlay_markers = [];
			window.overlay_markers_index = [];
			$(".array").val('');
			if(!$.isEmptyObject(createData(true)))
				$('#listings-table').DataTable().ajax.reload();
		}
	}
	function initOverlays(searchCounterFlag){
		if(window.overlays.length > 0){
			var radius = null;
			for(index in window.overlays){
				window.overlays[index].overlay.setMap(map_{!! $id !!});
				radius = null;
				if(window.overlays[index].type == "circle")
					radius = window.overlays[index].overlay.getRadius();
				for (var i = 0; i < window.markers.length; i++) {
					if(window.overlays[index].type == "circle"){
						if(google.maps.geometry.spherical.computeDistanceBetween(window.overlays[index].overlay.getCenter(),window.markers[i].getPosition()) > radius)
							window.markers[i].setMap(null);
						if(google.maps.geometry.spherical.computeDistanceBetween(window.overlays[index].overlay.getCenter(),window.markers[i].getPosition()) <= radius && $.inArray(i, window.overlay_markers_index) === -1){
							window.overlay_markers.push(window.markers[i].markerid);
							window.overlay_markers_index.push(i);
						}
					}else if(window.overlays[index].type =='rectangle'){
						if(window.overlays[index].overlay.getBounds().contains(window.markers[i].getPosition())!==true)
							window.markers[i].setMap(null);
						if(window.overlays[index].overlay.getBounds().contains(window.markers[i].getPosition())===true && $.inArray(i, window.overlay_markers_index) === -1){
							window.overlay_markers.push(window.markers[i].markerid);
							window.overlay_markers_index.push(i);
						}
					}else{
						if(google.maps.geometry.poly.containsLocation(window.markers[i].getPosition(), window.overlays[index].overlay)!==true)
							window.markers[i].setMap(null);
						if(google.maps.geometry.poly.containsLocation(window.markers[i].getPosition(), window.overlays[index].overlay)===true && $.inArray(i, window.overlay_markers_index) === -1){
							window.overlay_markers.push(window.markers[i].markerid);
							window.overlay_markers_index.push(i);
						}
					}
				}
			}
			updateResultCount(window.overlay_markers_index.length);
		}else
			updateResultCount(window.markers.length);
		if(typeof window.markers !== 'undefined' && window.markers.length > 0){
			var oldCenterFlag = false;
			for (var i = 0; i < window.markers.length; i++) {
				if(!oldCenterFlag && window.oldCenter == window.markers[i].getPosition())
					oldCenterFlag = true;
			}
			if(!oldCenterFlag && typeof window.markers !== 'undefined' && window.markers.length > 0){
				for (var i = 0; i < window.markers.length; i++)
					if(window.markers[i].getMap() !== null)
						window.oldCenter = window.markers[i].getPosition();
			}
		}
		if(searchCounterFlag && window.searchcounter > 1 || window.overlays.length > 0){
			window.map_{!! $id !!}.setZoom(window.oldZoomLevel);
			window.map_{!! $id !!}.setCenter(window.oldCenter);
		}
	}

	function initialize_{!! $id !!}() {
		var bounds = new google.maps.LatLngBounds();
		var infowindow = new google.maps.InfoWindow();
		var position = new google.maps.LatLng({!! $options['latitude'] !!}, {!! $options['longitude'] !!});

		var mapOptions_{!! $id !!} = {
			@if ($options['center'])
				center: position,
			@endif
			mapTypeId: google.maps.MapTypeId.{!! $options['type'] !!},
			disableDefaultUI: @if (!$options['ui']) true @else false @endif,
			scrollwheel: @if ($options['scrollWheelZoom']) true @else false @endif,
			fullscreenControl: @if ($options['fullscreenControl']) true @else false @endif,
			panControl: true,
			panControlOptions: {
			  position: google.maps.ControlPosition.TOP_RIGHT
			},
			zoomControl: true,
			zoomControlOptions: {
			  style: google.maps.ZoomControlStyle.LARGE,
			  position: google.maps.ControlPosition.TOP_RIGHT
			},
			scaleControl: true,
		};

		window.map_{!! $id !!} = new google.maps.Map(document.getElementById('map-canvas-{!! $id !!}'), mapOptions_{!! $id !!});
		map_{!! $id !!}.setTilt({!! $options['tilt'] !!});

		window.markers = [];
		var infowindows = [];
		var shapes = [];

		@foreach ($options['markers'] as $key => $marker)
			{!! $marker->render($key, $view) !!}
		@endforeach

		@foreach ($options['shapes'] as $key => $shape)
			{!! $shape->render($key, $view) !!}
		@endforeach

		@if ($options['overlay'] == 'BIKE')
			var bikeLayer = new google.maps.BicyclingLayer();
			bikeLayer.setMap(map_{!! $id !!});
		@endif

		@if ($options['overlay'] == 'TRANSIT')
			var transitLayer = new google.maps.TransitLayer();
			transitLayer.setMap(map_{!! $id !!});
		@endif

		@if ($options['overlay'] == 'TRAFFIC')
			var trafficLayer = new google.maps.TrafficLayer();
			trafficLayer.setMap(map_{!! $id !!});
		@endif

		var idleListener = google.maps.event.addListenerOnce(map_{!! $id !!}, "idle", function () {
			map_{!! $id !!}.setZoom({!! $options['zoom'] !!});

			@if (!$options['center'])
				map_{!! $id !!}.fitBounds(bounds);
			@endif

			@if ($options['locate'])
				if (typeof navigator !== 'undefined' && navigator.geolocation) {
					navigator.geolocation.getCurrentPosition(function (position) {
						map_{!! $id !!}.setCenter(new google.maps.LatLng(position.coords.latitude, position.coords.longitude));
					});
				}
			@endif

			@if (array_key_exists('triggerSetOldCenter', $options) && $options['triggerSetOldCenter'])
				if(window.oldZoomLevel !== null && window.oldCenter !== null){
					window.map_0.setZoom(window.oldZoomLevel);
					window.map_0.setCenter(window.oldCenter);
				}
			@endif
			updateResultCount((typeof window.markers === 'undefined'?0:(typeof window.overlay_markers_index === 'undefined'?window.markers.length:window.overlay_markers_index.length)));
		});

		@if (isset($options['controlMarkerBounds']))
			google.maps.event.addListener(map_{!! $id !!}, 'idle', function() {
				{!! $options['controlMarkerBounds'] !!}
			});
		@endif

        var map = map_{!! $id !!};

		@if (isset($options['eventBeforeLoad']))
			{!! $options['eventBeforeLoad'] !!}
		@endif

		@if (isset($options['eventAfterLoad']))
			google.maps.event.addListenerOnce(map_{!! $id !!}, "tilesloaded", function() {
				{!! $options['eventAfterLoad'] !!}
			});
		@endif

		@if ($options['cluster'])
			var markerClusterOptions = {
				imagePath: '{!! $options['clusters']['icon'] !!}',
				gridSize: {!! $options['clusters']['grid'] !!},
				maxZoom: @if ($options['clusters']['zoom'] === null) null @else {!! $options['clusters']['zoom'] !!} @endif,
				averageCenter: @if ($options['clusters']['center'] === true) true @else false @endif,
				minimumClusterSize: {!! $options['clusters']['size'] !!}
			};
			var markerCluster = new MarkerClusterer(map_{!! $id !!}, markers, markerClusterOptions);
		@endif

		window.drawingManager = new google.maps.drawing.DrawingManager({
          drawingControl: true,
          drawingControlOptions: {
            position: google.maps.ControlPosition.BOTTOM_CENTER,
            drawingModes: ['rectangle', 'circle', 'polygon']
          },
          circleOptions: {
            //fillColor: '#ffff00',
            //fillOpacity: 1,
            //strokeWeight: 5,
            clickable: false,
            //editable: true,
            zIndex: 1
          }
        });
        window.drawingManager.setMap(map_{!! $id !!});

        /*
         * For drawing, callback events.
         * Reference: https://developers.google.com/maps/documentation/javascript/drawinglayer
         */
        google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
        	window.polygonFlag = true;
			var radius = null;
			event['id'] = window.overlaycounter;
			window.overlaycounter++;
			window.overlays.push(event);
			if(event.type == "circle"){
				radius = event.overlay.getRadius();
				$("#polygonsWrapper").append("<div class='polygon-items'>Circle <span class='glyphicon glyphicon-remove-sign' polygon-id='"+event.id+"'></span></div>");
			}else if(event.type =='rectangle')
				$("#polygonsWrapper").append("<div class='polygon-items'>Rectangle <span class='glyphicon glyphicon-remove-sign' polygon-id='"+event.id+"'></span></div>");
			else
				$("#polygonsWrapper").append("<div class='polygon-items'>Shape <span class='glyphicon glyphicon-remove-sign' polygon-id='"+event.id+"'></span></div>");
			$("#polygonsWrapper .polygon-items span").off();
			$("#polygonsWrapper .polygon-items span").on("click", function(){
				for(index in window.overlays){
					if(window.overlays[index].id == $(this).attr('polygon-id')){
						window.overlays[index].overlay.setMap(null);
						window.overlays.splice(index, 1);
					}
				}
				$(this).off();
				$(this).closest('.polygon-items').remove();
				if(typeof window.markers.length !== 'undefined' && window.markers.length > 0)
					restoreMarkers();
			});
			for (var i = 0; i < window.markers.length; i++) {
				if(event.type == "circle"){
					if(google.maps.geometry.spherical.computeDistanceBetween(event.overlay.getCenter(),window.markers[i].getPosition()) <= radius && window.markers[i].getMap() === null){
						window.markers[i].setMap(window.map_{!! $id !!});
						window.overlay_markers.push(window.markers[i].markerid);
						window.overlay_markers_index.push(i);
					}else if(google.maps.geometry.spherical.computeDistanceBetween(event.overlay.getCenter(),window.markers[i].getPosition()) > radius && $.inArray(i, window.overlay_markers_index) === -1)
						window.markers[i].setMap(null);
					if(google.maps.geometry.spherical.computeDistanceBetween(event.overlay.getCenter(),window.markers[i].getPosition()) <= radius && $.inArray(i, window.overlay_markers_index) === -1){
						window.overlay_markers.push(window.markers[i].markerid);
						window.overlay_markers_index.push(i);
					}
				}else if(event.type =='rectangle'){
					if(event.overlay.getBounds().contains(window.markers[i].getPosition())===true && window.markers[i].getMap() === null){
						window.markers[i].setMap(window.map_{!! $id !!});
						window.overlay_markers.push(window.markers[i].markerid);
						window.overlay_markers_index.push(i);
					}else if(event.overlay.getBounds().contains(window.markers[i].getPosition())!==true && $.inArray(i, window.overlay_markers_index) === -1)
						window.markers[i].setMap(null);
					if(event.overlay.getBounds().contains(window.markers[i].getPosition())===true && $.inArray(i, window.overlay_markers_index) === -1){
						window.overlay_markers.push(window.markers[i].markerid);
						window.overlay_markers_index.push(i);
					}
				}else{
					if(google.maps.geometry.poly.containsLocation(window.markers[i].getPosition(), event.overlay)===true && window.markers[i].getMap() === null){
						window.markers[i].setMap(window.map_{!! $id !!});
						window.overlay_markers.push(window.markers[i].markerid);
						window.overlay_markers_index.push(i);
					}else if(google.maps.geometry.poly.containsLocation(window.markers[i].getPosition(), event.overlay)!==true && $.inArray(i, window.overlay_markers_index) === -1)
						window.markers[i].setMap(null);
					if(google.maps.geometry.poly.containsLocation(window.markers[i].getPosition(), event.overlay)===true && $.inArray(i, window.overlay_markers_index) === -1){
						window.overlay_markers.push(window.markers[i].markerid);
						window.overlay_markers_index.push(i);
					}
				}
			}
			if(typeof window.markers !== 'undefined' && window.markers.length > 0){
				updateResultCount(window.overlay_markers_index.length);
				$(".array").val(window.overlay_markers.join(','));
				$('#listings-table').DataTable().ajax.reload();
			}else
				updateResultCount(0);
		});

		maps.push({
			key: {!! $id !!},
			markers: markers,
			infowindows: infowindows,
			map: map_{!! $id !!},
			shapes: shapes
		});

		var input = document.getElementById('searchTextField');
		var autocomplete = new google.maps.places.Autocomplete(input);
		autocomplete.addListener('place_changed', function() {
			var place = autocomplete.getPlace();
			if (!place.geometry) {
				return;
			}
			// If the place has a geometry, then present it on a map.
			if (place.geometry.viewport) {
				map_{!! $id !!}.fitBounds(place.geometry.viewport);
			} else {
				map_{!! $id !!}.setCenter(place.geometry.location);
				//map_{!! $id !!}.setZoom(17);  // Why 17? Because it looks good.
			}
		});
	}

    @if (!$options['async'])

    	var interval = setInterval(function(){
			if(typeof google !== 'undefined'){
				initialize_0();
				window.infoWindow = new google.maps.InfoWindow();
				window.infoWindow.setContent("<div class='marker-loading'></div>");
				clearInterval(interval);
			}
		}, 200);
	    //google.maps.event.addDomListener(window, 'load', initialize_{!! $id !!});

    @endif

</script>