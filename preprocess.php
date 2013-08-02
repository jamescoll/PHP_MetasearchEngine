<?php

//this function examines the query for the presence of Boolean search terms then alters them 
//to the format supported by the underlying search engine
function filter_query($query) {



    //use string replace to allow our users to use AND as a Boolean search parameter
    //All of our search engines use OR so we do not need to edit our Query to facilitate its use
    //use string replace to allow our users to use NOT as a Boolean search parameter
    $bad = array("NOT ", "AND ", "'s", '"');
    $good = array("-", "+", "", "'");
    $query = str_replace($bad, $good, $query);

    return $query;
}

//this function uses the altervista service to provide query refinement on the first term entered by
//the user - it is called from within query.php
function synonym_lookup($query) {

    $apikey = "GdbOBg1JYIou4guyIKfq"; // NOTE: replace test_only with your own key 
    $word = $query; // any word 
    $language = "en_US"; // you can use: en_US, es_ES, de_DE, fr_FR, it_IT 
    $endpoint = "http://thesaurus.altervista.org/thesaurus/v1";


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$endpoint?word=" . urlencode($word) . "&language=$language&key=$apikey&output=json");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    //either return $result populated with the JSON decoded data
    if ($info['http_code'] == 200) {
        $result = json_decode($data, true);
    }


    //or return result as 0
    else {
        $result = 0;
    }

    return $result;
}

?>
