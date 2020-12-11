<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 11
 * Part 1
 */


// Script constants
const INPUT_FILE = 'input.txt';

const OCCUPIED = '#';
const FREE = 'L';
const FLOOR = '.';

// Representation of the waiting area
$waiting_area = [];

// Waiting areas through the time
$waiting_area_timelapse = [];

// Size of the waiting area
$waiting_height = 0;
$waiting_width = 0;

// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, "r");
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);
        $waiting_area[] = $input;
        $waiting_width = strlen($input);
        $waiting_height += 1;
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

$waiting_area_timelapse[] = $waiting_area;

/*
 * Function to return the number of occupied seat adjacent to it
 */
function adjacent_occupied(
    int $x,
    int $y
): int
{
    global $waiting_area_timelapse;
    global $waiting_width;
    global $waiting_height;

    // Copy the current state of waiting area
    $current_area = $waiting_area_timelapse[count($waiting_area_timelapse) - 1];
    $result = 0;
    foreach (range(max($x - 1, 0), min($x + 1, $waiting_width - 1)) as $dx) {
        foreach (range(max($y - 1, 0), min($y + 1, $waiting_height - 1)) as $dy) {
            if ($dx == $x && $dy == $y) {
                // It's exactly this seat - skip
                continue;
            }
            if ($current_area[$dy][$dx] == OCCUPIED) {
                $result += 1;
            }
        }
    }
    return $result;
}

// Simulation
$counter = 0;
while (true) {
    $change_happened = false;
    $current_area = $waiting_area_timelapse[count($waiting_area_timelapse) - 1];
    $new_area = [];
    foreach (range(0, $waiting_height - 1) as $y) {
        $line = '';
        foreach (range(0, $waiting_width - 1) as $x) {
            $cur_state = $current_area[$y][$x];
            if ($cur_state === FLOOR) {
                // NO change to this thing
                $line .= FLOOR;
            } else {
                $adjacent = adjacent_occupied($x, $y);
                if ($adjacent === 0 && $cur_state === FREE) {
                    // this should become occupied
                    $line .= OCCUPIED;
                    $change_happened = true;
                } else if ($adjacent >= 4 && $cur_state === OCCUPIED) {
                    // this should become occupied
                    $line .= FREE;
                    $change_happened = true;
                } else {
                    // No change
                    $line .= $cur_state;
                }
            }
        }
        $new_area[] = $line;
    }
    // Store this
    $waiting_area_timelapse[] = $new_area;
    $counter += 1;

    if (!$change_happened || $counter >= 300000) {
        break;
    }
}

// print_r($waiting_area_timelapse);
$latest_state = $waiting_area_timelapse[$counter];
// print_r($latest_state);
$results = 0;
foreach (range(0, $waiting_height - 1) as $y) {
    foreach (range(0, $waiting_width - 1) as $x) {
        if ($latest_state[$y][$x] == OCCUPIED) {
            $results += 1;
        }
    }
}

// print_r($waiting_area);
print('Answer to part 1: '.$results."\n");
