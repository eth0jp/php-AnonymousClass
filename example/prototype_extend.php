<?php

require_once 'AnonymousClass.php';

$classA = AnonymousClass::create(function($self) {
	$self->methodA = function($self) {
		echo "call: classA->methodA()\n";
	};

	$self->methodB = function($self) {
		echo "call: classA->methodB()\n";
	};
});

$classB = AnonymousClass::create(function($self) {
	$self->methodB = function($self) {
		echo "call: classB->methodB()\n";
	};

	$self->methodC = function($self) {
		echo "call: classB->methodC()\n";
	};
});
$classB->prototype = $classA();

$obj = $classB();
$obj->methodA();
$obj->methodB();
$obj->methodC();


/*
// JavaScript

var classA = function() {
	this.methodA = function() {
		console.log("call: classA.methodA()");
	};

	this.methodB = function() {
		console.log("call: classA.methodB()");
	};
};

var classB = function() {
	this.methodB = function() {
		console.log("call: classB.methodB()");
	};

	this.methodC = function() {
		console.log("call: classB.methodC()");
	};
};
classB.prototype = new classA();

var obj = new classB();
obj.methodA();
obj.methodB();
obj.methodC();
*/
