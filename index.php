<?php
// Find out when the data was last updated by the Maryland Department of Health
exec("curl https://services.arcgis.com/njFNhDsUCentVYJW/ArcGIS/rest/services/ZIPCodes_MD_1/FeatureServer/0", $outputdate, $returnvardate);

// Filter out formatting
$date = str_replace("<b>Last Edit Date:</b> ", "", $outputdate[117]);
$date = str_replace("<br/>", "", $date);

// Format date and fix time zone
$lastupdated = date('F j, Y \a\t g:i A', (strtotime($date) - (60 * 60 * 4)));

// Import list of valid city/town names into array
$filename = "citowns";
$lines = file($filename, FILE_IGNORE_NEW_LINES);

// If the user has not searched for any city/town yet and if the URL parameter 'loc' exists as a valid city/town name, automatically search for it
$loc = htmlentities($_GET["loc"]);
if(isset($_POST['submit'])) { } else {
	if (empty($loc)) { } else {
		if (in_array($loc, $lines)) {
			$_POST['citown'] = $loc;
			$_POST['submit'] = true;
		}
	}
}
?>

<!DOCTYPE html>
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-114099331-5"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-114099331-5');
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COVID-19 in Maryland</title>
    <link href="https://fonts.googleapis.com/css?family=Lato|Tinos" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lato&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>body{font-family:Tinos,serif;margin-left:5%;margin-right:5%;margin-top:5%;text-align:center;background-color:#d1d1e0}.button{background-color:#555;border:none;color:#fff;padding:15px 32px;text-align:center;text-decoration:none;display:inline-block;font-size:16px;margin:4px 2px;cursor:pointer}a{-moz-transition:color .2s ease-in-out,border-color .2s ease-in-out;-webkit-transition:color .2s ease-in-out,border-color .2s ease-in-out;-ms-transition:color .2s ease-in-out,border-color .2s ease-in-out;transition:color .2s ease-in-out,border-color .2s ease-in-out;border-bottom:solid 1px #e4e4e4;color:inherit;text-decoration:none}a:hover{color:#4acaa8!important}input[type=text],select{width:65%;padding:12px 20px;margin:8px 0;display:inline-block;border:1px solid #ccc;border-radius:4px;box-sizing:border-box;text-align:center}table{width:50%;font-family:sans-serif}table,td,th{border:1px solid #000;border-collapse:collapse}table th{background-color:#000;color:#fff}table tr:nth-child(even){background-color:#eee}table tr:nth-child(odd){background-color:#fff}#heading{font-size:275%}#updatedwhen{font-size:145%;font-family:Lato,sans-serif}#container{margin-left:5%;margin-right:5%}#inputlabel{font-size:145%;font-family:Lato,sans-serif}#res{font-size:150%;margin-left:10%;margin-right:10%}#tableres{margin:0 auto}#websiteby{font-size:135%}</style>
</head>
<body>
    <br>
    <form action="" method="POST">
        <h2 id="heading">Maryland Cases of COVID-19 By City/Town/ZIP</h2><br>
        <p id="updatedwhen">Data last updated by the Maryland Department of Health on <?php echo htmlentities($lastupdated);?></p>
        <br>
        <div id="container"><br>
	        <p id="inputlabel"><b>City/Town/ZIP Code</b></p>
	        <input type="text" id="citown" autocomplete="off" name="citown" placeholder="city name or town name or zip code" value="<?php if(isset($_POST['submit'])) { echo htmlentities($_POST['citown']); } ?>" required>
			<br><br><br>
	        <button type="submit" class="button" name="submit">check covid-19 cases</button><br><br><br>
	        <p id="res">
		<?php
		// Function 'getContents' courtesy of user 'raina77ow' on Stack Overflow. The rest of this code is originally written by Dhruvi Mirani in consultation with online resources.
                function getContents($str, $startDelimiter, $endDelimiter)
                {
                    $contents             = array();
                    $startDelimiterLength = strlen($startDelimiter);
                    $endDelimiterLength   = strlen($endDelimiter);
                    $startFrom            = $contentStart = $contentEnd = 0;
                    while (false !== ($contentStart = strpos($str, $startDelimiter, $startFrom))) {
                        $contentStart += $startDelimiterLength;
                        $contentEnd = strpos($str, $endDelimiter, $contentStart);
                        if (false === $contentEnd) {
                            break;
                        }
                        $contents[] = substr($str, $contentStart, $contentEnd - $contentStart);
                        $startFrom  = $contentEnd + $endDelimiterLength;
                    }
                    
                    return $contents;
                }

                // When the form is posted, start the data retrival
                if (isset($_POST['submit'])) {
                    
                    // If the user does not input a city/town/zip, return an error
                    if (empty($_POST['citown'])) {
                        echo ("You must enter a city name, town name, or ZIP code.");
                    } else {
                        
                        // Clean up user input: every letter is lowercase except the first letter of each word in the city/town name.
                        $citown = ucwords(strtolower($_POST['citown']));
                        
                        // City/town capitalization exceptions
                        if ($citown == "Bwi Airport") {
                            $citown = "BWI Airport";
                        } else if ($citown == "Mcdaniel") {
                            $citown = "McDaniel";
                        } else if ($citown == "Mchenry") {
                            $citown = "McHenry";
                        }
                        
                        // If the user enters a special character, return an error
                        if (!preg_match('/[^A-Za-z0-9\s]/', $citown)) {

                        	// If the input matches the format of a zip code
                            if (preg_match('/\b\d{5}\b/', $citown)) {

                                // Encode input for curl data retrival from MD Department of Health database and prevent remote code execution
                                $citownurl = escapeshellcmd(urlencode($citown));

                                // Issue data retrival request
                                exec("curl -XGET 'https://services.arcgis.com/njFNhDsUCentVYJW/arcgis/rest/services/ZIPCodes_MD_1/FeatureServer/0/query?f=json&where=ZIPCODE1=%27" . $citownurl . "%27&returnGeometry=false&outFields=*'", $output, $returnvar);

                                // If the ZIP code is not recognized by MD, return an error
                                if (strpos(json_encode($output), 'ProtectedCount') !== false) {
                                    
                                    // Filters out irrelevant information from response
                                    preg_match('/features\\\"\:\[(.*?)\]}"\]/', json_encode($output), $matches_rawcitown);
                                    $cleanoutput = $matches_rawcitown[1];
                                    
                                    // Find MD recognized City/Town name corresponding with entered ZIP
                                    $p_citown = getContents($cleanoutput, 'ZIPName\":\"', '\",\"ProtectedCoun');
                                    
                                    // Find ZIP code corresponding with entered ZIP 
                                    $p_zip = getContents($cleanoutput, 'ZIPCODE1\":\"', '\",\"ZIPName');
                                    
                                    // Find COVID-19 case count corresponding with entered ZIP
                                    $p_count = getContents($cleanoutput, 'ProtectedCount\":', ',\"Shape__Area');
                                    
                                    // If the returned ZIP does not match the clean user input, return an error. This should not happen on production but happened a few times during debugging.
                                    if ($p_zip[0] == $citown) {
                                        
                                        // Sanitize city/town name for table output, avoid XSS
                                        $citown = htmlentities($citown);
                                        
                                        // Combine ZIP and COVID-19 case count arrays into keys and values respectively for table output
                                        $zipstocounts = array_combine($p_zip, $p_count);
                                        
                                        // The Maryland Department of Health does not provide any data for ZIP codes with fewer than 8 cases. Establish a potential high-end of 7 cases for ZIP codes without data.
                                        $highend = 0;
                                        
                                        // Echo table headers
                                        echo "<table id='tableres' class='table table-sm'><thead class='thead-dark'><tr><th scope='col' style='width: 50%;'>Zip Code</th><th scope='col' style='width: 50%;'>Cases of COVID-19</th></tr></thead>";
                                        
                                        // Add a table row for each zip code of a city/town and its corresponding COVID-19 case number
                                        foreach ($zipstocounts as $key => $value) {
                                            if ($value == "null") {
                                                
                                                // The Maryland Department of Health does not provide any data for cities/towns with fewer than 8 cases. Establish a potential high-end of 7 cases for ZIP codes without data.
                                                $formatted_value = "0-7";
                                                $highend         = $highend + 7;
                                                
                                            } else {
                                                $formatted_value = $value;
                                            }
                                            echo ("<tbody><tr><td>$key</td><td>$formatted_value</td></tr>");
                                        }
                                        
                                        // Find the total number of COVID-19 cases in a ZIP code
                                        $totals = array_sum($p_count);
                                        
                                        // Account for ZIP codes with no provided data
                                        $highend = $totals + $highend;
                                        
                                        // If the high-end is equal to the total, no need to specify the high-end. If we do specify it, add the note explaining why.
                                        if ($highend == $totals) {
                                            echo "</tbody></table>";
                                        } else {
                                            echo "</tbody></table><br><p><b>NOTE:</b> There are between 0 and 7 cases of COVID-19 in $citown. The Maryland Department of Health does not provide any data for ZIP codes with fewer than 8 cases.</p>";
                                        }
                                    } else {
                                        echo ("An unexpected error occurred. Please contact Dhruvi Mirani for help.");
                                    }
                                } else {

                                    echo ("Invalid zip code.");
                                }

                            // If the input does not match a zip code
                            } else {

                            	// If the input does not have numbers or special characters
                                if (!preg_match('/[^A-Za-z\s]/', $citown)) {

                                    // Encode input for curl data retrival from MD Department of Health database and prevent remote code execution
                                    $citownurl = escapeshellcmd(urlencode($citown));
                                    
                                    // Issue data retrival request
                                    exec("curl -XGET 'https://services.arcgis.com/njFNhDsUCentVYJW/arcgis/rest/services/ZIPCodes_MD_1/FeatureServer/0/query?f=json&where=ZIPNAME=%27" . $citownurl . "%27&returnGeometry=false&outFields=*'", $output, $returnvar);
                                    
                                    // If the city/town is not recognized by MD, return an error
                                    if (strpos(json_encode($output), 'ProtectedCount') !== false) {
                                        
                                        // Filters out irrelevant information from response
                                        preg_match('/features\\\"\:\[(.*?)\]}"\]/', json_encode($output), $matches_rawcitown);
                                        $cleanoutput = $matches_rawcitown[1];
                                        
                                        // Find MD recognized City/Town names corresponding with entered City/Town name
                                        $p_citown = getContents($cleanoutput, 'ZIPName\":\"', '\",\"ProtectedCoun');
                                        
                                        // Find ZIP codes corresponding with entered City/Town name
                                        $p_zip = getContents($cleanoutput, 'ZIPCODE1\":\"', '\",\"ZIPName');
                                        
                                        // Find COVID-19 case counts corresponding with entered City/Town name
                                        $p_count = getContents($cleanoutput, 'ProtectedCount\":', ',\"Shape__Area');
                                        
                                        // If the returned city/town does not match the clean user input, return an error. This should not happen on production but happened a few times during debugging.
                                        if ($p_citown[0] == $citown) {
                                            
                                            // Sanitize city/town name for table output, avoid XSS
                                            $citown = htmlentities($citown);
                                            
                                            // Combine ZIP and COVID-19 case count arrays into keys and values respectively for table output
                                            $zipstocounts = array_combine($p_zip, $p_count);
                                            
                                            // The Maryland Department of Health does not provide any data for cities/towns with fewer than 8 cases. Establish a potential high-end of 7 cases for ZIP codes without data.
                                            $highend = 0;
                                            
                                            // Echo table headers
                                            echo "<table id='tableres' class='table table-sm'><thead class='thead-dark'><tr><th scope='col' style='width: 50%;'>$citown Zip Codes</th><th scope='col' style='width: 50%;'>Cases of COVID-19</th></tr></thead>";
                                            
                                            // Add a table row for each zip code of a city/town and its corresponding COVID-19 case number
                                            foreach ($zipstocounts as $key => $value) {
                                                if ($value == "null") {
                                                    
                                                    // The Maryland Department of Health does not provide any data for cities/towns with fewer than 8 cases. Establish a potential high-end of 7 cases for ZIP codes without data.
                                                    $formatted_value = "0-7";
                                                    $highend         = $highend + 7;
                                                    
                                                } else {
                                                    $formatted_value = $value;
                                                }
                                                echo ("<tbody><tr><td>$key</td><td>$formatted_value</td></tr>");
                                            }
                                            
                                            // Find the total number of COVID-19 cases in a city/town by adding up ZIP totals
                                            $totals = array_sum($p_count);
                                            
                                            // Account for ZIP codes with no provided data
                                            $highend = $totals + $highend;
                                            
                                            // If the high-end is equal to the total, no need to specify the high-end. If we do specify it, add the note explaining why.
                                            if ($highend == $totals) {
                                                echo "<tr><td>Total</td><td>$totals</td></tr></table>";
                                            } else {
                                                echo "<tr><td>Total</td><td>$totals-$highend</td></tr></tbody></table><br><p><b>NOTE:</b> There are between 0 and 7 cases of COVID-19 in some ZIP codes of $citown. The Maryland Department of Health does not provide any data for cities/towns with fewer than 8 cases.</p>";
                                            }
                                        } else {
                                            echo ("An unexpected error occurred. Please contact Dhruvi Mirani for help.");
                                        }
                                    } else {

                                        // Check which valid name is closest to entered name
                                        $distance = -1;

                                        foreach ($lines as $name) {

                                            $lev = levenshtein(htmlentities($citown), $name);

                                            if ($lev <= $distance || $distance < 0) {

                                                $closest  = $name;
                                                $distance = $lev;

                                            }
                                        }

                                        // Suggest closest name
                                        echo ("Invalid city/town name. Did you mean: <a href='?loc=$closest'><b>$closest</b></a>?");
                                    }

                                // If the input has letters or numbers (but does not match a zip code)
                                } else {
                                    echo ("Invalid city/town name or zip code.");
                                }
                            }
    
                        } else {
                            echo ("City/town names and zip codes cannot include special characters.");
                        }
                    }
                    echo "<br><br><br><br><br><br>";
                }
		?> 
	        </p>
    	</div>
    </form>
    <p id="websiteby">website by <a href="https://dhruvi.me" target="_blank">dhruvi mirani</a>
        <br>view source code <a href="https://github.com/decimal1700/md-covid-19" target="_blank">here</a>
    </p>
</body>
</html>
