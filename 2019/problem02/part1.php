<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2019 problem 02
 * Part 1
 */

include('lib.php');

// Script constants
const INPUT_FILE = 'input.txt';

// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, "r");
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);
        $machine = new Machine($input);
        print('Begin state '.$machine."\n");

        // Modify the begin state to help with fixing the error
        $machine->set(1, 12);
        $machine->set(2, 2);

        $machine->runUntilHalt();
        print('End state '.$machine."\n");
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

print('Answer to part 1: '.$machine->output()."\n");
