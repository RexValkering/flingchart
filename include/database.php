<?php
/**
 *  Author: Rex Valkering
 *
 *  This file contains all database interaction for the application.
 */

// Create a PDO object for connecting with the database.
global $db;
$db = new PDO(
    'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET,
    DB_USER, DB_PASSWORD
);

// The database version.
global $flingchart_db_version;
$flingchart_db_version = 1.0;

function create_tables() {
    global $db, $flingchart_db_version;

    /*
     * We'll set the default character set and collation for this table.
     * If we don't do this, some characters could end up being converted
     * to just ?'s when saved in our table.
     */
    $charset_collate = '';

    if (DB_CHARSET != '') {
      $charset_collate = "DEFAULT CHARACTER SET " . DB_CHARSET;
    }

    if (DB_COLLATE != '') {
      $charset_collate .= " COLLATE " . DB_COLLATE;
    }

    // Setup tables to create.
    $tables = [];

    // Create nodes table.
    $tables[] = "CREATE TABLE " . DB_PREFIX . "flingchart_nodes (
        id int(11) unsigned NOT NULL AUTO_INCREMENT,
        name varchar(128) NOT NULL DEFAULT '',
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // Create edges table.
    $tables[] = "CREATE TABLE " . DB_PREFIX . "flingchart_edges (
        id int(11) unsigned NOT NULL AUTO_INCREMENT,
        node_1 int(11) unsigned NOT NULL,
        node_2 int(11) unsigned NOT NULL,
        type varchar(8) NOT NULL DEFAULT '',
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // Create types table.
    // $tables[] = "CREATE TABLE " . DB_PREFIX . "flingchart_types (
    //     id int(11) unsigned NOT NULL AUTO_INCREMENT,
    //     name varchar(64) NOT NULL default '',
    //     type varchar(64) NOT NULL default '#FFFFFF',
    //     PRIMARY KEY  (id)
    // ) $charset_collate;";

    // // Create users table.
    // $tables[] = "CREATE TABLE " . DB_PREFIX . "flingchart_users (
    //     id int(11) unsigned NOT NULL AUTO_INCREMENT,
    //     user_login varchar(64) NOT NULL,
    //     user_password varchar(64) NOT NULL,
    //     user_email varchar(128) DEFAULT '',
    //     rank int(11) NOT NULL DEFAULT 0,
    //     PRIMARY KEY  (id)
    // ) $charset_collate;";

    // Create options table.
    $tables[] = "CREATE TABLE " . DB_PREFIX . "flingchart_options (
        id int(11) unsigned NOT NULL AUTO_INCREMENT,
        name varchar(64) NOT NULL,
        value varchar(64) NOT NULL DEFAULT '',
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // Add all tables to the database.
    foreach ($tables as $table) {
        $db->query($table);
    }

    // Add database version to options.
    update_option('flingchart_db_version', $flingchart_db_version);
}

/**
 *  Check if the database contains any tables, and if not, create them.
 */
function check_db() {
    global $flingchart_db_version;

    // We don't have support for updating databases yet.
    if (is_installed()) {
        $option = get_option('flingchart_db_version');

        if ($option === false)
            create_tables();
    }
    else 
        create_tables();
}

/**
 *  Check if flingchart was installed
 */
function is_installed() {
    global $db;

    // We assume that, if the flingchart options table isn't installed, the whole
    // site isn't installed.
    $sql = "SHOW TABLES LIKE '" . DB_PREFIX . "flingchart_options';";
    $q = $db->prepare($sql);
    $q->execute();

    $result = $q->fetch();
    if ($result == NULL)
        return false;
    return true;
}

/**
 *  Get a node by name or ID
 */
function get_node($node) {
    global $db;

    $sql = "SELECT * FROM " . DB_PREFIX . "flingchart_nodes WHERE name LIKE ?";
    if (is_numeric($node))
        $sql = "SELECT * FROM " . DB_PREFIX . "flingchart_nodes WHERE id = ?";
    
    $q = $db->prepare($sql);
    $q->execute([$node]);

    return $q->fetch();
}

/**
 *  Get an edge by the node ID's
 */
function get_edge($node_1, $node_2) {
    global $db;

    if ($node_1 > $node_2) {
        $temp = $node_1;
        $node_1 = $node_2;
        $node_2 = $temp;
    }

    $sql = "SELECT * FROM " . DB_PREFIX . "flingchart_edges WHERE node_1 = ? AND node_2 = ?";
    $q = $db->prepare($sql);
    $q->execute([$node_1, $node_2]);

    return $q->fetch();
}

/**
 *  Add a new node
 */
function add_node($node) {
    global $db;

    $sql = "INSERT INTO " . DB_PREFIX . "flingchart_nodes (name) VALUES (?);";
    $q = $db->prepare($sql);
    $q->execute([$node]);
    return $db->lastInsertId();
}

/**
 *  Add an edge between two nodes. Node_1 and node_2 are the IDs in
 *  ascending order, type is the color of the edge.
 */
function add_edge($node_1, $node_2, $type) {
    global $db;

    $sql = "INSERT INTO " . DB_PREFIX . "flingchart_edges (node_1, node_2, type) VALUES (?, ?, ?);";
    echo $sql;
    $q = $db->prepare($sql);
    $q->execute([$node_1, $node_2, $type]);
    print_r($q->errorInfo());
    return $db->lastInsertId();
}

/**
 *  Update an edge between two nodes. Node_1 and node_2 are the IDs in
 *  ascending order, type is the color of the edge.
 */
function update_edge($node_1, $node_2, $type) {
    global $db;

    $sql = "UPDATE " . DB_PREFIX . "flingchart_edges SET type=? WHERE node_1 = ? AND node_2 = ?";
    $q = $db->prepare($sql);
    $q->execute([$type, $node_1, $node_2]);
}

/**
 *  Get all nodes.
 */
function get_nodes() {
    global $db;

    $sql = "SELECT * FROM " . DB_PREFIX . "flingchart_nodes;";
    $q = $db->prepare($sql);
    $q->execute();

    return $q->fetchAll();
}

/**
 *  Get all edges.
 */
function get_edges() {
    global $db;

    $sql = "SELECT * FROM " . DB_PREFIX . "flingchart_edges;";
    $q= $db->prepare($sql);
    $q->execute();

    return $q->fetchAll();
}

/**
 *  Get the value of a database option.
 */
function get_option($name) {
    global $db;

    $sql = "SELECT * FROM " . DB_PREFIX . "flingchart_options WHERE name = ?;";
 
    // Execute query and fetch result.
    $q = $db->prepare($sql);
    $q->execute([$name]);
    $row = $q->fetch();
 
    return ($row ? $row['value'] : false);
}

/**
 *  Update a database option.
 */
function update_option($name, $value) {
    global $db;

    $option = get_option($name);
    if (!$option) {
        $sql = "INSERT INTO " . DB_PREFIX . "flingchart_options (name, value)
                VALUES (?, ?)";
        $q = $db->prepare($sql);
        $q->execute(array($name, $value));
    }
    else {
        $sql = "UPDATE " . DB_PREFIX . "flingchart_options SET value=? WHERE name=?;";
        $q = $db->prepare($sql);
        $q->execute(array($value, $name));
        
    }
}

/**
 *  Delete a database option.
 */
function delete_option($name) {
    global $db;

    $sql = "DELETE FROM " . DB_PREFIX . "flingchart_options WHERE name = ?;";

    // Execute query.
    $q = $db->prepare($sql);
    $q->execute(array($name));
}