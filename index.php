<?php 
require('database_parameters.php');

session_start();
$_SESSION['login'] = 'UserThree';

$connection = mysqli_connect($host, $username, $password, $database_name);
        
$data_result = mysqli_query($connection, "SELECT * FROM data");
echo mysqli_fetch_array($data_result, MYSQLI_ASSOC)['description'];

mysqli_close($connection);

echo '<br>';

$root = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]".str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname($_SERVER['SCRIPT_FILENAME']));
echo file_get_contents("$root/generate_result_links.php?user_id=$_SESSION[login]");
?>
<br>
<a href="course.php">Start course.</a>
