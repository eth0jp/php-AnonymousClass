<?php

require_once 'AnonymousClass.php';

$moduleA = AnonymousClass::create(function($self) {
	$self->methodA = function($self) {
		echo "call: moduleA->methodA()\n";
	};

	$self->methodB = function($self) {
		echo "call: moduleA->methodB()\n";
	};
});

$moduleB = AnonymousClass::create(function($self) {
	$self->methodB = function($self) {
		echo "call: moduleB->methodB()\n";
	};

	$self->methodC = function($self) {
		echo "call: moduleB->methodC()\n";
	};
});

$classA = AnonymousClass::create(function($self) {
	global $moduleA, $moduleB;
	$self->extend($moduleA());
	$self->extend($moduleB());

	$self->methodC = function($self) {
		echo "call: classA->methodC()\n";
	};

	$self->methodD = function($self) {
		echo "call: classA->methodD()\n";
	};
});

$obj = $classA();
$obj->methodA();
$obj->methodB();
$obj->methodC();
$obj->methodD();


/*
# Ruby

module ModuleA
  def methodA
    puts "call: ModuleA.methodA()"
  end

  def methodB
    puts "call: ModuleA.methodB()"
  end
end

module ModuleB
  def methodB
    puts "call: ModuleB.methodB()"
  end

  def methodC
    puts "call: ModuleB.methodC()"
  end
end

class ClassA
  include ModuleA
  include ModuleB

  def methodC
    puts "call: ClassA.methodC()"
  end

  def methodD
    puts "call: ClassA.methodD()"
  end
end

obj = ClassA.new
obj.methodA
obj.methodB
obj.methodC
obj.methodD
*/
