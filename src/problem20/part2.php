<?php
/*
 * part2.php
 *
 * Main program for Advent calendar 2020 problem 20
 * Part 2
 */


const INPUT_FILE = 'input.txt';

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
    $orientation = (abs($next_index_data[0]) + ($flip? 3 : 1)) % 4;

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
print_r($bottom_line);

// Construct the next line
$cur_line = [];
foreach ($bottom_line as $bottom) {
    $index = $bottom[2];
    $edge = $bottom[0];
    $match = $match_data[$index][$edge];

    print_r($match);

}




$result = 0;
/* foreach ($match_data as $tile_index => $data) { */
/*     if (count($data) === 2) { */
/*         // This tile has 2 neighbour - it's a corner one */
/*         $result *= $tile_index; */
/*     } */
/* } */

print('Answer to part 2: '.$result."\n");
