<?php
/*
 * part2.php
 *
 * Main program for Advent calendar 2019 problem 02
 * Part 2
 */

include('lib.php');

// Script constants
const INPUT_FILE = 'input.txt';
const TARGET_VALUE = 19690720;

const NOUN_MIN = 0;
const NOUN_MAX = 99;
const VERB_MIN = 0;
const VERB_MAX = 99;

// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, "r");
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}
fclose($handle);

// Signify if we have found an appropriate combination
$found = false;

// Loop through all possible options for noun and verb
foreach (range(NOUN_MIN, NOUN_MAX) as $noun) {
    foreach (range(VERB_MIN, VERB_MAX) as $verb) {
        $machine = new Machine($input);

        $machine->set(1, $noun);
        $machine->set(2, $verb);

        $machine->runUntilHalt();

        if ($machine->output() == TARGET_VALUE) {
            // We found a suitable combination of noun and vert
            print('Found the answer');
            $found = true;
            break 2;
        }
    }
}

if ($found) {
    print('Answer to part 2: '.(100 * $noun + $verb)."\n");
} else {
    print('Coud NOT find any appropriate combination'."\n");
}
