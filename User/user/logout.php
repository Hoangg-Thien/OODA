<?php
session_start();

// Xoá toàn bộ session (server-side)
$_SESSION = []; // hoặc session_unset()

// Xoá cookie phiên PHPSESSID (client-side)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Huỷ phiên làm việc
session_destroy();

// Chuyển hướng
header("Location: /index.php");
exit();
?>
