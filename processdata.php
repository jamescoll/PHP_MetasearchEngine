<!-- processdata.php - this file contains the functionality for entering users into our database using mysqli as well as their answers
 to the survey questions - it also does some rudimentary error-checking on the data -->

<?php
//We will use this setting so that the page can be displayed to just show the image even
//if no db data has been entered..this is for display purposes
error_reporting(0);
// Create connection
$con = mysqli_connect("csserver.ucd.ie", "95288198", "tudenwd7", "coll");

// Check connection
if (mysqli_connect_errno($con)) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Metasearch</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="Metasearch Engine" content="">
        <meta name="James Coll" content="">

        <!-- Le styles -->
        <link href="css/bootstrap.css" rel="stylesheet">
        <style type="text/css">
            body {
                padding-top: 20px;
                padding-bottom: 60px;
            }

            /* Custom container */
            .container {
                margin: 0 auto;
                max-width: 1000px;
            }
            .container > hr {
                margin: 60px 0;
            }

            /* Main marketing message and sign up button */
            .jumbotron {
                margin: 80px 0;
                text-align: center;
            }
            .jumbotron h1 {
                font-size: 100px;
                line-height: 1;
            }
            .jumbotron .lead {
                font-size: 24px;
                line-height: 1.25;
            }
            .jumbotron .btn {
                font-size: 21px;
                padding: 14px 24px;
            }

            /* Supporting marketing content */
            .marketing {
                margin: 60px 0;
            }
            .marketing p + h4 {
                margin-top: 28px;
            }
            .masthead h1,h2,h3 {
                text-align: center;
            }

            /* Customize the navbar links to be fill the entire space of the .navbar */
            .navbar .navbar-inner {
                padding: 0;
            }
            .navbar .nav {
                margin: 0;
                display: table;
                width: 100%;
            }
            .navbar .nav li {
                display: table-cell;
                width: 1%;
                float: none;
            }
            .navbar .nav li a {
                font-weight: bold;
                text-align: center;
                border-left: 1px solid rgba(255,255,255,.75);
                border-right: 1px solid rgba(0,0,0,.1);
            }
            .navbar .nav li:first-child a {
                border-left: 0;
                border-radius: 3px 0 0 3px;
            }
            .navbar .nav li:last-child a {
                border-right: 0;
                border-radius: 0 3px 3px 0;
            }
        </style>
        <link href="css/bootstrap-responsive.css" rel="stylesheet">

        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.js"></script>
        <![endif]-->

        <!-- Fav and touch icons -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
        <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">
        <link rel="shortcut icon" href="../assets/ico/favicon.png">
    </head>

    <body>

        <div class="container">

            <div class="masthead">
                <h1 style="font-size:350%">Metasearch</h1><br />
                <div class="navbar">
                    <div class="navbar-inner">
                        <div class="container">
                            <ul class="nav">
                                <li><a href="index.php">Home</a></li>
                                <li><a href="metrics.php">Metrics</a></li>
                                <li class="active"><a href="review.php">Reviews</a></li>
                                <li><a href="about.php">About</a></li>

                            </ul>
                        </div>
                    </div>
                </div><!-- /.navbar -->
            </div>

            <div style="text-align:center; padding:35px">

