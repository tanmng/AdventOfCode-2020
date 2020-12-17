<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 17
 * Part 1
 */


const INPUT_FILE = 'input.txt';
const TARGET = 6;
const ACTIVE = '#';
const INACTIVE = '.';

$space = []; // Our space by layer
$space[] = [];


// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, "r");
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);
        $space[0][] = str_split($input);
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

// Check the number of cells that are active nearby
function active_nearby(
    int $x,
    int $y,
    int $z
)
{
    global $space;
    $result = 0;
    foreach (range(-1, 1) as $dz) {
        $new_z = $z + $dz;
        if (!array_key_exists($new_z, $space)) {
            // This layer doesn't exist yet
            continue;
        }
        $current_layer = $space[$new_z];
        foreach (range(-1, 1) as $dy) {
            $new_y = $y + $dy;
            if (!array_key_exists($new_y, $current_layer)) {
                // This Y doesn't exist yet
                continue;
            }
            $current_line = $current_layer[$new_y];
            foreach (range(-1, 1) as $dx) {
                if ($dz === 0 && $dy === 0 && $dx === 0) {
                    // Same space => skip
                    continue;
                }
                $new_x = $x + $dx;

                if (!array_key_exists($new_x, $current_line)) {
                    // This is is not initialized yet
                    continue;
                }

                if ($current_line[$new_x] === ACTIVE) {
                    $result += 1;
                }
            }
        }
    }
    return $result;
}

// Function to help print the current space
function print_space() {
    global $space;
    foreach ($space as $z => $layer) {
        print('z='.$z."\n");
        // Heading
        print(' ');
        foreach(array_keys($layer[0]) as $x) {
            print($x < 0? ' ' : $x);
        }
        print("\n");
        foreach ($layer as $y => $line) {
            print(($y < 0? ' ' : $y).implode('', $line)."\n");
        }
        print("\n");
    }
}

$counter = 0;
while (true) {
    print('Iteration '.($counter + 1).': '."\n");

    $new_space = [];

    // How much space to each dimension we have for current space
    $z_range = array_keys($space);
    $y_range = array_keys($space[0]);
    $x_range = array_keys($space[0][0]);
    foreach(range(min($z_range) - 1, max($z_range) + 1) as $new_z) {
        $current_layer = [];
        foreach(range(min($y_range) - 1, max($y_range) + 1) as $new_y) {
            $current_line = [];
            foreach(range(min($x_range) - 1, max($x_range) + 1) as $new_x) {
                $nearby_active = active_nearby($new_x, $new_y, $new_z);
                if (in_array($new_x, $x_range) && in_array($new_y, $y_range) && in_array($new_z, $z_range)) {
                    // We have current data of this point
                    $current_state = $space[$new_z][$new_y][$new_x];
                } else {
                    $current_state = INACTIVE;
                }

                $new_state = $current_state;
                if ($current_state === ACTIVE) {
                    if (in_array($nearby_active, [2, 3])) {
                        $new_state  = ACTIVE;
                    }  else {
                        $new_state = INACTIVE;
                    }
                } else {
                    // Currently inactive
                    if (in_array($nearby_active, [3])) {
                        $new_state  = ACTIVE;
                    }  else {
                        $new_state = INACTIVE;
                    }
                }
                /* if ($new_x == 0 && $new_y === 1 && $new_z === -1 && $counter == 1) { */
                /*     print($current_state."\n"); */
                /*     print($new_state."\n"); */
                /*     return; */
                /* } */

                // Record this
                $current_line[$new_x] = $new_state;
            }
            // Record this
            $current_layer[$new_y] = $current_line;
        }
        $new_space[$new_z] = $current_layer;
    }

    // Record the new space
    $space = $new_space;
    print_space();
    $counter += 1;
    if ($counter === TARGET) {
        // Finished with simulation
        break;
    }
}

// Count the active
$active = 0;
foreach ($space as $layer) {
    foreach ($layer as $line) {
        foreach ($line as $cube) {
            if ($cube == ACTIVE) {
                $active += 1;
            }
        }
    }
}
print('Answer to part 1: '.$active."\n");
