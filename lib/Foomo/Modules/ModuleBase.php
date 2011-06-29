<?php

/*
 * bestbytes-copyright-placeholder
 */

namespace Foomo\Modules;

/**
 * base class if you want to build your own module
 * and by the way there is a wizard in the backend to create modules
 */
abstract class ModuleBase
{
	//---------------------------------------------------------------------------------------------
	// ~ Constants
	//---------------------------------------------------------------------------------------------

	const VERSION = '0.1.1';

	//---------------------------------------------------------------------------------------------
	// ~ Public static methods
	//---------------------------------------------------------------------------------------------

	/**
	 * include paths - called before the module is initialized
	 *
	 * @return string[]
	 */
	public static function getIncludePaths()
	{
		return array();
	}

	/**
	 * initialize you module here may add some auto loading, will also be called, when switching between modes with Foomo\Config::setMode($newMode)
	 */
	public static function initializeModule()
	{
	}

	/**
	 * describe your module - text only
	 *
	 * @return string
	 */
	public static function getDescription()
	{
		return get_called_class() . ' is a foomo module without a description';
	}

	/**
	 * get a view for an app
	 *
	 * @param mixed $app instance or class name
	 * @param string $template relative path from /path/to/your/module/teplates
	 * @param mixed $model whatever your model may be
	 *
	 * @return Foomo\View
	 */
	public static function getView($app, $template, $model = null)
	{
		if (!file_exists($template)) {
			if(substr($template, -4) != '.tpl') $template .= '.tpl';
			if (is_object($app)) {
				$className = get_class($app);
			} else {
				$className = $app;
			}
			if (strpos($className, '\\') !== false) {
				// we have a namespace - let us prepend it
				$classNameArray = explode('\\', $className);
				$template = implode(DIRECTORY_SEPARATOR, array_slice($classNameArray, 0, count($classNameArray)-1)) . DIRECTORY_SEPARATOR . $template;
			}
			// pick the right directory
			$moduleName = constant(\get_called_class() . '::NAME');
			$template = \Foomo\CORE_CONFIG_DIR_MODULES . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $template;
		}
		return \Foomo\View::fromFile($template, $model);
	}

	/**
	 * get a module translation for an app
	 *
	 * @param mixed $app instance or class name
	 * @param type $localeChain
	 *
	 * @return Foomo\Translation
	 */
	public static function getTranslation($app, $localeChain = null)
	{
		// locale/Foomo/My/App/en.yml
		$calledClassName = get_called_class();
		if(is_object($app)) {
			$namespace = get_class($app);
		} else {
			$namespace = $app;
		}
		return \Foomo\Translation::getModuleTranslation(constant($calledClassName . '::NAME'), $namespace, $localeChain);
	}

	/**
	 * get all the module resources
	 *
	 * @return Resource
	 */
	public static function getResources()
	{
		return array();
	}

	/**
	 * @return string
	 */
	public static function getCacheDir()
	{
		return \Foomo\Config::getCacheDir(self::getModuleName());
	}

	/**
	 * @return string
	 */
	public static function getTempDir()
	{
		return \Foomo\Config::getTempDir(self::getModuleName());
	}

	/**
	 * @return string
	 */
	public static function getVarDir()
	{
		return \Foomo\Config::getVarDir(self::getModuleName());
	}

	/**
	 * @return string
	 */
	public static function getHtdocsVarDir()
	{
		return \Foomo\Config::getHtdocsVarDir(self::getModuleName());
	}

	/**
	 * @return string
	 */
	public static function getLogDir()
	{
		return \Foomo\Config::getLogDir(self::getModuleName());
	}

	/**
	 * @param string $dirname [optional] relative path to subfolder
	 * @return string
	 */
	public static function getBaseDir($dirname='')
	{
		$ret = \Foomo\Config::getModuleDir(self::getModuleName());
		if ($dirname != '') $ret .= DIRECTORY_SEPARATOR . $dirname;
		if (!file_exists($ret)) throw new \Exception('Path ' . $ret . ' does not exist! ');
		return $ret;
	}

	/**
	 * @return string
	 */
	public static function getHtdocsUrl()
	{
		return \Foomo\Config::getHtdocsUrl(self::getModuleName());
	}

	/**
	 * @return string
	 */
	public static function getHtdocsVarUrl()
	{
		return \Foomo\Config::getHtdocsVarUrl(self::getModuleName());
	}

	/**
	 * @param string $name
	 * @param string $domain
	 */
	public static function getConfig($name, $domain='')
	{
		return \Foomo\Config::getConf(self::getModuleName(), $name, $domain);
	}

	//---------------------------------------------------------------------------------------------
	// ~ Private static methods
	//---------------------------------------------------------------------------------------------

	/**
	 * @return string defined module name
	 */
	private static function getModuleName()
	{
		return (!$name = constant(get_called_class() . '::NAME')) ? str_replace('\\', '.', get_called_class()) : $name;
	}
}