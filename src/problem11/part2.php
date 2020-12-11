<?php
/*
 * part2.php
 *
 * Main program for Advent calendar 2020 problem 11
 * Part 2
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
 * Function to return the number of occupied seat that we can see from the
 * specified one
 */
function saw_occupied(
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

    foreach ([-1, 0, 1] as $dx) {
        foreach ([-1, 0, 1] as $dy) {
            if ($dx == 0 && $dy == 0) {
                // It's exactly this seat - skip
                continue;
            }
            $new_x = $x + $dx;
            $new_y = $y + $dy;
            while (true) {
                if ($new_y < 0 || $new_y >= $waiting_height || $new_x < 0 || $new_x >= $waiting_width) {
                    // No longer in area
                    break;
                }
                // Think of how to move a queen on chess board
                $cur_state = $current_area[$new_y][$new_x];
                // print($new_y.', '.$new_x.': '.$cur_state."\n");
                if ($cur_state == FLOOR) {
                    // We're still looking at the floor -> keep going to the
                    // direction specified
                    $new_y += $dy;
                    $new_x += $dx;
                } else if ($cur_state == FREE) {
                    // We saw a free chair
                    break;
                } else if ($cur_state == OCCUPIED) {
                    // We saw a free chair
                    $result += 1;
                    break;
                }
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
                $adjacent = saw_occupied($x, $y);
                if ($adjacent === 0 && $cur_state === FREE) {
                    // this should become occupied
                    $line .= OCCUPIED;
                    $change_happened = true;
                } else if ($adjacent >= 5 && $cur_state === OCCUPIED) {
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

    if (!$change_happened || $counter >= 3000) {
        break;
    }
}

// print_r($waiting_area_timelapse);
/* return; */
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
print('Answer to part 2: '.$results."\n");
