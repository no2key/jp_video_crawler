<?php
/**
 * Abstract class to reader stream
 * 
 * Based in java.oi.Reader with some adaptions
 * 
 * @package		io
 * @author		Gustavo Gomes
 * @copyright	2006 Gustavo Gomes
 * @version		1.0
 */
abstract class Reader {

	protected $lock = false;
	
	/**
	 * Default constructor
	 * 
	 * @param	boolean $lock
	 */
	public function __construct($lock=false) {
		$this->lock = $lock;
	}
	
	/**
	 * Get lock status
	 * 
	 * @return	boolean
	 */
	public function getLock() {
		return $this->lock;
	}
	
	/**
	 * Set lock status
	 * 
	 * @param	boolean $lock
	 */
	public function setLock($lock) {
		$this->lock = $lock;
	}
	
	/**
	 * enable file lock
	 */
	public function enableLock() {
		$this->lock = true;
	}
	
	/**
	 * Disable file lock
	 */
	public function disableLock() {
		$this->lock = false;
	}
	
	/**
	 * Abstract method to read file based in a size
	 * 
	 * @param	int $size
	 */
	public abstract function read($size);
	
	/**
	 * Returns file length
	 * 
	 * @return	int
	 */
	public function avaliable() {}
	
	/**
	 * Reset pointer to first position in file
	 * 
	 * @return	boolean
	 */
	public function reset() {}
	
	/**
	 * Abstract method to close file
	 * 
	 * @return	boolean
	 */
	public abstract function close();
	
	/**
	 * unlock file
	 * 
	 * @return	boolean
	 */
	public abstract function unlock();
}
?>
