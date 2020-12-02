<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 02
 * Part 1
 */

// Script constants
const INPUT_FILE = 'input.txt';
const ENTRY_DELIMITER = ': ';   // Delimiter between the policy string and the password

class InvalidPolicyString extends Exception {}
/*
 * A class representing a policy
 */
class Policy {
    public function __construct(
        string $policy_string
    )
    {
        // Match the string to parts
        preg_match('/^([0-9]+)-([0-9]+) ([a-z])$/', $policy_string, $matches);

        if (count($matches) != 4) {
            // We're supposed to get 4 parts out of the input, first the whole
            // string, then the lower limit, then the upper and lastly the
            // character
            throw new InvalidPolicyString('Invalid policy string '.$policy_string.' match result was '.print_r($matches, true));
        }

        // Store the settings of the policy
        $this->lower_bound = intval($matches[1]);
        $this->upper_bound = intval($matches[2]);
        $this->char = $matches[3];

        // Make sure that upper limit is greater than lower limit as well
        if ($this->upper_bound < $this->lower_bound) {
            throw new InvalidPolicyString('Upper limit '.$this->upper_bound.' is less than lower one '.$this->lower_bound);
        }
    }

    /*
     * Return whether the password (parameter) is valid according to the policy
     */
    public function validate(
        string $password
    ): bool
    {
        // Create the frequency map of each character in the password
        $password_character_frequencies = array_count_values(str_split($password));

        // Check the frequency of the character we're supposed to match
        if (!array_key_exists($this->char, $password_character_frequencies)) {
            // The character didn't appear at all
            if ($this->lower_bound <= 0) {
                //  The character can appear 0 time
                return true;
            } else {
                // This character is supposed to appear more than 0 time
                return false;
            }
        } else {
            // Validate the the frequency is in the range
            $frequency = $password_character_frequencies[$this->char];

            return ($frequency >= $this->lower_bound) && ($frequency <= $this->upper_bound);
        }

    }
}

// Array of all the valid passwords
$valid_password_entries = [];

// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, "r");
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);

        // Break the input into parts for policy and the password itself
        $parts = explode(ENTRY_DELIMITER, $input);
        $policy = new Policy($parts[0]);
        $password = $parts[1];

        if ($policy->validate($password)) {
            // This password in this line is valid according to the policy in it
            $valid_password_entries[] = $input;
        }
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

//print_r($valid_password_entries);
print('Answer to part 1: '.count($valid_password_entries)."\n");
