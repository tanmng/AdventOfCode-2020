<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 24
 * Part 1
 */

ini_set('memory_limit', '2048M');

const INPUT_FILE = 'input.txt';
const DAY_COUNT = 100;

const BLACK = false;
const WHITE = true;

// Directions
const EAST = 0;
const SOUTH_EAST = 1;
const SOUTH_WEST = 2;
const WEST = 3;
const NORTH_WEST = 4;
const NORTH_EAST = 5;

/*
 * Represent a hex grid cell - Tile
 */
class Tile {
    public $x;
    public $y;
    public $z;

    public const CHANGE_VECTORS = [
        EAST        => [+1, -1, 0],
        SOUTH_EAST  => [0, -1, +1],
        SOUTH_WEST  => [-1, 0, +1],
        WEST        => [-1, +1, 0],
        NORTH_WEST  => [0, +1, -1],
        NORTH_EAST  => [+1, 0, -1],
    ];

    public function __construct(
        int $x,
        int $y,
        int $z
    )
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    // Move
    public function move(
        int $direction
    )
    {
        // How the values should change
        $change_vector = self::CHANGE_VECTORS[$direction];

        $this->x += $change_vector[0];
        $this->y += $change_vector[1];
        $this->z += $change_vector[2];
    }

    //  This can be counted as a hash too, right?
    public function __toString()
    {
        return implode(', ', [
            $this->x,
            $this->y,
            $this->z
        ]);
    }

    /*
     * Parse a string representation and form an object
     */
    public static function parse(
        string $representation
    )
    {
        $parts = explode(', ', $representation);
        return new self(
            intval($parts[0]),
            intval($parts[1]),
            intval($parts[2]),
        );
    }

    // Return all the adjacent in coordinate format
    public function adjacents(
    ): array
    {
        $result = [];
        foreach (self::CHANGE_VECTORS as $change_vector) {
            $result[] = new self(
                $this->x + $change_vector[0],
                $this->y + $change_vector[1],
                $this->z + $change_vector[2],
            );
        }

        return $result;
    }
}

/* print_r((new Tile(1, 2, 3))->adjacents()); */
/* return; */

// Mapping from the instruction to the logical direction that we have
$string_to_direction = [
    'e'     => EAST,
    'se'    => SOUTH_EAST,
    'sw'    => SOUTH_WEST,
    'w'     => WEST,
    'nw'    => NORTH_WEST,
    'ne'    => NORTH_EAST,
];

$instructions = [];

// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, "r");
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);

        // Record this instructions
        $instructions[] = $input;
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

// Record what tiles got flipped
$grid = [];

// Find the range value of x, y and z
$x_values = [];
$y_values = [];
$z_values = [];

/* print_r($instructions); */
/* return; */
// Go through the list of instructions and slowly crawl it
foreach ($instructions as $instruction) {
    $instruction_chars = str_split($instruction);
    $tile = new Tile(0, 0, 0);  // Start at the reference tile
    while (count($instruction_chars) > 0) {
        // While there are still instructions to follow
        $direction_string = '';
        while (true) {
            // Take out char from the instruction chars so that we can form
            // a proper instruction
            if (array_key_exists($direction_string, $string_to_direction)) {
                // We found a proper command
                break;
            }

            $direction_string .= array_shift($instruction_chars);
        }
        // $direction_string is now the string representing the direction that
        // we have

        // Move
        $tile->move($string_to_direction[$direction_string]);
    }
    // Debug
    // print($tile."\n");
    $tile_signature = strval($tile);
    if (array_key_exists($tile_signature, $grid)) {
        // We recorded this tile earlier
        // Flip it
        $grid[$tile_signature] = !$grid[$tile_signature];
    } else {
        // Never see this thing before
        // Set to black
        $grid[$tile_signature] = BLACK;
    }
}

// Count number of tiles that are black
$black_tile_count = 0;
foreach ($grid as $tile_colour) {
    if ($tile_colour === BLACK) {
        $black_tile_count += 1;
    }
}

print('Answer to part 1: '.$black_tile_count."\n");

// Store only the cells that are black
$black_tiles = [];
$black_tile_signatures = [];

foreach ($grid as $tile_signature => $colour) {
    if ($colour == BLACK) {
        $black_tiles[] = Tile::parse($tile_signature);
        $black_tile_signatures[] = $tile_signature;
    }
}

/* $test1 = [ */
/*     new Tile(0, 2, 3), */
/*     new Tile(1, 2, 3), */
/* ]; */
/* print_r($test1); */
/* print_r(array_diff($test1, [new Tile(0, 2, 3)])); */
/* return; */
/* print_r($black_tile_signatures); */
/* return; */

/*
 * Return number of adjacen tiles that are black
 */
function adjacent_black(
    array $black_tile_signatures,
    Tile $tile
): int
{
    $count = 0;
    foreach (Tile::CHANGE_VECTORS as $change_vector) {
        $new_x = $tile->x + $change_vector[0];
        $new_y = $tile->y + $change_vector[1];
        $new_z = $tile->z + $change_vector[2];

        $key = strval(new Tile($new_x, $new_y, $new_z));
        if (in_array($key, $black_tile_signatures)) {
            // It's black
            $count += 1;
        }
    }

    return $count;
}

/*
 * Count number of black
 */
function count_black(
    array $black_tiles
): int
{
    return count($black_tiles);
}

foreach (range(1, DAY_COUNT) as $day) {
    // Copy the grid
    $new_black_tiles = $black_tiles;
    $new_black_tile_signatures = $black_tile_signatures;

    $already_counted = [];

    // Form the list of tiles we should check
    $tiles_to_check = $black_tiles;
    foreach ($black_tiles as $tile) {
        $tiles_to_check = array_merge($tiles_to_check, $tile->adjacents());
    }
    array_unique($tiles_to_check);

    foreach ($tiles_to_check as $adjacent_tile) {
        $tile_signature = strval($adjacent_tile);
        if (in_array($tile_signature, $already_counted)) {
            // Already taken care of
            continue;
        }

        // Record that we took care of this
        $already_counted[] = $tile_signature;

        $is_black = in_array($tile_signature, $black_tile_signatures);

        // Count the number of adjacent black
        $adjacent_black_count = adjacent_black($black_tile_signatures, $adjacent_tile);
        if ($is_black && ($adjacent_black_count === 0 || $adjacent_black_count > 2)) {
            //  This should now be white
            $new_black_tiles = array_diff($new_black_tiles, [$adjacent_tile]);
            $new_black_tile_signatures = array_diff($new_black_tile_signatures, [$tile_signature]);
            continue;
        }

        if (!$is_black && $adjacent_black_count === 2) {
            // White with 2 adjacent black
            $new_black_tiles[] = $adjacent_tile;
            $new_black_tile_signatures[] = $tile_signature;
        }
    }

    $before_black = count_black($black_tiles);

    $black_tiles = $new_black_tiles;
    $black_tile_signatures = $new_black_tile_signatures;

    $current_black = count_black($black_tiles);

    print('Day '.$day.': '.$before_black.' -> '.$current_black."\n");
}

// Simulate day change in part 2
print('Answer to part 2: '.count_black($black_tiles)."\n");
