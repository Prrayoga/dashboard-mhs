<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function sudah_login()
{
    return isset($_SESSION['user_id']);
}

function wajib_login()
{
    if (!sudah_login()) {
        header("Location: login.php?pesan=belum_login");
        exit;
    }
}
