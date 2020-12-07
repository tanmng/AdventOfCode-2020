<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 07
 * Part 1
 */


// Script constants
const INPUT_FILE = 'input.txt';
const TARGET_COLOUR = 'shiny gold';

// Array of rules
$rules = [];

// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, "r");
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);

        // Try to split intput to create rule
        preg_match('/^(.*) bags contain (.*)\.$/', $input, $input_parts);

        if (count($input_parts) < 3) {
            // Not enough data in here
            // Skip
            continue;
        }

        // Origin - The bag that contain
        $origin = $input_parts[1];
        $destinations = $input_parts[2];

        $targets = [];
        foreach (explode(', ', $destinations) as $destination) {
            // Filter out the destination colour
            preg_match('/^([0-9]+) (.*) bags?$/', $destination, $parts);
            if (count($parts) < 3) {
                // NOt enough
                continue 2;
            }
            // Skip the number for now
            $targets[] = $parts[2];
        }

        // Store this
        $rules[$origin] = $targets;
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

print_r($rules);
// A simple BFS that we implement
$results = [];
// All the intermediary that can lead to our target colour
$potential = [TARGET_COLOUR];

while (true) {
    $found_new = false;
    foreach ($rules as $outer => $content) {
        // print_r($content);
        if (count(array_intersect($content, $potential))) {
            // This outer can contain what we need
            if (!in_array($outer, $results)) {
                $results[] = $outer;
                $potential[] = $outer;
                $found_new = true;
            }
        }
    }
    // print_r($potential);

    if (!$found_new) {
        // We didn't find anything new
        break;
    }
}

print_r($results);
print('Answer to part 1: '.count($results)."\n");
