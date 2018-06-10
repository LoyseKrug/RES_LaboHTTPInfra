$(function(){
	console.log("Loading destinations");
	
	function loadDestinations(){
		$.getJSON("/api/futur/destination/", function( destinations){
			console.log(destinations);
			var message = "Next stop : " + destinations[0].destination + " !";
			$(".text-faded").text(message);
		}); 	
	};
	
	loadDestinations();
	setInterval(loadDestinations, 2000);
});

