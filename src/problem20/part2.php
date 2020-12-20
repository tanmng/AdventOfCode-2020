<?php
/*
 * part2.php
 *
 * Main program for Advent calendar 2020 problem 20
 * Part 2
 */


const INPUT_FILE = 'input.txt';
const DEBUG = true;
const ROUGH_PIXEL = '#';    // A pixel that might be rough or a monster
const FOUND_MONSTER_PIXEL = 'O';    // Represent a monster that we have found
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

// Keep building the bottom line
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
    $flip = $next_index_data[2];

    if ($flip) {
        $currently_flip = !$currently_flip; // we can just have used a XOR - LOL
    }

    $orientation = (abs($next_index_data[0]) + ($currently_flip? 3 : 1)) % 4;

    // Record this
    $bottom_line[] = [
        $orientation,
        $currently_flip,
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
// print_r($matrix);

// This will store or image
$image_only = [];
print("Matrix: \n");
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
        $image_line = '';
        foreach ($cur_line as $i => $tile) {
            $output = $tile[$line_index];
            $image_line .= substr($output, 1, strlen($output) - 2);
            if (DEBUG && $line_index === count($cur_line[0])/2) {
                $output = substr_replace($output, $tile_line[$i][2], 3, 4);
            }
            print($output);
            if ($i < count($cur_line) - 1) {
                print(' ');
            }
        }
        // Record this into our final image
        if (!in_array($line_index, [0, count($cur_line[0]) -1])) {
            // Not the first or last
            $image_only[] = $image_line;
        }
        print("\n");
    }
    // break;
}

// Print the image for confirmation
print("Image: \n");
foreach ($image_only as $i => $image_line) {
    print(str_pad($i, 3, '   ', STR_PAD_LEFT).' '.$image_line."\n");
}

/*
 * Function to check if an image (stored as array) has a monster starting at
 * given coordinate
 * $y = line
 * $x = column
 * If it has a monster, return the coordinate of the pixels for the monster
 */
function has_monster (
    int $x,
    int $y,
    array $image
): array
{
    // Check if things are in range
    /*  012345678901234567890
     *0 *                 #
     *1 #    ##    ##    ###
     *2  #  #  #  #  #  #
     */
    $image_width = strlen($image[0]);
    $image_height = count($image);
    if ($x + 19 > $image_width - 1 || $x < 0) {
        // $x is not in range
        return ['s' => false];
    }
    if ($y < 0 || $y + 2 > $image_height - 1) {
        // $y is not in range
        return ['s' => false];
    }

    // Vector of changes where the cell should have the '#'
    $vectors = [
        [18, 0],
        [0, 1],
        [5, 1],
        [6, 1],
        [11, 1],
        [12, 1],
        [17, 1],
        [18, 1],
        [19, 1],
        [1, 2],
        [4, 2],
        [7, 2],
        [10, 2],
        [13, 2],
        [16, 2],
    ];

    $return_coordinates = [];
    foreach ($vectors as $vector) {
        $new_x = $x + $vector[0];
        $new_y = $y + $vector[1];
        // print('Trying '.$new_x.', '.$new_y." while original is ".$x.', '.$y."\n");

        if (strcmp($image[$new_y][$new_x], ROUGH_PIXEL)) {
            // Not a monster
            return ['s' => false];
        }

        $return_coordinates[] = [$new_x, $new_y];
    }
    // If we pass though the whole thing than it is a monster
    return ['s' => true, 'c' => $return_coordinates];
}

// Count the number of rough pixel first
/* print_r(array_count_values(str_split(implode('', $image_only)))); */
/* return; */
$rough_pixels_count = array_count_values(str_split(implode('', $image_only)))[ROUGH_PIXEL];
print('Total number of rought pixels: '.$rough_pixels_count."\n");

$max_monster_count = 0;
$max_monster_image = null;
$max_monster_pixels = null;

// Now, go through the image variation and check to see which one has monster
foreach (range(0, 1) as $flip) {
    $image_copy = $image_only;
    if ($flip === 1) {
        $image_copy = flipHorizontal($image_copy);
    }
    foreach (range(0, 2) as $rotation) {
        // Rotate a 4th time is stupid
        // Check to see if there is monster
        $found_monsters = 0;
        $monster_pixels = [];
        foreach (range(0, strlen($image_copy[0]) - 20) as $x) {
            foreach (range(0, count($image_copy) - 2) as $y) {
                $has_monster_status = has_monster($x, $y, $image_copy);
                if ($has_monster_status['s']) {
                    // A monster
                    $found_monsters += 1;
                    $monster_pixels = array_merge($monster_pixels, $has_monster_status['c']);
                }
            }
        }

        // Maybe we have more than 1 configurations where we see monster
        print('Flip '.$flip.' Rotate '.$rotation.', found: '.$found_monsters."\n");

        if ($found_monsters > $max_monster_count) {
            $max_monster_count = $found_monsters;
            $max_monster_pixels = $monster_pixels;
            $max_monster_image = $image_copy;
        }

        /* if ($found_monsters > 0) { */
        /*     // We found monster with this image configuration */
        /*     break 2; */
        /* } */

        // Rotate this
        $image_copy = rotate($image_copy);
    }
}
// return;

print('Number of monster found: '.$max_monster_count."\n");
print('Image in the right way: '."\n");
foreach ($max_monster_image as $i => $image_line) {
    print(str_pad($i, 3, '   ', STR_PAD_LEFT).' '.$image_line."\n");
}
// print_r($monster_pixels);
// Change the image
$image_with_monster = $max_monster_image;
foreach ($max_monster_pixels as $pixel) {
    if ($image_with_monster[$pixel[1]][$pixel[0]] === FOUND_MONSTER_PIXEL) {
        print('Overlap');
        return;
    }
    $image_with_monster[$pixel[1]][$pixel[0]] = FOUND_MONSTER_PIXEL;
}

print('Image with monster: '."\n");
foreach ($image_with_monster as $i => $image_line) {
    print(str_pad($i, 3, '   ', STR_PAD_LEFT).' '.$image_line."\n");
}

$ans = array_count_values(str_split(implode('', $image_with_monster)))[ROUGH_PIXEL];

// A monster has 15 pixels
print('Answer to part 2: '.$ans."\n");
