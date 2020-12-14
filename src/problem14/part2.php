<?php
/*
 * part2.php
 *
 * Main program for Advent calendar 2020 problem 14
 * Part 2
 */

// More memory for recursive
ini_set('memory_limit', '2048M');

// Script constants
const INPUT_FILE = 'input.txt';
const MASK_FLOAT_BIT = 'X';  // Signify that we should treat the bit as float
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

/*
 * Apply a mask to an address and return the list of address that should be
 * modify
 */
function apply_mask(
    string $mask,
    int $address
): array
{
    // Convert to binary, make sure it's MACHNINE_ARCHITECTURE bit long
    $address_bin = str_pad(decbin($address), MACHNINE_ARCHITECTURE, str_repeat('0', MACHNINE_ARCHITECTURE), STR_PAD_LEFT);
    $result_bin_bits = [];
    foreach (range(0, MACHNINE_ARCHITECTURE - 1) as $i) {
        if ($mask[$i] === '0') {
            // Unchanged
            $result_bin_bits[] = $address_bin[$i];
        } else if ($mask[$i] === '1'){
            // Overwrite
            $result_bin_bits[] = '1';
        } else {
            // Floatting
            $result_bin_bits[] = 'X';
        }
    }

    // We now need to generate all the address
    $bin_addresses = generate_address($result_bin_bits);
    $final = [];
    foreach ($bin_addresses as $bin) {
        $final[] = bindec($bin);
    }
    return $final;
}

// We have to do this recursively
function generate_address(
    array $bits_left,
    array $accumulator = ['']   // Initially this should contain 1 element - an empty string so that we can avoid having to check for the number of elements
): array
{
    if (count($bits_left) === 0) {
        // Nothing left to construct
        return $accumulator;
    } else {
        $first_bit_left = array_shift($bits_left);
        switch ($first_bit_left) {
        case '0':
        case '1':
            // Fixed bit
            foreach (range(0, count($accumulator) - 1) as $i) {
                $accumulator[$i] .= $first_bit_left;
            }
            break;
        case MASK_FLOAT_BIT:
            //Floatting bit
            $copy = $accumulator;
            $accumulator = [];
            foreach ($copy as $value) {
                $accumulator[] = $value.'0';
                $accumulator[] = $value.'1';
            }
            break;
        }
        return generate_address($bits_left, $accumulator);
    }
}


$memory = [];

$current_mask = null;
foreach ($lines as $line) {
    if (preg_match('/^mask = ([01X]+)$/', $line, $parts)) {
        // This is the mask instruction
        $current_mask = $parts[1];
        continue;
    }

    if (preg_match('/^mem\[([0-9]+)\] = ([0-9]+)$/', $line, $parts)) {
        // This is a memory assignment
        $address = intval($parts[1]);
        $value = intval($parts[2]);
        $all_addresses = apply_mask($current_mask, $address);
        foreach ($all_addresses as $temp_address) {
            // print('Set '.$temp_address.' to '.$value.' (with mask '.$current_mask.") \n");
            $memory[$temp_address] = $value;
        }
        continue;
    }
}

print('Answer for part 2: '.array_sum($memory)."\n");
