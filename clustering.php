<!--aggregation.php - this contains the various functions used when performing aggregation on the search engine results -->
<?php

//this is a third-party program for stemming
include('porterstemmer.php');

//this function performs stopword removal, symbol/number replacement and lowercasing on our snippets prior to prepare them for
//vectorization
function snippetPrepare(&$combinedArray) {

    $stopWords = array("a", "about", "above", "across", "after", "again", "against", "all", "almost", "alone", "along", "already", "also", "although", "always", "among", "an", "and", "another", "any", "anybody", "anyone", "anything", "anywhere", "are", "area", "areas", "around", "as", "ask", "asked", "asking", "asks", "at", "away", "b", "back", "backed", "backing", "backs", "be", "became", "because", "become", "becomes", "been", "before", "began", "behind", "being", "beings", "best", "better", "between", "big", "both", "but", "by", "c", "came", "can", "cannot", "case", "cases", "certain", "certainly", "clear", "clearly", "come", "could", "d", "did", "differ", "different", "differently", "do", "does", "done", "down", "down", "downed", "downing", "downs", "during", "e", "each", "early", "either", "end", "ended", "ending", "ends", "enough", "even", "evenly", "ever", "every", "everybody", "everyone", "everything", "everywhere", "f", "face", "faces", "fact", "facts", "far", "felt", "few", "find", "finds", "first", "for", "four", "from", "full", "fully", "further", "furthered", "furthering", "furthers", "g", "gave", "general", "generally", "get", "gets", "give", "given", "gives", "go", "going", "good", "goods", "got", "great", "greater", "greatest", "group", "grouped", "grouping", "groups", "h", "had", "has", "have", "having", "he", "her", "here", "herself", "high", "high", "high", "higher", "highest", "him", "himself", "his", "how", "however", "i", "if", "important", "in", "interest", "interested", "interesting", "interests", "into",
        "is", "it", "its", "itself", "j", "just", "k", "keep", "keeps", "kind", "knew", "know", "known", "knows", "l", "large", "largely", "last", "later", "latest", "least", "less", "let", "lets", "like", "likely", "long", "longer", "longest", "m", "made", "make", "making", "man", "many", "may", "me", "member", "members", "men", "might", "more", "most", "mostly", "mr", "mrs", "much", "must", "my", "myself", "n", "necessary", "need", "needed", "needing", "needs", "never", "new", "new", "newer", "newest", "next", "no", "nobody", "non", "noone", "not", "nothing", "now", "nowhere", "number", "numbers", "o", "of", "off", "often", "old", "older", "oldest", "on", "once", "one", "only", "open", "opened", "opening", "opens", "or", "order", "ordered", "ordering", "orders", "other", "others", "our", "out", "over", "p", "part", "parted", "parting", "parts", "per", "perhaps", "place", "places", "point", "pointed", "pointing", "points", "possible", "present", "presented", "presenting", "presents", "problem", "problems", "put", "puts", "q", "quite", "r", "rather", "really", "right", "right", "room", "rooms", "s", "said", "same", "saw", "say", "says", "second", "seconds", "see", "seem", "seemed", "seeming", "seems", "sees", "several", "shall", "she", "should", "show", "showed", "showing", "shows", "side", "sides", "since", "small", "smaller", "smallest", "so", "some", "somebody", "someone", "something", "somewhere", "state", "states", "still", "still", "such", "sure", "t", "take", "taken", "than", "that", "the", "their", "them", "then", "there", "therefore", "these", "they", "thing", "things", "think", "thinks", "this", "those", "though", "thought", "thoughts", "three", "through", "thus", "to", "today", "together", "too", "took", "toward", "turn", "turned", "turning", "turns", "two", "u", "under", "until", "up", "upon", "us", "use", "used", "uses", "v", "very", "w", "want", "wanted", "wanting", "wants", "was", "way", "ways", "we", "well", "wells", "went", "were", "what", "when", "where", "whether", "which", "while", "who", "whole", "whose", "why", "will", "with", "within", "without", "work", "worked", "working", "works", "would", "x", "y", "year", "years", "yet", "you", "young", "younger", "youngest", "your", "yours", "z",);
    $good = array("", "", "", "", "", "", "", "", "", "", " ", "zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine");
    $bad = array(".", ",", "...", "-", "(", ")", "!", "|", ":", ";", "  ", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9");



    $arrSize = sizeof($combinedArray);
    for ($i = 0; $i < $arrSize; $i++) {


        $combinedArray [$i][1] = str_replace($bad, $good, $combinedArray [$i][1]);

        $combinedArray [$i][1] = strtolower($combinedArray [$i][1]);


        $words = explode(" ", $combinedArray [$i][1]);
        foreach ($words as $word) {
            $stem = porterstemmer::Stem($word);
            if (!in_array($stem, $stopWords)) {
                $stemWords[] = $stem;
            }
        }

        $combinedArray [$i][1] = implode(" ", $stemWords);

        unset($stemWords);
    }
}

//this function creates a unique array of words from a snippet
//and then allows us to count the number of occurences of a given word
function snippetUniqueWords(&$combinedArray, &$score) {



    $length = sizeof($combinedArray);

    $count = 0;
    for ($i = 0; $i < $length; $i++) {

        $words = explode(" ", $combinedArray [$i][1]);

        $words = array_unique($words);


        foreach ($words as $word) {
            if (array_key_exists($word, $score)) {

                $count = $score[$word];
                $score[$word] = $count + 1;
            } else {

                $score[$word] = 1;
            }
        }
    }
}
//this function tells us how many times a given value
//repeats in a given array
function getRepetitions($value, array $values) {
    $length = count($values);
    if ($length == 0)
        return FALSE;
    $repetitions = 0;
    foreach ($values as $v) {
        if ($v == $value)
            $repetitions++;
    }
    return $repetitions;
}

//this function vectorizes a snippet with either the TF/IDF score or a zero depending on word-occurence
function snippetVectorise(&$combinedArray, $score) {

    $weight = array();

    $length = sizeof($combinedArray);

    for ($i = 0; $i < $length; $i++) {

        $words = explode(" ", $combinedArray [$i][1]);

        foreach ($words as $word) {

            $freq = getRepetitions($word, $words);

            $weight[$word] = $freq * log(($length / $score[$word]));
        }



        $vectorholder = array();

        $words = array_unique($words);
        $words = array_values($words);
        foreach ($score as $key => $value) {




            if (in_array($key, $words)) {


                $vectorholder [] = $weight[$key];
            } else {

                $vectorholder [] = 0.00;
            }
        }

        $combinedArray[$i][8] = $vectorholder;
    }
}

//calculate the initial magnitude
function magnitude(&$combinedArray) {

    $length = sizeof($combinedArray);
    $magnitude = 0;
    $holder = 0;

    for ($j = 0; $j < $length; $j++) {
        for ($i = 0; $i < $length; $i++) {
            if ($combinedArray[$j][8][$i] != 0) {

                $holder = $combinedArray[$j][8][$i] * $combinedArray[$j][8][$i];
                $magnitude = $magnitude + $holder;
            }
        }
        $magnitude = sqrt($magnitude);
        $combinedArray[$j][9] = $magnitude;
    }
}

//this function performs clustering using vector magnitudes
function documentCluster($centroid, $clusterMagnitude, $kmean, &$combinedArray) {




    $dot_product1 = 0;
    $dot_product2 = 0;

    $length = sizeof($combinedArray);

    for ($j = 0; $j < $length; $j++) {
        for ($k = 0; $k < $kmean; $k++) {
            for ($i = 0; $i < $length; $i++) {

                if ($combinedArray[$j][8][$i] != 0 && $centroid[$k] != 0) {
                    $dot_product1 = $centroid[$k][$i] * $combinedArray[$j][8][$i];

                    $dot_product2 = $dot_product2 + $dot_product1;
                }
            }

            $distance[$k] = $dot_product2 / ($clusterMagnitude[$k] * $combinedArray[$j][9]);
            $distance[$k] = 1 - $distance[$k];


            $dot_product2 = 0;
        }


        $smallest = array_search(min($distance), $distance) + 1;


        $combinedArray[$j][7] = $smallest;
    }
}

//this function calculates the centroid of our clusters
function clusterCentroid($kmean, &$combinedArray, $centroid) {

    $length = sizeof($combinedArray);
    $centroid = array();
    for ($k = 0; $k < $kmean; $k++) {
        for ($i = 0; $i < $length; $i++) {
            $centroid[$k][$i] = 0;
        }
    }
    $cluster_no = 0;



    for ($i = 0; $i < $length; $i++) {

        for ($j = 0; $j < $length; $j++) {

            for ($k = 0; $k < $kmean; $k++) {
                $cluster_no = $k + 1;
                if ($combinedArray[$j][7] == $cluster_no) {
                    $centroid[$k][$i] = $centroid[$k][$i] + $combinedArray[$j][8][$i];
                }
                $centroid[$k][$i] = $centroid[$k][$i] / $length;
            }
        }
    }

    return $centroid;
}

//re-calculate the vector magnitudes
function vectorMagnitude($vectorsize, $kmean, &$centroid) {

    $holder = 0;
    $magnitude1 = 0;
    $magnitude2 = array();
    for ($k = 0; $k < $kmean; $k++) {
        $magnitude2[$k] = 0;
    }

    for ($k = 0; $k < $kmean; $k++) {

        for ($i = 0; $i < $vectorsize; $i++) {

            $holder = $centroid[$k][$i] * $centroid[$k][$i];
            $magnitude1 = $magnitude1 + $holder;
        }
        $magnitude2[$k] = sqrt($magnitude1);


        $magnitude1 = 0;
    }


    $clusterMagnitude = $magnitude2;
    return $clusterMagnitude;
}

//this function contains the entire clustering process
function cluster(&$aggregatedArray, $kmean) {



    snippetPrepare($aggregatedArray);

    $score = array();

    snippetUniqueWords($aggregatedArray, $score);

    snippetVectorise($aggregatedArray, $score);



    for ($j = 0; $j < $kmean; $j++) {
        $aggregatedArray[$j][7] = $j + 1;
    }


    $clusterMagnitude = array();
    $centroid = array();


    $vectorsize = sizeof($aggregatedArray[0][8]);

    magnitude($aggregatedArray);

    for ($i = 0; $i < $kmean; $i++) {

        $clusterMagnitude [$i] = $aggregatedArray[$i][9];
        $centroid[$i] = $aggregatedArray[$i][8];
    }


    documentCluster($centroid, $clusterMagnitude, $kmean, $aggregatedArray);


    for ($u = 0; $u < 6; $u++) {

        $centroid = clusterCentroid($kmean, $aggregatedArray, $centroid);


        $clusterMagnitude = vectorMagnitude($vectorsize, $kmean, $centroid);


        documentCluster($centroid, $clusterMagnitude, $kmean, $aggregatedArray);
    }
}

?>
