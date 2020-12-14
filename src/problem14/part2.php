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
    // Convert to binary, make sure it's 36 bit long
    $address_bin = str_pad(decbin($address), 36, '000000000000000000000000000000000000', STR_PAD_LEFT);
    $result_bin_bits = [];
    foreach (range(0, 35) as $i) {
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
    $bin_addresses = generate_address($result_bin_bits, []);
    $final = [];
    foreach ($bin_addresses as $bin) {
        $final[] = bindec($bin);
    }
    return $final;
}

// We have to do this recursively
function generate_address(
    array $bits_left,
    array $accumulator
): array
{
    if (count($bits_left) === 0) {
        // Nothing left to construct
        return $accumulator;
    } else {
        $first_bit_left = $bits_left[0];
        switch ($first_bit_left) {
        case '0':
        case '1':
            // Fixed bit
            if (count($accumulator) === 0) {
                // Nothing in accumulator yet
                $accumulator[] = $first_bit_left;
            } else {
                foreach ($accumulator as $i => $temp) {
                    $accumulator[$i] .= $first_bit_left;
                }
            }
            break;
        case 'X':
            //Floatting bit
            if (count($accumulator) === 0) {
                // Nothing yet
                $accumulator = ['0', '1'];
            } else {
                $copy = $accumulator;
                $accumulator = [];
                foreach ($copy as $value) {
                    $accumulator[] = $value.'0';
                    $accumulator[] = $value.'1';
                }
            }
            break;
        }
    }
    array_shift($bits_left);
    return generate_address($bits_left, $accumulator);
}


$memory = [];

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
        $all_addresses = apply_mask($current_mask, $address);
        foreach ($all_addresses as $temp_address) {
            // print('Set '.$temp_address.' to '.$value.' (with mask '.$current_mask.") \n");
            $memory[$temp_address] = $value;
        }
        // print_r($memory);
        continue;
        // break;
    }
}

print('Answer for part 2: '.array_sum($memory)."\n");
