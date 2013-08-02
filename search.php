<!-- search.php - this file contains the functions used to get data from the search engines as well as to process that data and 
 populate an array with it - it contains functions which check the string format of urls and which ensure that no duplicates exist
 within the search results (a problem with the engines you have to search multiple times to get more than N results) -->

<?php

function remove_leading($url) {
    $disallowed = array('http://www.', 'http://', 'https://', 'www.');
    foreach ($disallowed as $d) {
        if (strpos($url, $d) === 0) {
            return str_replace($d, '', $url);
        }
    }
    return $url;
}

function remove_trailing_backslash($url) {
    $url = rtrim($url, "/");
    return $url;
}

//this function checks a returned array for duplicates and 
//eliminates them updating the rank as it goes

function check_duplicates($resultArray) {

    //what we care about is checking for matches in array 0
    //and updating the rank in array 2


    $arrSize = sizeof($resultArray);

    for ($i = 0; $i < $arrSize; $i++) {
        for ($j = 0; $j < $arrSize; $j++) {

            if ($resultArray[$i][0] == $resultArray[$j][0] && $resultArray[$i][2] != $resultArray[$j][2]) {

                unset($resultArray[$j]);
            }
        }
    }



    $arrSize = sizeof($resultArray);

    //create an array for the non-duplicate values
    $cleanResultArray = array();


    for ($i = 0; $i < $arrSize; $i++) {
        if (!empty($resultArray[$i][0])) {
            array_push($cleanResultArray, $resultArray[$i]);
        }
    }

    //update the rank so it's correctly reflected
    $cleanArrSize = sizeof($cleanResultArray);

    for ($i = 0; $i < $cleanArrSize; $i++) {
        $cleanResultArray[$i][2] = $i + 1;
    }


    return $cleanResultArray;
}

function searchBing($query, $numResults) {


    $bingResultArray = array();

    $bingAcctKey = 'kVIyHSMN6Be6Tah8CXtUZzKogfGazplboxmQEHloD4M';

    $bingRootUri = 'https://api.datamarket.azure.com/Bing/Search';


    // Construct the full URI for the query.
    //for one hundred results we're going to have to do this twice


    $requestUri = "$bingRootUri/Web?\$format=json&Query=$query&\$top=$numResults";


    // Encode the credentials and create the stream context.

    $auth = base64_encode("$bingAcctKey:$bingAcctKey");

    $data = array
        (
        'http' => array(
            'request_fulluri' => true,
            // ignore_errors can help debug – remove for production. This option added in PHP 5.2.10
            'ignore_errors' => true,
            'header' => "Authorization: Basic $auth")
    );

    $context = stream_context_create($data);

    // Get the response from Bing.

    $response = file_get_contents($requestUri, 0, $context);



    // Decode the response. 
    $jsonObj = json_decode($response);


    $i = 0;

    foreach ($jsonObj->d->results as $value) {
        $tmpStr = remove_leading($value->Url);
        $tmpStr = remove_trailing_backslash($tmpStr);
        $tmpStr = strip_tags($tmpStr);
        $bingResultArray[$i][0] = trim($tmpStr);
        $bingResultArray[$i][1] = $value->Description;
        //for a rank
        $bingResultArray[$i][2] = $i + 1;
        //combMNZ score
        $bingResultArray[$i][3] = 0.00;
        //included in aggregation
        $bingResultArray[$i][4] = 0;
        //which search engine 1 = bing, 2=blekko, 3=google
        $bingResultArray[$i][5] = "Bing";
        //number of search engines returning
        $bingResultArray[$i][6] = 1;
        $bingResultArray[$i][7]= $value->Title;
        $i++;
    }

    //this is the code that gets 100 results from bing
    //it will only display 50 and we want 100 so we call it twice based on our input variable
    if ($numResults > 50) {

        $Results = $numResults - 50;
        $requestUri = "$bingRootUri/Web?\$format=json&Query=$query&\$skip=50&\$top=$Results";

        $response = file_get_contents($requestUri, 0, $context);

        // Decode the response. 
        $jsonObj = json_decode($response);



        foreach ($jsonObj->d->results as $value) {

            $tmpStr = remove_leading($value->Url);
            $tmpStr = remove_trailing_backslash($tmpStr);
            $tmpStr = strip_tags($tmpStr);
            $bingResultArray[$i][0] = trim($tmpStr);
            $bingResultArray[$i][1] = $value->Description;
            //for a rank
            $bingResultArray[$i][2] = $i + 1;
            //combMNZ score

            $bingResultArray[$i][3] = 0.00;
            //included in aggregation
            $bingResultArray[$i][4] = 0;
            //which search engine 1 = bing, 2=blekko, 3=google
            $bingResultArray[$i][5] = "Bing";
            $bingResultArray[$i][6] = 1;
            $bingResultArray[$i][7]= $value->Title;
            $i++;
        }
    }
    $bingResultArray = check_duplicates($bingResultArray);

    return $bingResultArray;
}

