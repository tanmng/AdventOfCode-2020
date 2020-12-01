<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 01
 * Part 1
 */

// Script constants
const INPUT_FILE = 'input.txt';
const REQUIRED_SUM = 2020;

/*
 * Function to return the pair number of some number
 */

function pairNumber(int $a): int
{
    return REQUIRED_SUM - $a;
}

// Hash map to record all the pairNumber of numbers we found while reading the
// file
$all_numbers = [];

// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, "r");
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);
        if (!is_numeric($input)) {
            // Not a number on this line
            continue;
        }

        $number = intval($input);
        $pair = pairNumber($number);

        // Check if we found the number earlier
        if (in_array($pair, $all_numbers)) {
            // We found the number earlier (we record the pari number)
            print('Found a pair '.$number.' and '.$pair.' sum '.($number + $pair)."\n");
            print('Multiply: '.($number * $pair)."\n");
            break;
        }

        // Record this number
        $all_numbers[] = $number;
    }
    if (!feof($handle)) {
         print('Finished reading the input file but did not find the data');
    }
}
fclose($handle);
