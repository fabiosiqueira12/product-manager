<?php

//DATABASE CONFIG
$DB_LOCAL = [
	'host' => 'localhost',
	'dbname' => 'adapta_portal',
	'user' => 'root',
	'pass' => ''
];
$DB_HOST = [
	'host' => 'localhost',
	'dbname' => 'gncnewsc_database',
	'user' => 'gncnewsc_user',
	'pass' => 'OgTzYzGVz)rs'
];
$whitelist = array(
	'127.0.0.1',
	'::1',
	'localhost'
);
$finalDB;
if (in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
	$finalDB = $DB_LOCAL;
} else {
	$finalDB = $DB_HOST;
}


$link = mysqli_connect($finalDB['host'], $finalDB['user'], $finalDB['pass']);
mysqli_select_db($link, $finalDB['dbname']);

$tables = '*';

//get all of the tables
$tables = array();
$result = mysqli_query($link, 'SHOW TABLES');
while ($row = mysqli_fetch_row($result)) {
	$tables[] = $row[0];
}

$return = "";

//cycle through
foreach ($tables as $table) {

	$result = mysqli_query($link,'SELECT * FROM ' . $table);
	$num_fields = mysqli_num_fields($result);

	$return .= 'DROP TABLE ' . $table . ';';
	$row2 = mysqli_fetch_row(mysqli_query($link,'SHOW CREATE TABLE ' . $table));
	$return .= "\n\n" . $row2[1] . ";\n\n";

	for ($i = 0; $i < $num_fields; $i++) {
		while ($row = mysqli_fetch_row($result)) {
			$return .= 'INSERT INTO ' . $table . ' VALUES(';
			for ($j = 0; $j < $num_fields; $j++) {
				$row[$j] = addslashes($row[$j]);
				$row[$j] = str_replace("\n", "\\n", $row[$j]);
				if (isset($row[$j])) {
					$return .= '"' . $row[$j] . '"';
				} else {
					$return .= '""';
				}
				if ($j < ($num_fields - 1)) {
					$return .= ',';
				}
			}
			$return .= ");\n";
		}
	}
	$return .= "\n\n\n";
}

//save file
$date = date("Y-m-d_H-i-s");
$myfile = fopen('../backups/backup-gnc' . strval($date) . '.sql', "w") or die("Unable to open file!");
fwrite($myfile, $return);
fclose($myfile);