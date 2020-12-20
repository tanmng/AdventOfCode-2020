<?php
/*
 * part2.php
 *
 * Main program for Advent calendar 2020 problem 20
 * Part 2
 */


const INPUT_FILE = 'input.txt';
const DEBUG = true;
$line_count = strcmp(INPUT_FILE, 'input.txt') === 0? 12 : 3; // Magic

$tiles = [];
$cur_tile_id = null;
$cur_tile = [];

// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, "r");
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);

        if (strlen($input) === 0) {
            // Empty line
            // Ending of a tile
            $tiles[$cur_tile_id] = $cur_tile;
            $cur_tile = [];
            continue;
        }

        if (preg_match('/^Tile ([0-9]+):$/', $input, $parts)) {
            // It's the title row
            $cur_tile_id = intval($parts[1]);
        } else {
            // Content of a tile
            $cur_tile[] = $input;
        }
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

// Store the last tile
$tiles[$cur_tile_id] = $cur_tile;

/*
 * Return the values on the edge of a tile given the index
 * Note that the values should be in clockwise order
 */
function get_edge(
    int $tile_index,
    int $edge_index
): string
{
    global $tiles;
    switch ($edge_index) {
    case 0:
        // Right edge
        $values = [];
        foreach ($tiles[$tile_index] as $line) {
            $values[] = substr($line, -1);
        }
        return implode('', $values);
        break;
    case 1:
        // Bottom edge
        return strrev(end($tiles[$tile_index]));
        break;
    case 2:
        // Left edge
        $values = [];
        foreach ($tiles[$tile_index] as $line) {
            $values[] = substr($line, 0, 1);
        }
        return implode('', array_reverse($values));
        break;
    case 3:
        // Top edge
        return $tiles[$tile_index][0];
        break;
    }
}


/*
 * Check if 2 tiles can fit together on any edge, if so, return the edge number
 * for both tile
 */
function check_lineup (
    int $index1,
    int $index2
): array
{
    foreach (range(0, 3) as $edge1_index) {
        $edge1 = get_edge($index1, $edge1_index);
        foreach (range(0, 3) as $edge2_index) {
            $edge2 = get_edge($index2, $edge2_index);

            if (strcmp(strrev($edge1), $edge2) === 0) {
                // Match without flipping
                return [true, $edge1_index, $edge2_index, false];
            }

            if (strcmp($edge1, $edge2) === 0) {
                // Match with flipping on one side
                return [true, $edge1_index, $edge2_index, true];
            }
        }
    }

    return [false];
}


/* print_r($tiles); */
/* print(count($tiles)); */

// Build matching data
$match_data = [];
foreach ($tiles as $tile_index1 => $tile1) {
    $cur_data = [];
    foreach ($tiles as $tile_index2 => $tile2) {
        if ($tile_index1 === $tile_index2) {
            // Same tile, no need
            continue;
        }

        $lineup_data = check_lineup($tile_index1, $tile_index2);

        if ($lineup_data[0]) {
            $cur_data[$lineup_data[1]] = [
                $lineup_data[2],
                $tile_index2,
                $lineup_data[3],
            ];
        }
    }

    $match_data[$tile_index1] = $cur_data;
}

/* print_r($match_data); */
/* return; */

// Assemble this image
$tiles_not_yet_used = array_keys($tiles);

// The south west corner
$corner0 = null;
// Choose a corner
foreach ($match_data as $index => $match) {
    if (count($match) === 2 && array_key_exists(0, $match) && array_key_exists(3, $match))  {
        // A corner piece - use it
        $corner0 = $index;
        break;
    }
}

/* print($corner0); */
/* return; */

$tile_matrix = [];

// Orientation - the edge that should be on top (per our perspective) after we
// flip whichever way it is
// Flip - If the image should be flip
// Index - index of the image
$bottom_line = [[3, false, $corner0]];
$forward_edge = 0;

