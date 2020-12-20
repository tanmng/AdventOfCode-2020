<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 20
 * Part 1
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
): bool
{
    foreach (range(0, 3) as $edge1_index) {
        $edge1 = get_edge($index1, $edge1_index);
        foreach (range(0, 3) as $edge2_index) {
            $edge2 = get_edge($index2, $edge2_index);

            if (strcmp(strrev($edge1), $edge2) === 0) {
                // Match without flipping
                return true;
            }

            if (strcmp($edge1, $edge2) === 0) {
                // Match with flipping on one side
                return true;
            }
        }
    }

    return false;
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

        if (check_lineup($tile_index1, $tile_index2)) {
            $cur_data[] = $tile_index2;
        }
    }

    $match_data[$tile_index1] = $cur_data;
}

print_r($match_data);

$result = 1;
foreach ($match_data as $tile_index => $data) {
    if (count($data) === 2) {
        // This tile has 2 neighbour - it's a corner one
        $result *= $tile_index;
    }
}

print('Answer to part 1: '.$result."\n");
