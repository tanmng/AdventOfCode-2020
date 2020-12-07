<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 07
 * Part 2
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
                // This must be a unit bag - bag that does not contain anything
                // else
                $rules[$origin] = [];
                continue 2;
            }
            $targets[] = [
                intval($parts[1]),
                $parts[2],
            ];
        }

        // Store this
        $rules[$origin] = $targets;
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

// print_r($rules);
// return;
// A simple DFS that we implement
// All the things that we store inside our shiny gold bag (and its content too)
$total_content = [];
// Fir we create a structure to store the result
foreach (array_keys($rules) as $bag_colour) {
    $total_content[$bag_colour] = 0;
}

// Help to avoid infinite recursion
$content_map = [];


/*
 * Return how many bags we contain inside given colour
 */
function addUp($colour) {
    global $content_map;
    global $rules;
    global $total_content;
    if (array_key_exists($colour, $content_map)) {
        // We already found this earlier
        return $content_map[$colour];
    }

    // Basic case - when it doesn't contain anything
    if (count($rules[$colour]) === 0) {
        return [];
    } else {
        // OK, there's something here
        $total = [];

        foreach ($rules[$colour] as $target_object) {
            // print_r($target_object);
            $quantity = $target_object[0];
            $target_colour = $target_object[1];
            $total[$target_colour] = $quantity;
        }
        foreach ($rules[$colour] as $target_object) {
            // print_r($target_object);
            $quantity = $target_object[0];
            $target_colour = $target_object[1];

            // Dig this
            $content_of_target_colour = addUp($target_colour);

            if (count($content_of_target_colour) === 0) {
                // nothing to do here
                continue;
            }

            // Scale this
            $final_content_of_target_colour = [];
            foreach ($content_of_target_colour as $child_colour => $target_quantity) {
                $final_content_of_target_colour[$child_colour] = $quantity * $target_quantity;
            }
            // Merge this in
            foreach (array_keys($total_content) as $child_colour) {
                // MAN THIS IS UGLY
                if (array_key_exists($child_colour, $total) && array_key_exists($child_colour, $final_content_of_target_colour)) {
                    // It's here
                    $total[$child_colour] += $final_content_of_target_colour[$child_colour];
                } else if (array_key_exists($child_colour, $total) && !array_key_exists($child_colour, $final_content_of_target_colour)) {
                    // Skip
                    continue;
                } else if (!array_key_exists($child_colour, $total) && array_key_exists($child_colour, $final_content_of_target_colour)) {
                    $total[$child_colour] = $final_content_of_target_colour[$child_colour];
                }
            }
        }

        // Complete
        $content_map[$colour] = $total;
        return $total;
    }
}

$results = addUp(TARGET_COLOUR);
print('Answer to part 2: '.array_sum(array_values($results))."\n");
