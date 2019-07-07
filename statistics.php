<?php

require('database_parameters.php');

session_start();
$user_id = filter_input(INPUT_GET, 'user_id');
$lesson_id = filter_input(INPUT_GET, 'lesson_id');

if (!(isset($_SESSION['login']) and $user_id == $_SESSION['login']) and ! isset($_SESSION['admin']))
{
    echo 'Forbidden!';
    exit();
}
$root = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]".str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname($_SERVER['SCRIPT_FILENAME']));
$connection = mysqli_connect($host, $username, $password, $database_name);

$data_result = mysqli_query($connection, "SELECT * FROM data");
$lessons_count = mysqli_fetch_array($data_result, MYSQLI_ASSOC)['lessons_count'];

echo '<b>' . $user_id . '</b><br>';
output_rank_table($connection, $user_id, $lesson_id);
output_results_table($connection, $user_id, $lesson_id);
echo file_get_contents("$root/generate_result_links.php?user_id=$user_id");
mysqli_close($connection);
if($lesson_id < $lessons_count-1)
    echo "<a href=$root/course.php>Next lesson.</a><br>";
else
    echo "Course Over, congratulations!";

function output_rank_table($connection, $user_id, $lesson_id)
{
    echo "<table border='1'>";
    echo "<caption>Lesson ".($lesson_id+1).":</caption>";
    echo "<tr>";
    echo "<th>Username</th>";
    echo "<th>Efficiency</th>";
    echo "<th>Rank</th>";
    echo "</tr>";
    $efficiency_result = mysqli_query($connection, "SELECT * FROM avg_efficiency WHERE lesson_id='$lesson_id' ORDER BY value DESC");
    $last_eff = 99999999;
    $rank = 0;
    $i = 0;
    while ($row = mysqli_fetch_array($efficiency_result, MYSQLI_ASSOC))
    {
        $eff = $row['value'];
        if ($eff < $last_eff)
        {
            $last_eff = $eff;
            $rank++;
        }
        if (($row['user_id'] == $user_id) or ( $i++ < 10))
        {
            echo "<tr>";
            $params = ($row['user_id'] == $user_id ? "color='blue' face='bold'" : "");
            add_cell("<font $params><b>$row[user_id]</b></font>");
            add_cell($eff);
            add_cell($rank);
            echo "</tr>";
        }
    }
    echo "</table>";
}

function output_results_table($connection, $user_id, $lesson_id)
{
    echo "<table border='1'>";
    echo "<caption>Your answers:</caption>";
    echo "<tr>";
    echo "<th>Your answer</th>";
    echo "<th>Proper answer</th>";
    echo "<th>Time taken</th>";
    echo "<th>Efficiency</th>";
    echo "<th>View explanation</th>";
    echo "</tr>";

    $answers_result = mysqli_query($connection, "SELECT * FROM answers WHERE user_id='$user_id' AND lesson_id='$lesson_id'");
    while ($row = mysqli_fetch_array($answers_result, MYSQLI_ASSOC))
    {
        $question_result = mysqli_query($connection, "SELECT * FROM questions WHERE id='$row[question_id]'");
        $explanation = mysqli_fetch_array($question_result, MYSQLI_ASSOC)['explanation'];
        echo "<tr>";
        add_cell($row['answer']);
        add_cell($row['proper_answer']);
        add_cell($row['time']);
        add_cell($row['efficiency']);
        add_cell("<button onclick=\"alert('$explanation')\">Explanation</button>");
        echo "</tr>";
    }
    echo "</table>";
}

function add_cell($v)
{
    echo "<td>";
    echo $v;
    echo "</td>";
}