function searchBlekko($query, $numResults) {





    $blekkoResultArray = array();


    $blekkoRootUri = 'http://blekko.com/ws/';


    $url = $blekkoRootUri . '?q=' . $query . '+/json+/ps=' . $numResults;


    // initiate cURL
    $ch = curl_init();
    // set the URL
    curl_setopt($ch, CURLOPT_URL, $url);
    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // get the web page source into $data
    $data = curl_exec($ch);
    //delete this later but it confirms that the search data is output
    //echo $data;

    $js = json_decode($data);

    //here we populate an array for aggregation

    $i = 0;





    foreach ($js->{'RESULT'} as $item) {

        $tmpStr = strip_tags($item->{'display_url'});
        $tmpStr = remove_trailing_backslash($tmpStr);
        $tmpStr = remove_leading($tmpStr);
        $tmpStr = trim($tmpStr);
        $blekkoResultArray[$i][0] = strip_tags($item->{'display_url'});
        $blekkoResultArray[$i][1] = strip_tags($item->{'snippet'});
        //this gives a rank
        $blekkoResultArray[$i][2] = $i + 1;
        //combMNZ

        $blekkoResultArray[$i][3] = 0.00;
        //included in aggregation
        $blekkoResultArray[$i][4] = 0;
        //which search engine 2 in this case
        $blekkoResultArray[$i][5] = "Blekko";
        //how many search engines
        $blekkoResultArray[$i][6] = 1;
        
        $blekkoResultArray[$i][7] = strip_tags($item->{'url_title'});
        
        $i++;
    }

    $blekkoResultArray = check_duplicates($blekkoResultArray);

    return $blekkoResultArray;
    // Substitute the results placeholder. Ready to go. 
}

function searchGoogle($query, $numResults) {



    $googleResultArray = array();

    $googleAcctKey = 'hc+TUI3f03XThqcHJk4auJCOdaVHJ0FSSCAZFc8tvAc=';

    $googleRootUri = 'http://www.google.com/custom';


    $googlecounter = 0;

    //using url encode here causes weirdness
    $html = file_get_html($googleRootUri . '?start=0&num=' . $numResults . '&q=' . $query . '&client=google-csbe&cx=' . $googleAcctKey);

    foreach ($html->find('a.l') as $e) {
        ++$googlecounter;
    }


    for ($i = 0; $i < $googlecounter; $i++) {

        $googleurl = $html->find('a.l');
        $googletitle = $html->find('a.l');
        $googlesnippet = $html->find('div.std');
        $tmpStr = remove_leading($googleurl[$i]->href);
        $tmpStr = remove_trailing_backslash($tmpStr);
        $googleResultArray[$i][0] = trim($tmpStr);
        $tempStr = substr($googlesnippet[$i]->innertext, 0, strpos($googlesnippet[$i]->innertext, "<br><span class=\"a\">"));
        $googleResultArray[$i][1] = strip_tags($tempStr);
        $googleResultArray[$i][2] = $i + 1;
        $googleResultArray[$i][3] = 0.00;
        $googleResultArray[$i][4] = 0;
        $googleResultArray[$i][5] = "Google";
        $googleResultArray[$i][6] = 1;
        $googleResultArray[$i][7] = strip_tags($googletitle[$i]->innertext);
        
    }

    //clear dom to deal with the memory leak issue
    $html->clear();
    unset($html);
    $googleResultArray = check_duplicates($googleResultArray);
    return $googleResultArray;
}

?>
