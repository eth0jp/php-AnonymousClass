<?php

require_once 'AnonymousClass/Exception.php';

class AnonymousClass
{
	protected $_construct = null;
	protected $_properties = null;
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
		$this->_properties = array();
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
		// constructor
		$construct = $this->_construct;

		// constructor arguments
		$arguments = func_get_args();
		$self = new self(null, $this);
		array_unshift($arguments, $self);

		// call constructor
		call_user_func_array($construct, $arguments);

		return $self;
	}

	/**
	 * extend
	 *
	 * Add $module properties to $this.
	 * This method is similar to ruby's include/extend method.
	 *
	 * @param AnonymousClass $module
	 *
	 * @return AnonymousClass
	 */
	public function extend(AnonymousClass $module)
	{
		// extend task
		$task = array();
		while ($module) {
			array_unshift($task, $module);
			$module = $module->_prototype;
		}

		// extend
		foreach ($task as $module)  {
			foreach ($module->_properties as $key=>$value) {
				$this->$key = $value;
			}
		}

		// call extended
		$extended = null;
		try {
			$extended = $module->extended;
		} catch (AnonymousClass_Exception $e) {
		}
		if (isset($extended)) {
			if (is_callable($extended)) {
				$extended($module, $this);
			} else {
				throw new AnonymousClass_Exception('Call to undefined method: '.get_class($module).'::extended');
			}
		}

		return $this;
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
			$value = $this->$name;
			return true;
		} catch(AnonymousClass_Exception $e) {
		}
		return false;
	}

	/**
	 * hasOwnProperty
	 *
	 * Returns true if the specified property is in the specified object.
	 * Prototype is not evaluated.
	 *
	 * @param $name Property name
	 *
	 * @return bool
	 */
	public function hasOwnProperty($name)
	{
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
			$value = $this->$name;
			return isset($value);
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

	public static function createByObject($object)
	{
		if (!is_object($object)) {
			$func = get_called_class().'::createByObject()';
			throw new AnonymousClass_Exception('Argument 1 passed to '.$func.' must be an instance of some class');
		}
		if ($object instanceof AnonymousClass) {
			return $object;
		}

		$self = new self();
		$object = clone $object;
		$class = new ReflectionObject($object);

		// copy properties
		$properties = $class->getProperties();
		foreach ($properties as $property) {
			$property->setAccessible(true);
			$name = $property->getName();
			$self->$name = null;
			$self->$name = $property->getValue($object);
		}

		// copy methods
		$methods = $class->getMethods();
		foreach ($methods as $method) {
			$name = $method->getName();
			$self->$name = function($self) use($object, $properties, $method) {
				$args = func_get_args();
				$self = array_shift($args);

				// set AnonymousClass to Class
				foreach ($properties as $property) {
					$property->setAccessible(true);
					$name = $property->getName();
					$property->setValue($object, $self->$name);
				}

				// call
				$retval = $method->invokeArgs($object, $args);

				// set Class to AnonymousClass
				foreach ($properties as $property) {
					$property->setAccessible(true);
					$name = $property->getName();
					$self->$name = $property->getValue($object);
				}

				return $retval;
			};
		}

		return $self;
	}
}
