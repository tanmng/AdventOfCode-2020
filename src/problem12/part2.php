<?php
/*
 * part2.php
 *
 * Main program for Advent calendar 2020 problem 12
 * Part 2
 */


// Script constants
const INPUT_FILE = 'input.txt';

$instructions = [];
// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, 'r');
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);
        $instructions[] = $input;
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

$x = 0;
$y = 0;
$increment = [10, -1];   // 10, east; 1 north

foreach ($instructions as $instruction) {
    preg_match('/^([NSEWLRF])([0-9]+)+$/', $instruction, $parts);
    $command = $parts[1];
    $value = intval($parts[2]);
    switch ($command) {
    case 'F':
        $x += $value * $increment[0];
        $y += $value * $increment[1];
        break;
    case 'N':
        $increment[1] -= $value;
        break;
    case 'E':
        $increment[0] += $value;
        break;
    case 'S':
        $increment[1] += $value;
        break;
    case 'W':
        $increment[0] -= $value;
        break;
    case 'R':
        $square = ($value / 90) % 4;
        if ($square === 0)
            continue 2;
        $temp_increment = $increment;
        foreach (range(0, $square - 1) as $i) {
            $increment = [-$temp_increment[1], $temp_increment[0]];
            $temp_increment = $increment;
        }
        break;
    case 'L':
        $square = ($value / 90) % 4;
        if ($square === 0)
            continue 2;
        $temp_increment = $increment;
        foreach (range(0, $square - 1) as $i) {
            $increment = [$temp_increment[1], -$temp_increment[0]];
            $temp_increment = $increment;
        }
        break;
    }

    // print($instruction.': '.$command.'-'.$value.': '.$x.', '.$y.', increment: ('.implode(', ', $increment).")\n");
}

print('Answer: '.abs($x) + abs($y)."\n");
