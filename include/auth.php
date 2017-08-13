<?php
/**
 *  Author: Rex Valkering
 *
 *  This is currently not used within the application, but was once created
 *  with the purpose of allowing accounts.
 */

// An array of ranks, with their capabilities.
$ranks = array(
    0 => 'user',
    1 => 'moderator',
    2 => 'admin'
);

/**
 *  Check if the user is logged in.
 */
function user_is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 *  Get the user ID.
 */
function user_id() {
    if (user_is_logged_in())
        return $_SESSION['user_id'];
    else
        return false;
}

/**
 *  Get whether the user is an admin.
 */
function is_admin() {
    if (user_is_logged_in())
        return ($_SESSION['rank'] >= 2);
    else
        return false;
}

/**
 *  Log a brotha in.
 */
function login_user($uid) {
    if (user_is_logged_in())
        return;

    $_SESSION['user_id'] = $uid;
    $_SESSION['rank'] = 0;
}
