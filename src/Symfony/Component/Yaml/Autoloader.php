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

		$_class = str_replace('_', '/', str_replace('Symfony_Component_Yaml_', '', $class));

		require dirname(__FILE__) . '/' . $_class . '.php';

		if (!class_exists($class) && !interface_exists($class) && (!function_exists('trait_exists') || !trait_exists($class))) {
            throw new InvalidArgumentException(sprintf('Unable to load class "%s"', $class));
        }

		return true;
	}
}
