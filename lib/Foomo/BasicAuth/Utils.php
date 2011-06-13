<?php
/*
 * bestbytes-copyright-placeholder
 */

namespace Foomo\BasicAuth;

/**
 * basic auth file CRUD
 */
class Utils {
	/**
	 * get hash table of users / password hashes
	 * 
	 * @param string $domain
	 * 
	 * @return hash array('user1' => 'hash1', 'user2' => 'hash2', ...)
	 */
	public static function getUsers($domain)
	{
		$users = array();
		$authFilename = \Foomo\BasicAuth::getAuthFilename($domain);
		if(file_exists($authFilename)) {
			$rawUsers = explode(chr(10), file_get_contents($authFilename));
			
			foreach($rawUsers as $line) {
				$line = trim($line);
				if(empty($line)) {
					continue;
				}
				$parts = explode(':', $line);
				$users[$parts[0]] = $parts[1];
			}
		}
		return $users;
	}
	/**
	 * auth domain files
	 * 
	 * @return string
	 */
	public static function getDomains()
	{
		$ret = array();
		$dirIterator = new \DirectoryIterator(\Foomo\BasicAuth::getAuthDirname());
		foreach($dirIterator as $fileInfo) {
			/* @var $fileInfo SplFileInfo */
			if(!$fileInfo->isDir() && !$fileInfo->isDot() && $fileInfo->isFile()) {
				$ret[] = $fileInfo->getBasename();
			}
		}
		return $ret;
	}
	/**
	 * update / create (if does not exist) user
	 *
	 * @param string $domain
	 * @param string $name
	 * @param string $password
	 * @param string $hashAlgorythm so far crypt only
	 *
	 * @return boolean
	 */
	public static function updateUser($domain, $name, $password, $hashAlgorythm = 'crypt')
	{
		$file = \Foomo\BasicAuth::getAuthFilename($domain);
		$userFound = false;
		$users = self::getUsers($domain);
		$users[$name] = self::hash($password, $hashAlgorythm);
		return self::saveUsers($domain, $users);
	}
	private static function saveUsers($domain, $users)
	{
		$fp = fopen(\Foomo\BasicAuth::getAuthFilename($domain), 'w');
		if($fp === false) {
			return false;
		} else {
			foreach($users as $name => $hash) {
				fwrite($fp, $name . ':' . $hash . chr(10));
			}
			fclose($fp);
			return true;
		}
	}
	/**
	 * delete user in a domain
	 * 
	 * @param string $domain
	 * @param string $user
	 * 
	 * @return boolean
	 */
	public static function deleteUser($domain, $user)
	{
		$users = self::getUsers($domain);
		unset($users[$user]);
		return self::saveUsers($domain, $users);
	}
	/**
	 * delete a domain
	 * 
	 * @param string $domain
	 * 
	 * @return boolean
	 */
	public static function deleteDomain($domain)
	{
		return \unlink(\Foomo\BasicAuth::getAuthFilename($domain));
	}
	/**
	 * create an auth domain file
	 * 
	 * @param type $domain
	 * 
	 * @return boolean
	 */
	public static function createDomain($domain)
	{
		return \touch(\Foomo\BasicAuth::getAuthFilename($domain));
	}
	/**
	 * hash a password into an authfile
	 * 
	 * @param string $password
	 * @param string $algorythm so far only crypt is supported
	 * @param string $salt
	 * 
	 * @return string
	 */
	public static function hash($password, $algorythm = 'crypt', $salt = null)
	{
		switch($algorythm) {
			case 'crypt':
				if(is_null($salt)) {
					$salt = self::getSaltChar() . self::getSaltChar();
				}
				$hash = crypt($password, $salt);
				break;
			default:
				trigger_error('unsopported hasing algorythm ' . $algorythm, E_USER_ERROR);
		}
		return $hash;
	}
	private static function getSaltChar()
	{
		if(rand(0,1)) {
			return chr(rand(65, 90));
		} else {
			return chr(rand(97, 122));
		}
	}
}