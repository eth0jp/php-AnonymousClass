<?php

class AnonymousClass_Exception extends Exception
{
	public function __construct($message='', $code=0, Exception $previous=null)
	{
		parent::__construct($message, $code, $previous);

		$anonymousclass_path = dirname(__FILE__).'.php';
		$trace = null;
		foreach ($this->getTrace() as $t) {
			if ($t['file']!=$anonymousclass_path) {
				$class = isset($t['class']) ? $t['class'] : '';
				$function = isset($t['function']) ? $t['function'] : '';
				if ($class!='AnonymousClass' || strpos($function, '__')!==0) {
					$trace = $t;
					break;
				}
			}
		}
		if ($trace) {
			$this->file = $trace['file'];
			$this->line = $trace['line'];
		}
	}
}
