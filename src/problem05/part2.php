<?php
/*
 * part2.php
 *
 * Main program for Advent calendar 2020 problem 05
 * Part 2
 */

// Script constants
const INPUT_FILE = 'input.txt';

$seat_numbers = [];
// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, "r");
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);

        $bin_string = str_replace(['F', 'B', 'L', 'R'], [0, 1, 0, 1], $input);

        $current_seat_id = bindec($bin_string);

        // Store this
        $seat_numbers[] = $current_seat_id;

    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

// Go through the list of numbers and find the missing one
$missing = array_diff(range(min($seat_numbers), max($seat_numbers)), $seat_numbers);

foreach ($missing as $potential) {
    if (in_array($potential + 1, $seat_numbers) && in_array($potential - 1, $seat_numbers)) {
        // Bingo
        print('Answer to part 2: '.$potential."\n");
    }
}

// print_r($missing);
