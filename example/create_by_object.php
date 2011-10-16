<?php

require_once 'AnonymousClass.php';

class Calc1
{
	private $_value = 0;

	public function add($arg)
	{
		$this->_value += $arg;
		return $this->_value;
	}
}

class Calc2
{
	private $_value = 0;

	public function sub($arg)
	{
		$this->_value -= $arg;
		return $this->_value;
	}
}

$calc = AnonymousClass::create();
$calc->extend(AnonymousClass::createByObject(new Calc1()));
$calc->extend(AnonymousClass::createByObject(new Calc2()));

echo $calc->add(20)."\n";
echo $calc->sub(5)."\n";
echo $calc->add(10)."\n";
echo $calc->sub(15)."\n";


/*
# Ruby

module Calc1
  def add(arg)
    @_value = (@_value || 0) + arg
  end
end

module Calc2
  def sub(arg)
    @_value = (@_value || 0) - arg
  end
end

calc = Class.new
calc.extend(Calc1)
calc.extend(Calc2)

puts calc.add(20)
puts calc.sub(5)
puts calc.add(10)
puts calc.sub(15)
*/
