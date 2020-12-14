<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 14
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

$memory = [];

/*
 * Apply a mask and then return the value
 */
function apply_mask(
    string $mask,
    int $value
): int
{
    // Convert to binary, make sure it's 36 bit long
    $value_bin = str_pad(decbin($value), 36, '000000000000000000000000000000000000', STR_PAD_LEFT);
    $result_bin = '';
    foreach (range(0, 35) as $i) {
        if ($mask[$i] === 'X') {
            $result_bin .= $value_bin[$i];
        } else {
            // Overwrite
            $result_bin .= $mask[$i];
        }
        // print($result_bin."\n");
    }
    return bindec($result_bin);
}

$current_mask = null;
foreach ($lines as $line) {
    if (preg_match('/^mask = ([01X]+)$/', $line, $parts)) {
        // This is the mask instruction
        $current_mask = $parts[1];
        // print('Current mask: '.$current_mask."\n");
        continue;
    }

    if (preg_match('/^mem\[([0-9]+)\] = ([0-9]+)$/', $line, $parts)) {
        // This is a memory assignment
        $address = intval($parts[1]);
        $value = intval($parts[2]);
        // print('Set '.$address.' to '.$value.' (with mask '.$current_mask.") \n");
        $memory[$address] = apply_mask($current_mask, $value);
        // print_r($memory);
        continue;
        // break;
    }
}

print('Answer for part 1: '.array_sum($memory)."\n");
