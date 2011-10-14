<?php

require_once 'AnonymousClass.php';


// Animal

$animal = AnonymousClass::create(function($self) {
	$self->cry_count = 0;

	$self->cry = function($self) {
		echo "unknown...\n";
	};
});
$animal->hello = function($self) {
	echo "Hello ".$self->name."!\n";
	$self->cry();
};


// Dog

$dog = AnonymousClass::create(function($self, $name) {
	$self->name = $name;
	$self->cry = function($self) {
		echo "wan!\n";
		$self->cry_count++;
	};
});
$dog->prototype = $animal();

$poti = $dog('pochi');
$poti->hello();


// Cat

$cat = $animal();
$cat->cry = function($self) {
	echo "nyaa!\n";
	$self->cry_count++;
};

$tama = $cat();
$tama->name = "tama";
$tama->hello();


/* JavaScript
// Animal

var animal = function() {
	this.cry_count = 0;

	this.cry = function() {
		console.log("unknown...");
	};
};
animal.prototype.hello = function() {
	console.log("Hello "+this.name);
	this.cry();
};


// Dog

var dog = function(name) {
	this.name = name;
	this.cry = function() {
		console.log("wan!");
		this.cry_count++;
	};
};
dog.prototype = new animal();

var poti = new dog("pochi");
poti.hello();


// Cat

var cat = new animal();
cat.cry = function() {
	console.log("nyaa!");
	this.cry_count++;
};

var tama = (function() {var F=function(){}; F.prototype=cat; return new F();})();
console.log(tama);
tama.name = "tama";
tama.hello();
*/
