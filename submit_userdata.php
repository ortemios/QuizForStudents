<?php

require('database_parameters.php');

session_start();
$user_id = $_SESSION['login'];
$userdata = json_decode(filter_input(INPUT_POST, 'data'), true);

$connection = mysqli_connect($host, $username, $password, $database_name);

$user_result = mysqli_query($connection, "SELECT * FROM users WHERE id='$user_id'");
$lesson_id = mysqli_fetch_array($user_result, MYSQLI_ASSOC)['lesson_id'];
header("Location: statistics.php?user_id=$user_id&lesson_id=$lesson_id");

// Remove old answers and avg_efficiency for current(user_id, lesson_id)
mysqli_query($connection, "DELETE FROM answers WHERE user_id='$user_id' AND lesson_id='$lesson_id'");
mysqli_query($connection, "DELETE FROM avg_efficiency WHERE user_id='$user_id' AND lesson_id='$lesson_id'");

// Add answers
$sum_efficiency = 0;
foreach ($userdata as $v)
{
    $a = $v['answer_data'];
    $query = "INSERT INTO answers VALUES(NULL, '$user_id', '$lesson_id', '$a[answer]', '$a[proper_answer]', '" . ($a['time'] / 1000) . "', '$a[efficiency]', '$a[question_id]')";
    mysqli_query($connection, $query);
    $sum_efficiency += $a['efficiency'];
}

// Add avg_efficiency
$efficiency = ($sum_efficiency / count($userdata));
$query = "INSERT INTO avg_efficiency VALUES(NULL, '$user_id', '$lesson_id', '$efficiency')";
mysqli_query($connection, $query);

// Get total efficiency
$total_efficiency = 0;
$efficiency_result = mysqli_query($connection, "SELECT * FROM avg_efficiency WHERE user_id='$user_id'");
while ($row = mysqli_fetch_array($efficiency_result, MYSQLI_ASSOC))
{
    $total_efficiency += $row['value'];
}
// Save user's data
if ($efficiency > 70)
    $lesson_id++;
$query = "UPDATE users SET lesson_id='$lesson_id', total_efficiency='$total_efficiency' WHERE id='$user_id'";
mysqli_query($connection, $query);

mysqli_close($connection);


