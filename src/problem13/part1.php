<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 13
 * Part 1
 */


// Script constants
const INPUT_FILE = 'input.txt';

$lines = [];

// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, 'r');
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);
        $lines[] = $input;
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

$value = intval($lines[0]);
$buses = [];
foreach (explode(',', $lines[1]) as $bus) {
    if (is_numeric($bus)) {
        $buses[] = intval($bus);
    }
}

// Try to increase
$target = $value;
$counter = 0;
while (true) {
    $found = false;
    foreach ($buses as $bus) {
        if ($target % $bus === 0) {
            // Suitable time
            $found = true;
            break;
        }
    }

    if ($found) {
        print('Answer for part 1: '.($target - $value) * $bus."\n");
        break;
    } else {
        $target += 1;
    }

    $counter += 1;
    if ($counter === 1000) {
        print('Failed');
        break;
    }
}


/* print($value); */
/* print_r($buses); */
