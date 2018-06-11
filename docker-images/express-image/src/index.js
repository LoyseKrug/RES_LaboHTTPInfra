var Chance = require('chance');
var express = require('express');
var ip = require("ip");

var chance = new Chance();

//serveur avec http de node
/*const http = require('http');

http.createServer((request, response) => {
  let body = [];
  request.on('end', () => {
    response.end("The " + chance.date({string: true, american: false}) + " you will probably be a " + chance.profession() + " in "  + chance.country({ full: true }) + " earning " + chance.dollar({max: 1000000}) + "$ per year. Lucky you!"); 
  });
}).listen(80);

*/

//server avec express js

var app = express();

app.get('/', function(req, res){
	res.send(generateFuture());
});


app.get('/destination/', function(req, res){
	res.send(destinations());
});

app.listen(80, function(){
	console.log('Accepting requests on port 80.');
});

function generateFuture(){
	var numberPossibleFuturs = chance.integer({
		min: 1,
		max: 5
	});
	
	console.log(numberPossibleFuturs);
	//on crée un tableau qui va contenir tous nos futurs possibles
	var futurs = [];
	for(var i = 0; i < numberPossibleFuturs; ++i){
		futurs.push({
			hireDate : chance.date({string: true, american: false}),
			profession : chance.profession(),
			country : chance.country({ full: true }),
			salary : chance.dollar({max: 1000000}),
			ip : ip.address()
		});
	};
	console.log(futurs);
	return futurs;
}

function destinations(){
	//on crée un tableau qui va contenir tous nos futurs possibles
	var destinations = [];
	for(var i = 0; i < 10; ++i){
		destinations.push({
			destination : chance.city()
		});
	};
	console.log(destinations);
	return destinations;
}





