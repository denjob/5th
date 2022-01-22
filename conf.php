<?php
DEFINED('PAGE_LIMIT') OR DEFINE('PAGE_LIMIT', 3); //per page

try{
	$db = new PDO('mysql:host=localhost;dbname=5th;charset=utf8;', 'root', '');
} catch (PDOException $e) {
    echo "Error connection DB: " . $e->getMessage();
    exit();
}
?>
