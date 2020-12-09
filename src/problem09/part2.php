<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 09
 * Part 1
 */


// Script constants
const INPUT_FILE = 'input.txt';
$required_sum = strcmp(INPUT_FILE, 'sample.txt') === 0? 127 : 21806024;

// Array of numbers
$numbers = [];

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
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

// Go through the lsit of numbers and find contiguous numbers that add up to
// REQURIED_SUM
$candidates = [$numbers[0], $numbers[1]];
$next_index = 2; // Index of the next element from $numbers to add to our list of candidates
while (true) {
    $current_sum = array_sum($candidates);
    if ($current_sum === $required_sum) {
        // Found the set
        break;
    } else if ($current_sum < $required_sum) {
        // We need some more
        array_push($candidates, $numbers[$next_index]);
        $next_index += 1;
    } else if ($current_sum > $required_sum) {
        // We have to remove some numbers from the begining
        array_shift($candidates);
    }

}

/* print_r($candidates); */
/* print(array_sum($candidates)); */

// Found the one
print('Answer to part 2: '.min($candidates) + max($candidates)."\n");
