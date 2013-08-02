<!-- query.php - this constitutes the 'main' function within our program - it calls the functions to do query refinement, search, 
 aggregation as well as clustering - it also formats the output for the user -->

<?php

include_once('simple_html_dom.php');
include_once('search.php');
include_once('aggregation.php');
include_once('clustering.php');
include_once('preprocess.php');


$finalResultStr = "";
$numResults = 100;

error_reporting(E_ALL);
//ini_set('display_errors', 'Off');
ini_set('max_execution_time', 300);


//set to zero for boolean testing
$outputType = 0;

// Read the contents of the .html file into a string.

$contents = file_get_contents('index.php');




if ($_POST['query'] || $_POST['thesaurus']) {

    if ($_POST['query']) {
        $query = $_POST['query'];
	
    }
    
    if ($_POST['outputType']) {
        //1 is show all engines 2 is aggregate 3 is cluster
    	$outputType = $_POST['outputType'];
    }
    
    

    if ($_POST['thesaurus']) {
        $query = $_POST['thesaurus'];
    }

    

    
	//easter egg
	if($query == "ninja"||$query == "Ninja")
	{
		
		$outputType = -1;
		
		$ninjaImg = "<a href=\"index.php\"><img src=\"img\\metaNin.jpg\" alt=\"Happy Easter\" style=\" display: block; margin-left: auto; margin-right: auto\"></a>";
		$contents = str_replace($contents, $ninjaImg, $contents);
		
	} 
    



    //check for our boolean operators
    $query = filter_query($query);

    

    if ($_POST['rewrite']) {
        
        //we only do a look-up for the first word
        $originalQuery = $query;
        $queryArr = explode(' ', trim($query));
        $lookUp= $queryArr[0];
        
        //do a thesaurus lookup
        $result = synonym_lookup($lookUp);
        
        
        if ($result) {



            $rewriteResultStr .= '<div class="span12" style="background-color:#DFEFF0; padding:8px 8px; text-align:center; font-size:110%">';
            $rewriteResultStr .= '<form method="POST" action="query.php">';
            $rewriteResultStr .= "You searched for <em> $originalQuery </em> did you mean? <br /><br />";

            
            

            foreach ($result["response"] as $value) {


                $synonym = explode("|", $value["list"]["synonyms"]);
                //make sure query term doesn't appear in synonym list (Proper noun problem)
                //compare it to query and first letter capitalised query
                $capQuery = ucfirst($query);
                if ($synonym[0] != $query && $synonym[0] != $capQuery) {
                    $rewriteResultStr .= '<input name="thesaurus" type="radio" value="' . $originalQuery. ' ' . $synonym[0] . '" />';
                    $rewriteResultStr .= $value["list"]["category"] . " " . $synonym[0] . "<br /><br />";
                } else {
                    $rewriteResultStr .= '<input name="thesaurus" type="radio" value="' . $originalQuery. ' ' . $synonym[1] . '" />';
                    $rewriteResultStr .= $value["list"]["category"] . " " . $synonym[1] . "<br /><br />";
                }
            }
            //here we'll say if the input is 1 then show default as one...etc...
            //save the user from having to reselect the type of search they want by cloaking the form values
            if ($outputType == 1) {
                $rewriteResultStr .= '
            <input name="outputType" type="radio" value="1" checked="checked" style="display:none"> 
		
<input  class="radio inline" name="outputType" type="radio" value="2" style="display:none" >  
		
            <input  class="radio inline" name="outputType" type="radio" value="3" style="display:none" > ';
            }

            if ($outputType == 2) {
                $rewriteResultStr .= '<input name="outputType" type="radio" value="1" style="display:none"> 
		
<input  class="radio inline" name="outputType" type="radio" value="2" checked="checked" style="display:none" >  
		
            <input  class="radio inline" name="outputType" type="radio" value="3" style="display:none" > ';
            }

            if ($outputType == 3) {
                $rewriteResultStr .= '<input name="outputType" type="radio" value="1"  style="display:none"> 
		
<input  class="radio inline" name="outputType" type="radio" value="2" style="display:none" >  
		
            <input  class="radio inline" name="outputType" type="radio" value="3" checked="checked" style="display:none" > ';
            }



            $rewriteResultStr .= '<input name="bt_search" type="submit" value="Rewrite"> </form>';
        }
        //or return result as 0
        else {
            $rewriteResultStr .= '<div class="span12" style="background-color:#DFEFF0; padding:8px 8px; text-align:center; font-size:110%">';
            $rewriteResultStr .= "No Matching Words Found <br />";
        }

        $rewriteResultStr .= "<br />";
        $rewriteResultStr .= "</div>";

        //show the user their rewritten query in the query box
        $queryBox = '<input class="input-large search-query" name="query" type="text" size="60" maxlength="60" value="" />';
        $newQueryBox = '<input class="input-large search-query" name="query" type="text" size="60" maxlength="60" value="' . $query . '" />';

        $contents = str_replace($queryBox, $newQueryBox, $contents);
        //we will also replace the code that places the search box with a box containing the new query string.....
        $contents = str_replace('<div class="span4" id="divCheckboxBing" style="display: none;"></div>
	 <div class="span4" id="divCheckboxBlekko" style="display: none;"></div>
        <div class="span4" id="divCheckboxGoogle" style="display: none;"></div>', $rewriteResultStr, $contents);
    }



    $query = urlencode("'$query'");








    //we can alter these as they no longer need to get passed to the search engines
    $bingResultStr = '';
    $googleResultStr = '';
    $blekkoResultStr = '';


    //do our searches

    $bingResultArray = searchBing($query, $numResults);
    $blekkoResultArray = searchBlekko($query, $numResults);
    $googleResultArray = searchGoogle($query, $numResults);
}



//display our results by search engine
if ($outputType == 1) {

    $bingResultStr = '';
    $googleResultStr = '';
    $blekkoResultStr = '';

    $arrSize = sizeof($bingResultArray);


    
    $bingResultStr .= '<div class="span4" style="padding:4px 4px"><h3>Bing</h3>';

    for ($i = 0; $i < $arrSize; $i++) {

        $bingResultStr .= '<p><a href="http://';
        $bingResultStr .= $bingResultArray[$i][0];
        $bingResultStr .= '" >';
        //make sure the link doesn't bleed when displayed
        if (strlen($bingResultArray[$i][7]) > 40) {
            $bingResultStr .= substr($bingResultArray[$i][7], 0, 40);
            $bingResultStr .= '...';
        } else {
            $bingResultStr .= $bingResultArray[$i][7];
        }
        
        $bingResultStr .= '</a></p>';
        $bingResultStr .= "<p>";
        $bingResultStr .= $bingResultArray[$i][1];
        $bingResultStr .= "</p>";
        $bingResultStr .= "<p> Rank: ";
        $bingResultStr .= $bingResultArray[$i][2];
        $bingResultStr .= "</p>";
    }
    $bingResultStr .= '</div>';

    $contents = str_replace('<div class="span4" id="divCheckboxBing" style="display: none;"></div>', $bingResultStr, $contents);

    $arrSize = sizeof($blekkoResultArray);

    
    $blekkoResultStr .= '<div class="span4" style="padding:4px 4px"><h3>Blekko</h3>';


    for ($i = 0; $i < $arrSize; $i++) {

        $blekkoResultStr .= '<p><a href="http://';
        $blekkoResultStr .= $blekkoResultArray[$i][0];
        $blekkoResultStr .= '" >';
        //make sure the link doesn't bleed when displayed
        if (strlen($blekkoResultArray[$i][7]) > 40) {
            $blekkoResultStr .= substr($blekkoResultArray[$i][7], 0, 40);
            $blekkoResultStr .= '...';
        } else {
            $blekkoResultStr .= $blekkoResultArray[$i][7];
        }
       
        $blekkoResultStr .= '</a></p>';
        $blekkoResultStr .= "<p>";
        $blekkoResultStr .= $blekkoResultArray[$i][1];
        $blekkoResultStr .= "</p>";
        $blekkoResultStr .= "<p> Rank: ";
        $blekkoResultStr .= $blekkoResultArray[$i][2];
        $blekkoResultStr .= "</p>";
    }

    $blekkoResultStr .= '</div>';

    $contents = str_replace('<div class="span4" id="divCheckboxBlekko" style="display: none;"></div>', $blekkoResultStr, $contents);

    $arrSize = sizeof($googleResultArray);

    
    $googleResultStr .= '<div class="span4" style="padding:4px 4px"><h3>Google</h3>';


    for ($i = 0; $i < $arrSize; $i++) {

        $googleResultStr .= '<p><a href="http://';
        $googleResultStr .= $googleResultArray[$i][0];
        $googleResultStr .= '" >';
        //make sure the link doesn't bleed when displayed
        if (strlen($googleResultArray[$i][7]) > 40) {
            $googleResultStr .= substr($googleResultArray[$i][7], 0, 40);
            $googleResultStr .= '...';
        } else {
            $googleResultStr .= $googleResultArray[$i][7];
        }
        
        $googleResultStr .= '</a></p>';
        $googleResultStr .= "<p>";
        $googleResultStr .= $googleResultArray[$i][1];
        $googleResultStr .="</p>";
        $googleResultStr .= "<p> Rank: ";
        $googleResultStr .= $googleResultArray[$i][2];
        $googleResultStr .= "</p>";
    }

    $googleResultStr .= '</div>';

    $contents = str_replace('<div class="span4" id="divCheckboxGoogle" style="display: none;"></div>', $googleResultStr, $contents);

    //show the user their rewritten query in the query box
    $queryBox = '<input class="input-large search-query" name="query" type="text" size="60" maxlength="60" value="" />';
    $formattedQuery = urldecode($query);
    $formattedQuery = str_replace("'", "", $formattedQuery);
    $newQueryBox = '<input class="input-large search-query" name="query" type="text" size="60" maxlength="60" value="' . $formattedQuery . '" />';

    $contents = str_replace($queryBox, $newQueryBox, $contents);
}

//display our aggregated results
if ($outputType == 2) {


    $combinedArray = aggregateCombMNZ($googleResultArray, $bingResultArray, $blekkoResultArray);

//show the user their rewritten query in the query box
    $queryBox = '<input class="input-large search-query" name="query" type="text" size="60" maxlength="60" value="" />';
    $formattedQuery = urldecode($query);
    $formattedQuery = str_replace("'", "", $formattedQuery);
    $newQueryBox = '<input class="input-large search-query" name="query" type="text" size="60" maxlength="60" value="' . $formattedQuery . '" />';

    $contents = str_replace($queryBox, $newQueryBox, $contents);
    $arrSize = sizeof($combinedArray);





    
   $finalResultStr .= '<div class="span12" style="padding:8px 8px; text-align:center; font-size:110%">';

    $finalResultStr .= "<h3>Aggregated Results</h3>";
    for ($i = 0; $i < $arrSize; $i++) {
        //1. url 2. snippet 3. rank 4. combMNZ 5. aggregated 6. search engine 7. number of engines

        $finalResultStr .= '<a href="http://';
        $finalResultStr .= $combinedArray[$i][0];
        $finalResultStr .= '">';
         //make sure the title doesn't bleed when displayed
        if (strlen($combinedArray[$i][10]) > 85) {
            $finalResultStr .= substr($combinedArray[$i][10], 0, 85);
            $finalResultStr .= '...';
        } else {
            $finalResultStr .= $combinedArray[$i][10];
        }
        
        
        $finalResultStr .= '</a>';
        $finalResultStr .= "<br /><br /><p>";
        $finalResultStr .= $combinedArray[$i][1];
        $finalResultStr .= "</p>";
        $finalResultStr .= "<p><em> <u>Combined Rank:</u> ";
        $finalResultStr .= $combinedArray[$i][2];
        $finalResultStr .= " <u>CombMNZ:</u> ";
        $finalResultStr .= number_format($combinedArray[$i][3], 4);
        $finalResultStr .= " <u>Returned By:</u> ";
        $finalResultStr .= $combinedArray[$i][5];
        $finalResultStr .= " <u>Number of Engines:</u> ";
        $finalResultStr .= $combinedArray[$i][6];
        $finalResultStr .= "</em></p><br />";
    }

    $finalResultStr .= "</div>";

    $contents = str_replace('<div class="span4" id="divCheckboxBing" style="display: none;"></div>
	 <div class="span4" id="divCheckboxBlekko" style="display: none;"></div>
        <div class="span4" id="divCheckboxGoogle" style="display: none;"></div>', $finalResultStr, $contents);
}

//display our clustered results
if ($outputType == 3) {

    $combinedArray = aggregateCombMNZ($googleResultArray, $bingResultArray, $blekkoResultArray);


    $clusteredArray = $combinedArray;

    //we will need to look into the object array in order to do the cluster mappings
    cluster($clusteredArray, 4);


    //copy the original snippets back into the clustered array


    $arrSize = sizeof($clusteredArray);

    for ($i = 0; $i < $arrSize; $i++) {
        $clusteredArray[$i][1] = $combinedArray[$i][1];
    }



    //sort array by cluster index
    arraySortByColumn($clusteredArray, 7, SORT_ASC);







    $finalResultStr .= '<div class="span12">';



    $check == 0;

    for ($i = 0; $i < $arrSize; $i++) {
        //1. url 2. snippet 3. rank 4. combMNZ 5. aggregated 6. search engine 7. number of engines
        //this splits off the clusters into differently coloured columns
        if ($clusteredArray[$i][7] == 1 && $check == 0) {

           
	    $finalResultStr .= '<div class="span3" style="padding:3px 3px">';
            $check = 1;
        }
        if ($clusteredArray[$i][7] == 2 && $check == 1) {
            $finalResultStr .= '</div>';
            
	    $finalResultStr .= '<div class="span3" style="padding:3px 3px">';
            $check = 2;
        }
        if ($clusteredArray[$i][7] == 3 && $check == 2) {
            $finalResultStr .= '</div>';
            
	    $finalResultStr .= '<div class="span3" style="padding:3px 3px">';
            $check = 3;
        }
        if ($clusteredArray[$i][7] == 4 && $check == 3) {
            $finalResultStr .= '</div>';
           
	    $finalResultStr .= '<div class="span3" style="padding:3px 3px">';
            $check = 4;
        }


        $finalResultStr .= '<p><a href="http://';
        $finalResultStr .= $clusteredArray[$i][0];
        $finalResultStr .= '" >';
        //make sure the title doesn't bleed when displayed
        if (strlen($clusteredArray[$i][10]) > 20) {
            $finalResultStr .= substr($clusteredArray[$i][10], 0, 20);
            $finalResultStr .= '...';
        } else {
            $finalResultStr .= $clusteredArray[$i][10];
        }

       
        $finalResultStr .= "</a><p><em> ";
        $finalResultStr .= $clusteredArray[$i][1];
        $finalResultStr .= "</em></p>";
    }

    $finalResultStr .= "</div></div>";
//show the user their rewritten query in the query box
    $queryBox = '<input class="input-large search-query" name="query" type="text" size="60" maxlength="60" value="" />';
    $formattedQuery = urldecode($query);
    $formattedQuery = str_replace("'", "", $formattedQuery);
    $newQueryBox = '<input class="input-large search-query" name="query" type="text" size="60" maxlength="60" value="' . $formattedQuery . '" />';

    $contents = str_replace($queryBox, $newQueryBox, $contents);
    $contents = str_replace('<div class="span4" id="divCheckboxBing" style="display: none;"></div>
	 <div class="span4" id="divCheckboxBlekko" style="display: none;"></div>
        <div class="span4" id="divCheckboxGoogle" style="display: none;"></div>', $finalResultStr, $contents);
}


echo $contents;
?>


