<?php

require_once 'AnonymousClass.php';

$classA = AnonymousClass::create();
$classA->staticPropertyA = "ABCDEF";
$classA->staticMethodA = function($self) {
	echo "call: classA->staticMethodA()\n";
};
$classA->staticMethodB = function($self) {
	echo "call: classA->staticMethodB()\n";
	echo "prop: ".$self->staticPropertyA."\n";
};

echo $classA->staticPropertyA."\n";
$classA->staticMethodA();
$classA->staticMethodB();


/*
// JavaScript

var classA = {};
classA.staticPropertyA = "ABCDEF";
classA.staticMethodA = function() {
	console.log("call: classA.staticMethodA()");
};
classA.staticMethodB = function() {
	console.log("call: classA.staticMethodB()");
	console.log("prop: "+this.staticPropertyA);
};

console.log(classA.staticPropertyA);
classA.staticMethodA();
classA.staticMethodB();
*/
