<?php
if (isset($_GET['export'])) 
{
    $jsonString = urldecode($_GET['export']);
    $arrayJson = json_decode($jsonString, true);

    // Set the response headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="compare_table.csv"');

    // Open the file
    $output = fopen('php://output', 'w');

    // Get the column headers
    $columnHeaders = array(
        'MD5',
        'Names',
        'Total Votes',
        'File Size',
        'Meaningful Name',
        'File Extension',
        'Library Name',
        'Processes Created',
        'Processes Terminated',
        'Tags',
        'Verdicts'
    );

    // Write the column headers as a single row in the CSV
    fputcsv($output, $columnHeaders);

    // Write the data rows to the CSV
    foreach ($arrayJson as $key => $row) {
        // Check if the expected keys are present in the $row array by the name
        $md5 = $row['md5'];
        $names = isset($row['names']) ? $row['names'] : '';
        $totalVotes = isset($row['total_votes']) ? $row['total_votes'] : '';
        $fileSize = isset($row['file_size']) ? $row['file_size'] : '';
        $meaningfulName = isset($row['meaningful_name']) ? $row['meaningful_name'] : '';
        $fileExtension = isset($row['file_extension']) ? $row['file_extension'] : '';
        $libraryName = isset($row['library_name']) ? $row['library_name'] : '';
        $processesCreated = isset($row['processes_created']) ? $row['processes_created'] : '';
        $processesTerminated = isset($row['processes_terminated']) ? $row['processes_terminated'] : '';
        $tags = isset($row['tags']) ? $row['tags'] : '';
        $verdicts = isset($row['verdicts']) ? $row['verdicts'] : '';

        // Get the values for each row
        $rowValues = array(
            $md5,
            $names,
            $totalVotes,
            $fileSize,
            $meaningfulName,
            $fileExtension,
            $libraryName,
            $processesCreated,
            $processesTerminated,
            $tags,
            $verdicts
        );
        fputcsv($output, $rowValues); // Write in the CSV
    }

    // Close the output stream
    fclose($output);

    exit();
}




?>
