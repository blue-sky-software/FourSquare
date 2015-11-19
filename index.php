<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>FourSquare</title>
    <link type="text/css" rel="stylesheet" href="style.css">
  </head>
  <body>
	<!-- Panel -->
	<div id="panel">
		<input id="chk0" type="checkbox"/><label>Arts & Entertainment</label> 
		<br>
		<input id="chk1" type="checkbox"/><label>Food</label> 
		<br>
		<input id="chk2" type="checkbox"/><label>Nightlife Spot</label> 
		<br>
		<input id="chk3" type="checkbox"/><label>Outdoors & Recreation</label> 
		<br>
		<input id="chk4" type="checkbox"/><label>Shop & Service</label> 
		<br>
		<input id="chk5" type="checkbox"/><label>Travel & Transport</label> 
		<br>
		<input id="chk6" type="checkbox"/><label>College & Universities</label> 
		<br>
		<input id="chk7" type="checkbox"/><label>Professional & Other places</label> 
		<br>
		<input id="chk8" type="checkbox"/><label>Residence</label> 
		<br>
		<br>
		<label class="title">Limit(K):</label>
		<br>
		<input type="range" min="0" max="50" step="1" value="25" style="width:100px;" onchange="updateTextInput(this.value, 'limit');"> 
		<br>
		<input type="text" id="limit" value="" disabled style="width:100px;"/>
		<br>
		<br>
		<label class="title">Radius(M):</label>
		<br>
		<input type="range" min="0" max="3000" step="100" value="1500" style="width:100px;" onchange="updateTextInput(this.value, 'radius');"> 
		<br>
		<input type="text" id="radius" value="" disabled style="width:100px;"/>
		<br>
		<input type="submit" id="submit" value="submit" onclick="onSubmit();">
		<input type="hidden" name="lat" id="lat" value="52">
		<input type="hidden" name="lng" id="lng" value="21">
	</div>
	<!-- Map -->
	<div id="map"/>

	<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places"></script>

	<script type="text/javascript">
	function updateTextInput(val, target) {
		document.getElementById(target).value=val; 
	}

	var is_allcheck = 1;
	var map, beachMarker, venueList = new Array();

	function initMap() {
		var myLatlng = new google.maps.LatLng(
			document.getElementById('lat').value,
			document.getElementById('lng').value);
		map = new google.maps.Map(document.getElementById('map'), {
		zoom: 12,
		center: myLatlng
		});

		document.getElementById('limit').value = 25;
		document.getElementById('radius').value = 1500;

		beachMarker = new google.maps.Marker({
			position: myLatlng,
			map: map,
			//draggable:true
			});

		google.maps.event.addListener(map, 'click', function(event) {
			clearMarker(1);
			beachMarker = new google.maps.Marker({
			  position: event.latLng,
			  map: map
			});

			document.getElementById('lat').value = event.latLng.lat();
			document.getElementById('lng').value = event.latLng.lng();
		});
	}

	function clearMarker(all) {
		if(all === 1) {
			beachMarker.setMap(null);
		}

		for(var i = 0; i < venueList.length; i++) {
			venueList[i].setMap(null);
		}
	}

	function drawVenues(venues) {
		for(var i = 0; i < venues.length; i++) {
			var pos = new google.maps.LatLng(venues[i].location.lat, venues[i].location.lng);
			var path = venues[i].categories[0].icon.prefix + 'bg_32' + venues[i].categories[0].icon.suffix;
			var marker = new google.maps.Marker({
			position: pos,
			map: map,
			icon: path
			});
			if(is_allcheck === 0) {
				var address = "";
				if(typeof venues[i].location.address != "undefined") {
					address = "<br>" + venues[i].location.address;
				}
				var contentString = "<div><b>" + venues[i].name + "</b>" + address +"<br>" + venues[i].location.lat + "<br>" + venues[i].location.lng + "<br></div>";
				var infowindow = new google.maps.InfoWindow({content:contentString});
				infowindow.open(map,marker);
			}
			venueList.push(marker);
		}
	}

	function onSubmit() {
		
		var ll = getll();
		var limit = document.getElementById('limit').value;
		var radius = document.getElementById('radius').value;
		var catId = getcategorId();

		var url = "https://api.foursquare.com/v2/venues/search";
		url += "?ll=" + ll;
		url += "&intent=browse&oauth_token=";
		url += "&limit=" + limit;
		url += "&radius=" + radius;
		url += "&categoryId=" + catId;

		clearMarker(0);
		$.get( url, function( data ) {
			$( ".result" ).html( data );
			if(data.meta.code === 200) {
				drawVenues(data.response.venues);
			}
		});
	}

	function getll() {
		return document.getElementById('lat').value + ',' + document.getElementById('lng').value;
	}

	function getcategorId()
	{
		var ret = '';
		var list = ["4d4b7104d754a06370d81259", 
					"4d4b7105d754a06374d81259",
					"4d4b7105d754a06376d81259",
					"4d4b7105d754a06377d81259",
					"4d4b7105d754a06378d81259",
					"4d4b7105d754a06379d81259",
					"4d4b7105d754a06372d81259",
					"4d4b7105d754a06375d81259",
					"4e67e38e036454776db1fb3a"];
		for(var i = 0; i < 9; i++) {
			if(document.getElementById("chk"+i).checked === true) {
				if(ret !== '') ret += ',';
				ret += list[i];
			}
		}

		if(ret === '')
		{
			is_allcheck = 1;
			for(var i = 0; i < 9; i++) {
				if(ret !== '') ret += ',';
				ret += list[i];
			}
		} else {
			is_allcheck = 0;
		}

		return ret;
	
	}

	$(document).ready(function(){
    	initMap();
	});

	</script>
	</body>
</html>