<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 09
 * Part 1
 */


// Script constants
const INPUT_FILE = 'input.txt';

$preamble_length = strcmp(INPUT_FILE, 'sample.txt') === 0? 5 : 25;

// Array of numbers
$numbers = [];

function validateNumber(
    int $number,
    int $index
): bool
{
    // Validate if the given number can be sum of the previous $preamble_length
    global $numbers;
    global $preamble_length;

    if ($index < $preamble_length) {
        // Not suitable
        return false;
    }

    foreach (range(0, $preamble_length) as $i) {
        foreach (range(0, $preamble_length) as $j) {
            if ($i === $j) {
                // Not suitable for a pair
                continue;
            }

            $current_sum = $numbers[$index - $i] + $numbers[$index - $j];

            if ($current_sum ===  $number) {
                // Found the one
                return true;
            }
        }
    }

    return false;
}


// Keep count
$current_number_index = 0;

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
        $numbers[] = $current_number;
        // Validate this
        if ($current_number_index > $preamble_length) {
            if (!validateNumber($current_number, $current_number_index)) {
                // Found the one
                print('Answer to part 1: '.$current_number.' (index '.$current_number_index.")\n");
                return;
            }
        }

        $current_number_index += 1;

    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}
