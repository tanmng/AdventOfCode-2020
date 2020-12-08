<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 08
 * Part 1
 */


// Script constants
const INPUT_FILE = 'input.txt';

$instructions = [];

// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, "r");
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);

        $instructions[] = $input;
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

// Execute the program
$accumulator = 0;
$program_pointer = 0;

// Mark which instructions was already executed
$executed = [];

while (true) {
    // Execute everything
    $current_instruction = $instructions[$program_pointer];

    if (in_array($program_pointer, $executed)) {
        // We executed this earlier
        break;
    }

    // Mark this is executed
    $executed[] = $program_pointer;

    if (preg_match('/^nop.*$/', $current_instruction)) {
        // No op - progress by one
        $program_pointer += 1;
    } elseif (preg_match('/^acc ([+-][0-9]+)$/', $current_instruction, $parts)) {
        // Accumulation
        $program_pointer += 1;
        $accumulator += intval($parts[1]);
    } elseif (preg_match('/^jmp ([+-][0-9]+)$/', $current_instruction, $parts)) {
        // Jump
        $program_pointer += intval($parts[1]);
    }
}


print('Answer to part 1: '.$accumulator."\n");
