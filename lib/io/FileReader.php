<?php

// Require class
// PEAR Package - File
require_once('File.php');
// Require class
// Package - IO
require_once('Reader.php');
require_once('exception/FileNotFoundException.php');

/**
 * Provide reader of files
 *
 * Based in java.oi.FileReader with some adaptions
 *
 * @package		io
 * @author		Gustavo Gomes
 * @copyright	2006 Gustavo Gomes
 * @version		1.0
 */
class FileReader extends Reader {

	const READ = "rb";
	const DEFAULT_READSIZE = 1024;

	private $file;

	/**
	 * Default constructor
	 *
	 * <b>Overload</b>
	 * __construct(FileExtend)
	 * __construct(string)
	 *
	 * @param	FileExtend/string $file
	 * @throws	IOException
	 */
	public function __construct($file) {
		if (is_string($file))
			$file = new FileExtend($file);
		else if (!$file instanceof FileExtend)
			throw new IOException("Parameter \$file is not a FileExtend object");

		if (!$file->exists())
			throw new FileNotFoundException();
		$this->file = $file;

		parent::__construct(false);
	}

	/**
	 * Read file based in a size
	 *
	 * @param	int $size
	 * @return	string
	 * @throws	IOException
	 */
	public function read($size) {
		if ($this->file->isReadable())
			return File::read($this->file->getPath(),$size,$this->getLock());
		else
			throw new IOException("File is not readable.");
	}

	/**
	 * Read only one character of the file
	 *
	 * @return	string
	 * @throws	IOException
	 */
	public function readChar() {
		return $this->read(1);
	}

	/**
	 * Read all content within the file
	 *
	 * @return	string
	 * @throws	IOException
	 */
	public function readAll() {
		return $this->read($this->file->length());
	}

	/**
	 * Read one line of the file
	 *
	 * @return	string
	 * @throws	IOException
	 */
	public function readLine() {
		return $this->read(self::DEFAULT_READSIZE);
	}

	/**
	 * Returns the file length
	 *
	 * @return	int - the size returned is in bytes format
	 */
	public function avaliable() {
		return $this->file->length();
	}
	/**
	 * Reset the pointer for first position of the file
	 *
	 * @return	boolean
	 */
	public function reset() {
		return File::rewind($this->file->getPath(),self::READ);
	}

	/**
	 * Close this file resource
	 *
	 * @return	boolean
	 */
	public function close() {
		return File::close($this->file->getPath(),self::READ);
	}

	/**
	 * Unlock file
	 *
	 * @return	boolean
	 */
	public function unlock() {
		return File::unlock($this->file->getPath(),self::READ);
	}
}
?>