<?php

require_once 'config/init.php';

$q = strtolower($_GET["term"]);

$return = array();

$a = json_encode($_GET);

$query = mysql_query("SELECT title FROM items WHERE title LIKE '%$q%'");
while ($row = mysql_fetch_array($query)) {
	array_push($return, array('label' => $row['title'], 'value' => $row['title']));
}

echo(json_encode($return));

?>