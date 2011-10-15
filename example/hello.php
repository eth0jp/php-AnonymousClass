<?php

require_once 'AnonymousClass.php';

$hello_cls = AnonymousClass::create(function($self) {
	$self->hello = function($self, $value) {
		echo "Hello ".$value."!\n";
	};
});
$hello_ins = $hello_cls();
$hello_ins->hello("world");


/*
// JavaScript

var hello_cls = function() {
	this.hello = function(value) {
		console.log("Hello "+value+"!");
	};
};
var hello_ins = new hello_cls();
hello_ins.hello("world");
*/
