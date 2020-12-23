<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 23
 * Part 1
 */

ini_set('memory_limit', '2048M');

const SAMPLE_STRING = '389125467';
const INPUT_STRING = '284573961';
// const ABSOLUTE_MAX = 1e6;
const ABSOLUTE_MAX = 9;
const START = 1;
const ROUND_LIMIT = 100;

$input = INPUT_STRING;

// Looking up index of a number is slow, so we need a better way
// We'll use a linked-list for this shit
$linked_list = [];

foreach (str_split($input) as $cup) {
    $cups[] = intval($cup);
}

$cup_min = min($cups);
$cup_max = max($cups);

// Form the linked list
foreach ($cups as $i => $cup) {
    $linked_list[$cup] = $cups[($i + 1) % count($cups)];
}

/*
 * Help with debug
 */
function print_list(
    $linked_list,
    int $start_value
): string
{
    $result = $start_value;
    $pointer = $linked_list[$start_value];
    while ($pointer != $start_value) {
        $result .= $pointer;
        $pointer = $linked_list[$pointer];
    }

    return $result;
}

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

    print('Round '.str_pad($round_index, 4, '    ', STR_PAD_LEFT).', destination cup '.$dest_cup.', become: '.print_list($linked_list, $current_cup)."\n");
}

// Traverse the linked list to form our result
$result = '';
$pointer = $linked_list[START];

while ($pointer != START) {
    $result .= $pointer;
    $pointer = $linked_list[$pointer];
}
print('Answer to part 1: '.$result."\n");
