<?php
require '../vendor/autoload.php';

/**
 * Reads the MongoDB connection URI from the environment.
 * Apache (mod_php) doesn't forward container env vars to getenv() unless
 * PassEnv is configured, so we fall back to $_ENV and $_SERVER as well.
 *
 * @throws RuntimeException if the variable is not found at all.
 */
function getMongoUrl(): string
{
    $url = getenv('MONGODB_URL');          // works when PassEnv is set in Apache
    if (!$url)
        $url = $_ENV['MONGODB_URL'] ?? null;
    if (!$url)
        $url = $_SERVER['MONGODB_URL'] ?? null;
    if (!$url) {
        throw new \RuntimeException(
            'MONGODB_URL environment variable is not set. ' .
            'Make sure it is defined in .env and that passenv.conf is loaded by Apache.'
        );
    }
    return $url;
}

/**
 * Reads the VirusTotal API key from the environment.
 * Uses the same Apache PassEnv-aware fallback chain as getMongoUrl().
 *
 * @throws RuntimeException if the variable is not found at all.
 */
function getVirusTotalAPIKey(): string
{
    $key = getenv('VIRTUSTOTAL_API');          // works when PassEnv is set in Apache
    if (!$key)
        $key = $_ENV['VIRTUSTOTAL_API'] ?? null;
    if (!$key)
        $key = $_SERVER['VIRTUSTOTAL_API'] ?? null;
    if (!$key) {
        throw new \RuntimeException(
            'VIRTUSTOTAL_API environment variable is not set. ' .
            'Make sure it is defined in .env and that passenv.conf is loaded by Apache.'
        );
    }
    return $key;
}

#This function get Json file and insert it to the db
function insertToDB($md5, $file)
{
    $client = new \MongoDB\Client(getMongoUrl());
    $db = $client->malwereDB;
    $collection = $db->malweres;

    // Decode the JSON into an array and lowercase all the keys and values
    $documents = json_decode(strtolower($file), true);

    // Insert the array into MongoDB
    $collection->insertOne($documents);
    /*
    // Find the inserted document
    $result = $collection->findOne(array("md5" => $md5));

    // Print the result
    var_dump($result);*/
}

#This function checks if the md5 exists in the db
function isExist($md5)
{
    $client = new \MongoDB\Client(getMongoUrl());
    $db = $client->malwereDB;
    $collection = $db->malweres;
    $result = $collection->findOne(array("md5" => $md5));
    if ($result != NULL) {
        return True;
    }
    return False;
}

