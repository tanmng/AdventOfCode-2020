<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 12
 * Part 1
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

// print_r($instructions);

$x = 0;
$y = 0;
$heading_index = 0;   // east
$headings = [
    [1, 0], // East
    [0, 1], // South
    [-1, 0], // West
    [0, -1], // North
];
foreach ($instructions as $instruction) {
    preg_match('/^([NSEWLRF])([0-9]+)+$/', $instruction, $parts);
    $command = $parts[1];
    $value = intval($parts[2]);
    switch ($command) {
    case 'N':
        // Moving north
        $y -= $value;
        break;
    case 'E':
        // Moving east
        $x += $value;
        break;
    case 'S':
        // Moving south
        $y += $value;
        break;
    case 'W':
        // Moving wst
        $x -= $value;
        break;
    case 'F':
        $x += $value * $headings[$heading_index][0];
        $y += $value * $headings[$heading_index][1];
        break;
    case 'R':
        $square = $value / 90;
        $heading_index = ($heading_index + $square) % 4;
        break;
    case 'L':
        $square = $value / 90;
        $heading_index = ($heading_index - $square + 5 * 4) % 4;
        break;
    }

    // print($instruction.': '.$command.'-'.$value.': '.$x.', '.$y.', heading: '.$heading_index."\n");
}

print('Answer: '.abs($x) + abs($y)."\n");
