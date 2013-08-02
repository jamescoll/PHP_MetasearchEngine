<!-- review.php - this page contains the code which reads the survey questions from the database and formats them in a readable way for the
 user - it passes input data to processdata.php for entry into the database and processing -->

<?php
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
                <h4>User Review</h4>
                <h5>After you submit you can view survey data</h5>
                <hr>
                <p>1 = strongly disagree | 2 = disagree | 3 = undecided | 4 = agree | 5 = strongly agree </p>
                <hr>
                <form class="form-search" method="POST" action="processdata.php">  

                    <?php
                    //get the set of questions from the question table in the database and then output them
                    //as formatted html
                    $result = mysqli_query($con, "SELECT * FROM Questions");
                    $questNum = 1;
                    while ($row = mysqli_fetch_array($result)) {
                        echo $row['questionId'] . " " . $row['questionTxt'] . "<br />";
                        echo '<br /><label class="radio inline">
  <input type="radio" name="question' . $questNum . '" value="1"> 1
</label>
<label class="radio inline">
  <input type="radio" name="question' . $questNum . '" value="2"> 2
</label>
<label class="radio inline">
  <input type="radio" name="question' . $questNum . '" value="3"> 3
</label>
<label class="radio inline">
  <input type="radio" name="question' . $questNum . '" value="4"> 4
</label>
<label class="radio inline">
  <input type="radio" name="question' . $questNum . '" value="5"> 5
</label><br /><br />';
                        $questNum += 1;
                    }
                    ?>


                    <div class="control-group">
                        <label class="control-label" for="inputEmail">Email</label>
                        <div class="controls">
                            <input name="email" type="text" id="inputEmail" placeholder="Email">
                        </div>
                        <label class="control-label" for="firstName">First Name</label>
                        <div class="controls">
                            <input name ="fname" type="text" id="inputfirstName" placeholder="First Name">
                        </div>
                        <label class="control-label" for="lastName">Last Name</label>
                        <div class="controls">
                            <input name="lname" type="text" id="inputlastName" placeholder="Last Name">
                        </div>
                    </div>


                    <input class="btn" name="bt_submit" type="submit" value="Submit">         
                </form> 

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
//close our connection
mysqli_close($con);
?>
