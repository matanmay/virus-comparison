<?php
// Tailwind class applied to cells whose value appears in >1 viruses (shared/highlighted)
define("MARKCLASS", "bg-brand-600/30 text-brand-300 font-semibold rounded px-1");
define("MARKSPAN", "bg-brand-600/30 text-brand-300 font-semibold rounded px-1");

// Common TD classes reused in every row
define("TD_LABEL", 'class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-gray-400 bg-gray-800/80 border-b border-white/10 whitespace-nowrap align-middle"');
define("TD_VALUE", 'class="px-4 py-3 text-gray-300 font-mono text-xs border-b border-white/5 break-all align-top"');
define("TR_BASE", 'class="hover:bg-white/5 transition-colors"');

function showRemoveButton($arrayJson)
{
    echo '<tr class="border-b border-white/10">';
    echo '<td class="px-4 py-2 bg-gray-800/80"></td>';
    foreach ($arrayJson as $row) {
        echo '<td class="px-4 py-2 text-center bg-gray-800/80">';
        echo '<button onclick="deleteColumn(this)"
                    class="inline-flex items-center justify-center w-7 h-7 rounded-lg
                           border border-red-500/40 bg-red-500/10 text-red-400
                           hover:bg-red-500/20 hover:text-red-300 transition-all duration-150
                           text-xs font-bold"
                    title="Remove column">
                    <i class="fa-solid fa-xmark"></i>
                  </button>';
        echo '</td>';
    }
    echo '</tr>';
}



function showMD5($arrayJson)
{
    echo '<tr ' . TR_BASE . '>';
    echo '<td ' . TD_LABEL . '>MD5</td>';
    foreach ($arrayJson as $row) {
        echo '<td ' . TD_VALUE . '>' . htmlspecialchars((string) ($row['md5'] ?? '')) . '</td>';
    }
    echo '</tr>';
}


function showNames($arrayJson)
{
    echo '<tr ' . TR_BASE . '>';
    echo '<td ' . TD_LABEL . '>Names</td>';
    $names = array_column($arrayJson, 'names');
    $counts = array_count_values(array_map('trim', explode(',', implode(',', $names))));
    foreach ($arrayJson as $row) {
        $names = explode(',', (string) ($row['names'] ?? ''));
        echo '<td ' . TD_VALUE . '>';

        for ($i = 0; $i < count($names); $i++) {
            echo '<span';

            // Check if current vote has already been displayed
            $countNames = $counts[trim($names[$i])];
            if ($countNames > 1) {
                echo ' class="' . MARKCLASS . '"';
            }

            echo '>' . trim($names[$i]) . '</span>';
            if ($i < count($names) - 1) {
                echo '<br>'; // Add line break between votes
            }
        }

        echo '</td>';
    }
    echo '</tr>';
}

function showTotalVotes($arrayJson)
{
    echo '<tr ' . TR_BASE . '>';
    echo '<td ' . TD_LABEL . '>Total Votes</td>';
    $votes = array_column($arrayJson, 'total_votes');
    $counts = array_count_values(array_map('trim', explode(',', implode(',', $votes))));
    foreach ($arrayJson as $row) {
        $votes = explode(',', (string) ($row['total_votes'] ?? ''));
        echo '<td ' . TD_VALUE . '>';

        for ($i = 0; $i < count($votes); $i++) {
            echo '<span';

            // Check if current vote has already been displayed
            $countVotes = $counts[trim($votes[$i])];
            if ($countVotes > 1) {
                echo ' class="' . MARKCLASS . '"';
            }

            echo '>' . trim($votes[$i]) . '</span>';
            if ($i < count($votes) - 1) {
                echo '<br>'; // Add line break between votes
            }
        }

        echo '</td>';
    }
    echo '</tr>';
}

function showFileSize($arrayJson)
{
    echo '<tr ' . TR_BASE . '>';
    echo '<td ' . TD_LABEL . '>File Size</td>';
    $size = array_column($arrayJson, 'file_size');
    $counts = array_count_values(array_map('trim', explode(',', implode(',', $size))));
    foreach ($arrayJson as $row) {
        $size = explode(',', (string) ($row['file_size'] ?? ''));
        echo '<td ' . TD_VALUE . '>';

        for ($i = 0; $i < count($size); $i++) {
            echo '<span';

            // Check if current size has already been displayed
            $countVotes = $counts[trim($size[$i])];
            if ($countVotes > 1) {
                echo ' class="' . MARKCLASS . '"';
            }

            echo '>' . trim($size[$i]) . '</span>';
            if ($i < count($size) - 1) {
                echo '<br>'; // Add line break between votes
            }
        }

        echo '</td>';
    }
    echo '</tr>';
}

