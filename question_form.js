/* global parseFloat */
var question = document.getElementById("question");
var answers = document.getElementById("answers");
var timer = document.getElementById("timer");
var progress_bar = document.getElementById("progress_bar");
var error_warning = document.getElementById("message");
var attempt_warning = document.getElementById("attempt_warning");

var questions = [];
var current_question;
var current_answer;
var question_indices = [];
var question_index = 0;

var time_remaining = 0;
var attempts = 1;
var wrong_answers = 0;

// Load questions and fill answer_data(per each question)
$.get('get_questions.php', function(data) {
    questions = $.parseJSON(data);
    for(var i = 0; i < questions.length; i++)
    {
        var answer_data =
        {
            answer: "-",
            proper_answer: questions[i]['answer_' + questions[i]['proper_answer']],
            time: 0,
            efficiency: 0,
            question_id: questions[i].id
        };
        questions[i].answer_data = answer_data;
        questions[i].index = i;
        question_indices.push(i);
    }
    perform_question();
});

// Loads and displays questions[question_id]
function perform_question()
{
    if (question_index === question_indices.length)
    {
        $.redirect('submit_userdata.php', {'data': JSON.stringify(questions)}, "POST");
    }
    current_question = questions[question_indices[question_index]];
    current_answer = current_question.answer_data;
    
    time_remaining = current_question.seconds * 1000;
    
    question.innerHTML = 
            "Question " + (current_question.index+1) + "/" + questions.length + ": " +
            current_question.text;
    answers.innerHTML = "";
    for(var i = 0; i < current_question.answers; i++)
    {
        var text = current_question['answer_' + i];
        var t = '<input type="radio" name="answer"/>' + text + '</input>';
        answers.innerHTML += t + '<br>\n';
    }
    var t = "<input type='radio' name='answer'/>I dont't know</input>";
    answers.innerHTML += t + '<br>\n';
}

// Saves current answer's data and increases question_id
function finish_question(right_answer)
{
    var efficiency = 0;
    if (right_answer)
    {
        var time_bonus = (1 - current_answer.time / (current_question.seconds * 1000)) * 100;
        efficiency = 100 - 50 * wrong_answers + time_bonus;
    }

    current_answer.efficiency = efficiency;
    error_warning.innerHTML = "";
    attempt_warning.innerHTML = "";
    attempts = 1;
    wrong_answers = 0;
}

// On submit button click
document.getElementById("submit").onclick = function() {
    // Get selected and proper answers
    var radios = document.getElementsByName('answer');
    var proper_answer = parseInt(current_question.proper_answer);
    var dont_know = radios.length - 1;
    var answer = dont_know;
    for (var i = 0; i < dont_know; i++)
    {
        if (radios[i].checked)
        {
            answer = i;
            current_answer.answer = current_question['answer_' + i];
            break;
        }
    }
    if(answer === proper_answer || answer === dont_know)
    {
        finish_question(answer === proper_answer);
    }
    else
    {
        error_warning.innerHTML = 'Wrong answer!';
        wrong_answers++;
        question_indices.push(current_question.index);
    }
    question_index++;
    // If wrong answer, question_id did not increase and we display the same question
    perform_question();
};

// Timer, activates every 100ms, updates progress bar
var intervalID = setInterval(function()
{
    current_answer.time += 100;
    
    if(time_remaining <= 0)
    {
        attempts++;
        attempt_warning.innerHTML = "Warning: attempt №" + attempts;
        perform_question();
    }
    else
    {
        time_remaining -= 100;
    }
    timer.innerHTML = parseFloat(time_remaining / 1000).toFixed(1);
    progress_bar.style.width = (time_remaining / (current_question.seconds*1000)*100).toString() + "%";
}, 100);
