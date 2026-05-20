<?php
session_start();
session_unset();
session_destroy();

// ── Clear the Remember Me cookies ────────────────────────────────────────────
// Setting expiry in the past tells the browser to delete them immediately
setcookie('cubiertos_email', '', time() - 3600, '/');
setcookie('cubiertos_pass',  '', time() - 3600, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logging out...</title>
    <script>
        // Clear the loader memory so the animation plays again on the next login
        sessionStorage.removeItem("hasSeenLoader");

        // Also delete both cookies from JS side just to be safe
        document.cookie = "cubiertos_email=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        document.cookie = "cubiertos_pass=;  expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

        // Redirect to login page
        window.location.href = "http://localhost/webtools-main/HTML/login.html";
    </script>
</head>
<body>
</body>
</html>