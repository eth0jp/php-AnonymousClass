<?php

require_once 'AnonymousClass.php';

$calc_cls = AnonymousClass::create(function($self) {
	$total = 0;

	$self->add = function($self, $value) use(&$total) {
		$total += $value;
		return $self;
	};

	$self->sub = function($self, $value) use(&$total) {
		$total -= $value;
		return $self;
	};

	$self->total = function($self) use(&$total) {
		return $total;
	};
});

$calc = $calc_cls();
$calc->add(1);
$calc->add(2);
$calc->add(3)
echo $calc->total()."\n";

echo $calc_cls()->add(10)->add(10)->add(10)->total()."\n";


/*
// JavaScript

var calc_cls = function() {
	var total = 0;

	this.add = function(value) {
		total += value;
		return this;
	};

	this.sub = function(value) {
		total -= value;
		return this;
	};

	this.total = function() {
		return total;
	};
};

var calc = new calc_cls();
calc.add(1);
calc.add(2);
calc.add(3)
console.log(calc.total());

console.log((new calc_cls()).add(10).add(10).add(10).total());
*/
