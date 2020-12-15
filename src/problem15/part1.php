<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 15
 * Part 1
 */


const INPUT = '6,13,1,15,2,0';
const SAMPLE1 = '0,3,6';
const SAMPLE2 = '2,1,3';
const TARGET_TURN = 2020;

// Begin by splitting the input
$input_raw = INPUT;
$input_array = explode(',', $input_raw);

$turn_counter = 0;

// Memory
$memory = [];
$memory_frequency = [];

while (true) {
    // Logic of the gam
    if ($turn_counter < count($input_array)) {
        // Still init phase
        $memory[] = intval($input_array[$turn_counter]);
    } else {
        // Already in the game
        $last_spoken = $memory[$turn_counter - 1];

        // Check if that number has been spoken twice before
        if (array_key_exists($last_spoken, $memory_frequency) && $memory_frequency[$last_spoken] > 1) {
            // This has been spoken more than 1 (including the last time it was
            // spoken)
            // Find the last time (excluding the right before turn)
            for ($i = $turn_counter - 2; $i >= 0; $i--) {
                if ($memory[$i] === $last_spoken) {
                    // Found it
                    break;
                }
            }
            $memory[] = $turn_counter - 1 - $i;
        } else {
            // Not seen before -> say zero
            $memory[] = 0;
        }
    }
    $memory_frequency = array_count_values($memory);

    // print_r($memory);

    // Debug
    // print('Turn '.($turn_counter + 1).': say '.$memory[$turn_counter]."\n");

    $turn_counter += 1;
    if ($turn_counter === TARGET_TURN) {
        // Complete
        break;
    }
}


print('Answer for part 1: '.$memory[$turn_counter - 1]."\n");
