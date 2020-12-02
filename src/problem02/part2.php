<?php
/*
 * part2.php
 *
 * Main program for Advent calendar 2020 problem 02
 * Part 2
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
            // string, then the first index, then the second index  and lastly
            // the character
            throw new InvalidPolicyString('Invalid policy string '.$policy_string.' match result was '.print_r($matches, true));
        }

        // Store the settings of the policy
        $this->index1 = intval($matches[1]);
        $this->index2 = intval($matches[2]);
        $this->char = $matches[3];

        // Neither of the index can be 0 - since the policy doesn't use index 0
        if ($this->index1 === 0 || $this->index2 === 0) {
            throw new InvalidPolicyString('One of the index is zero '.$this->index1.' or '.$this->index2.' '.$policy_string);
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
        // Note that we add the first character to be 0 so that in this array
        // the relevant characters start at index 0
        $password_characters = str_split('0'.$password);

        // Get the character at the index location
        // Make sure to check the array limit first
        $char_at_index1 = ($this->index1 < count($password_characters))? $password_characters[$this->index1] : null;
        $char_at_index2 = ($this->index2 < count($password_characters))? $password_characters[$this->index2] : null;

        // Return true in only one of these 2 cases
        // When exactly one of the char we found match what we expect in the
        // policy
        return ($char_at_index1 === $this->char && $char_at_index2 !== $this->char) || ($char_at_index2 === $this->char && $char_at_index1 !== $this->char);
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

// print_r($valid_password_entries);
print('Answer to part 2: '.count($valid_password_entries)."\n");
