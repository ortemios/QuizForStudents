<?php

require('database_parameters.php');

session_start();
$id = $_SESSION['login'];

$connection = mysqli_connect($host, $username, $password, $database_name);

$result1 = mysqli_query($connection, "SELECT * FROM users WHERE id='$id'");
$lesson_id = mysqli_fetch_array($result1, MYSQLI_ASSOC)['lesson_id'];
$result2 = mysqli_query($connection, "SELECT * FROM questions WHERE lesson_id='$lesson_id'");
if(mysqli_num_rows($result2) == 0) // Last lesson complete
{
    exit();
}
$questions = array();
$i = 0;
while ($row = mysqli_fetch_array($result2, MYSQLI_ASSOC)) 
{
    $questions[$i++] = $row;
}

mysqli_close($connection);

echo json_encode($questions);

