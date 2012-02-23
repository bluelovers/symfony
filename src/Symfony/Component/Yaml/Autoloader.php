<?php

class Symfony_Component_Yaml_Autoloader
{
	/**
	 * Registers sfTemplateAutoloader as an SPL autoloader.
	 */
	static public function register()
	{
		ini_set('unserialize_callback_func', 'spl_autoload_call');
		spl_autoload_register(array(new self, 'autoload'));
	}

	/**
	 * Handles autoloading of classes.
	 *
	 * @param  string  $class  A class name.
	 *
	 * @return boolean Returns true if the class has been loaded
	 */
	public function autoload($class)
	{
		if (0 !== strpos($class, 'Symfony_Component_Yaml_'))
		{
			return false;
		}

		require dirname(__FILE__) . '/' . $class . '.php';

		return true;
	}
}
