<!--aggregation.php - this contains the various functions used when performing aggregation on the search engine results -->
<?php

//this function normalizes a rank 1-100 to a decimal value 1-0
//it accepts a rank (integer) and a number of results (int)
//it returns a normalized rank (float)
function convertRankOneToZero($i, $nResults) {

    settype($nResults, "float");
    settype($i, "float");
    return ($nResults - ($i - 1.0000)) / $nResults;
}

//this function sorts an array by a specified column
//it accepts an array (by reference), a column (int), and a direction (asc/desc)
function arraySortByColumn(&$arr, $col, $dir) {
    $sort_col = array();
    foreach ($arr as $key => $row) {
        $sort_col[$key] = $row[$col];
    }

    array_multisort($sort_col, $dir, $arr);
}
//this function implements the CombMNZ method of aggregation
//it accepts three arrays representing the three search engines
//it returns an aggregated sorted array
function aggregateCombMNZ($googleArray, $bingArray, $blekkoArray) {



    
    
    //get the result set sizes in case a search engine hasn't returned
    //the specified number of results

    $googleResultSetSize = sizeof($googleArray);
    $bingResultSetSize = sizeof($bingArray);
    $blekkoResultSetSize = sizeof($blekkoArray);




    $m = 0;

    for ($i = 0; $i < $googleResultSetSize; $i++) {


        for ($j = 0; $j < $bingResultSetSize; $j++) {

            if (strcmp($googleArray[$i][0], $bingArray[$j][0]) == 0 && $googleArray[$i][4] != 1 && $bingArray[$j][4] != 1) {

                //1. url 2. snippet 3. rank 4. combMNZ 5. aggregated 6. search engine 7. number of engines 8. the cluster number (default zero)
                //our paired url 9. the title
                $combinedTmpArray[$m][0] = $googleArray[$i][0];

                $combinedTmpArray[$m][1] = $googleArray[$i][1];

                //our combined rank - lowest is best 
                $combinedTmpArray[$m][2] = convertRankOneToZero($googleArray[$i][2], $googleResultSetSize) + convertRankOneToZero($bingArray[$j][2], $bingResultSetSize);
                //our combMNZ score
                $combinedTmpArray[$m][3] = $combinedTmpArray[$m][2] * 2;
                $combinedTmpArray[$m][4] = 1;
                //make sure these elements aren't counted twice
                $googleArray[$i][4] = 1;
                $bingArray[$j][4] = 1;
                //string identifier for search engines
                $combinedTmpArray[$m][5] = "Google Bing";
                //the number of search engines returning
                $combinedTmpArray[$m][6] = 2;
                //the index of the cluster - this will be filled in in the cluster function
                $combinedTmpArray[$m][7] = 0;
                //an array for the cluster vectors
                $combinedTmpArray[$m][8] = array();
                //a value for snippet magnitude
                $combinedTmpArray[$m][9] = 0;
                //the display title
                $combinedTmpArray[$m][10] =  $googleArray[$i][7];
                $m++;
            }
        }
    }



    $j = sizeof($combinedTmpArray);

    for ($i = 0; $i < $googleResultSetSize; $i++) {

        if ($googleArray[$i][4] == 0) {
            $combinedTmpArray[$j][0] = $googleArray[$i][0];

            $combinedTmpArray[$j][1] = $googleArray[$i][1];

            //our combined rank - lowest is best 
            $combinedTmpArray[$j][2] = convertRankOneToZero($googleArray[$i][2], $googleResultSetSize);
            //our combMNZ score
            $combinedTmpArray[$j][3] = $combinedTmpArray[$j][2];
            $combinedTmpArray[$j][4] = 1;
            //make sure these elements aren't counted twice
            $googleArray[$i][4] = 1;
            //string identifier for search engines
            $combinedTmpArray[$j][5] = $googleArray[$i][5];
            //the number of search engines returning
            $combinedTmpArray[$j][6] = 1;
            //the index of the cluster - this will be filled in in the cluster function
            $combinedTmpArray[$j][7] = 0;
            //an array for the cluster vectors
            $combinedTmpArray[$j][8] = array();
            $combinedTmpArray[$j][9] = 0;
            //the display title
            $combinedTmpArray[$j][10] =  $googleArray[$i][7];
            $j++;
        }
    }

    for ($i = 0; $i < $bingResultSetSize; $i++) {


        if ($bingArray[$i][4] == 0) {
            $combinedTmpArray[$j][0] = $bingArray[$i][0];

            $combinedTmpArray[$j][1] = $bingArray[$i][1];

            //our combined rank - lowest is best 
            $combinedTmpArray[$j][2] = convertRankOneToZero($bingArray[$i][2], $bingResultSetSize);
            //our combMNZ score
            $combinedTmpArray[$j][3] = $combinedTmpArray[$j][2];
            $combinedTmpArray[$j][4] = 1;
            //make sure these elements aren't counted twice
            $bingArray[$i][4] = 1;
            //string identifier for search engines
            $combinedTmpArray[$j][5] = $bingArray[$i][5];
            //the number of search engines returning
            $combinedTmpArray[$j][6] = 1;
            //the index of the cluster - this will be filled in in the cluster function
            $combinedTmpArray[$j][7] = 0;
            //an array for the cluster vectors
            $combinedTmpArray[$j][8] = array();
            $combinedTmpArray[$j][9] = 0;
            //the display title
            $combinedTmpArray[$j][10] =  $bingArray[$i][7];
            $j++;
        }
    }

    for ($i = 0; $i < $j; $i++) {

        for ($m = 0; $m < $blekkoResultSetSize; $m++) {

            if (strcmp($combinedTmpArray[$i][0], $blekkoArray[$m][0]) == 0 && $blekkoArray[$m][4] != 1) {


                //our combined rank - lowest is best 
                $combinedTmpArray[$i][2] += convertRankOneToZero($blekkoArray[$m][2], $blekkoResultSetSize);
                //our combMNZ score
                if ($combinedTmpArray[$i][6] == 1) {
                    $combinedTmpArray[$i][3] = $combinedTmpArray[$i][2] * 2.0;
                } else {
                    $combinedTmpArray[$i][3] = $combinedTmpArray[$i][2] * 3.0;
                }
                $combinedTmpArray[$i][4] = 1;
                //make sure these elements aren't counted twice
                $blekkoArray[$m][4] = 1;

                //string identifier for search engines
                $combinedTmpArray[$i][5] .= " Blekko";
                //the number of search engines returning
                $combinedTmpArray[$i][6] += 1;
                //the index of the cluster - this will be filled in in the cluster function
                $combinedTmpArray[$i][7] = 0;
                //an array for the cluster vectors
                $combinedTmpArray[$i][8] = array();
                //the display title
                $combinedTmpArray[$i][9] = 0;
                
            }
        }
    }

    for ($i = 0; $i < $blekkoResultSetSize; $i++) {

        if ($blekkoArray[$i][4] == 0) {
            $combinedTmpArray[$j][0] = $blekkoArray[$i][0];

            $combinedTmpArray[$j][1] = $blekkoArray[$i][1];

            //our combined rank - lowest is best 
            $combinedTmpArray[$j][2] = convertRankOneToZero($blekkoArray[$i][2], $blekkoResultSetSize);
            //our combMNZ score
            $combinedTmpArray[$j][3] = $combinedTmpArray[$j][2];
            $combinedTmpArray[$j][4] = 1;
            //make sure these elements aren't counted twice
            $blekkoArray[$i][4] = 1;
            //string identifier for search engines
            $combinedTmpArray[$j][5] = $blekkoArray[$i][5];
            //string identifier for search engines
            $combinedTmpArray[$j][6] = 1;
            //the index of the cluster - this will be filled in in the cluster function
            $combinedTmpArray[$j][7] = 0;
            //an array for the cluster vectors
            $combinedTmpArray[$j][8] = array();
            $combinedTmpArray[$j][9] = 0;
            //the display title
            $combinedTmpArray[$j][10] =  $blekkoArray[$i][7];
            $j++;
        }
    }


    //the last step is to order the array which we will do by the 3rd index of the array i.e. the CombMNZ value
    arraySortByColumn($combinedTmpArray, 3, SORT_DESC);

    return $combinedTmpArray;

    
}

?>
