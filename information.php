<?php
require_once "whitakers-words-master/whits_end.php";
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Azeeza Eagal's Capstone Project: Summer 2021.">
    <title>Parsing Whitaker's WORDS</title>
    <link rel="stylesheet" href="information.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  </head>

  <body>
    <div>
      <h2><u> Parsing Whitaker's<a href="http://archives.nd.edu/words.html">  WORDS</a>:</u></h2>
      
      <?php
          // Retrieves Latin Input 
          $latin_input = isset($_POST['latin_input']) ? $_POST['latin_input'] : "";
          //Make Translation Array
          //$translation = array();

      ?>

      <h3> Whit's <a href="http://archives.nd.edu/words.html">WORDS</a>:</h3>  
      <p> 
      <?php  
          //Whit's Words Portion: Displays Whit's WORDS entries
          print "<pre>";
          $new_line_trigger = false;

          foreach(whit($latin_input) as $values)
          {
            //output dictionary entries
            echo "    $values\n";
            
            //spacing boolean for clearer output
            if($new_line_trigger === true)
            {
              echo "\n";
              $new_line_trigger = false;
            }

            if(strpos($values, '[X')) 
            {
               $new_line_trigger = true;
            }
          }
          
          
          print "</pre>"; 
        ?>
       
      
      </p>

      <h3>Parsed:</h3> 
      <?php 
      
      // create an array to aid with the next parsing section.
      if($latin_input == trim($latin_input) && strpos($latin_input, ' '))
      {
        $multi_word = explode(" ", $latin_input);
      } 

      //Parsed: parses the dictionary entry and outputs meaningful data about the Latin input's grammar
      if(isset($multi_word))
      {
        for($i=0; $i < sizeof($multi_word); $i++)
        { 
          print "<h4> $multi_word[$i]: </h4>";
          $dict_entry = whit($multi_word[$i]);
          print "<pre>"; 
          parse($dict_entry);
          print "</pre>"; 
        }
        
      }
      else
      {
        $dict_entry = whit($latin_input);
        print "<h4> $latin_input: </h4>";
        print "<pre>";
        parse($dict_entry);
        print "</pre>"; 
      } 
      ?>

      <h3> English: </h3> 
      <p> 
      
      <?php 
        //Google Translate Portion: https://www.sitepoint.com/using-google-translate-api-php/
      
        // Get's Google Latin translation of the input
        $apiKey = 'AIzaSyCxpCo0NMO66BwvK53nGHWSeSJjXxdP4IQ';
        $text = $latin_input;
        $url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . '&q=' . rawurlencode($text) . '&source=la&target=en';
        
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handle);
        $responseDecoded = json_decode($response, true);
        $responseCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);      //fetch the HTTP response code
        curl_close($handle);
        

    if($responseCode != 200) {
        echo 'Fetching translation failed! Server response code:' . $responseCode . '<br>';
        echo 'Error description: ' . $responseDecoded['error']['errors'][0]['message'];
    }
    else {
        echo 'Google Translate Translation: ' . $responseDecoded['data']['translations'][0]['translatedText'] . '<br>';

        // William Whitaker's Words Translation 
        echo "William Whitaker Translation:";

        if(isset($multi_word))
        {
            for($i=0; $i < sizeof($multi_word); $i++)
            { 
              $dict_entry = whit($multi_word[$i]);
             
              translate($dict_entry);
              
            }
        
        }
        else
        {
            $dict_entry = whit($latin_input);
            
            translate($dict_entry);
             
        } 
            
        }
        
?>
      
      </p>
  </body>
</html>
    