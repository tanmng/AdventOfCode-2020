<?php
/*
 * lib.php
 *
 * Library of all shared classes we need for this problem
 */

/*
 * Simple representation of a vector
 */
class D2Vector
{
    public $x;
    public $y;

    public function __construct (
        int $begin_x,
        int $begin_y
    )
    {
        $this->x = $begin_x;
        $this->y = $begin_y;
    }

    public function __toString () {
        return implode(', ', [$this->x, $this->y]);
    }
}

class ReachTheEndException extends Exception {};
/*
 * Class representing a field
 */
class Field
{
    // 2-d representation of the field - the line we can represent as just
    // a string
    private $data;
    private $width;     // Width of the field (note that the width can loop)
    private $height;     // Height of the field - theoretically we can just use the length of data, but we want to work faster

    // Representation
    private const TREE = '#';
    private const OPEN = '.';
    private const ENCOUNTERED_TREE = 'X';
    private const ENCOUNTERED_OPEN = 'O';

    // Constructor
    public function __construct (
        string $first_line
    )
    {
        // Create this object and its value
        $this->height = 1;
        $this->width = strlen($first_line);

        $this->data = [$first_line];

        // Validation - TODO
    }

    /*
     * Add a line to the field
     */
    public function addLine (
        string $line
    )
    {
        // Validation - TODO
        // Add the line
        $this->height += 1;
        $this->data[] = $line;
    }

    /*
     * Get the new coordinate for a move
     * Providing the coordinate of the begining, the move direction
     */
    public function move(
        D2Vector $begin_coordinate,
        D2Vector $movement_direction
    ): D2Vector
    {
        // Check if moving on using this has reached the end of field
        $new_y = $begin_coordinate->y + $movement_direction->y;

        if ($new_y >= $this->height) {
            // Throw an exception to signify that we reached the end of the
            // field
            throw new ReachTheEndException('We have reached the end, new y '.$new_y);
        }

        // Make sure to wrap around
        $new_x = ($begin_coordinate->x + $movement_direction->x) % $this->width;

        // print($begin_coordinate.' + '.$movement_direction.' = '.$new_x.', '.$new_y."\n");
        // validation - TODO

        // Return the new coordinate
        return new D2Vector($new_x, $new_y);
    }

    /*
     * Get the object at the coordinate
     */
    public function get (
        D2Vector $coordinate
    ): string
    {
        // Validation - TODO
        $char = $this->data[$coordinate->y][$coordinate->x];
        // print($coordinate.': '.$char);
        return $char;
    }

    /*
     * Check if the object at given coordinate is a tree
     */
    public function isTree (
        D2Vector $coordinate
    ): bool
    {
        // Validation - TODO
        return strcmp($this->get($coordinate), self::TREE) == 0;
    }
}

