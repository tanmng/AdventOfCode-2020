<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2019 problem 04
 * Part 1
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

    $has_a_pair = false;    // Whether there's 2 adjacent digits that are the same

    for ($index = 0; $index < count($password_characters) - 1; $index++) {
        $cur_digit = intval($password_characters[$index]);
        $next_digit = intval($password_characters[$index + 1]);

        if ($next_digit < $cur_digit) {
            // The digit just decreased
            return false;
        }

        if ($next_digit === $cur_digit) {
            // We found a pair of adjacent digits to be the same
            $has_a_pair = true;
        }
    }
    // All the digit are increasing

    // If the password has a pair of adjacent digits that are the same then it's
    // valid
    return $has_a_pair;
}

$valid_passwords = []; // Record all the valid one
foreach (range(INPUT_MIN, INPUT_MAX) as $potential_password) {
    if (isValidPassword($potential_password)) {
        // Record this one
        $valid_passwords[] = $potential_password;
    }
}

print('Answer to part 1: '.count($valid_passwords)."\n");
