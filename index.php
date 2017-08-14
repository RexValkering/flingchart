<?php
/**
 *  Author: Rex Valkering
 *
 *  Main application
 */
require_once('include/includes.php');
?>
<html>
<head>
<meta charset="utf-8">
<style>

.node {
  stroke: #fff;
  stroke-width: 1.5px;
}

.link {
  stroke: #999;
  stroke-opacity: .6;
}

</style>
</head>
<body>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script src="js/functions.js"></script>

<input type="hidden" id="graph-height" value="<?php echo GRAPH_HEIGHT; ?>" />
<input type="hidden" id="graph-width" value="<?php echo GRAPH_WIDTH; ?>" />

<h3>Voeg een koppel toe</h3>
<form action="app/addedge.php" method="POST">
<input type="text" name="node_1" />
<input type="text" name="node_2" />
<select name="color">
    <option value="#CC333F">Seks</option>
    <option value="#338CCC">Zoenen</option>
</select>
<input type="submit" />
</form>

<!-- <h3>Meld een koppel als incorrect</h3>
<form action="app/reportedge.php" method="POST">
<input type="text" name="node_1" />
<input type="text" name="node_2" />
<input type="submit" />
</form> -->

</body></html>