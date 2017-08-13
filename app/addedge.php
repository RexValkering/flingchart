<?php

require_once("../include/includes.php");

$one = $_POST['node_1'];
$two = $_POST['node_2'];
$type = $_POST['color'];

// Make sure nodes aren't the same.
if ($one != $two)
{
    $node_1 = get_node($one);
    $node_2 = get_node($two);

    if (!$node_1)
        $node_1 = get_node(add_node($one));
    
    if (!$node_2)
        $node_2 = get_node(add_node($two));

    if ($node_1['id'] != $node_2['id']) {

        // Make sure order is okay.
        if ($node_1['id'] > $node_2['id']) {
            $temp = $node_1;
            $node_1 = $node_2;
            $node_2 = $temp;
        }

        $edge = get_edge($node_1['id'], $node_2['id']);
        if (!$edge) {
            add_edge($node_1['id'], $node_2['id'], $type);
        }
        elseif ($edge['type'] != $type) 
            update_edge($node_1['id'], $node_2['id'], $type);
    }
}

header('Location: ' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . SITE_ROOT);
exit();
