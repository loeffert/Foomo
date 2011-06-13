<?php

/*
 * bestbytes-copyright-placeholder
 */

namespace Foomo;

use Foomo\Modules\Manager;

/**
 * manage foomo from the command line
 */
class CliManager {

	/**
	 * get the runmode
	 *
	 * @return string
	 */
	public function getRunMode()
	{
		return Config::getMode();
	}

	/**
	 * create a module
	 * 
	 * @param string $name name of the module
	 * @param string $description short description
	 * @param string[] $dependencies modules that are needed to run this module
	 * 
	 * @return string
	 */
	public function createModule($name, $description, $dependencies)
	{
		Modules\Utils::createModule($name, $description, $dependencies);
	}

	/**
	 * enable a module
	 *
	 * @param string $moduleName name of the module
	 *
	 * @return boolean
	 */
	public function enableModule($moduleName)
	{
		return Manager::enableModule($moduleName);
	}

	/**
	 * disable a module
	 *
	 * @param string $moduleName name of the module
	 *
	 * @return boolean
	 */
	public function disableModule($moduleName)
	{
		return Manager::disableModule($moduleName);
	}

	/**
	 * get module info
	 *
	 * @return array
	 */
	public function getModules()
	{
		return array(
			'available' => Manager::getAvailableModules(),
			'enabled' => Manager::getEnabledModules()
		);
	}

	/**
	 * get a config
	 *
	 * @param string $module 
	 * @param string $domain 
	 * @param string $subDomain
	 *
	 * @return array
	 */
	public function getConfig($module, $domain, $subDomain = null)
	{
		$config = Config::getConf($module, $domain, $subDomain);
		return $config->getValue();
	}

	/**
	 * set config values for a given module/domain, configuration will be created if it does not exist already
	 *
	 * @param string $configFile YAML config file
	 * @param string $module
	 * @param string $domain
	 * @param string $subDomain
	 * 
	 * @return array
	 */
	public function setConfig($configFile, $module, $domain, $subDomain = null)
	{
		if (file_exists($configFile) && $yaml = file_get_contents($configFile)) {
			$config = Config::getConf($module, $domain, $subDomain);
			$config->setValue(Yaml::parse($yaml));
			Config::setConf($config, $module, $subDomain);
			return $config->getValue();
		} else {
			trigger_error("File $configFile does not exist or is empty!", E_USER_ERROR);
		}
	}

	/**
	 * Edit a config with $EDITOR
	 * 
	 * @param string $module 
	 * @param string $domain 
	 * @param string $subDomain 
	 */
	public function editConfig($module, $domain, $subDomain = null)
	{
		/* @var $config Foomo\Config\AbstractConfig */
		echo 'not implemented yet';
		if (!isset($_SERVER['EDITOR'])) {
			trigger_error('$EDITOR is not set', E_USER_ERROR);
		}
		die('this in not implemented yet - still looking for a good way to invoke $EDITOR');
		//$_SERVER['EDITOR'] = '/usr/bin/pico';
		/*
		  $config = Foomo\Config::getConf($module, $domain, $subDomain);
		  $configClass = get_class($config);
		  $editFile = Foomo\Config::getTempDir() . DIRECTORY_SEPARATOR . 'configEdit.yml';
		  $yaml = (string) $config;
		  file_put_contents($editFile, $yaml);
		  $call = $_SERVER['EDITOR'] . ' ' . escapeshellarg($editFile) ;
		  echo $call . PHP_EOL;
		  `$call`;
		  $newYaml = file_get_contents($editFile);
		  $config->setValue(\Foomo\Yaml::parse($newYaml));
		  Foomo\Config::setConf($config, $module, $subDomain);
		  //unlink($editFile);
		 */
	}

	/**
	 * reset the auto loader
	 *
	 * @return void
	 */
	public function resetAutoLoader()
	{
		return AutoLoader::reset(true);
	}

}