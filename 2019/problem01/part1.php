<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2019 problem 01
 * Part 1
 */

// Script constants
const INPUT_FILE = 'input.txt';

/*
 * Calculate the fuel requirement for given mass according to the logic provided
 */
function fuelRequirement (
    int $mass
) : int
{
    return intval(floor($mass / 3)) - 2;
}

// Total amount of fueld needed
$total_fuel = 0;

// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, "r");
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);
        $input_mass = intval($input);
        $total_fuel += fuelRequirement($input_mass);
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

//print_r($valid_password_entries);
print('Answer to part 1: '.$total_fuel."\n");
