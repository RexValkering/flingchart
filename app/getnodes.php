<?php 
require_once("../include/includes.php");

$nodes = get_nodes();

exit(json_encode($nodes));
?>