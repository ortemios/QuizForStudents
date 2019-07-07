<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="jquery.redirect.js"></script>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <?php 
        require('database_parameters.php');
        
        session_start();
        $connection = mysqli_connect($host, $username, $password, $database_name);
        
        $data_result = mysqli_query($connection, "SELECT * FROM data");
        $lessons_count = mysqli_fetch_array($data_result, MYSQLI_ASSOC)['lessons_count'];
        $user_result = mysqli_query($connection, "SELECT * FROM users WHERE id='$_SESSION[login]'");
        $lesson_id = mysqli_fetch_array($user_result, MYSQLI_ASSOC)['lesson_id'];
        
        mysqli_close($connection);
        
        echo "Welcome $_SESSION[login]!<br>";
        if($lesson_id < $lessons_count)
            echo "You are doing lesson ".($lesson_id+1)." of total $lessons_count.";
        else
        {
            echo "You have passed the course!";
            exit();
        }
        ?>
        <br>
        <div id="submit_form"></div>
        <div id="progress_bar_bg">
            <div id="progress_bar"></div>
        </div>
        <br>
        <font id="message" color="red"></font>
        <font id="attempt_warning" color="blue"></font>
        <br>
        <a id="question">Question</a>
        <br>
        <div id="answers">
        </div>
        <a id>Time remaining: </a>
        <a id="timer">10.0</a>
        <br>
        <button id="submit">Submit</button>
    </body>
    <script type="text/javascript" src="question_form.js"></script>
</html>
