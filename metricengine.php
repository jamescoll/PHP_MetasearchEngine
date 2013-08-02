<!-- metricengine.php - these functions and this code allow evaluations to be run real-time from the query list and golden standards list -->

<?php
include_once('simple_html_dom.php');
include_once('search.php');
include_once('aggregation.php');
include_once('clustering.php');
include_once('preprocess.php');

ini_set('display_errors', 'Off');
set_time_limit(0);
$numTestQueries = 5;
$numResults = 100;
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
                                <li class="active"><a href="metrics.php">Metrics</a></li>
                                <li><a href="review.php">Reviews</a></li>
                                <li><a href="about.php">About</a></li>

                            </ul>
                        </div>
                    </div>
                </div><!-- /.navbar -->
            </div>





            <?php

            //this function checks the golden list for duplicate entries (i.e. those that lead to the same location and eliminates them
            function clean_goldenlist(&$resultArray) {


                $arrSize = sizeof($resultArray);

                for ($i = 0; $i < $arrSize; $i++) {
                    for ($j = 0; $j < $arrSize; $j++) {

                        if ($resultArray[$i][1] == $resultArray[$j][1] && $i != $j) {

                            $resultArray[$j][1] .= "DUPLICATE";
                        }
                    }
                }
            }
            //this function loads the query list from a file and populates an array with them for processing
            function populate_querylist() {

                $file = fopen("data/querys.txt", "r") or exit("Unable to open file!");

                $querylist = array();


                while (!feof($file)) {

                    array_push($querylist, fgets($file));
                }

                fclose($file);

                return $querylist;
            }
            
            //this function loads the results list from a file and populates an array with them for processing

            function populate_goldenlist() {

                $file = fopen("data/relevance_judgments.txt", "r") or exit("Unable to open file!");

                $goldenlist = array();

                $index = 0;

                $tmpStr = "";

                while (!feof($file)) {
                    $line = fgets($file);
                    //break up our line by spaces
                    $entries = explode(" ", $line);
                    //let's zero-index these so they match the query values
                    $goldenlist[$index][0] = (intval($entries[0]) - 151);
                    //trim it so it will match with our search result arrays

                    if (isset($entries[1])) {
                        $tmpStr = remove_leading($entries[1]);
                        //get rid of trailing whitespace
                        $tmpStr = trim($tmpStr);
                        //now lose the backslash
                        $tmpStr = remove_trailing_backslash($tmpStr);
                        //any other tags
                        $goldenlist[$index][1] = strip_tags($tmpStr);
                    }
                    $line = "";
                    $entries = "";
                    $tmpStr = "";
                    $index++;
                }

                fclose($file);
                return $goldenlist;
            }

            $querylist = populate_querylist();
            $goldenlist = populate_goldenlist();
            clean_goldenlist($goldenlist);

            $evaluationArray = array();

            /* our stats array is going to contain 
              0  $query
              1   $bingprecisionAtThree
              2   $bingprecisionAtFive
              3   $bingprecisionAtTen
              4   $bingprecisionAtFifteen
              5   $googleprecisionAtThree
              6   $googleprecisionAtFive
              7   $googleprecisionAtTen
              8   $googleprecisionAtFifteen
              9   $blekkoprecisionAtThree
              10   $blekkoprecisionAtFive
              11   $blekkoprecisionAtTen
              12   $blekkoprecisionAtFifteen
              13   $combprecisionAtThree
              14   $combprecisionAtFive
              15   $combprecisionAtTen
              16   $combprecisionAtFifteen
              17   $bingRecall
              18   $blekkoRecall
              19   $googleRecall
              20   $combRecall
              21  $bingprecisionAtFinal
              22  $blekkoprecisionAtFinal
              23  $googleprecisionAtFinal
              24  $combprecisionAtFinal
              25  $bingFMeasure
              26  $blekkoFMeasure
              27 $googleFMeasure
              28 $combFMeasure

             */




            //get the current date/time for the evaluation
            $date = date('Y/m/d H:i:s');
            //is this a complete test or just a limited query test
            echo "<h3>Test run at " . $date . "</h3>";
            if ($numTestQueries == 50) {
                echo "<h3>Full Evaluation Run</h3>";
            } else {
                echo "<h3>Running for " . $numTestQueries . " queries</h3>";
            }
            //display data in a table
            echo "<table class=\"table table-bordered\">";
            echo "<tr><th>Query</th>";
            echo "<th>p@3</th>";
            echo "<th>p@5</th>";
            echo "<th>p@10</th>";
            echo "<th>p@15</th>";
            echo "<th>p@Mx</th>";
            echo "<th>Doc Count</th>";
            echo "<th>Recall</th>";
            echo "<th>F-measure</th>";
            echo "<th>Search Engine</th></tr>";


            //this loop goes through the data outputting the various metrics on-the-fly

            for ($m = 0; $m < $numTestQueries; $m++) {

                $tmpStrA = $querylist[$m];
                $evaluationArray[$m][0] = $tmpStrA;

                $query = urlencode("'$tmpStrA'");

                $bingResultArray = searchBing($query, $numResults);
                $blekkoResultArray = searchBlekko($query, $numResults);
                $googleResultArray = searchGoogle($query, $numResults);
                $combinedArray = aggregateCombMNZ($googleResultArray, $bingResultArray, $blekkoResultArray);


                $bingArrSize = sizeof($bingResultArray);
                $googleArrSize = sizeof($googleResultArray);
                $blekkoArrSize = sizeof($blekkoResultArray);

                $goldArrSize = sizeof($goldenlist);

                $bingcount = 0;
                $googlecount = 0;
                $blekkocount = 0;
                $combinedcount = 0;
                $bingprecisionAtThree = 0.0;
                $bingprecisionAtFive = 0.0;
                $bingprecisionAtTen = 0.0;
                $bingprecisionAtFifteen = 0.0;
                $bingprecisionAtFinal = 0.0;
                $bingFMeasure = 0.0;
                $googleprecisionAtThree = 0.0;
                $googleprecisionAtFive = 0.0;
                $googleprecisionAtTen = 0.0;
                $googleprecisionAtFifteen = 0.0;
                $googleprecisionAtFinal = 0.0;
                $googleFMeasure = 0.0;
                $blekkoprecisionAtThree = 0.0;
                $blekkoprecisionAtFive = 0.0;
                $blekkoprecisionAtTen = 0.0;
                $blekkoprecisionAtFifteen = 0.0;
                $blekkoprecisionAtFinal = 0.0;
                $blekkoFMeasure = 0.0;
                $combprecisionAtThree = 0.0;
                $combprecisionAtFive = 0.0;
                $combprecisionAtTen = 0.0;
                $combprecisionAtFifteen = 0.0;
                $combprecisionAtFinal = 0.0;
                $combFMeasure = 0.0;
                $bingRecall = 0.0;
                $blekkoRecall = 0.0;
                $googleRecall = 0.0;
                $combRecall = 0.0;


                for ($i = 0; $i < $bingArrSize; $i++) {

                    for ($j = 0; $j < $goldArrSize; $j++) {
                        if ($goldenlist[$j][0] == $m) {

                            if ($bingResultArray[$i][0] == $goldenlist[$j][1]) {

                                $bingcount++;
                            }
                        }
                    }

                    if ($i == 2) {

                        $bingprecisionAtThree = ($bingcount / 3.0);
                    }
                    if ($i == 4) {
                        $bingprecisionAtFive = ($bingcount / 5.0);
                    }
                    if ($i == 9) {

                        $bingprecisionAtTen = ($bingcount / 10.0);
                    }
                    if ($i == 14) {

                        $bingprecisionAtFifteen = ($bingcount / 15.0);
                    }
                }
                $bingprecisionAtFinal = ($bingcount / $bingArrSize);
                $bingRecall = ($bingcount / 100.0);
                $evaluationArray[$m][1] = $bingprecisionAtThree;
                $evaluationArray[$m][2] = $bingprecisionAtFive;
                $evaluationArray[$m][3] = $bingprecisionAtTen;
                $evaluationArray[$m][4] = $bingprecisionAtFifteen;
                $evaluationArray[$m][21] = $bingprecisionAtFinal;
                $bingFMeasure = (2.0 * $bingprecisionAtFinal * $bingRecall) / ($bingprecisionAtFinal + $bingRecall);
                $evaluationArray[$m][25] = $bingFMeasure;



                echo "<tr><td><b>" . $querylist[$m] . "</b></td>";
                echo "<td>" . number_format($bingprecisionAtThree, 10, '.', '') . "</td>";
                echo "<td>" . number_format($bingprecisionAtFive, 10, '.', '') . "</td>";
                echo "<td>" . number_format($bingprecisionAtTen, 10, '.', '') . "</td>";
                echo "<td>" . number_format($bingprecisionAtFifteen, 10, '.', '')  . "</td>";
                echo "<td>" . number_format($bingprecisionAtFinal, 10, '.', '')  . "</td>";
                echo "<td>" . $bingArrSize . "</td>";
                echo "<td>" . number_format($bingRecall, 10, '.', '')  . "</td>";
                echo "<td>" . number_format($bingFMeasure, 10, '.', '')  . "</td>";
                echo "<td><em>Bing</em></td></tr>";


                $evaluationArray[$m][17] = $bingRecall;


                for ($i = 0; $i < $blekkoArrSize; $i++) {

                    for ($j = 0; $j < $goldArrSize; $j++) {
                        if ($goldenlist[$j][0] == $m) {

                            if ($blekkoResultArray[$i][0] == $goldenlist[$j][1]) {

                                $blekkocount++;
                            }
                        }
                    }
                    if ($i == 2) {

                        $blekkoprecisionAtThree = ($blekkocount / 3.0);
                    }
                    if ($i == 4) {
                        $blekkoprecisionAtFive = ($blekkocount / 5.0);
                    }
                    if ($i == 9) {

                        $blekkoprecisionAtTen = ($blekkocount / 10.0);
                    }
                    if ($i == 14) {

                        $blekkoprecisionAtFifteen = ($blekkocount / 15.0);
                    }
                }
                $blekkoprecisionAtFinal = ($blekkocount / $blekkoArrSize);
                $blekkoRecall = ($blekkocount / 100.0);
                $evaluationArray[$m][9] = $blekkoprecisionAtThree;
                $evaluationArray[$m][10] = $blekkoprecisionAtFive;
                $evaluationArray[$m][11] = $blekkoprecisionAtTen;
                $evaluationArray[$m][12] = $blekkoprecisionAtFifteen;
                $evaluationArray[$m][22] = $blekkoprecisionAtFinal;
                $blekkoFMeasure = (2.0 * $blekkoprecisionAtFinal * $blekkoRecall) / ($blekkoprecisionAtFinal + $blekkoRecall);
                $evaluationArray[$m][26] = $blekkoFMeasure;



               echo "<tr><td><b>" . $querylist[$m] . "</b></td>";
                echo "<td>" . number_format($blekkoprecisionAtThree, 10, '.', '') . "</td>";
                echo "<td>" . number_format($blekkoprecisionAtFive, 10, '.', '') . "</td>";
                echo "<td>" . number_format($blekkoprecisionAtTen, 10, '.', '') . "</td>";
                echo "<td>" . number_format($blekkoprecisionAtFifteen, 10, '.', '')  . "</td>";
                echo "<td>" . number_format($blekkoprecisionAtFinal, 10, '.', '')  . "</td>";
                echo "<td>" . $blekkoArrSize . "</td>";
                echo "<td>" . number_format($blekkoRecall, 10, '.', '')  . "</td>";
                echo "<td>" . number_format($blekkoFMeasure, 10, '.', '')  . "</td>";
                echo "<td><em>Blekko</em></td></tr>";



                $evaluationArray[$m][18] = $blekkoRecall;


                for ($i = 0; $i < $googleArrSize; $i++) {

                    for ($j = 0; $j < $goldArrSize; $j++) {
                        if ($goldenlist[$j][0] == $m) {

                            if ($googleResultArray[$i][0] == $goldenlist[$j][1]) {

                                $googlecount++;
                            }
                        }
                    }
                    if ($i == 2) {

                        $googleprecisionAtThree = ($googlecount / 3.0);
                    }
                    if ($i == 4) {
                        $googleprecisionAtFive = ($googlecount / 5.0);
                    }
                    if ($i == 9) {

                        $googleprecisionAtTen = ($googlecount / 10.0);
                    }
                    if ($i == 14) {

                        $googleprecisionAtFifteen = ($googlecount / 15.0);
                    }
                }
                $googleRecall = ($googlecount / 100.0);
                $googleprecisionAtFinal = ($googlecount / $googleArrSize);
                $evaluationArray[$m][5] = $googleprecisionAtThree;
                $evaluationArray[$m][6] = $googleprecisionAtFive;
                $evaluationArray[$m][7] = $googleprecisionAtTen;
                $evaluationArray[$m][8] = $googleprecisionAtFifteen;
                $evaluationArray[$m][23] = $googleprecisionAtFinal;
                $googleFMeasure = (2.0 * $googleprecisionAtFinal * $googleRecall) / ($googleprecisionAtFinal + $googleRecall);
                $evaluationArray[$m][27] = $googleFMeasure;

                echo "<tr><td><b>" . $querylist[$m] . "</b></td>";
                echo "<td>" . number_format($googleprecisionAtThree, 10, '.', '') . "</td>";
                echo "<td>" . number_format($googleprecisionAtFive, 10, '.', '') . "</td>";
                echo "<td>" . number_format($googleprecisionAtTen, 10, '.', '') . "</td>";
                echo "<td>" . number_format($googleprecisionAtFifteen, 10, '.', '')  . "</td>";
                echo "<td>" . number_format($googleprecisionAtFinal, 10, '.', '')  . "</td>";
                echo "<td>" . $googleArrSize . "</td>";
                echo "<td>" . number_format($googleRecall, 10, '.', '')  . "</td>";
                echo "<td>" . number_format($googleFMeasure, 10, '.', '')  . "</td>";
                echo "<td><em>Google</em></td></tr>";



                $evaluationArray[$m][19] = $googleRecall;


                for ($i = 0; $i < 100; $i++) {

                    for ($j = 0; $j < $goldArrSize; $j++) {
                        if ($goldenlist[$j][0] == $m) {

                            if ($combinedArray[$i][0] == $goldenlist[$j][1]) {

                                $combinedcount++;
                            }
                        }
                    }
                    if ($i == 2) {

                        $combprecisionAtThree = ($combinedcount / 3.0);
                    }
                    if ($i == 4) {
                        $combprecisionAtFive = ($combinedcount / 5.0);
                    }
                    if ($i == 9) {

                        $combprecisionAtTen = ($combinedcount / 10.0);
                    }
                    if ($i == 14) {

                        $combprecisionAtFifteen = ($combinedcount / 15.0);
                    }
                }
                $combprecisionAtFinal = ($combinedcount / 100.0);
                $combRecall = ($combinedcount / 100.0);
                $evaluationArray[$m][13] = $combprecisionAtThree;
                $evaluationArray[$m][14] = $combprecisionAtFive;
                $evaluationArray[$m][15] = $combprecisionAtTen;
                $evaluationArray[$m][16] = $combprecisionAtFifteen;
                $evaluationArray[$m][24] = $combprecisionAtFinal;
                $combFMeasure = (2.0 * $combprecisionAtFinal * $combRecall) / ($combprecisionAtFinal + $combRecall);
                $evaluationArray[$m][28] = $combFMeasure;

              echo "<tr><td><b>" . $querylist[$m] . "</b></td>";
                echo "<td>" . number_format($combprecisionAtThree, 10, '.', '') . "</td>";
                echo "<td>" . number_format($combprecisionAtFive, 10, '.', '') . "</td>";
                echo "<td>" . number_format($combprecisionAtTen, 10, '.', '') . "</td>";
                echo "<td>" . number_format($combprecisionAtFifteen, 10, '.', '')  . "</td>";
                echo "<td>" . number_format($combprecisionAtFinal, 10, '.', '')  . "</td>";
                echo "<td>100</td>";
                echo "<td>" . number_format($combRecall, 10, '.', '')  . "</td>";
                echo "<td>" . number_format($combFMeasure, 10, '.', '')  . "</td>";
                echo "<td><em>Aggregated</em></td></tr>";


                $evaluationArray[$m][20] = $combRecall;
            }

            echo "</table>";

            $bingMeanAveragePrecision = 0.0;
            $blekkoMeanAveragePrecision = 0.0;
            $googleMeanAveragePrecision = 0.0;
            $combMeanAveragePrecision = 0.0;

            $bingAverageFMeasure = 0.0;
            $googleAverageFMeasure = 0.0;
            $blekkoAverageFMeasure = 0.0;
            $combAverageFMeasure = 0.0;

            for ($m = 0; $m < $numTestQueries; $m++) {

                $bingAveragePrecision = 0.0;
                $blekkoAveragePrecision = 0.0;
                $googleAveragePrecision = 0.0;
                $combAveragePrecision = 0.0;



                $bingAveragePrecision += $evaluationArray[$m][1];

                $bingAveragePrecision += $evaluationArray[$m][2];

                $bingAveragePrecision += $evaluationArray[$m][3];

                $bingAveragePrecision += $evaluationArray[$m][4];
                $bingAveragePrecision = $bingAveragePrecision / 100.0;
                $bingMeanAveragePrecision += $bingAveragePrecision;
                $bingAverageFMeasure += $evaluationArray[$m][25];



                $googleAveragePrecision += $evaluationArray[$m][5];
                $googleAveragePrecision += $evaluationArray[$m][6];
                $googleAveragePrecision += $evaluationArray[$m][7];
                $googleAveragePrecision += $evaluationArray[$m][8];
                $googleAveragePrecision = $googleAveragePrecision / 100.0;
                $googleMeanAveragePrecision += $googleAveragePrecision;
                $googleAverageFMeasure += $evaluationArray[$m][27];



                $blekkoAveragePrecision += $evaluationArray[$m][9];
                $blekkoAveragePrecision += $evaluationArray[$m][10];
                $blekkoAveragePrecision += $evaluationArray[$m][11];
                $blekkoAveragePrecision += $evaluationArray[$m][12];
                $blekkoAveragePrecision = $blekkoAveragePrecision / 100.0;
                $blekkoMeanAveragePrecision += $blekkoAveragePrecision;
                $blekkoAverageFMeasure += $evaluationArray[$m][26];


                $combAveragePrecision += $evaluationArray[$m][13];
                $combAveragePrecision += $evaluationArray[$m][14];
                $combAveragePrecision += $evaluationArray[$m][15];
                $combAveragePrecision += $evaluationArray[$m][16];
                $combAveragePrecision = $combAveragePrecision / 100.0;
                $combMeanAveragePrecision += $combAveragePrecision;
                $combAverageFMeasure += $evaluationArray[$m][28];
            }

            echo "<h3>Overall Statistics</h3>";

            echo "<table class=\"table table-bordered\">";
            echo "<tr><th>Search Engine</th>";
            echo "<th>MAP</th>";
            echo "<th>F-Measure</th></tr>";

            $bingMeanAveragePrecision = $bingMeanAveragePrecision / $numTestQueries;
            $bingAverageFMeasure = $bingAverageFMeasure / $numTestQueries;
            echo "<tr><td>Bing</td><td>" . $bingMeanAveragePrecision . "</td><td>" . $bingAverageFMeasure . "</td></tr>";
            echo "<tr><td>Google</td><td>" . $googleMeanAveragePrecision . "</td><td>" . $googleAverageFMeasure . "</td></tr>";
            echo "<tr><td>Blekko</td><td>" . $blekkoMeanAveragePrecision . "</td><td>" . $blekkoAverageFMeasure . "</td></tr>";
            echo "<tr><td>Aggregated</td><td>" . $combMeanAveragePrecision . "</td><td>" . $combAverageFMeasure . "</td></tr></table>";
            ?>

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

