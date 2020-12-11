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
    // Representation the view from the chair
    // 0 - north
    // So on - Clockwise
    $view_directions = array_fill(0, 8, 0);
    // Look north
    foreach (range(max($y - 1, 0), 0) as $dy) {
        if ($dy === $y) {
            // Skip
            continue;
        }
        $inview = $current_area[$dy][$x];
        if ($inview == FREE) {
            // See a free seat
            break;
        }
        if ($inview == OCCUPIED) {
            // Found an occupied seat looking up
            $view_directions[0] = 1;
            break;
        }
    }

    // Look south
    foreach (range(min($y + 1, $waiting_height - 1), $waiting_height - 1) as $dy) {
        if ($dy === $y) {
            // Skip
            continue;
        }
        $inview = $current_area[$dy][$x];
        if ($inview == FREE) {
            // See a free seat
            break;
        }
        if ($inview == OCCUPIED) {
            // Found an occupied seat looking up
            $view_directions[4] = 1;
            break;
        }
    }

    // Look west
    foreach (range(max($x - 1, 0), 0) as $dx) {
        if ($dx === $x) {
            // Skip
            continue;
        }
        $inview = $current_area[$y][$dx];
        if ($inview == FREE) {
            // See a free seat
            break;
        }
        if ($inview == OCCUPIED) {
            // Found an occupied seat looking up
            $view_directions[6] = 1;
            break;
        }
    }

    // Look east
    foreach (range(min($x + 1, $waiting_width - 1), $waiting_width - 1) as $dx) {
        if ($dx === $x) {
            // Skip
            continue;
        }
        $inview = $current_area[$y][$dx];
        if ($inview == FREE) {
            // See a free seat
            break;
        }
        if ($inview == OCCUPIED) {
            // Found an occupied seat looking up
            $view_directions[2] = 1;
            break;
        }
    }

    // Look north east
    foreach (range(0, min($y, $waiting_width - $x - 1)) as $delta) {
        if ($delta == 0) {
            continue;
        }
        $dx = $x + $delta;
        $dy = $y - $delta;
        $inview = $current_area[$dy][$dx];
        if ($inview == FREE) {
            // See a free seat
            break;
        }
        if ($inview == OCCUPIED) {
            // Found an occupied seat looking up
            $view_directions[1] = 1;
            break;
        }
    }
    // Look north west
    foreach (range(0, min($y, $x)) as $delta) {
        if ($delta == 0) {
            continue;
        }
        $dx = $x - $delta;
        $dy = $y - $delta;
        $inview = $current_area[$dy][$dx];
        if ($inview == FREE) {
            // See a free seat
            break;
        }
        if ($inview == OCCUPIED) {
            // Found an occupied seat looking up
            $view_directions[7] = 1;
            break;
        }
    }

    // Look south east
    foreach (range(0, min($waiting_height - $y - 1, $waiting_width - $x - 1)) as $delta) {
        if ($delta == 0) {
            continue;
        }
        $dx = $x + $delta;
        $dy = $y + $delta;
        $inview = $current_area[$dy][$dx];
        if ($inview == FREE) {
            // See a free seat
            break;
        }
        if ($inview == OCCUPIED) {
            // Found an occupied seat looking up
            $view_directions[3] = 1;
            break;
        }
    }

    // Look south west
    foreach (range(0, min($waiting_height - $y - 1, $x)) as $delta) {
        if ($delta == 0) {
            continue;
        }
        $dx = $x - $delta;
        $dy = $y + $delta;
        $inview = $current_area[$dy][$dx];
        if ($inview == FREE) {
            // See a free seat
            break;
        }
        if ($inview == OCCUPIED) {
            // Found an occupied seat looking up
            $view_directions[5] = 1;
            break;
        }
    }
    // print_r($view_directions);
    return array_sum($view_directions);
}

// Some basic test
if (false) {
    $waiting_area_timelapse = [
        [
            '.......#.',
            '...#.....',
            '.#.......',
            '.........',
            '..#L....#',
            '....#....',
            '.........',
            '#........',
            '...#.....',
        ],
    ];
    $waiting_height = 9;
    $waiting_width = 9;
    print(adjacent_occupied(3, 4)."\n");

    $waiting_area_timelapse = [
        [
            '.##.##.',
            '#.#.#.#',
            '##...##',
            '...L...',
            '##...##',
            '#.#.#.#',
            '.##.##.',
        ],
    ];
    $waiting_height = 7;
    $waiting_width = 7;
    print(adjacent_occupied(3, 3)."\n");

    $waiting_area_timelapse = [
        [
            '.............',
            '.L.L.#.#.#.#.',
            '.............',
        ],
    ];
    $waiting_height = 3;
    $waiting_width = 13;
    print(adjacent_occupied(1, 1)."\n");
    return;
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
