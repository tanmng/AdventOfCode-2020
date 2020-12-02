<?php
/*
 * part2.php
 *
 * Main program for Advent calendar 2019 problem 04
 * Part 2
 */

// Script constants
// Range of input
const INPUT_MIN = 284639;
const INPUT_MAX = 748759;

/*
 * Return whether a number is a valid password
 */
function isValidPassword(
    int $password
): bool
{
    $password_characters = str_split($password);

    if (count($password_characters) !== 6) {
        // Password must be 6 digit long
        return false;
    }

    $digits_with_pair = [];    // Array of all the digits whose the digit immediately after is the same as it
    // If this list has any duplicate then we know that the repeating digit was
    // longer than 2
    //
    // Note that we don't have to worry about cases such as aabbaa since that
    // will fail the increasing digit rule

    for ($index = 0; $index < count($password_characters) - 1; $index++) {
        $cur_digit = intval($password_characters[$index]);
        $next_digit = intval($password_characters[$index + 1]);

        if ($next_digit < $cur_digit) {
            // The digit just decreased
            return false;
        }

        if ($next_digit === $cur_digit) {
            // We found a pair of adjacent digits to be the same
            $digits_with_pair[] = $cur_digit;
        }
    }
    // All the digit are increasing

    // We just now need a pair of digits (and exactly a pair) to the the same
    $repeating_digit_frequency = array_count_values ($digits_with_pair);
    return (in_array(1, array_values($repeating_digit_frequency)));
}

$valid_passwords = []; // Record all the valid one
foreach (range(INPUT_MIN, INPUT_MAX) as $potential_password) {
    if (isValidPassword($potential_password)) {
        // Record this one
        $valid_passwords[] = $potential_password;
    }
}

print('Answer to part 2: '.count($valid_passwords)."\n");
