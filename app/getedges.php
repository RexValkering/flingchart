<?php 
require_once("../include/includes.php");

$edges = get_edges();

exit(json_encode($edges));
?>