function showMeaningfulName($arrayJson)
{
    echo '<tr ' . TR_BASE . '>';
    echo '<td ' . TD_LABEL . '>Meaningful Name</td>';
    $meaningful_names = array_column($arrayJson, 'meaningful_name');
    $counts = array_count_values(array_map('trim', explode(',', implode(',', $meaningful_names))));
    foreach ($arrayJson as $row) {
        $meaningful_names = explode(',', (string) ($row['meaningful_name'] ?? ''));
        echo '<td ' . TD_VALUE . '>';

        for ($i = 0; $i < count($meaningful_names); $i++) {
            echo '<span';

            // Check if current name has already been displayed
            $countNames = $counts[trim($meaningful_names[$i])];
            if ($countNames > 1) {
                echo ' class="' . MARKCLASS . '"';
            }

            echo '>' . trim($meaningful_names[$i]) . '</span>';
            if ($i < count($meaningful_names) - 1) {
                echo '<br>'; // Add line break between names
            }
        }

        echo '</td>';
    }
    echo '</tr>';
}

function showFileExtension($arrayJson)
{
    echo '<tr ' . TR_BASE . '>';
    echo '<td ' . TD_LABEL . '>File Extension</td>';
    $file_extensions = array_column($arrayJson, 'file_extension');
    $counts = array_count_values(array_map('trim', explode(',', implode(',', $file_extensions))));
    $totalRows = count($arrayJson);
    foreach ($arrayJson as $row) {
        $current_file_extensions = explode(',', (string) ($row['file_extension'] ?? ''));
        echo '<td ' . TD_VALUE . '>';

        for ($i = 0; $i < count($current_file_extensions); $i++) {
            echo '<span';

            // Check if current file extension appears in all rows
            $countFileExtensions = $counts[trim($current_file_extensions[$i])]; // Use the correct variable name here
            if ($countFileExtensions == $totalRows) { // Use the correct variable name here
                echo ' class="' . MARKCLASS . '"';
            }

            echo '>' . trim($current_file_extensions[$i]) . '</span>';
            if ($i < count($current_file_extensions) - 1) {
                echo '<br>'; // Add line break between file extensions
            }
        }

        echo '</td>';
    }
    echo '</tr>';
}

function showLibraryName($arrayJson)
{
    echo '<tr ' . TR_BASE . '>';
    echo '<td ' . TD_LABEL . '>Library Name</td>';
    $library_name = array_column($arrayJson, 'library_name');
    $counts = array_count_values(array_map('trim', explode(',', implode(',', $library_name))));
    $totalRows = count($arrayJson);
    foreach ($arrayJson as $row) {
        $library_name = explode(',', (string) ($row['library_name'] ?? ''));
        echo '<td ' . TD_VALUE . '>';

        for ($i = 0; $i < count($library_name); $i++) {
            echo '<span';

            // Check if current process appears in all rows
            $countProcesses = $counts[trim($library_name[$i])];
            if ($countProcesses == $totalRows) {
                echo ' class="' . MARKCLASS . '"';
            }

            echo '>' . trim($library_name[$i]) . '</span>';
            if ($i < count($library_name) - 1) {
                echo '<br>'; // Add line break between processes
            }
        }

        echo '</td>';
    }
    echo '</tr>';
}

function showProcessesTerminated($arrayJson)
{
    echo '<tr ' . TR_BASE . '>';
    echo '<td ' . TD_LABEL . '>Processes Terminated</td>';
    $processes_terminated = array_column($arrayJson, 'processes_terminated');
    $counts = array_count_values(array_map('trim', explode(',', implode(',', $processes_terminated))));
    $totalRows = count($arrayJson);
    foreach ($arrayJson as $row) {
        $processes_terminated = explode(',', (string) ($row['processes_terminated'] ?? ''));
        echo '<td ' . TD_VALUE . '>';

        for ($i = 0; $i < count($processes_terminated); $i++) {
            echo '<span';

            // Check if current process appears in all rows
            $countProcesses = $counts[trim($processes_terminated[$i])];
            if ($countProcesses == $totalRows) {
                echo ' class="' . MARKCLASS . '"';
            }

            echo '>' . trim($processes_terminated[$i]) . '</span>';
            if ($i < count($processes_terminated) - 1) {
                echo '<br>'; // Add line break between processes
            }
        }

        echo '</td>';
    }
    echo '</tr>';
}

function showProcessesCreated($arrayJson)
{
    echo '<tr ' . TR_BASE . '>';
    echo '<td ' . TD_LABEL . '>Processes Created</td>';
    $processes_created = array_column($arrayJson, 'processes_created');
    $counts = array_count_values(array_map('trim', explode(',', implode(',', $processes_created))));
    $totalRows = count($arrayJson);
    foreach ($arrayJson as $row) {
        $processes_created = explode(',', (string) ($row['processes_created'] ?? ''));
        echo '<td ' . TD_VALUE . '>';

        for ($i = 0; $i < count($processes_created); $i++) {
            echo '<span';

            // Check if current process appears in all rows
            $countProcesses = $counts[trim($processes_created[$i])];
            if ($countProcesses == $totalRows) {
                echo ' class="' . MARKCLASS . '"';
            }

            echo '>' . trim($processes_created[$i]) . '</span>';
            if ($i < count($processes_created) - 1) {
                echo '<br>'; // Add line break between processes
            }
        }

        echo '</td>';
    }
    echo '</tr>';
}

