<?php
/*
 * all_in_one.php
 *
 * Main program for Advent calendar 2020 problem 19
 * Solving both parts at one
 */

ini_set('memory_limit', '2048M');

const INPUT_FILE = 'input.txt';
const TARGET_RULE = 0;

$raw_rules = [];
$images = [];
$section = 0;

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
            // empty line
            $section += 1;
        } else {
            switch ($section) {
                case 0:
                    $raw_rules[] = $input;
                    break;
                case 1:
                    $images[] = $input;
                    break;
            }
        }
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

// Construct our logical implementation of rule
$logical_rules = [];
$rules_requirement = []; // Mark what other rules one rule might require to build itself
$rule_regex = [];   // Regex of each rule (we use regex for testing if an image satisfy the rule)

foreach ($raw_rules as $raw_rule) {
    // Get the parts from it
    $parts = explode(': ', $raw_rule, 2);
    $rule_index = intval($parts[0]);
    $rule_body = $parts[1];

    if (preg_match('/^"[a-z]+"$/', $rule_body)) {
        // This rule is literal
        $logical_rules[$rule_index] = trim($rule_body, '"');

        $rule_regex[$rule_index] = trim($rule_body, '"');
    } else {
        // This is combination
        $parts_again = explode(' | ', $rule_body);
        $requirement_set = [];
        $distinct_requirements = [];
        foreach ($parts_again as $comb_part) {
            $cur_set = [];
            foreach (explode(' ', $comb_part) as $num) {
                $cur_set[] = intval($num);
                $distinct_requirements[] = intval($num);
            }
            $requirement_set[] = $cur_set;
        }

        // Record this
        $logical_rules[$rule_index] = $requirement_set;
        $rules_requirement[$rule_index] = array_unique($distinct_requirements);
    }
}

/* print_r($logical_rules); */
/* return; */
$all_rules_index = array_keys($logical_rules);
// print_r($rules_requirement);

$counter = 0;
while (!array_key_exists(TARGET_RULE, $rule_regex)) {
    $rules_found_so_far = array_keys($rule_regex);
    // While we still have not build the literal for our target rule yet
    // Go through the list of rules, find which one currently does not have
    // literal and all its condition satisfied, then build it
    foreach (array_diff($all_rules_index, $rules_found_so_far) as $index) {
        // Check if it has all the requirements
        if (count(array_diff($rules_requirement[$index], $rules_found_so_far)) > 0) {
            // We have not got all this requirements yet
            continue;
        }

        // We got everything we need
        $all_the_literals = [];
        foreach ($logical_rules[$index] as $group) {
            $dependent_in_group = [];
            foreach ($group as $dependent) {
                $dependent_in_group [] = $rule_regex[$dependent];
            }

            $all_the_literals[] = implode('', $dependent_in_group);
        }

        $regex = implode('|', $all_the_literals);
        $wrap_needed = count($all_the_literals) > 1;    // If we need to wrap this one regex
        //


        // Record this
        $rule_regex[$index] = implode('',[
            $wrap_needed? '(' : null,
            $regex,
            $wrap_needed? ')' : null,
        ]);
    }

    // print_r($rules_literal);

    $counter += 1;

    if ($counter === 1000000) {
        // Something is wrong
        print('Failed'."\n");
        return;
    }
}

/* print_r($rule_regex); */
/* return; */

// Answers to part 1 and 2
$ans_1 = 0;
$ans_2 = 0;
// Create the regex for part 2, note that this is manually deduced, since this
// does not have to be generic
$regex_rule0_part2 = '/^(?<one>('.$rule_regex[42].')+)(?<two>('.$rule_regex[31].')+)$/';
foreach ($images as $image) {
    // Part 1 - no cycles
    if (preg_match('/^'.$rule_regex[TARGET_RULE].'$/', $image)) {
        // Valid image for part 1
        $ans_1 += 1;
    }

    // Part 2 - cycles
    if (preg_match($regex_rule0_part2, $image, $parts)) {
        // A very cheap hack to ensure that the portion for rule 42 is longer
        // than the portion for rule 31,
        // It should have been count of occurence but that's boring
        if (strlen($parts['one']) > strlen($parts['two'])) {
            // Valid image for part 2
            $ans_2 += 1;
        }
    }
}

print('Answer to part 1: '.$ans_1."\n");
print('Answer to part 2: '.$ans_2."\n");

