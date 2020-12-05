<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 05
 * Part 1
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

// print_r($seat_numbers);
print('Answer to part 1: '.max($seat_numbers)."\n");
