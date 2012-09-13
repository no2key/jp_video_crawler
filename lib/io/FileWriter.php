<?php

// Require class
// PEAR Package - File
require_once('File.php');
// Require class
// Package - io
require_once('Writer.php');
require_once('exception/IOException.php');

/**
 * Provide write and append in files
 *
 * Based in java.oi.FileWriter with some adaptions
 *
 * @package		io
 * @author		Gustavo Gomes
 * @copyright	2006 Gustavo Gomes
 * @version		1.0
 */
class FileWriter extends Writer {

	const APPEND = "ab";
	const WRITE = "wb";

	// CRLF =>
	// 		UNIX = \n
	//		Windows = \r\n
	//		Mac = \r
	const CRLF = "\n";

	private $file = null;

	private $append = false;

	/**
	 * Default constructor
	 *
	 * <b>Overload</b>
	 * __construct(FileExtend)
	 * __construct(string)
	 * __construct(FileExtend/string, boolean)
	 *
	 * @param	FileExtend/string $file
	 * @param	boolean $append
	 * @throws	IOException
	 */
	public function __construct($file,$append=false) {
		if (is_string($file))
			$file = new FileExtend($file);
		else if (!$file instanceof FileExtend)
			throw new IOException("Parameter \$file is not a FileExtend object");

		$this->append = $append;
		$this->file = $file;

		parent::__construct(false);
	}

	/**
	 * Apppend info in a file
	 *
	 * @return	boolean
	 * @throws	IOException
	 */
	public function append($info) {
		return $this->writeByMode($info,self::APPEND);
	}

	/**
	 * With info in a file
	 *
	 * @return	boolean
	 * @throws	IOException
	 */
	public function write($info) {
		if ($this->append)
			return $this->writeByMode($info,self::APPEND);
		else
			return $this->writeByMode($info,self::WRITE);
	}

	/**
	 * Write info in a file based in mode specified
	 *
	 * @return	boolean
	 * @throws	IOException
	 */
	private function writeByMode($info,$mode) {
		// If file not exists or it exists and is writable
		if (($this->file->exists() && $this->file->isWritable()) || !$this->file->exists()) {
			return File::write($this->file->getPath(),$info, $mode,$this->lock);
		} else {
			throw new IOException("The file [".$this->file->buildPath()."] is read only.");
		}
	}

	/**
	 * Write one line in a file
	 *
	 * @return	boolean
	 * @throws	IOException
	 */
	public function writeLine($line) {
		if (($this->file->exists() && $this->file->isWritable()) || !$this->file->exists()) {
			return File::writeLine($this->file->getPath(),$line,self::WRITE,self::$CRLF,$this->lock);
		} else {
			throw new IOException("The file [".$this->file->buildPath()."] is read only.");
		}
	}

	/**
	 * Apeend one line in a file
	 *
	 * @return	boolean
	 * @throws	IOException
	 */
	public function appendLine($line) {
		if (($this->file->exists() && $this->file->isWritable()) || !$this->file->exists()) {
			return File::writeLine($this->file->getPath(),$line,self::APPEND,self::CRLF,$this->lock);
		} else {
			throw new IOException("The file [".$this->file->buildPath()."] is read only.");
		}
	}

	/**
	 * Close the resource file
	 *
	 * @return	boolean
	 */
	public function close() {
		if ($this->append)
			return File::close($this->file->getPath(),self::APPEND);
		else
			return File::close($this->file->getPath(),self::WRITE);
	}

	/**
	 * flush file
	 *
	 * @return	boolean
	 */
	public function flush() {
		$pointer = null;
		$pointer = &File::_getFilePointer($this->file->getPath(),self::APPEND,$this->lock);
		if ($pointer) {
			return fflush($pointer);
		} else {
			$pointer = &File::_getFilePointer($this->file->buildPath(),self::WRITE,$this->lock);
			if ($pointer)
				return fflush($pointer);
			else
				return false;
		}
	}

	/**
	 * Unlock file
	 *
	 * @return	boolean
	 */
	public function unlock() {
		if ($this->append)
			return File::unlock($this->file->getPath(),self::APPEND);
		else
			return File::unlock($this->file->getPath(),self::WRITE);
	}
}
?>