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
