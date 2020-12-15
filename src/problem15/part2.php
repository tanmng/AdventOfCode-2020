<?php
/*
 * part2.php
 *
 * Main program for Advent calendar 2020 problem 15
 * Part 2
 */

// More memory for all the memories
ini_set('memory_limit', '2048M');

const INPUT = '6,13,1,15,2,0';
const SAMPLE1 = '0,3,6';
const SAMPLE2 = '2,1,3';
const TARGET_TURN = 30000000;
const PROGRESS_INTERVAL = 1e6;

// Begin by splitting the input
$input_raw = INPUT;
$input_array = explode(',', $input_raw);

$turn_counter = 0;

// Memory - no need for this
// $memory = [];
$last_spoken = null;
$next_number = null;

// Record the last turn that we found given number
$positions = [];


foreach ($input_array as $val) {
    $value = intval($val);
    $last_spoken = $value;

    // Because this is new
    $positions[$value] = $turn_counter;

    $turn_counter += 1;

    // Determine the next number right from here
    $next_number = 0; // Always will be
}

while (true) {
    // Say the number we know we will say
    $last_spoken = $next_number;

    if (array_key_exists($last_spoken, $positions)) {
        // The number we just said appeared before (thos one), so the next number we will say will have to be the diff
        $next_number = $turn_counter - $positions[$last_spoken];
    } else {
        $next_number = 0;
    }
    $positions[$last_spoken] = $turn_counter;

    // Debug
    if (($turn_counter + 1) % PROGRESS_INTERVAL === 0) {
        print('Turn '.($turn_counter + 1).'/'.TARGET_TURN.': say '.$last_spoken."\n");
    }

    $turn_counter += 1;
    if ($turn_counter === TARGET_TURN) {
        // Complete
        break;
    }
}


print('Answer for part 2: '.$last_spoken."\n");
