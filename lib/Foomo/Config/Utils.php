<?php

/*
 * bestbytes-copyright-placeholder
 */

namespace Foomo\Config;

use Foomo\Modules\Manager;
use Foomo\Config;
use Foomo\AutoLoader;
use DirectoryIterator;
use ReflectionClass;

/**
 * config utils
 */
class Utils {

	public static function getConfigs()
	{
		$ret = array();
		foreach (self::getMap() as $moduleName => $moduleDir) {
			$ret[$moduleName][''] = self::getConfigsInDir($moduleDir);
			$subDomains = self::getDomains($moduleDir);
			$hasAConf = false;
			foreach ($subDomains as $subDomain => $subDomainPath) {
				$ret[$moduleName][$subDomain] = self::getConfigsInDir($subDomainPath);
				if (count($ret[$moduleName][$subDomain]) > 0) {
					$hasAConf = true;
				} else {
					unset($ret[$moduleName][$subDomain]);
				}
			}
			if (!$hasAConf && count($ret[$moduleName]['']) == 0) {
				unset($ret[$moduleName]);
			}
		}
		return $ret;
	}

	private static function getMap()
	{
		$map = array();
		$baseDir = Config::getConfigDir() . DIRECTORY_SEPARATOR . 'modules';
		foreach (Manager::getEnabledModules() as $enabledModuleName) {
			$map[$enabledModuleName] = $baseDir . DIRECTORY_SEPARATOR . $enabledModuleName;
		}
		return $map;
	}

	public static function getOldConfigs()
	{
		$ret = array();
		foreach (self::getMap() as $moduleName => $moduleDir) {
			foreach (self::getOldConfigsInDir($moduleDir) as $oldConfig) {
				$ret[] = $oldConfig;
				$oldConfig->module = $moduleName;
				$oldConfig->domain = '';
			}
			$subDomains = self::getDomains($moduleDir);
			foreach ($subDomains as $subDomain => $subDomainPath) {
				$oldConfigs = self::getOldConfigsInDir($subDomainPath);
				foreach ($oldConfigs as $oldConfig) {
					$oldConfig->module = $moduleName;
					$oldConfig->domain = $subDomain;
					$ret[] = $oldConfig;
				}
			}
		}
		return $ret;
	}
	/**
	 * remove old configurations
	 */
	public static function removeOldConfigs()
	{
		$oldConfigs = self::getOldConfigs();
		foreach ($oldConfigs as $oldConfig) {
			/* @var $oldConfig OldConfig */
			unlink($oldConfig->filename);
		}
	}

	const NEEDLE_CONFIG_OLD = '.yml-old-';
	const NEEDLE_CONFIG_DELETED = '.yml-deleted-';

	private static function getOldConfigsInDir($dir)
	{
		$ret = array();
		if (file_exists($dir) && \is_dir($dir)) {
			foreach (new DirectoryIterator($dir) as $fileInfo) {
				/* @var $fileInfo \SplFileInfo */
				if ($fileInfo->isFile()) {
					foreach (array(self::NEEDLE_CONFIG_DELETED, self::NEEDLE_CONFIG_OLD) as $needle) {
						if (\strpos($fileInfo->getFilename(), $needle) !== false) {
							$oldConfig = new OldConfig();
							$oldConfig->filename = $fileInfo->getPathname();
							$oldConfig->type = ($needle == self::NEEDLE_CONFIG_DELETED) ? OldConfig::TYPE_DELETED : OldConfig::TYPE_BACKUP;
							$oldConfig->name = \substr($fileInfo->getFilename(), 0, \strpos($fileInfo->getFilename(), $needle));
							$weirdStringDate = \substr($fileInfo->getFilename(), - strlen('YYYY-MM-DD_HH-II-SS'));
							$weirdStringDate[4] = '-';
							$weirdStringDate[7] = '-';
							$weirdStringDate[10] = ' ';
							$weirdStringDate[13] = ':';
							$weirdStringDate[16] = ':';
							$oldConfig->id = \md5($fileInfo->getPathname());
							$oldConfig->timestamp = \strtotime($weirdStringDate);
							$ret[] = $oldConfig;
						}
					}
				}
			}
		}
		return $ret;
	}

	/**
	 * basically gets all the not dot folder
	 *
	 * @param string $baseDir
	 * @return array domain => path 
	 */
	private static function getDomains($baseDir)
	{
		$ret = array();
		if (is_dir($baseDir)) {
			$dir = new DirectoryIterator($baseDir);
			foreach ($dir as $file) {
				$name = $file->getFilename();
				if ($file->isDir() && substr($name, 0, 1) != '.' && $name != 'modules') {
					$ret[$name] = $baseDir . DIRECTORY_SEPARATOR . $name;
				}
			}
		}
		return $ret;
	}

	private static function getConfigsInDir($baseDir)
	{
		$ret = array();
		$domainConfigClasses = self::getAllDomainConfigClasses();
		foreach ($domainConfigClasses as $domainConfigClass) {
			$domain = self::domainConfigClassNameToDomain($domainConfigClass);
			$candidate = $baseDir . DIRECTORY_SEPARATOR . $domain . '.yml';
			if (file_exists($candidate)) {
				$ret[$domain] = $candidate;
			}
		}
		return $ret;
	}

	/**
	 * scan the class loader for all subclasses of Foomo\Config\AbstractConfig
	 *
	 * @return array
	 */
	public static function getAllDomainConfigClasses()
	{
		$classes = array();
		$classMap = AutoLoader::getClassMap();
		foreach (array_keys($classMap) as $className) {
			if (class_exists($className)) {
				$refl = new ReflectionClass($className);
				if ($refl->isSubclassOf('Foomo\\Config\\AbstractConfig') && !$refl->isAbstract()) {
					$classes[constant($refl->getName() . '::NAME')] = $refl->getName();
				}
			}
		}
		// sort the keys
		$keys = array_keys($classes);
		sort($keys);
		$ret = array();
		foreach($keys as $key) {
			$ret[$key] = $classes[$key];
		}
		return $ret;
	}

	/**
	 * translate a classname to a config domain name
	 *
	 * @param string $className
	 * 
	 * @return string
	 */
	private static function domainConfigClassNameToDomain($className)
	{
		/* @var $inst Foomo\Config\AbstractConfig */
		$inst = new $className;
		return $inst->getName();
	}

}