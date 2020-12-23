<?php
/*
 * part2.php
 *
 * Main program for Advent calendar 2020 problem 23
 * Part 2
 */

ini_set('memory_limit', '2048M');

const SAMPLE_STRING = '389125467';
const INPUT_STRING = '284573961';
const ABSOLUTE_MAX = 1e6;
// const ABSOLUTE_MAX = 9;
const START = 1;
const ROUND_LIMIT = 1e7;

$input = INPUT_STRING;

// Looking up index of a number is slow, so we need a better way
// We'll use a linked-list for this shit
$linked_list = [];

foreach (str_split($input) as $cup) {
    $cups[] = intval($cup);
}

$cup_min = min($cups);
$cup_max = max($cups);

foreach ($cups as $i => $cup) {
    if ($i < count($cups) - 1) {
        $linked_list[$cup] = $cups[$i + 1];
    } else {
        // Last one of the original cups
        $linked_list[$cup] = $cup_max + 1;
    }
}

// We need to fill in the list now
foreach (range($cup_max + 1, ABSOLUTE_MAX) as $cup) {
    if ($cup < ABSOLUTE_MAX) {
        // Not the end yet
        $linked_list[$cup] = $cup + 1;
    } else {
        // Absolute max -> it should point to the beginning of our orignal cups
        $linked_list[$cup] = $cups[0];
    }
}
$cup_max = ABSOLUTE_MAX;
// Sanity check
print('Count: '.count($linked_list). ' max '.$cup_max.' min '.$cup_min."\n");

// Start from the absolute beginning
$current_cup = $cups[0];

foreach (range(1, ROUND_LIMIT) as $round_index) {
    // Pick up the 3 values that will be removed from the list
    $picked_up = [];
    $pointer = $linked_list[$current_cup];
    foreach (range(1, 3) as $i) {
        // Record this number as picked up
        $picked_up[] = $pointer;

        // Travese the list
        $pointer = $linked_list[$pointer];
    }
    // Now pointer pointed at the next current cup

    // Select the next cup
    $dest_cup = $current_cup - 1;
    while (true) {
        if ($dest_cup < $cup_min) {
            // Out of range
            $dest_cup = $cup_max;
        }
        if (!in_array($dest_cup, $picked_up)) {
            // Dest cup is in the remaining
            break;
        }
        $dest_cup -= 1;
    }

    // We now have destination cup
    $next_to_destination = $linked_list[$dest_cup];

    // Relink
    $linked_list[$dest_cup] = $picked_up[0];    // First picked up value
    $linked_list[end($picked_up)] = $next_to_destination; // End of the picked up value
    $linked_list[$current_cup] = $pointer;

    // Update current cup
    $current_cup = $pointer;

    if ($round_index % 1e6 === 0) {
        print('Round '.$round_index.' picked up '.implode(',', $picked_up).'; destination cup '.$dest_cup."\n");
    }
}

// Form the result for part 2
$pointer = $linked_list[START];
$result = $pointer * $linked_list[$pointer];

print('Answer to part 2: '.$result."\n");
