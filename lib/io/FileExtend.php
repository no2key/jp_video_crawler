<?php
// Require class
// Find in oi package
require_once('exception/FileException.php');

// Require class
// PEAR
require_once('File/Util.php');

/**
 * Class that provide functionalities to the abstraction of file and directory
 *
 * Based in java.oi.File with some adaptions
 *
 * @package		io
 * @author		Gustavo Gomes
 * @copyright	2006 Gustavo Gomes
 * @version		1.0
 * @see			java.io.File
 */
class FileExtend {

	//** Constants
	const separator = DIRECTORY_SEPARATOR;
	const separatorChar = DIRECTORY_SEPARATOR;
	const pathSeparator = PATH_SEPARATOR;
	const pathSeparatorChar = PATH_SEPARATOR;

	private static $FILE_WIN = "";

	const SORT_NONE = 0;
	const SORT_REVERSE = 1;
	const SORT_NAME = 2;
	const SORT_SIZE = 4;
	const SORT_DATE = 8;
	const SORT_RANDOM = 16;

	const LIST_FILES = 1;
	const LIST_DIRS = 2;
	const LIST_DOTS = 4;
	const LIST_ALL = 7;

	// Name of file or directory
	private $name = "";

	// file or directory path
	private $path = "";

	/**
	 * FileExtend constructor
	 *
	 * <b>Overload</b>
	 * FileExtend(filename : string)
	 * FileExtend(parent : string, child : string)
	 *
	 * @param	string $pathName
	 * @param	atring $name
	 * @throws	FileException
	 */
	public function __construct($pathName,$name=null) {
		self::$FILE_WIN = defined('OS_WINDOWS') ? OS_WINDOWS : !strncasecmp(PHP_OS, 'win', 3);
		$parts = array();
		$path = "";

		// If has defined only the first parameter
		if ($pathName != "" && $name == null) {
			// get the separator type used
			$pathName = str_replace("\\",self::separator,$pathName);
			$pathName = str_replace("/",self::separator,$pathName);
			$parts = explode(self::separator,$pathName);

			$last = 1;
			if ($parts[count($parts)-1] == "")
				$last = 2;

			for ($i = 0;$i < (count($parts)-$last);$i++)
				$this->path .= $parts[$i].self::separator;

			$this->path .= $parts[$i];
			$this->name = $parts[$i];


		// If defined two parameters
		} else if ($name != "") {
			if ($name[strlen($name)-1] == "\\" || $name[strlen($name)-1] == "/")
				$name[strlen($name)-1] = "";
			$this->name = $name;

			if ($pathName[strlen($pathName)-1] != "\\" && $pathName[strlen($pathName)-1] != "/")
				$this->path = $pathName.self::separator.$name;
			else
				$this->path = $pathName.$name;
		} else {
			throw new FileException("File name is not defined.");
		}
	}

	/**
	 * Get name file/directory
	 *
	 * @return	string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Get file/directory path
	 *
	 * @return	string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * Build path for web format (URI)
	 *
	 * @return	string
	 */
	public function buildPath() {
		return str_replace("\\","/",$this->path);
	}

	/**
	 * Get parent directory for this path
	 *
	 * @return	string
	 */
	public function getParent() {
		return dirname($this->path);
	}

	public function getCompleteParent() {
		return dirname($this->path).self::separator;
	}

	/**
	 * Get the file or directory absolute path
	 *
	 * @return	string
	 */
	public function getAbsolutePath() {
		return realpath($this->buildPath());
	}

	public function getAbsoluteDirectoryPath() {
		if ($this->isFile())
			return realpath(dirname($this->buildPath()));
		else
			return $this->getAbsolutePath();
	}

	public function isFile() {
		return is_file($this->buildPath());
	}

	public function isDirectory() {
		return is_dir($this->buildPath());
	}

	public function isDir() {
		return $this->isDirectory();
	}

	/**
	 * Informate if this file or directory contain a absolute path
	 *
	 * @return	boolean
	 */
	public function isAbsolute() {
		$path = $this->path;
		if (preg_match('/(?:\/|\\\)\.\.(?=\/|$)/', $path)) {
			return false;
		}
		if (self::$FILE_WIN) {
			return preg_match('/^[a-zA-Z]:(\\\|\/)/', $path);
		}
		return ($path{0} == '/') || ($path{0} == '~');
	}

	public function isWritable() {
		return is_writable($this->buildPath());
	}

	public function canWrite() {
		return $this->isWritable();
	}

	public function isReadable() {
		return is_readable($this->buildPath());
	}

	public function canRead() {
		return $this->isReadable();
	}

	/**
	 * Returns the file/directory length
	 *
	 * @return	float
	 */
	public function length() {
		return filesize($this->buildPath());
	}

	/**
	 * Return true if it exists and false if it do not exists
	 *
	 * @return	boolean
	 */
	public function exists() {
		if ($this->isDirectory()) {
			return true;
		} else {
			if ($this->isFile() && file_exists($this->buildPath()))
				return true;
			else
				return false;
		}
	}

	/**
	 * Rename this file/directory to file passed with parameter
	 * This method do not only rename, but move current file to
	 * file defined in parameter.
	 *
	 * @param	FileExtend $destination
	 * @throws	FileException
	 */
	public function renameTo($destination) {
		if (is_String($destination))
			return rename($this->buildPath(),$destination);
		else if (is_object($destination) && $destination instanceof FileExtend)
			return rename($this->buildPath(),$destination->getPath());
		else
			throw new FileExceptiob("Impossible rename this file with this parameter");
	}

