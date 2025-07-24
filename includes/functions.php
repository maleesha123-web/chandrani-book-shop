<?php

function check_login() {
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit();
    }
}

function is_admin() {
    return isset($_SESSION['user']) && $_SESSION['user']['user_type'] === 'admin';
}

?>
