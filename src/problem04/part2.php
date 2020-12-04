<?php
/*
 * part2.php
 *
 * Main program for Advent calendar 2020 problem 04
 * Part 2
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

        // Validate the field value
        switch ($field_name) {
        case 'byr':
            if (!is_numeric($field_value) || intval($field_value) < 1920 || intval($field_value) > 2002) {
                // print('byr: '.$field_value."\n");
                // Did not pass validation for birth year
                return false;
            }
            break;
        case 'iyr':
            if (!is_numeric($field_value) || intval($field_value) < 2010 || intval($field_value) > 2020) {
                // print('iyr: '.$field_value."\n");
                // Did not pass validation for issued year
                return false;
            }
            break;
        case 'eyr':
            if (!is_numeric($field_value) || intval($field_value) < 2020 || intval($field_value) > 2030) {
                // print('eyr: '.$field_value."\n");
                // Did not pass validation for expiration year
                return false;
            }
            break;
        case 'hgt':
            preg_match('/^([0-9]+)(cm|in)$/', $field_value, $height_parts);

            if (count($height_parts) !== 3) {
                // print('hgt: '.$field_value." regex\n");
                return false;
            }

            $height = intval($height_parts[1]);
            $height_unit = $height_parts[2];
            switch ($height_unit) {
            case 'cm':
                if ($height < 150 || $height > 193) {
                    // print('hgt: '.$field_value."\n");
                    // Failed the height test
                    return false;
                }
                break;
            case 'in':
                if ($height < 59 || $height > 76) {
                    // print('hgt: '.$field_value."\n");
                    // Failed the height test
                    return false;
                }
                break;
            default:
                // print('hgt: '.$field_value."\n");
                // Not a supported unit
                break;
            }
            break;
        case 'hcl':
            if (!preg_match('/^#[a-f0-9]{6}$/', $field_value)) {
                // print('hcl: '.$field_value."\n");
                // Failed the hair colour
                return false;
            }
            break;
        case 'ecl':
            if (!in_array($field_value, ['amb', 'blu', 'brn', 'gry', 'grn', 'hzl', 'oth'])) {
                // print('ecl: '.$field_value."\n");
                // Failed the eyey colour
                return false;
            }
            break;
        case 'pid':
            if (!preg_match('/^[0-9]{9}$/', $field_value)) {
                // print('pid: '.$field_value."\n");
                // Failed the passport ID
                return false;
            }
            break;
        }

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
$index = 0;
$valid_indices = [];
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
                $valid_indices[] = $index;
            }

            // Refresh buffer
            $current_lines = [];

            $index += 1;
        }
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

print('Answer to part 2: '.count($valid_passports)."\n");
print('Passport count: '.count($all_passports)."\n");