function showTags($arrayJson)
{
    echo '<tr ' . TR_BASE . '>';
    echo '<td ' . TD_LABEL . '>Tags</td>';
    $tags = array_column($arrayJson, 'tags');
    $counts = array_count_values(array_map('trim', explode(',', implode(',', $tags))));
    $totalRows = count($arrayJson);
    foreach ($arrayJson as $row) {
        $tags = explode(',', (string) ($row['tags'] ?? ''));
        echo '<td ' . TD_VALUE . '>';

        for ($i = 0; $i < count($tags); $i++) {
            echo '<span';

            // Check if current process appears in all rows
            $countProcesses = $counts[trim($tags[$i])];
            if ($countProcesses == $totalRows) {
                echo ' class="' . MARKCLASS . '"';
            }

            echo '>' . trim($tags[$i]) . '</span>';
            if ($i < count($tags) - 1) {
                echo '<br>'; // Add line break between processes
            }
        }

        echo '</td>';
    }
    echo '</tr>';
}

function showVerdicts($arrayJson)
{
    echo '<tr ' . TR_BASE . '>';
    echo '<td ' . TD_LABEL . '>Verdicts</td>';
    $verdicts = array_column($arrayJson, 'verdicts');
    $counts = array_count_values(array_map('trim', explode(',', implode(',', $verdicts))));
    $totalRows = count($arrayJson);
    foreach ($arrayJson as $row) {
        $verdicts = explode(',', (string) ($row['verdicts'] ?? ''));
        echo '<td ' . TD_VALUE . '>';

        for ($i = 0; $i < count($verdicts); $i++) {
            echo '<span';

            // Check if current process appears in all rows
            $countProcesses = $counts[trim($verdicts[$i])];
            if ($countProcesses == $totalRows) {
                echo ' class="' . MARKCLASS . '"';
            }

            echo '>' . trim($verdicts[$i]) . '</span>';
            if ($i < count($verdicts) - 1) {
                echo '<br>'; // Add line break between processes
            }
        }

        echo '</td>';
    }
    echo '</tr>';
}

function showMalicious($arrayJson)
{
    echo '<tr ' . TR_BASE . '>';
    echo '<td ' . TD_LABEL . '>Malicious</td>';
    $malicious = array_column($arrayJson, 'malicious');
    $counts = array_count_values(array_map('trim', explode(',', implode(',', $malicious))));
    $totalRows = count($arrayJson);
    foreach ($arrayJson as $row) {
        $maliciousVal = (int) ($row['malicious'] ?? 0);
        $undetectedVal = (int) ($row['undetected'] ?? 0);
        $total = $maliciousVal + $undetectedVal;
        $malicious = explode(',', (string) $maliciousVal);
        echo '<td ' . TD_VALUE . '>';

        for ($i = 0; $i < count($malicious); $i++) {
            echo '<span';

            // Check if current process appears in all rows
            $countMal = $counts[trim($malicious[$i])];
            if ($countMal == $totalRows) {
                echo ' class="' . MARKCLASS . '"';
            }

            echo '>' . $maliciousVal . '/' . $total . '</span>';
            if ($i < count($malicious) - 1) {
                echo '<br>'; // Add line break between processes
            }
        }

        echo '</td>';
    }

    echo '</tr>';
}

/*function showMalicious($arrayJson)
{
    echo '<tr> <td style="vertical-align: middle; font-size: 17px; font-weight: bold;">';
    echo 'Malicious';
    echo '</td>';
    $prev_value = null; 

    foreach ($arrayJson as $row) {
        echo '<td';

        // Check if the 'malicious' and 'undetected' keys exist in the array
        if (isset($row['malicious']) && isset($row['undetected'])) {
            $total = $row['malicious'] + $row['undetected']; 
            $malicious_result = $row['malicious'] .'/' .$total;

            // Highlight if same value
            if ($prev_value == $malicious_result) {
                echo ' class="' . MARKCLASS . '"';
            }
            echo '>';
            echo $malicious_result;
        } else {
            echo '>';
            echo 'N/A';  // print N/A if 'malicious' or 'undetected' is not set
        }

        echo '</td>';
        $prev_value = $malicious_result ?? null; // assign null if $malicious_result is not set
    }

    echo '</tr>';
}*/

?>

<!-- <script>
// delete column from the comparison table
function deleteColumn(button) {
    var colIndex = button.parentNode.cellIndex;
    var table = document.getElementById("multipleTable");
    var rows = table.rows;

    // get the MD5 of the column being removed
    var md5 = rows[1].cells[colIndex].textContent.trim();

    // loop through all the rows in the table and remove the cells in the given column index
    for (var i = 0; i < rows.length; i++) {
        rows[i].deleteCell(colIndex);
    }

    
    document.write(md5);
}
</script> -->