<?php
session_start();
$_SESSION = []; // kosongkan data session
// Hapus cookie session di browser
if (ini_get("session.use_cookies")) {
    $p = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $p['path'],
        $p['domain'],
        $p['secure'],
        $p['httponly']
    );
}
session_destroy(); // hancurkan session di server
header("Location: login.php?pesan=logout");
exit;
