<?php
/*
 * part2_dp.php
 *
 * Main program for Advent calendar 2020 problem 10
 * 
 * Here we use Dynamic programming instead of the magic as in part2.php
 */


// Script constants
const INPUT_FILE = 'input.txt';

// Array of adapters
$adapters = [];

// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, "r");
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);

        $current_number = intval($input);
        $adapters[] = $current_number;
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

// Add the 2 fake adapters - the begin and end
$adapters[] = 0;
$adapters[] = max($adapters) + 3;

// Sort the number
sort ($adapters);


// Use this cache to speed things up
$wayToTheEndCache = [];
/*
 * Function to return the number of ways we can reach the device while skipping
 * potentially skipable adapters
 *
 * Parameters
 *   $index -> the index in the chain of the adapter
 *Return:
 *   The number of ways
 */
function wayToTheEnd(
    int $index
): int
{
    global $adapters, $wayToTheEndCache;

    if ($index === count($adapters) - 1 || $index === count($adapters) - 2) {
        // We are either at the very end of the one right next to it
        // There's only 1 way to reach the end
        return 1;
    }

    if (array_key_exists($index, $wayToTheEndCache)) {
        return $wayToTheEndCache[$index];
    }

    // Different cases
    $total = 0;
    for ($candidate_index = $index + 1; $candidate_index < min($index + 4, count($adapters) - 1); $candidate_index++ ) {
        // Here candidate_index is the index of the POTENTIALLY next adapter that we will jump to
        // Note that $j cannot be more than i+4 (because then we will exceed the
        // joltage jump limit
        if ($adapters[$candidate_index] - $adapters[$index] <= 3) {
            // We can jump to J
            $total += wayToTheEnd($candidate_index);
        }
    }

    // Record this to reuse for later
    $wayToTheEndCache[$index] = $total;
    return $total;
}

// We start from index 0 - the wall
$solution = wayToTheEnd(0);
print('Answer to part 2: '.$solution."\n");
