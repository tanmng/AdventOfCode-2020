<?php
/*
 * part2.php
 *
 * Main program for Advent calendar 2020 problem 20
 * Part 2
 */


const INPUT_FILE = 'sample.txt';

// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, "r");
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

print('Answer to part 2: '.null."\n");
