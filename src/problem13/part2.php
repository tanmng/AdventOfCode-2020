<?php
/*
2* part2.php
 *
 * Main program for Advent calendar 2020 problem 13
 * Part 2
 */


// Script constants
const INPUT_FILE = 'input.txt';

$lines = [];
// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, 'r');
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);
        $lines[] = $input;
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

$buses = [];

foreach (explode(',', $lines[1]) as $index => $bus) {
    if (is_numeric($bus)) {
        $buses[] = [
            $index,
            intval($bus),
        ];
    }
}

/*
 * Poor man implementation of GCD
 */
function gcd(
    int $x,
    int $y
): int
{
    if ($y == 0)
        return $x;
    return gcd($y, $x%$y);
} 

$candidate = 100000000000000;
$increment = 1;
$counter += 1;
while (true) {
    $suitable = true;
    // print('Trying '.$candidate.', increment: '.$increment."\n");
    foreach ($buses as $bus) {
        if (($candidate + $bus[0]) % $bus[1] !== 0) {
            $suitable = false;
        } else {
            // This candidate satisfy the given bus -> we should modify the
            // increment accordingly
            $increment = ($increment * $bus[1]) / gcd($increment, $bus[1]);
        }
    }

    if ($suitable) {
        print('Answer for part 2: '.$candidate."\n");
        break;
    } else {
        $candidate += $increment;
    }

    $counter += 1;
    if ($counter === 10000000) {
        print('Failed'."\n");
        break;
    }
}

// print_r($buses);

