<?php
/*
 * part2.php
 *
 * Main program for Advent calendar 2019 problem 01
 * Part 2
 */

// Script constants
const INPUT_FILE = 'input.txt';

$fuel_table = [];   // Mapping from given mass to its weight requirement so that we don't have to run recursion for each and every mass

/*
 * Calculate the fuel requirement for given mass according to the logic provided
 */
function fuelRequirement (
    int $mass
) : int
{
    global $fuel_table;
    if (array_key_exists($mass, $fuel_table)) {
        // We encountered this mass and calculated for it earlier
        return $fuel_table[$mass];
    }

    // Calculate the fuel purely for this mass
    $fuel = intval(floor($mass / 3)) - 2;

    if ($fuel <= 0) {
        // Stopper for recursion, we can't have negative fuel
        return 0;
    }

    // Return the fuel for this one mass and the fuel for the fueld itself
    $required_fuel = $fuel + fuelRequirement($fuel);

    // Make sure to record this first so we don't have to calculate this again
    $fuel_table[$mass] = $required_fuel;

    return $required_fuel;
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
print('Answer to part 2: '.$total_fuel."\n");
