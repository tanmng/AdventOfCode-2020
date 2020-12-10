<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 10
 * Part 1
 */


// Script constants
const INPUT_FILE = 'input.txt';

// Array of adapters
$adapters = [];

// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, "r");
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);

        $current_number = intval($input);
        $adapters[] = $current_number;
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

// Sort the number
sort ($adapters);

// Add the device into this list
$device_input = $adapters[count($adapters) - 1] + 3;
$adapters[] = $device_input;

// print_r($adapters);
// Traverse and find the diff
$diffs = array_fill(1, 3, 0);

// Record the first adapter
$diffs[$adapters[0] - 0] += 1;

for ($index = 0; $index + 1 < count($adapters); $index++) {
    $rating = $adapters[$index];
    $diff = $adapters[$index + 1] - $rating;

    // Store this
    $diffs[$diff] += 1;
}

// print_r($diffs);
print('Answer to part 1: '.$diffs[1] * $diffs[3]."\n");
