<?php
/*
 * part2.php
 *
 * Main program for Advent calendar 2020 problem 19
 * Part 2
 */

ini_set('memory_limit', '2048M');

const INPUT_FILE = 'input2.txt';
const TARGET_RULE = 0;
const RULES_WITH_LOOP = [8, 11];

const EXCLUSION = [0, 8, 11];

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
$built_rules = [];  // Record which rule is now built
// Now build the rules literal
$rules_literal = [];


foreach ($raw_rules as $raw_rule) {
    // Get the parts from it
    $parts = explode(': ', $raw_rule);
    $rule_index = intval($parts[0]);
    $rule_body = $parts[1];

    if (preg_match('/^"[a-z]+"$/', $rule_body)) {
        // This rule is literal
        $logical_rules[$rule_index] = trim($rule_body, '"');
        $built_rules[] = $rule_index;

        $rules_literal[$rule_index] = [trim($rule_body, '"')];
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

/*
 * Return all the possible strings created by combining 2 arrays together
 */
function array_concat(
    array $one,
    array $two
): array
{
    $result = [];
    foreach ($one as $one_element) {
        foreach ($two as $two_element) {
            $result[] = $one_element.$two_element;
        }
    }

    return $result;
}

/*
 * Follow similar approach as problem 1, we just need to rebuild all literal
 * until we need to build 0, 8 and 11
 *
 * We can then construct 8 and 11 as regex and 0 as combination of them
 *
 * Simple
 */

// print_r($logical_rules);
$all_rules_index = array_keys($logical_rules);
// print_r($rules_requirement);

$counter = 0;
while (count($rules_literal) < count($all_rules_index) - count(EXCLUSION)) {
    $rules_found_so_far = array_keys($rules_literal);
    // While we still have not build the literal for our target rule yet
    // Go through the list of rules, find which one currently does not have
    // literal and all its condition satisfied, then build it
    foreach (array_diff($all_rules_index, $rules_found_so_far) as $index) {
        if (in_array($index, EXCLUSION)) {
            // No need to build these
            continue;
        }
        // Check if it has all the requirements
        if (count(array_diff($rules_requirement[$index], $rules_found_so_far)) > 0) {
            // We have not got all this requirements yet
            continue;
        }

        // We got everything we need
        $all_the_literals = [];
        foreach ($logical_rules[$index] as $group) {
            $result = [];
            foreach ($group as $i => $dependent) {
                if ($i === 0) {
                    $result = $rules_literal[$dependent];
                } else {
                    // Combine this
                    $result = array_concat($result, $rules_literal[$dependent]);
                }
            }

            $all_the_literals = array_merge($all_the_literals, $result);
        }

        // Record this
        $rules_literal[$index] = $all_the_literals;
    }

    // print_r($rules_literal);

    $counter += 1;

    if ($counter === 1000000) {
        // Something is wrong
        print('Failed'."\n");
        return;
    }
}

/* print_r($rules_literal[42]); */
/* print_r($rules_literal[31]); */
/* print_r(array_intersect($rules_literal[42], $rules_literal[31])); */

/*
 * Return whether an image satisfy rule 0
 */
function validate(
    string $image
)
{
    global $rules_literal;
    // MAGIC
    $global_regex = '/^(('.implode('|', $rules_literal[42]).')+)(('.implode('|', $rules_literal[31]).')+)$/';

    $status = preg_match_all($global_regex, $image, $data);
    if ($status == 0) {
        // No match
        return false;
    } else {
        // Care for the number
        $rule42_portion = $data[1];
        $rule31_portion = $data[3];

        // A very cheap hack =))
        return strlen($rule42_portion[0]) > strlen($rule31_portion[0]);
        // print_r($data);
        // return true;
    }

    // return false;
}


$ans = 0;
foreach ($images as $image) {
    if (validate($image)) {
        // Valid image
        $ans += 1;
    }
}

print('Answer to part 2: '.$ans."\n");
