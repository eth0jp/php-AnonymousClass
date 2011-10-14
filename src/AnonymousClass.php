<?php

require_once 'AnonymousClass/Exception.php';

class AnonymousClass
{
	protected $_construct = null;
	protected $_properties = array();
	protected $_prototype = null;

	/**
	 * __construct
	 *
	 * Constructor.
	 * Create new AnonymousClass instance.
	 *
	 * @param Closure $construct        Constructor of this class
	 * @param AnonymousClass $prototype Prototype class
	 *
	 * @return AnonymousClass
	 */
	public function __construct(Closure $construct=null, AnonymousClass $prototype=null)
	{
		if (is_null($construct)) {
			$construct = function(){};
		}
		$this->_construct = $construct;
		$this->_prototype = $prototype;
	}

	/**
	 * newInstance
	 *
	 * Creates a new class instance from given arguments.
	 *
	 * @return AnonymousClass
	 */
	public function newInstance()
	{
		$construct = $this->_construct;

		$args = func_get_args();
		$self = new self(null, $this);
		array_unshift($args, $self);

		call_user_func_array($construct, $args);
		return $self;
	}

	/**
	 * hasProperty
	 *
	 * Returns true if the specified property is in the specified object.
	 * Prototype is evaluated.
	 *
	 * @param $name Property name
	 *
	 * @return bool
	 */
	public function hasProperty($name)
	{
		try {
			$val = $this->$name;
			//return isset($val);
			return true;
		} catch(AnonymousClass_Exception $e) {
		}
		return false;
	}

	/**
	 * hasOwnProperty
	 *
	 * Returns a boolean indicating whether the object has the specified property.
	 * Prototype is not evaluated.
	 *
	 * @param $name Property name
	 *
	 * @return bool
	 */
	public function hasOwnProperty($name)
	{
		//return isset($this->_properties[$name]);
		return array_key_exists($name, $this->_properties);
	}

	/**
	 * __get
	 *
	 * Magic method.
	 * Utilized for reading data from inaccessible properties.
	 *
	 * @param $name Property name
	 *
	 * @return mixed $value
	 */
	public function __get($name)
	{
		if ($name=='construct') {
			return $this->_construct;
		}
		if ($name=='prototype') {
			if (is_null($this->_prototype)) {
				$this->_prototype = new self();
			}
			return $this->_prototype;
		}
		$self = $this;
		while ($self) {
			if (array_key_exists($name, $self->_properties)) {
				return $self->_properties[$name];
			}
			$self = $self->_prototype;
		}
		throw new AnonymousClass_Exception('Undefined property: '.get_class($this).'::'.$name);
	}

	/**
	 * __set
	 *
	 * Magic method.
	 * Run when writing data to inaccessible properties. 
	 *
	 * @param $name  Property name
	 * @param $value Value
	 *
	 * @return mixed $value
	 */
	public function __set($name, $value)
	{
		if ($name=='prototype') {
			if ($value instanceof AnonymousClass) {
				$this->_prototype = $value;
				return;
			}
			throw new AnonymousClass_Exception('Value is not an AnonymouseClass instance');
		}
		$this->_properties[$name] = $value;
	}

	/**
	 * __isset
	 *
	 * Magic method.
	 * Triggered by calling isset() or empty() on inaccessible properties.
	 *
	 * @param $name Property name
	 *
	 * @return bool
	 */
	public function __isset($name)
	{
		try {
			$val = $this->$name;
			return isset($val);
		} catch(AnonymousClass_Exception $e) {
		}
		return false;
	}

	/**
	 * __unset
	 *
	 * Magic method.
	 * Invoked when unset() is used on inaccessible properties.
	 *
	 * @param $name Property name
	 *
	 * @return bool
	 */
	public function __unset($name)
	{
		unset($this->_properties[$name]);
	}

	/**
	 * __call
	 *
	 * Magic method.
	 * Triggered when invoking inaccessible methods in an object context.
	 *
	 * @param $name            Method name
	 * @param array $arguments arguments
	 *
	 * @return mixed
	 */
	public function __call($name, array $arguments)
	{
		$func = null;
		try {
			$func = $this->$name;
		} catch(AnonymousClass_Exception $e) {
		}
		if (is_callable($func)) {
			array_unshift($arguments, $this);
			return call_user_func_array($func, $arguments);
		}
		throw new AnonymousClass_Exception('Call to undefined method: '.get_class($this).'::'.$name);
	}

	/**
	 * __invoke
	 *
	 * Magic method.
	 * Creates a new class instance from given arguments.
	 * Alias of AnonymousClass->newInstance().
	 *
	 * @return AnonymousClass
	 */
	public function __invoke()
	{
		$arguments = func_get_args();
		return call_user_func_array(array($this, 'newInstance'), $arguments);
	}

	/**
	 * create
	 *
	 * Create new AnonymousClass instance.
	 * Alias of AnonymousClass->__construct().
	 *
	 * @return AnonymousClass
	 */
	public static function create(Closure $construct=null, AnonymousClass $prototype=null)
	{
		return new self($construct, $prototype);
	}
}