<?php
if ($_POST['email'] && $_POST['fname'] && $_POST['lname']) {
    $email = $_POST['email'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];

    //place the user into the database
    $insertUser = "INSERT INTO Users (lastName, firstName, emailAddress) VALUES ('$lname', '$fname', '$email')";

    if (!mysqli_query($con, $insertUser)) {
        echo "Error inserting user: " . mysqli_error($con);
    }
    //get the id of the user so we can associate it with a question
    $getUserId = "SELECT userId, emailAddress FROM Users";


    //mysqli forces us to do this in a rather convoluted way
    //when we match by email address then we return the user id
    //into a variable - it would be nice to have an elegant way to do this
    $query = mysqli_query($con, $getUserId);

    while ($row = mysqli_fetch_array($query)) {
        $uNum = $row["userId"];
        $eMail = $row["emailAddress"];
        if ($eMail == $email) {
            $userId = $uNum;
        }
    }

    if ($_POST['question1']) {
        $q1 = $_POST['question1'];
        $insertAnswer = "INSERT INTO Answers (answerNo, questionId, userId) VALUES ('$q1', 1, '$userId')";
        if (!mysqli_query($con, $insertAnswer)) {
            echo "Error inserting answer: " . mysqli_error($con);
        }
    }
    if ($_POST['question2']) {
        $q2 = $_POST['question2'];
        $insertAnswer = "INSERT INTO Answers (answerNo, questionId, userId) VALUES ('$q2', 2, '$userId')";
        if (!mysqli_query($con, $insertAnswer)) {
            echo "Error inserting answer: " . mysqli_error($con);
        }
    }
    if ($_POST['question3']) {
        $q3 = $_POST['question3'];
        $insertAnswer = "INSERT INTO Answers (answerNo, questionId, userId) VALUES ('$q3', 3, '$userId')";
        if (!mysqli_query($con, $insertAnswer)) {
            echo "Error inserting answer: " . mysqli_error($con);
        }
    }
    if ($_POST['question4']) {
        $q4 = $_POST['question4'];
        $insertAnswer = "INSERT INTO Answers (answerNo, questionId, userId) VALUES ('$q4', 4, '$userId')";
        if (!mysqli_query($con, $insertAnswer)) {
            echo "Error inserting answer: " . mysqli_error($con);
        }
    }
    if ($_POST['question5']) {
        $q5 = $_POST['question5'];
        $insertAnswer = "INSERT INTO Answers (answerNo, questionId, userId) VALUES ('$q5', 5, '$userId')";
        if (!mysqli_query($con, $insertAnswer)) {
            echo "Error inserting answer: " . mysqli_error($con);
        }
    }
    if ($_POST['question6']) {
        $q6 = $_POST['question6'];
        $insertAnswer = "INSERT INTO Answers (answerNo, questionId, userId) VALUES ('$q6', 6, '$userId')";
        if (!mysqli_query($con, $insertAnswer)) {
            echo "Error inserting answer: " . mysqli_error($con);
        }
    }
    if ($_POST['question7']) {
        $q7 = $_POST['question7'];
        $insertAnswer = "INSERT INTO Answers (answerNo, questionId, userId) VALUES ('$q7', 7, '$userId')";
        if (!mysqli_query($con, $insertAnswer)) {
            echo "Error inserting answer: " . mysqli_error($con);
        }
    }
    if ($_POST['question8']) {
        $q8 = $_POST['question8'];
        $insertAnswer = "INSERT INTO Answers (answerNo, questionId, userId) VALUES ('$q8', 8, '$userId')";
        if (!mysqli_query($con, $insertAnswer)) {
            echo "Error inserting answer: " . mysqli_error($con);
        }
    }



    echo "<h2>Thank you for filling out my survey</h2><hr><h3>Current Survey Results</h3>";
    echo "<img src=\"img\\userreview.png\" alt=\"User Review@20 Users\" >";
} else {
    echo "<h2>Did you forget to enter something?</h2><hr><h3>Current Survey Results</h3>";
    ;
    echo "<img src=\"img\\userreview.png\" alt=\"User Review@20 Users\" >";
}
?>
            </div>

            <hr>

            <div class="footer" style="text-align:center">
                <p>&copy; James Coll</p>
            </div>

        </div> <!-- /container -->

        <!-- Le javascript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="js/jquery.js"></script>
        <script src="js/bootstrap-transition.js"></script>
        <script src="js/bootstrap-alert.js"></script>
        <script src="js/bootstrap-modal.js"></script>
        <script src="js/bootstrap-dropdown.js"></script>
        <script src="js/bootstrap-scrollspy.js"></script>
        <script src="js/bootstrap-tab.js"></script>
        <script src="js/bootstrap-tooltip.js"></script>
        <script src="js/bootstrap-popover.js"></script>
        <script src="js/bootstrap-button.js"></script>
        <script src="js/bootstrap-collapse.js"></script>
        <script src="js/bootstrap-carousel.js"></script>
        <script src="js/bootstrap-typeahead.js"></script>

    </body>
</html>


                <?php
                mysqli_close($con);
                ?>
