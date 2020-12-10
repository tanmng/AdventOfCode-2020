<?php
/*
 * part2.php
 *
 * Main program for Advent calendar 2020 problem 10
 * Part 2
 */


// Script constants
const INPUT_FILE = 'sample.txt';

// Array of adapters
$adapters = [];

// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, "r");
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);

        $current_number = intval($input);
        $adapters[] = $current_number;
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

// Sort the number
sort ($adapters);
// print_r($adapters);

// Split the  list of adapters to islands whose distance to previous and next
// one is 3 (careful of the first one)
$adapter_groups = [];
$current_group = [];
$max_group_length = 0;
for ($index = 0; $index < count($adapters); $index++) {
    // $diff_backward = $index == 0? $adapters[$index] : $adapters[$index] - $adapters[$index - 1];
    $diff_forward = $index === count($adapters) - 1? 3 : $adapters[$index + 1] - $adapters[$index];
    $current_group[] = $adapters[$index];

    if ($diff_forward >= 3 || $index === count($adapters) - 1) {
        // We begin a new group
        if (count($current_group)) {
            $adapter_groups[] = $current_group;
            $max_group_length = max($max_group_length, count($current_group));
        }
        $current_group = [];
    }
}
// Note that within the group we have only diff of 1
// And our group is 5 at the most
//
// Note that this value is not the same for all input
// Make sure you understand what you're doing
$ans = 7;

// Except for the first group, everything else follow the simple rule
// You can remove at most the content (excluding the begin and end)
foreach ($adapter_groups as $index => $current_group) {
    if ($index === 0) {
        // Skip the first group
        continue;
    }
    // MAGIC
    // MUAHAHAHAHAHAHAH
    switch (count($current_group)) {
    case 5:
        // Group of 5
        $ans *= 7;
        break;
    case 4:
        $ans *= 4;
        break;
    case 3:
        $ans *= 2;
        break;
    }
}
// print_r($adapter_groups[0]);
print('Answer to part 2: '.$ans."\n");
