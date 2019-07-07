<?php

require('database_parameters.php');

$root = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]".str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname($_SERVER['SCRIPT_FILENAME']));

$user_id = filter_input(INPUT_GET, 'user_id');

$connection = mysqli_connect($host, $username, $password, $database_name);

$data_result = mysqli_query($connection, "SELECT * FROM data");
$lessons_count = mysqli_fetch_array($data_result, MYSQLI_ASSOC)['lessons_count'];
$user_result = mysqli_query($connection, "SELECT * FROM users WHERE id='$user_id'");
$last_lesson = mysqli_fetch_array($user_result, MYSQLI_ASSOC)['lesson_id'];
echo "<br>";
for($i = 0; $i < $last_lesson; $i++)
{
    if($i == $lessons_count)
        break;
    $url = "$root/statistics.php?user_id=$user_id&lesson_id=$i";
    echo "<a href=$url>Lesson ".($i+1)."</a><br>";
}

mysqli_close($connection);