function getFromAPI($md5)
{
    $virusDoesntExists = false;

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://www.virustotal.com/api/v3/files/$md5",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "x-apikey: " . getVirusTotalAPIKey()
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        // if the response contains the NotFoundError error message, the md5 does not exists
        if (strpos($response, '"code":"NotFoundError"') !== false) {
            $virusDoesntExists = true;
            return $virusDoesntExists;
        } else {
            $json_pretty = json_encode(json_decode($response), JSON_PRETTY_PRINT);
        }
    }

    $obj = json_decode($response, true);

    if (isset($obj['data']['attributes']['names'])) {
        $names = $obj['data']['attributes']['names'];
        $names = array_unique($names); // remove duplicates
        natcasesort($names);
        $names = implode(", ", $names);
    } else {
        $names = "";
    }


    if (isset($obj['data']['attributes']['total_votes'])) {
        $total_votes = $obj['data']['attributes']['total_votes'];
        natcasesort($total_votes);
        $total_votes = implode(", ", array_filter($total_votes));
    } else {
        $total_votes = "";
    }

    if (isset($obj['data']['attributes']['size'])) {
        $size = $obj['data']['attributes']['size'];
    } else {
        $size = "";
    }

    if (isset($obj['data']['attributes']['meaningful_name'])) {
        $meaningful_name = $obj['data']['attributes']['meaningful_name'];
    } else {
        $meaningful_name = "";
    }

    if (isset($obj['data']['attributes']['type_extension'])) {
        $type_extension = $obj['data']['attributes']['type_extension'];
    } else {
        $type_extension = "";
    }

    if (isset($obj['data']['attributes']['pe_info']['import_list'])) {
        $import_list = $obj['data']['attributes']['pe_info']['import_list'];
    } else {
        $import_list = "";
    }

    if (is_array($import_list) && array_key_exists('library_name', $import_list[0])) {
        $library_name = array_column($import_list, 'library_name');
        $library_name = array_unique($library_name); // remove duplicates
        natcasesort($library_name);
        $library_name = implode(", ", array_filter($library_name));
    } else {
        $library_name = "";
    }

    $malicious = "";
    $undetected = "";
    if (isset($obj['data']['attributes']['last_analysis_stats'])) {
        $malicious = $obj['data']['attributes']['last_analysis_stats']['malicious'];
        $undetected = $obj['data']['attributes']['last_analysis_stats']['undetected'];
    }


    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://www.virustotal.com/api/v3/files/$md5/behaviour_summary",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "x-apikey: " . getVirusTotalAPIKey()
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        $json_pretty = json_encode(json_decode($response), JSON_PRETTY_PRINT);
    }

    $obj = json_decode($response, true);

    if (isset($obj['data']['processes_terminated'])) {
        $processes_terminated = implode(",\n ", array_filter($obj['data']['processes_terminated']));
        $segments = explode(',', $processes_terminated);
        $last_parts = array();

        foreach ($segments as $segment) {
            $last_backslash_position = strrpos($segment, '\\');
            if ($last_backslash_position !== false) {
                $last_part = substr($segment, $last_backslash_position + 1);
            } else {
                $last_part = $segment;
            }
            array_push($last_parts, $last_part);
        }

        natcasesort($last_parts);
        $last_parts = array_unique($last_parts); // remove duplicates
        $processes_terminated = implode(', ', $last_parts);
    } else {
        $processes_terminated = "";
    }

    if (isset($obj['data']['processes_created'])) {
        $processes_created = $obj['data']['processes_created'];
        $last_parts = array();

        foreach ($processes_created as $process) {
            $last_backslash_position = strrpos($process, '\\');
            if ($last_backslash_position !== false) {
                $last_part = substr($process, $last_backslash_position + 1);
            } else {
                $last_part = $process;
            }
            array_push($last_parts, $last_part);
        }

        natcasesort($last_parts);
        $last_parts = array_unique($last_parts); // remove duplicates
        $processes_created = implode(', ', $last_parts);
    } else {
        $processes_created = "";
    }

    if (isset($obj['data']['tags'])) {
        $tags = $obj['data']['tags'];
        $tags = array_unique($tags); // remove duplicates
        natcasesort($tags);
        $tags = implode(",\n ", array_filter($tags));
    } else {
        $tags = "";
    }

    if (isset($obj['data']['verdicts'])) {
        $verdicts = $obj['data']['verdicts'];
        $verdicts = array_unique($verdicts); // remove duplicates
        natcasesort($verdicts);
        $verdicts = implode(",\n ", array_filter($verdicts));
    } else {
        $verdicts = "";
    }



    #create Json File
    $jsonData = json_encode(array(
        "_id" => $md5,
        "md5" => $md5,
        "names" => $names,
        "total_votes" => $total_votes,
        "file_size" => $size,
        "meaningful_name" => $meaningful_name,
        "file_extension" => $type_extension,
        "library_name" => $library_name,
        "processes_terminated" => $processes_terminated,
        "processes_created" => $processes_created,
        "tags" => $tags,
        "verdicts" => $verdicts,
        "malicious" => $malicious,
        "undetected" => $undetected
    ));
    #$jsonData = json_encode($jsonData);
    #echo $jsonData;
    insertToDB($md5, $jsonData); #inserting to our DB
}

#this method get md5 and return the data from our DB
function getFromDB($md5)
{
    #connecting to DB
    $client = new \MongoDB\Client(getMongoUrl());
    $db = $client->malwereDB;
    $collection = $db->malweres;
    // Find the inserted document
    $result = $collection->findOne(array("md5" => $md5));
    return $result;
}

#This method gets md5 and check if is exist in the db, if not add it and return it else just return it
function getData($md5)
{
    if (!isExist($md5)) {
        // md5 does not exist on virus total
        if (getFromAPI($md5) === true) {
            return null;
        }
    }
    return getFromDB($md5);
}

#this method get a one JSON FILE and print it on HTML Table
function showMyOnlyOneJson($file)
{
    echo '<table name="table" id="table" class="table table-hover" border="1">
    <tr>
        <th>Key</th>
        <th>Value</th>
    </tr>';
    foreach ($file as $key => $value) {
        if ($value != 'null' && $key != '_id' && $value != '') {
            echo "<tr>";
            echo "<td>$key</td>";
            echo "<td>$value</td>";
            echo "</tr>";
        }
    }
    echo '</table>';
}


