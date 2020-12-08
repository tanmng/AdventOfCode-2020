<?php
/*
 * part2.php
 *
 * Main program for Advent calendar 2020 problem 08
 * Part 2
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

/*
 * Function to try and change one instruction and check if we can execute the
 * whole thing
 *
 * Return if this is the right case, and if so, the value of accumulator
 */
function trial(
    int $instruction_index
): array
{
    global $instructions;

    // Check if the instruction from the index is a jmp or noop (we can only
    // change those)
    if (!preg_match('/^(nop|jmp).*$/', $instructions[$instruction_index])) {
        // Not a suitable candidate for execution
        return [ false, null ];
    }

    // Execute the program
    $accumulator = 0;
    $program_pointer = 0;

    // Mark which instructions was already executed
    $executed = [];


    while (true) {
        if ($program_pointer >= count($instructions)) {
            // We reached a location outside of the instruction bootrom
            return [ true, $accumulator ];
        }
        // Execute everything
        $current_instruction = $instructions[$program_pointer];

        if (in_array($program_pointer, $executed)) {
            // We executed this command earlier
            // Since our boot program is stateless, this must mean an infinite
            // loop
            return [ false, null ];
        }

        // print($instruction_index.' - '.$program_pointer.': '.$current_instruction."\n");

        // Mark this is executed
        $executed[] = $program_pointer;

        if (preg_match('/^nop ([+-][0-9]+)$/', $current_instruction, $parts)) {
            // No op - progress by one
            if ($program_pointer == $instruction_index) {
                // We are supposed to try and change this one to a jmp
                $program_pointer += intval($parts[1]);
            } else {
                // Behave normally
                $program_pointer += 1;
            }
        } elseif (preg_match('/^acc ([+-][0-9]+)$/', $current_instruction, $parts)) {
            // Accumulation
            $program_pointer += 1;
            $accumulator += intval($parts[1]);
        } elseif (preg_match('/^jmp ([+-][0-9]+)$/', $current_instruction, $parts)) {
            // Jump
            if ($program_pointer == $instruction_index) {
                // We are supposed to try and change this one to a noop
                $program_pointer += 1;
            } else {
                // Behave normally
                $program_pointer += intval($parts[1]);
            }
        }
    }
}

foreach (range(0, count($instructions) - 1) as $candidate) {
    // Loop through and find a suitable candidate for changing
    $result = trial($candidate);

    if ($result[0] === true) {
        // Found a suitable one
        print('Answer to part 2: '.$result[1].' (change instruction number '.$candidate.") \n");
    }
}
