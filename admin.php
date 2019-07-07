<?php
session_start();

if (!isset($_SERVER['PHP_AUTH_USER']) or $_SERVER['PHP_AUTH_USER'] != 'root' or $_SERVER['PHP_AUTH_PW'] != 'toor') {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    exit;
} else {
    echo "<p>Hello, {$_SERVER['PHP_AUTH_USER']}!</p>";
    $_SESSION['admin'] = true;
}
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="jquery.redirect.js"></script>
<input id='login' name='login' placeholder="User's login"></input>
<button onclick="$.redirect('statistics.php', { 'user_id': $('#login').val() }, 'GET');">View</button>
