<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 14
 * Part 1
 */


// Script constants
const INPUT_FILE = 'input.txt';
const MASK_KEEP_BIT = 'X';  // Signify that we should keep the value when applying mask
const MACHNINE_ARCHITECTURE = 36;   // How many bits we have in our machine

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
    // Convert to binary, make sure it's MACHNINE_ARCHITECTURE bit long
    $value_bin = str_pad(decbin($value), MACHNINE_ARCHITECTURE, str_repeat('0', MACHNINE_ARCHITECTURE), STR_PAD_LEFT);
    $result_bin = '';
    foreach (range(0, MACHNINE_ARCHITECTURE - 1) as $i) {
        if ($mask[$i] === MASK_KEEP_BIT) {
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
