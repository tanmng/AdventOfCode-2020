<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 06
 * Part 1
 */


// Script constants
const INPUT_FILE = 'input.txt';

$all_answers = [];
$result = 0;
// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, "r");
// A buffer of all the  current lines for the curernt passport
$current_lines = [];
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);

        if (strlen($input)) {
            // Not an empty line
            $current_lines[] = $input;
        } else {
            // Empty line - Conclude here
            $group_member_count = count($current_lines);
            $current_group_answer = implode('', $current_lines);

            // Analyze it
            $question_frequency = array_count_values(str_split($current_group_answer));

            $current_count = 0;
            foreach($question_frequency as $question => $frequency) {
                if ($frequency === $group_member_count) {
                    // This question is answered by all
                    $current_count += 1;
                }
            }

            // print($current_group_answer.': '.$current_count."\n");

            $result += $current_count;

            // Refresh buffer
            $current_lines = [];
        }
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

print('Answer to part 2: '.$result."\n");
