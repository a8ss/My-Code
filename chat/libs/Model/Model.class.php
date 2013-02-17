<?php
class Model {
	/**
	 * PDO对象
	 * @var object
	 */
	public $pdo;
	
	
	
	public function __construct() {
		$this->pdo = new PDO ( 'mysql:host=' . DBHOST . ';dbname=' . DBNAME, DBUSER, DBPWD );
		
	}
	
	/**
	 * 获得一个Memcache对象
	 * @return Memcache
	 */
	
	public function getMemcache(){
		$mem = new Memcache();
		$mem->addServer(MEMHOST);
		return $mem;
	}
	
	
}