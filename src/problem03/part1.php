<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 03
 * Part 1
 */

// Script constants
const INPUT_FILE = 'input.txt';
include('lib.php');

$counter = 0;
// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, "r");
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);

        if ($counter == 0) {
            $field = new Field($input);
        } else {
            $field->addLine($input);
        }
        $counter += 1;
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

// Return the field
// print_r($field);

// Move through this field
$input_movement = new D2Vector(3, 1);

// Keep moving until we reached the end, keep track of all the tress
$tree_counter = 0;
$cur_place = new D2Vector(0, 0);
while (true) {
    // Keep moving
    try {
        $next_place = $field->move($cur_place, $input_movement);
        $cur_place = $next_place;

        if ($field->isTree($cur_place)) {
            $tree_counter += 1;
        }
    } catch (ReachTheEndException $e) {
        // We reached the end
        break;
    }
}

print('Answer to part 1: '.$tree_counter."\n");
