<?php
// Find out when the data was last updated by the Maryland Department of Health
exec("curl https://services.arcgis.com/njFNhDsUCentVYJW/arcgis/rest/services/MDH_COVID_19_Dashboard_Feature_Layer_ZIPCodes_MEMA/FeatureServer/0/", $outputdate, $returnvardate);

// Filter out formatting
$date = str_replace("<b>Last Edit Date:</b> ", "", $outputdate[119]);
$date = str_replace("<br/>", "", $date);

// Format date and fix time zone
$lastupdated = date('F j, Y \a\t g:i A', (strtotime($date) - (60 * 60 * 4)));

if (strpos($lastupdated, '1969') !== false) { Header ("Location: /covid/error"); }

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
<html class="text-dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="stylesheet" href="/assets/bootstrap/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lora">
    <link rel="stylesheet" href="/assets/bootstrap/fonts/ionicons.min.css">
    <link rel="stylesheet" href="/assets/bootstrap/css/Article-Clean.css">
    <link rel="stylesheet" href="/assets/bootstrap/css/Footer-Dark.css">
    <link rel="stylesheet" href="/assets/bootstrap/css/Highlight-Clean.css">
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-114099331-5"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-114099331-5');
    </script>
    <style>body { background-color: #282d32; margin: 2%; } </style>
    <title>COVID-19 in Maryland</title>
</head>

<body>
    <div class="border-dark shadow-lg align-items-center highlight-clean" style="background-color: rgb(182,203,204);margin: 0;width: 100%;padding: 30px;padding-top: 60px;padding-bottom: 60px;">
        <h2 class="text-center"><strong>Maryland Cases of COVID-19 By City/Town/ZIP</strong></h2>
        <div class="container">
            <header></header>
        </div>
        <h5 class="text-center text-dark"><strong>Data last updated by the Maryland Department of Health on <?php echo htmlentities($lastupdated);?></strong></h5>
    </div>
    <div class="card" style="border-top: 1px solid black; border-bottom: none; border-left: 0px; border-right: 0px;">
        <div class="card-header" style="background-color: rgb(207,207,207);padding: 50px;padding-top: 5%;padding-bottom: 5%;"><strong style="margin: 2px;">City/Town/ZIP Code</strong><form style="margin: 0;padding: 0;" action="" method="POST"><input type="text" class="form-control" id="citown" autocomplete="off" name="citown"  value="<?php if(isset($_POST['submit'])) { echo htmlentities($_POST['citown']); } ?>" required>
            <p></p><button class="btn btn-outline-dark active border rounded-0" type="submit" name="submit">check covid-19 cases</button></form></div>
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
                                    echo ("<p style='background-color: rgb(207,207,207); text-align: center; padding: 2%;'>You must enter a city name, town name, or ZIP code.</p>");
                                } else {
                                    
                                    // Clean up user input: every letter is lowercase except the first letter of each word in the city/town name.
                                    $citown = ucwords(strtolower(trim($_POST['citown'])));
                                    
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
                                            exec("curl -XGET 'https://services.arcgis.com/njFNhDsUCentVYJW/arcgis/rest/services/MDH_COVID_19_Dashboard_Feature_Layer_ZIPCodes_MEMA/FeatureServer/0/query?f=json&where=ZIPCODE1=%27" . $citownurl . "%27&returnGeometry=false&outFields=*'", $output, $returnvar);

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
						    echo " <div class='table-responsive' style='padding: 100px;color: #cfcfcf;background-color: #cfcfcf;margin: 0px;padding-top: 5%;padding-bottom: 2%;padding-right: 50px;padding-left: 50px;'>
            <table class='table' style='text-align: center'>
                <thead>
                    <tr>
                        <th class='' style='background-color: #343a40; color: white; border-right: 1px solid white; border-top: 1px solid black; border-left: 1px solid black; width: 50%;'>Zip Code</th>
                        <th class='' style='background-color: #343a40; color: white; border-top: 1px solid black; border-right: 1px solid black; width: 50%;'>Cases of COVID-19</th>
                    </tr>
                </thead>";                                                    
                                                    // Add a table row for each zip code of a city/town and its corresponding COVID-19 case number
                                                    foreach ($zipstocounts as $key => $value) {
                                                        if ($value == "null") {
                                                            
                                                            // The Maryland Department of Health does not provide any data for cities/towns with fewer than 8 cases. Establish a potential high-end of 7 cases for ZIP codes without data.
                                                            $formatted_value = "0-7";
                                                            $highend         = $highend + 7;
                                                            
                                                        } else {
                                                            $formatted_value = $value;
                                                        }
                                                        echo ("<tbody class='border rounded-0 border-dark'>
                    <tr class='border rounded-0 border-dark'>
                        <td class='border rounded-0 border-dark' style='background-color: white;'>$key</td>
                        <td class='border rounded-0 border-dark' style='background-color: white;'>$formatted_value</td></tr>");
                                                    }
                                                    
                                                    // Find the total number of COVID-19 cases in a ZIP code
                                                    $totals = array_sum($p_count);
                                                    
                                                    // Account for ZIP codes with no provided data
                                                    $highend = $totals + $highend;
                                                    
                                                    // If the high-end is equal to the total, no need to specify the high-end. If we do specify it, add the note explaining why.
                                                    if ($highend == $totals) {
                                                        echo "</tbody></table></div>";
                                                    } else {
                                                        echo "</tbody></table></div><br><p style='background-color: rgb(207,207,207); text-align: center; padding: 2%;'><b>NOTE:</b>  There are between 0 and 7 cases of COVID-19 in $citown. The Maryland Department of Health does not provide any data for ZIP codes with fewer than 8 cases.</p>";
                                                    }
                                                } else {
                                                    echo ("<p style='background-color: rgb(207,207,207); text-align: center; padding: 2%;'>An unexpected error occurred. Please contact Dhruvi Mirani for help.</p>");
                                                }
                                            } else {

                                                echo ("<p style='background-color: rgb(207,207,207); text-align: center; padding: 2%;'>Invalid zip code.</p>");
                                            }

                                        // If the input does not match a zip code
                                        } else {

                                            // If the input does not have numbers or special characters
                                            if (!preg_match('/[^A-Za-z\s]/', $citown)) {

                                                // Encode input for curl data retrival from MD Department of Health database and prevent remote code execution
                                                $citownurl = escapeshellcmd(urlencode($citown));
                                                
                                                // Issue data retrival request
                                                exec("curl -XGET 'https://services.arcgis.com/njFNhDsUCentVYJW/arcgis/rest/services/MDH_COVID_19_Dashboard_Feature_Layer_ZIPCodes_MEMA/FeatureServer/0/query?f=json&where=ZIPNAME=%27" . $citownurl . "%27&returnGeometry=false&outFields=*'", $output, $returnvar);
                                                
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
                                                        echo " <div class='table-responsive' style='padding: 100px;color: #cfcfcf;background-color: #cfcfcf;margin: 0px;padding-top: 5%;padding-bottom: 2%;padding-right: 50px;padding-left: 50px;'>
            <table class='table' style='text-align: center'>
                <thead>
                    <tr>
                        <th class='' style='background-color: #343a40; color: white; border-right: 1px solid white; border-top: 1px solid black; border-left: 1px solid black; width: 50%;'>
                        $citown Zip Codes</th>
                        <th class='' style='background-color: #343a40; color: white; border-top: 1px solid black; border-right: 1px solid black; width: 50%;'>Cases of COVID-19</th>
                    </tr>
                </thead>";
                                                        
                                                        // Add a table row for each zip code of a city/town and its corresponding COVID-19 case number
                                                        foreach ($zipstocounts as $key => $value) {
                                                            if ($value == "null") {
                                                                
                                                                // The Maryland Department of Health does not provide any data for cities/towns with fewer than 8 cases. Establish a potential high-end of 7 cases for ZIP codes without data.
                                                                $formatted_value = "0-7";
                                                                $highend         = $highend + 7;
                                                                
                                                            } else {
                                                                $formatted_value = $value;
                                                            }
                                                            echo ("<tbody class='border rounded-0 border-dark'>
                    <tr class='border rounded-0 border-dark'>
                        <td class='border rounded-0 border-dark' style='background-color: white;'>$key</td>
                        <td class='border rounded-0 border-dark' style='background-color: white;'>$formatted_value</td></tr>");
                                                        }
                                                        
                                                        // Find the total number of COVID-19 cases in a city/town by adding up ZIP totals
                                                        $totals = array_sum($p_count);
                                                        
                                                        // Account for ZIP codes with no provided data
                                                        $highend = $totals + $highend;
                                                        
                                                        // If the high-end is equal to the total, no need to specify the high-end. If we do specify it, add the note explaining why.
                                                        if ($highend == $totals) {
                                                            echo "<tr class='border rounded-0 border-dark'>
                        <td class='border rounded-0 border-dark' style='background-color: #E6E6E6;'>Total</td>
                        <td class='border rounded-0 border-dark' style='background-color: #E6E6E6;'>$totals</td>
                    </tr>
                </tbody>
            </table></div>";
                                                        } else {
                                                            echo "<tr class='border rounded-0 border-dark'>
                        <td class='border rounded-0 border-dark' style='background-color: #E6E6E6;'>Total</td>
                        <td class='border rounded-0 border-dark' style='background-color: #E6E6E6;'>$totals-$highend</td>
                    </tr>
                </tbody>
            </table></div><p style='background-color: rgb(207,207,207); text-align: center; padding: 2%;'><b>NOTE:</b> There are between 0 and 7 cases of COVID-19 in some ZIP codes of $citown. The Maryland Department of Health does not provide any data for cities/towns with fewer than 8 cases.</p>";
                                                        }
                                                    } else {
                                                        echo ("<p style='background-color: rgb(207,207,207); text-align: center; padding: 2%;'>An unexpected error occurred. Please contact Dhruvi Mirani for help.</p>");
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
                                                    echo ("<p style='background-color: rgb(207,207,207); text-align: center; padding: 2%;'>Invalid city/town name. Did you mean: <a href='?loc=$closest' style='color: black; text-decoration: underline;'><b>$closest</b></a></p>");
                                                }

                                            // If the input has letters or numbers (but does not match a zip code)
                                            } else {
                                                echo ("<p style='background-color: rgb(207,207,207); text-align: center; padding: 2%;'>Invalid city/town name or zip code.</p>");
                                            }
                                        }
                
                                    } else {
                                        echo ("<p style='background-color: rgb(207,207,207); text-align: center; padding: 2%;'>City/town names and zip codes cannot include special characters.</p>");
                                    }
                                }
                            }
                            ?> 
	    <div class="footer-dark" style="margin-top: -2%;">
	        <footer>
	            <div class="container">
	                <div class="row">
	                    <div class="col-md-6 item text">
	                        <h3>website by  <a href="https://dhruvi.me" target="_blank" style="color: white; text-decoration: underline;">dhruvi mirani</a><br><br><br>view source code <a href="https://github.com/decimal1700/md-covid-19" target="_blank" style="color: white; text-decoration: underline;">here</a></h3>
	                    </div>
	                    <div class="col item social"><a href="https://instagram.com/dhruvimirani"><i class="icon ion-social-instagram"></i></a><a href="https://github.com/decimal1700"><i class="icon ion-social-github"></i></a></div>
	                </div>
	            </div>
	        </footer>
	    </div>
	</div>
</body>
</html>
