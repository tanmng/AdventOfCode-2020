<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 21
 * Part 1
 */


const INPUT_FILE = 'input.txt';

// Construct our data
// $food = [];
$ingredient_allergent = [];
$all_ingredients = [];
$all_allergents = [];
$ingredient_listing = [];

// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, "r");
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);

        // Split this thing
        preg_match('/^([^(]+) \(contains (.*)\)$/', $input, $parts);
        // print_r($parts);
        // $food_name = trim($parts[1]);
        $ingredient_string = trim($parts[1]);
        $allergent_string= trim($parts[2]);
        $ingredients = explode(' ', $ingredient_string);
        $allergents = explode(', ', $allergent_string);

        $ingredient_listing = array_merge($ingredient_listing, $ingredients);
        $all_ingredients = array_unique(array_merge($all_ingredients, $ingredients));
        $all_allergents = array_unique(array_merge($all_allergents, $allergents));

        $ingredient_allergent[] = [
            'i' => $ingredients,
            'a' => $allergents,
        ];
        /* $food[$food_name] = [ */
        /*     'i' => $ingredients, */
        /*     'a' => $allergents, */
        /* ]; */
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

// print_r($ingredient_allergent);

// Mapping from ingredient to the allergent they absolutely contain, note that
// this is not exhaustive (ie. not all alergent of an ingredent is in here)
$absolutely_contain = [];

// Some debugging
/* foreach ($ingredient_allergent as $pair) { */
/*     print('Ingredients '.count($pair['i']).' - '.count($pair['a']).' allergents'."\n"); */
/* } */

// Build a better data
$i_to_a = [];
$i_to_indices = [];
$i_to_not_a = [];  // Mapping from an ingredient to all the allergents that it cannot contain
$a_to_i = [];
$a_to_indices = [];
foreach ($all_ingredients as $ingredient) {
    $i_to_a[$ingredient] = [];
    $i_to_indices[$ingredient] = [];
    $i_to_not_a[$ingredient] = [];
}
foreach ($all_allergents as $allergent) {
    $a_to_i[$allergent] = [];
    $a_to_indices[$allergent] = [];
}

foreach ($ingredient_allergent as $index => $pair) {
    foreach ($pair['i'] as $ingredient) {
        $i_to_a[$ingredient] = array_unique(array_merge($i_to_a[$ingredient], $pair['a']));
        $i_to_indices[$ingredient][] = $index;
    }
    foreach ($pair['a'] as $allergent) {
        $a_to_i[$allergent] = array_unique(array_merge($a_to_i[$allergent], $pair['i']));
        $a_to_indices[$allergent][] = $index;
    }

    // Here's the deduction for part 1
    // If an ingredient does not appear in here, it cannot contain any of the
    // allergent in here, because another ingredient (which is in this pair)
    // must have contain it, and an allergent is only in 1 ingredient
    foreach (array_diff($all_ingredients, $pair['i']) as $ingredient) {
        $i_to_not_a[$ingredient] = array_unique(array_merge($i_to_not_a[$ingredient], $pair['a']));
    }
}

// print("Ingredients to allergents:\n");
// print_r($i_to_a);
// print("Allergent to ingredeients:\n");
// print_r($a_to_i);
// print("Ingredients to not:\n");
// print_r($i_to_not_a);
$ingredient_frequency = array_count_values($ingredient_listing);
$result = 0;
$inert_ingredients = [];
foreach ($all_ingredients as $ingredient) {
    if (count($i_to_not_a[$ingredient]) === count($all_allergents)) {
        // This ingredient does not contain any of the listed allergent
        $result += $ingredient_frequency[$ingredient];

        // mark that this one is inert
        $inert_ingredients[] = $ingredient;
    }
}
print('Answer to part 1: '.$result."\n");
// print_r($inert_ingredients);

// Re-constrcutred a to i since we no longer care about the inert one
foreach ($all_allergents as $allergent) {
    $a_to_i[$allergent] = array_diff($a_to_i[$allergent], $inert_ingredients);
}

// Use the data from part 1 to filter out a to i even more
foreach ($all_ingredients as $ingredient) {
    if (in_array($ingredient, $inert_ingredients)) {
        // NO need to take are of this
        continue;
    }

    foreach ($i_to_not_a[$ingredient] as $allergent) {
        $a_to_i[$allergent] = array_diff($a_to_i[$allergent], [$ingredient]);
    }
}

// Somehow the key is all mixed up and I'm too lazy to fix it
/* foreach ($a_to_i as $i => $set_a) { */
/*     $a_to_i[$i] = array_values($set_a); */
/* } */

/* print("Allergent to ingredient:\n"); */
/* print_r($a_to_i); */


// Now, apply deduction in part 2
$counter = 0;
$already_found = [];// The list of ingredient we already found what allergent they contain
while (true) {
    $all_is_solved = true;
    foreach ($a_to_i as $allergent => $set_ingredient) {
        if (count($set_ingredient) === 1) {
            // This one ingredient must be it
            $already_found[end($set_ingredient)] = $allergent;
        } else {
            // There are allergent that we still have not finished deduction
            // with
            $all_is_solved = false;
        }
    }

    if ($all_is_solved) {
        // Break from here
        break;
    }

    // Now, apply the set of already_found to deduce our case
    foreach ($a_to_i as $allergent => $set_ingredient) {
        if (count($set_ingredient) > 1) {
            // This allergent have more than 1 candidates for ingredients that
            // might contain it
            $a_to_i[$allergent] = array_diff($set_ingredient, array_keys($already_found));
        }
    }

    $counter += 1;
    if ($counter === 20) {
        // Something is very wrong
        print('Something is wrong, deduction does not work'."\n");
        return;
    }
}

// Sort this the map of a to i using key - the allergent
ksort($a_to_i);

print("Allergent to ingredient:\n");
print_r($a_to_i);

$answer_to_part2 = [];
foreach ($a_to_i as $allergent => $set_ingredient) {
    $answer_to_part2[] = end($set_ingredient);
}

print('Answer to part 2: '.implode(',',$answer_to_part2)."\n");
