<?php

require_once('../../../io/FileExtend.php');
require_once('../../../io/FileReader.php');
require_once('../../../io/FileWriter.php');
require_once('../../../io/FormatFileSize.php');

/**
 * Example file
 *
 * Provide functionalities demonstration offer to the abstraction
 *
 * @author		Gustavo Gomes
 * @copyright	2006 Gustavo
 */

echo "<b>Test of the FileExtend, FileReader and FileWriter classes</b><br />";
try {
	/**
	 * List all files in a directory
	 */
	echo "<b>List all files in a directory</b><br>";
	$file = new FileExtend("../../");
	$files = $file->listFiles();
	for ($i = 0;$i < count($files);$i++) {
		$out  = ($files[$i]->isDir() ? 'Dir' : 'File');
		$out .= ' <a href="'.$files[$i]->buildPath().'">'.$files[$i]->getName().'</a> - ';
		$out .= FormatFileSize::formatWithName($files[$i]->length(),FormatFileSize::SIZE_AUTO).' <br>';
		echo $out;
	}
	echo "<br>";

	/**
	 * Create a directory
	 */
	$dir = new FileExtend("dir/name/arqs/forfiles");
	$dir2 = new FileExtend("dir/name/arqs/forfiles2");
	$dir->mkdirs();
	$dir2->mkdirs();
	$dir->delete();

	/**
	 * Create and write with append mode
	 */
	$file2 = new FileExtend("testing.html");
	$fw = new FileWriter($file2);
	if ($fw->appendLine("<b>test</b>"))
		echo "Success in Create and write with append mode";
	else
		echo "Fail in Create and write with append mode";

	/**
	 * File reading
	 */
	echo "<br><br><b>File strem of the test.php file</b><br>";
	$fr = new FileReader($file2);
	while (($str = $fr->readLine()) !== false)
		echo $str."<br>";
	echo '<br><br>'.htmlspecialchars($str);

	/**
	 * Read one line of a file and write in this file
	 */
	$fr2 = new FileReader(new FileExtend("counter.txt"));
	if (($n = $fr2->readLine()) !== false) {
		$n++;
		$fw2 = new FileWriter(new FileExtend("counter.txt")	);
		if ($fw2->write($n))
			echo "Success in Read one line of a file and write in this file - value = ".$n;
		else
			echo "Fail in Read one line of a file and write in this file - value = ".$n;
	} else
		echo "Error on the read";
} catch (FileException $fe) {
	echo $fe;
} catch (FileNotFoundException $fnfe) {
	echo $fnfe;
} catch (IOException $e) {
	echo $e;
}
?>