function hgmCodeAddress(){
	var address = document.getElementById("hgm-address").value;
	hgmGeocoder.geocode({'address': address},function(results,status){
		if(status == google.maps.GeocoderStatus.OK){
			var latLongResult = results[0].geometry.location;
			hgmGeoMap.setCenter(latLongResult);
			marker = new google.maps.Marker({
				map: hgmGeoMap, 
				position: latLongResult
			});
			//Populate location result
			hgmLocation = String(latLongResult);
			hgmLocation = hgmLocation.replace("(","").replace(")","").replace(" ","");
			document.getElementById("hgm-latlong").value = hgmLocation;
			eventObject.dispatchHgmEvent('geocoded');
		}
		else alert("Geocode Failed: " + status);
	});
}
		
/* ~~~~~~~~~ Events ~~~~~~~~~ */

//Geocoded Event
hgmCreateEvent('geocoded');

//Dispatcher	
function hgmEventDispatcher(){ this.events = []; }
//Event Listener
hgmEventDispatcher.prototype.addEventlistener = function(event,callback){
	this.events[event] = this.events[event] || [];
	if(this.events[event]) this.events[event].push(callback);
}
//Remove Event Listener
hgmEventDispatcher.prototype.removeEventlistener = function(event,callback){
	if(this.events[event]){
		var listeners = this.events[event];
		for(var i = listeners.length-1; i>=0; --i ){
			if(listeners[i] === callback){
				listeners.splice(i,1);
				return true;
			}
		}
	}
	return false;
}
//Dispatch Event Prototype
hgmEventDispatcher.prototype.dispatch = function(event){
	if(this.events[event]){
		var listeners = this.events[event],len = listeners.length;
		while(len--){ listeners[len](this);	}		
	}
}
//Create Event
function hgmCreateEvent(eventName){
	hgmEventObject.prototype = new hgmEventDispatcher();
	hgmEventObject.prototype.dispatchHgmEvent = function(eventName){ this.dispatch(eventName); }
	eventObject = new hgmEventObject();
	function hgmEventObject(){ hgmEventDispatcher.call(this); }
}