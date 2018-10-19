<?php

/**
 * App Initial
 *
 */

namespace EatWhat;

use EatWhat\EatWhatRequest;
use EatWhat\EatWhatStatic;
use EatWhat\Generator\Generator;
use EatWhat\Exceptions\EatWhatException;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

class AppInit
{

	/**
	 * App initalal config
	 *
	 */
	public $initConfig;

	/**
	 * request
	 *
	 */
	public $request;

	/**
	 * Initial app
	 *
	 */
	public function _Init($initConfig)
	{
		$this->initConfig = $initConfig;

		ob_start("ob_gzhandler");

		session_start();

		date_default_timezone_set("Asia/Shanghai");

		$this->register();

		$this->initInput();

		DEVELOPMODE && $this->setErrorDisplayAndHandle();

		// create request
		$this->request = new EatWhatRequest();

		//verify api and method
		$this->request->addMiddleWare(Generator::middleware("verifyApiAndMethod"));

		//verify sign
		if($this->initConfig["api_verify_sign"]) {
			$_GET["paramsSign"] = EatWhatStatic::getParamsSign();
			$this->request->addMiddleWare(Generator::middleware("verifySign"));
		}

		// verify user
		if($_COOKIE["access_token"]) {
			$this->request->addMiddleWare(Generator::middleware("verifyAccessToken"));
		}
		
		// invoke
		$this->request->invoke();
	}

	/**
	 * autoload
	 *
	 */
	public function register($prepend = false)
	{
		spl_autoload_register([$this, "autoLoadRegister"], false, $prepend);
	}


	/**
	 * autoload method
	 *
	 */
	public function autoLoadRegister($class)
	{
		$file = $this->findFile($class);
		if($file && EatWhatStatic::checkFile($file)) 
			require_once $file;
		else 
			throw new EatWhatException($class." class file is not exists.");
	}

	/**
	 * autoload class method
	 *
	 */
	public function findFile($class) 
	{
    	$subPath = $class;
    	$suffix  = '';

    	if( isset($this->initConfig['classmap_static'][$class]) ) {
			$file = $this->initConfig['classmap_static'][$class];
			return $file;
		}

		while( false !== $lastPos = strrpos($subPath, '\\') ) {
			$suffix = substr($subPath, $lastPos).$suffix;
			$suffix = str_replace('\\', DS, $suffix);
			$subPath = substr($subPath, 0, $lastPos);
			if ( isset($this->initConfig['classmap_namespace'][$subPath]) ) {
				$file = $this->initConfig['classmap_namespace'][$subPath].$suffix.$this->initConfig['class_file_ext'];
				return $file;
			}
		}
	}

	/**
	 * set error display level and handle
	 * 
	 */
	public function setErrorDisplayAndHandle()
	{
		error_reporting(E_ALL);
		ini_set('display_errors','On');
		ini_set('display_startup_errors','On');
		ini_set('log_errors','On');

		$this->registerErrorHandle();
	}

	/**
	 * register error handle
	 * 
	 */
	public function registerErrorHandle()
	{
		$whoops = new Run;
		$whoops->pushHandler(new PrettyPageHandler);
		$whoops->register();
	}
	
	/**
	 * init input
	 * 
	 */
	public function initInput()
	{
		EatWhatStatic::checkPostMethod() && ($_GET = array_merge($_GET, $_POST));
	}
}