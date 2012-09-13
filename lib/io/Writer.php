<?php
/**
 * Abstract class to writer stream
 * 
 * Based in java.oi.Writer with some adaptions
 * 
 * @package		io
 * @author		Gustavo Gomes
 * @copyright	2006 Gustavo Gomes
 * @version		1.0
 */
abstract class Writer {

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
	 * Write determinate string in a file
	 * 
	 * @param	string $info
	 */
	public abstract function write($info);
	
	/**
	 * flush file
	 * 
	 * @return	boolean
	 */
	public abstract function flush();
	
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
