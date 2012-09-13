<?php
/**
 * Class that provide the file size formatting with or less format name
 * attached.
 * 
 * @package		io
 * @author		Gustavo Gomes
 * @copyright	2006 Gustavo Gomes
 * @version		1.0
 */
class FormatFileSize {
	
	const SIZE_UNIT = 1024;
	
	const SIZE_B = 0;
	const SIZE_KB = 1;
	const SIZE_MB = 2;
	const SIZE_GB = 3;
	// Terabytes
	const SIZE_TB = 4;
	// Petabytes
	const SIZE_PB = 5;
	// Exabytes
	const SIZE_EB = 6;
	// Zettabytes
	const SIZE_ZB = 7;
	// Yottabytes
	const SIZE_YB = 8;
	// Xentabytes
	const SIZE_XB = 9;
	// Wektabytes
	const SIZE_WB = 10;
	const SIZE_AUTO = 11;
	
	private static $sizeNames=array("Bytes",
									"KB",
									"MB",
									"GB",
									"TB",
									"PB",
									"EB",
									"ZB",
									"YB",
									"XB",
									"WB"
								);
	
	private static $sizeCompleteName= array("Bytes",
											"Quilobytes",
											"Megabytes",
											"Gigabytes",
											"Terabytes",
											"Petabytes",
											"Exabytes",
											"Zettabytes",
											"Yottabytes",
											"Xentabytes",
											"Wektabytes"
										);

	/**
	 * Default constructor
	 */
	private function __construct() {
	}

	/**
	 * Format size from Bytes to a type specific
	 * 
	 * @param	long $size - in bytes
	 * @param	int $type
	 * @return	int
	 */
	private static function formatSize($size,&$type) {
		if ($size > 0 && $type >= 0 && $type <= 11) {
			if ($type != 0 && $type != 11) {
				$size = $size / pow(2, $type * 10);
			} else if ($type == 11) {
				for ($i = 0;$size > self::SIZE_UNIT;$i++)
					$size = $size / pow(2,10);
				$type = $i;
			}
			return $size;
		}
		$type = 0;
		return 0;
	}
	
	/**
	 * Format size from Bytes to a type specific with or less
	 * name this type
	 * 
	 * @param	long $size - in bytes
	 * @param	int $type
	 * @param	boolean $showName
	 * @return	string
	 */
	public static function format($size,$type=0,$showName=false) {
		$size = self::formatSize($size,$type);
		if ($showName) {
			return round($size,2)." ".self::$sizeNames[$type];
		} else {
			return round($size,2);
		}
	}

	/**
	 * Format size from Bytes to a type specific with
	 * name this type
	 * 
	 * @param	long $size - in bytes
	 * @param	int $type
	 * @return	string
	 */
	public static function formatWithName($size,$type=0) {
		return self::format($size,$type,true);
	}
}
?>
