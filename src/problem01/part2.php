<?php
/*
 * part2
 *
 * Part 2 of problem 01
 */

// Script constants
const INPUT_FILE = 'input.txt';
const REQUIRED_SUM = 2020;

// Hash map to record all input numbers
$all_input_numbers = [];

// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, "r");
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);
        if (!is_numeric($input)) {
            // Not a number on this line
            continue;
        }

        $number = intval($input);

        // Record this number
        $all_input_numbers[] = $number;
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
    fclose($handle);
}

/*
 * Class to represent a pair of number and the expected values that we should
 * see
 */
class InvalidNumberPairException extends Exception {}

class NumberPair
{
    function __construct(int $a, int $b) {
        // We don't know if this is true, but we will enforce that the pair
        // consists of different numbers
        if ($a === $b) {
            throw new InvalidNumberPairException('The 2 numbers are the same, '.$a.' and '.$b);
        }
        $this->a = $a;
        $this->b = $b;
        $this->sum = $a + $b;
        // The value that should complete this pair
        $this->expect = REQUIRED_SUM - $this->sum;

        // Since the array of numbers is always > 0, we need to ensure that
        // expect satisfy that requirement
        if ($this->expect <= 0) {
            throw new InvalidNumberPairException('The 2 numbers '.$a.' and '.$b.' sum is larger than '.REQUIRED_SUM);
        }

        // Ensure that the value we are expecting is not one of the pair
        if ($this->expect === $a || $this->expect === $b) {
            throw new InvalidNumberPairException('The expected value is actually one of the pair');
        }

        // Calculate the multiplication
        $this->mul = $a * $b * $this->expect;
    }
}

// Construct all the pair of numbers that we have from the input array
$number_pairs = [];
foreach ($all_input_numbers as $index => $number1) {
    for ($i = $index + 1; $i < count($all_input_numbers); $i++) {
        try {
            // Record this pair
            $this_pair = new NumberPair($number1, $all_input_numbers[$i]);

            // CHeck if the expected value from the pair is indeed from the
            // input
            // Note that we already vaidate everything when we construct the
            // pair
            if (in_array($this_pair->expect, $all_input_numbers)) {
                // Found this pair whose expect value is already in the input
                // So just print it out and exit
                print('Found a suitable set, submit the mul from the output below: '."\n");
                print_r($this_pair);
                break 2;
            }
        } catch (InvalidNumberPairException $e) {
            // Got an invalid pair of number
            continue;
        }
    }
}
