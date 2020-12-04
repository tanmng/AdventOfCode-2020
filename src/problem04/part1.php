<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 04
 * Part 1
 */

// Script constants
const INPUT_FILE = 'input.txt';

// List of required fields
const REQUIRED_FIELDS = [
    'byr',
    'iyr',
    'eyr',
    'hgt',
    'hcl',
    'ecl',
    'pid',
    // 'cid', -- Not need this at all muahahah
];

/*
 * Check whether a field is a valid passport
 */
function isValidPassport(
    string $passport_raw
): bool
{
    // Break the thing into parts
    $parts = explode(' ', $passport_raw);

    // Go through the parts and check the list of fields
    $field_list = [];
    foreach ($parts as $part) {
        $field_parts = explode(':', $part);
        $field_name = $field_parts[0];
        $field_value = $field_parts[1];

        // Record this
        $field_list[] = $field_name;
    }

    // The passport is valid if it contains all the required fiels
    // print_r($field_list);
    $diff = array_diff(REQUIRED_FIELDS, $field_list);
    // print_r($diff);
    return (0 === count($diff));
}

$all_passports = [];
$valid_passports = [];
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
            $current_passport = implode(' ', $current_lines);

            $all_passports[] = $current_passport;

            if (isValidPassport($current_passport)) {
                // Valid one - record
                $valid_passports[] = $current_passport;
            }

            // Refresh buffer
            $current_lines = [];
        }
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

print('Answer to part 1: '.count($valid_passports)."\n");
print('Passport count: '.count($all_passports)."\n");
