<?php
/*
 * all_in_one.php
 *
 * Main program for Advent calendar 2020 problem 16
 * Both parts
 */

// Script constants
const INPUT_FILE = 'input.txt';

$field_conditions = [];
$my_ticket = null;
$nearby_tickets = [];

$section_index = 0;

// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, "r");
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);
        if (strlen($input) === 0) {
            // Empty line
            // Skip
            continue;
        }
        if ($input === 'your ticket:' || $input === 'nearby tickets:') {
            // Not very interesting
            // New section
            $section_index += 1;
            continue;
        }
        if ($section_index === 0) {
            // This is the field conditions
            preg_match('/^([^:]+): ([0-9]+)-([0-9]+) or ([0-9]+)-([0-9]+)$/', $input, $parts);
            $field_conditions[] = [
                'name'  => $parts[1],
                'ranges'  => [
                    [ intval($parts[2]), intval($parts[3]) ],
                    [ intval($parts[4]), intval($parts[5]) ],
                ],
            ];
            continue;
        }
        if ($section_index === 1) {
            // My ticket
            $my_ticket = [];
            foreach (explode(',', $input) as $part) {
                $my_ticket[] = intval($part);
            }
            continue;
        }
        if ($section_index === 2) {
            // Near by tickets
            $current_ticket = [];
            foreach (explode(',', $input) as $part) {
                $current_ticket[] = intval($part);
            }
            // Save this
            $nearby_tickets[] = $current_ticket;
        }
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}
// These to help speed things up
$condition_counts = count($field_conditions);
$field_count = count($my_ticket);

/*
 * check if a value satisfy given condition
 */
function validate_field(
    array $condition,
    int $value
): bool
{
    $lower0 = $condition['ranges'][0][0];
    $upper0 = $condition['ranges'][0][1];
    $lower1 = $condition['ranges'][1][0];
    $upper1 = $condition['ranges'][1][1];

    if (($value >= $lower0 && $value <= $upper0) || ($value >= $lower1 && $value <= $upper1)) {
        return true;
    } else {
        return false;
    }
}

$valid_nearby = [];

// print_r($field_conditions);
// print_r($nearby_tickets);

// Find the invalid tickets 
$sum = 0;
foreach ($nearby_tickets as $nearby_ticket) {
    $has_invalid_field = false;
    foreach ($nearby_ticket as $nearby_value) {
        // Check if this value is valid in any of the conditions
        $invalid_all_fields = true;
        foreach ($field_conditions as $condition) {
            if (validate_field($condition, $nearby_value)) {
                // This value is valie
                $invalid_all_fields = false;
                continue;
            }
        }
        if ($invalid_all_fields) {
            // print($nearby_value."\n");
            $sum += $nearby_value;
            $has_invalid_field = true;
        }
    }
    if (!$has_invalid_field) {
        //  This ticket does NOT have invalid field
        $valid_nearby[] = $nearby_ticket;
    }
}
print('Answer to part 1: '.$sum."\n");

// Part 2
// Construct the list of candidates (for the condition) of each field
$condition_candidates = [];
foreach (range(0, $field_count - 1) as $i) {
    $condition_candidates[] = range(0, $condition_counts - 1);
}

// Go through the valid nearby ticket and check if the fields are valid
foreach ($valid_nearby as $nearby_ticket) {
    foreach ($nearby_ticket as $index => $value) {
        // Fetch the list of candidate
        $potential_conditions = $condition_candidates[$index];
        $to_remove = [];
        foreach ($potential_conditions as $condition_index) {
            // Fetch the condition
            $this_condition = $field_conditions[$condition_index];

            if (!validate_field($this_condition, $value)) {
                // This condition is not for this field anymore
                $to_remove[] = $condition_index;
                continue;
            }
        }
        $condition_candidates[$index] = array_values(array_diff($potential_conditions, $to_remove));
    }
}

// Debug
// print_r($condition_candidates);

$counter = 0;
// Apply deduction to find things out
while (true) {
    $no_change = true;
    $fixed_value = [];
    foreach ($condition_candidates as $field_index => $conditions) {
        if (count($conditions) === 1) {
            // This condition must be for this field
            // We will remove this from all the others
            $fixed_value[] = $conditions[0];
        } else {
            // We must modify anything that doesn't have 1 conditions
            $no_change = false;
        }
    }

    // Loop again to remove things properly
    foreach ($condition_candidates as $field_index => $conditions) {
        if (count($conditions) > 1) {
            // We must modify anything that doesn't have 1 conditions
            $condition_candidates[$field_index] = array_values(array_diff($conditions, $fixed_value));
        }
    }

    if ($no_change) {
        break;
    }
    $counter += 1;
    if ($counter === 50) {
        print('Loop too much');
        return;
    }
}

// Complete everything, each field should correspond to only 1 condition
$ans = 1;
foreach (range(0, $field_count - 1) as $field_index) {
    // Check the name
    $field_condition = $field_conditions[end($condition_candidates[$field_index])];
    if (preg_match('/^departure .*$/', $field_condition['name'])) {
        // This is a departure field
        $ans *= $my_ticket[$field_index];
    }
}

// print_r($condition_candidates);
// print_r(count($valid_nearby));
// print_r(count($nearby_tickets));

print('Answer to part 2: '.$ans."\n");