#This function get 2 json file and do mege between them
function mergeJson($json1, $json2)
{
    $json1 = json_encode($json1);
    $json2 = json_encode($json2);
    // Convert JSON objects to arrays
    $array1 = json_decode($json1, true);
    $array2 = json_decode($json2, true);

    // Merge arrays
    #$mergedArray = array_merge($array1, $array2);
    $mergedArray = array_merge($array1, $array2);
    #print("!!!!!");
    #print_r($mergedArray);
    // Convert merged array back to JSON
    $mergedJson = json_encode($mergedArray);
    #$mergedJson = json_decode($mergedJson, true);
    // Output merged JSON

    return $mergedJson;
}

#This method gets a array of jsons and show the data in html table
function printCompareTable($arrayJson)
{
    require_once('showCompareController.php');

    // ── Toolbar: filter + export ──────────────────────────────────────────────
    echo '<div class="flex flex-wrap items-center gap-4 mb-6">';
    printgetCategoriesListAsCombobox(getCategoriesList());
    echo '<a href="exportCSV.php?export=' . urlencode(json_encode($arrayJson)) . '"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600
                   hover:bg-brand-500 text-white text-sm font-semibold shadow-lg
                   shadow-brand-900/50 transition-all duration-200 hover:-translate-y-0.5">
            <i class="fa-solid fa-file-csv"></i> Export to CSV
         </a>';
    echo '</div>';

    // ── Comparison table ──────────────────────────────────────────────────────
    echo '<div class="overflow-x-auto rounded-2xl border border-white/10">';
    echo '<table id="multipleTable" class="w-full text-sm text-left">';
    echo '<thead>';

    showRemoveButton($arrayJson);
    showMD5($arrayJson);
    showNames($arrayJson);
    showMalicious($arrayJson);
    # showTotalVotes($arrayJson); # always null
    showFileSize($arrayJson);
    showMeaningfulName($arrayJson);
    showFileExtension($arrayJson);
    showLibraryName($arrayJson);
    showProcessesCreated($arrayJson);
    showProcessesTerminated($arrayJson);
    showTags($arrayJson);
    showVerdicts($arrayJson);

    echo '</thead></table></div>';

    filterJavaScript();
}

#This method is return array of the categories that we compare
function getCategoriesList()
{
    $names = array(
        "MD5",
        "Names",
        "Total Votes",
        "File Size",
        "Meaningful Name",
        "File Extension",
        "Libary Name",
        "Processes Created",
        "Processes Terminated",
        "Tags",
        "Verdicts"
    );
    return $names;
}

#This method prints a styled select for all the categories
function printgetCategoriesListAsCombobox($arrayCategories)
{
    echo '<div class="flex items-center gap-2">';
    echo '<label for="categories" class="text-sm font-medium text-gray-400 whitespace-nowrap">Filter category:</label>';
    echo '<select name="categories" id="categories" onchange="comboFilter()"
               class="rounded-lg bg-gray-800 border border-white/10 px-3 py-2 text-sm
                      text-gray-200 outline-none focus:ring-2 focus:ring-brand-500
                      cursor-pointer transition-all duration-150">';
    echo '<option value="">All categories</option>';
    foreach ($arrayCategories as $category) {
        echo '<option value="' . htmlspecialchars($category) . '">' . htmlspecialchars($category) . '</option>';
    }
    echo '</select>';
    echo '</div>';
}

#This method is doing the filter based on the categories combobox using JavaScript function
function filterJavaScript()
{
    echo '
    <script type="text/JavaScript"> 
    function comboFilter() {
        let filter = document.getElementById("categories").value; //get the chosen value from the combobox
        let table = document.getElementById("multipleTable"); //get the HTML table
        let tr = table.getElementsByTagName("tr"); //get array of HTML rows
        
          
        for (let i = 0; i < tr.length; i++) 
        {
            let element = tr[i].getElementsByTagName("td")[0]; // get the first <td> element
            let cellValue = element.textContent.trim(); // get the text content of the cell and trim whitespace
            if (cellValue == "MD5") {
                tr[i].style.display = ""; // always show the "MD5" row to identification
            }
            else if (cellValue != filter && filter !== "") 
            {
                tr[i].style.display = "none";
                //document.write(cellValue);
            } else {
                tr[i].style.display = "";
            }
        }
    }
    
    </script>';
}