// Keep building the top line
$currently_flip = false;
while (true) {
    $current_last_element_of_top_line = end($bottom_line)[2];

    if (!array_key_exists($forward_edge, $match_data[$current_last_element_of_top_line])) {
        // End of the line
        break;
    }

    // Find the index of the next one
    $next_index_data = $match_data[$current_last_element_of_top_line][$forward_edge];

    $next_index = $next_index_data[1];
    $flip = $next_index_data[2]; // If it's negative then it's flipped

    if ($flip) {
        $currently_flip = !$currently_flip; // we can just have used a XOR - LOL
    }

    $orientation = (abs($next_index_data[0]) + ($currently_flip? 3 : 1)) % 4;

    // Record this
    $bottom_line[] = [
        $orientation,
        $flip,
        $next_index,
    ];

    // Calculate the forward edge
    $forward_edge = ($next_index_data[0] + 2) % 4;
}

// This is actually bottom line from our point of view
// print_r($bottom_line);

// Matrix of tiles
$matrix = [];
$matrix[] = $bottom_line;   // Make sure to reverse this at the end

// Construct the next line
foreach (range(1, $line_count - 1) as $none) {
    $cur_line = [];
    $prev_line = end($matrix);
    foreach ($prev_line as $i => $bottom) {
        $index = $bottom[2];
        $edge = $bottom[0];
        $match = $match_data[$index][$edge];

        $orientation = ($match[0] + 2) % 4;
        $flip = $bottom[1] ^ $match[2];
        $next_index = $match[1];

        // Construct the object
        $cur_line[] = [
            $orientation,
            $flip,
            $next_index,
        ];
    }
    $matrix[] = $cur_line;
}

$matrix = array_reverse($matrix);

// Great, we now have the matrix
/* print_r($matrix); */
/* return; */

// Functions to help display things
//
/*
 * Rotate a tile 90 degree clockwise
 */
function rotate(
    array $tile
): array
{
    $char_count = strlen($tile[0]);
    $elements = array_fill(0, $char_count, []);
    foreach ($tile as $line) {
        foreach (range(0, $char_count - 1) as $i) {
            $elements[$i][] = substr($line, $i, 1);
        }
    }
    $result = [];
    foreach (range(0, $char_count - 1) as $i) {
        $result[] = strrev(implode('', $elements[$i]));
    }

    return $result;
}

/*
 * Function to help flip things horizontally
 * Note that flipping vertically is just the same as flipping vertically and
 * then rotate 180 degree
 */
function flipHorizontal(
    array $tile
): array
{
    $result = [];
    foreach ($tile as $line) {
        $result[] = strrev($line);
    }

    return $result;
}

/* print_r(rotate([ */
/*     '123', */
/*     '456', */
/*     '789', */
/* ])); */

// Try to print the matrix just to check and confirm things are all good
// - visually
print_r($matrix);
foreach ($matrix as $tile_line) {
    $cur_line = [];
    foreach ($tile_line as $tile_data) {
        $tile_index = $tile_data[2];
        $flip = $tile_data[1];
        $orientation = $tile_data[0];

        $cur_tile = $tiles[$tile_index];
        // Rotate
        // if necessary
        $rotation = (3 - $orientation);
        if ($rotation > 0) {
            foreach (range(0, $rotation - 1) as $e) {
                $cur_tile = rotate($cur_tile);
            }
        }

        // Flip if needed
        if ($flip) {
            $cur_tile = flipHorizontal($cur_tile);
        }
        $cur_line[] = $cur_tile;
    }
    // Now print
    // print_r($cur_line);
    foreach (range(0, count($cur_line[0]) - 1) as $line_index) {
        print(str_pad($line_index, 3, '    ', STR_PAD_LEFT).' ');
        foreach ($cur_line as $i => $tile) {
            $output = $tile[$line_index];
            if (DEBUG && $line_index === count($cur_line[0])/2) {
                $output = substr_replace($output, $tile_line[$i][2], 3, 4);
            }
            print($output);
            if ($i < count($cur_line) - 1) {
                print(' ');
            }
        }
        print("\n");
    }
    // break;
}



$result = 0;
/* foreach ($match_data as $tile_index => $data) { */
/*     if (count($data) === 2) { */
/*         // This tile has 2 neighbour - it's a corner one */
/*         $result *= $tile_index; */
/*     } */
/* } */

print('Answer to part 2: '.$result."\n");