	/**
	 * Delete this file or directory case it exists
	 *
	 * @return	boolean
	 */
	public function delete() {
		if ($this->exists()) {
			if ($this->isDirectory())
				return @rmdir($this->buildPath());
			else if ($this->isFile())
				return @unlink($this->buildPath());
			else
				return false;
		} else {
		 	return false;
		}
	}

	/**
	 * Create a directory based in this abstract pathname
	 *
	 * @return	boolean
	 */
	public function mkdir() {
		return @mkdir($this->buildPath(),0777);
	}

	/**
	 * Create a directory and your parents if necessary,
	 * based in this abstract pathname
	 *
	 * @return	boolean
	 */
	public function mkdirs() {
		$dirs = explode("/",$this->buildPath());
		$currentDir = "";
		for ($i = 0;$i < count($dirs);$i++) {
			$currentDir .= $dirs[$i].self::separator;
			$dir = new FileExtend($currentDir);
			if (!$dir->exists()) {
				if (!$dir->mkdir())
					return false;
			}
		}
		return true;
	}

	/**
	 * Get file or directory last modifier
	 *
	 * @return	string - date on standard [yyyy-mm-dd hh:mm:ss]
	 */
	public function lastModified() {
		if ($this->exists())
			return date("Y-m-d H:i:s",filemtime($this->buildPath()));
	}

	/**
	 * Create an array bidimensional with files and directories that directory
	 * Case directory don't exists or this FileExtend class don't a directory then return null
	 *
	 * Example:
	 * $file = new FileExtend("path/something");
	 * print_r($file->listFilesName());
	 *
	 * Output:
	 * array ([0] => array([name] => ex.js, [size] => 2015, [date] => 10251226485),
	 * [1] => array([name] => ex2.js, [size] => 1000, [date] => 10251226485))
	 *
	 * @param	int $list
	 * @param	int $sort
	 * @return	array
	 */
	public function listFilesName($list=null, $sort=null) {
		if ($this->exists() && $this->isDir()) {
			if ($list == null)
				$list = self::LIST_ALL;
			if ($sort == null)
				$sort = self::SORT_NAME;
			$files = File_Util::listDir($this->buildPath(),$list,$sort,null);
			for ($i = 0;$i < count($files);$i++)
				$filesName[] = $files[$i]->name;
			return $filesName;
		} else {
			return null;
		}
	}


	/**
	 * Same implemetation of the method above, but
	 * insteand of common array for each file, this method return
	 * a type FileExtend for each file or directory.
	 *
	 * Example:
	 * $file = new FileExtend("path/something");
	 * $list = $file->listFiles();
	 * if ($list != null)
	 * 		echo $list[0]->getName().$list[0]->length();
	 *
	 * @param	int $list
	 * @param	int $sort
	 * @return	array
	 */
	public function listFiles($list=null, $sort=null) {
		if ($this->exists() && $this->isDir()) {
			$filesObj = array();
			if ($list == null)
				$list = self::LIST_ALL;
			if ($sort == null)
				$sort = self::SORT_NAME;
			$files = File_Util::listDir($this->buildPath(),$list,$sort,null);
			for ($i = 0;$i < count($files);$i++)
				$filesObj[] = new FileExtend($this->buildPath(),$files[$i]->name);
			return $filesObj;
		} else {
			return null;
		}
	}

	/**
	 * Get some file or directory informations
	 *
	 * @return	array
	 */
	public function getInformation() {
		if ($this->exists()) {
			$stat = stat($this->buildPath());
			$statReturn['modify'] = date("Y-m-d H:i:s",$stat['mtime']);
			$statReturn['change'] = date("Y-m-d H:i:s",$stat['ctime']);
			$statReturn['access'] = date("Y-m-d H:i:s",$stat['atime']);
			$statReturn['size'] = $stat['size'];
			return $statReturn;
		} else {
			return null;
		}
	}

	/**
	 * Alises for the method above
	 *
	 * @return	array
	 */
	public function getInfo() {
		return $this->getInformation();
	}

	/**
	 * Get file extension
	 *
	 * @return	string
	 */
	public function getExtension() {
		if ($this->isDirectory())
			return "folder";
		$parts = explode(".",$this->name);
		return $parts[count($parts)-1];
	}

	/**
	 * Get the usable space in disk
	 *
	 * @return	int
	 */
	public function getUsableSpace() {
		return ($this->getTotalSpace() - $this->getFreeSpace());
	}

	/**
	 * Get free space in disk
	 *
	 * @return	int
	 */
	public function getFreeSpace() {
		return disk_free_space($this->getAbsolutePath());
	}

	/**
	 * Get total disk space
	 *
	 * @return	int
	 */
	public function getTotalSpace() {
		return disk_total_space($this->getAbsolutePath());
	}

	/**
	 * Get total space in directory
	 *
	 * If the file has been a directory return your total size
	 * If the file has been a file, return total size of the directory that
	 * it be.
	 *
	 * @return	int
	 */
	public function getDirectoryTotalSpace() {
		$size = 0;
		if ($this->isDirectory())
			$this->getSizeOfDirectory($size,$this);
		else
			$this->getSizeOfDirectory($size,new FileExtend($this->getAbsoluteDirectoryPath()));
		return $size;
	}

	private function getSizeOfDirectory(&$size,$dirs) {
		$files = $dirs->listFiles();
		for ($i = 0;$i < count($files);$i++) {
			if ($files[$i]->getName() != "." && $files[$i]->getName() != "..") {
				$size += $files[$i]->length();
				if ($files[$i]->isDirectory())
					$this->getSizeOfDirectory($size,$files[$i]);
			}
		}
	}

	/**
	 * For echo this class objects
	 *
	 * @return	string
	 */
	public function __toString() {
		return $this->path;
	}
}
?>