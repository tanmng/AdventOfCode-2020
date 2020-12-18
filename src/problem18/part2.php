<?php
/*
 * part2.php
 *
 * Main program for Advent calendar 2020 problem 18
 * Part 2
 */


const INPUT_FILE = 'input.txt';

/*
 * Evaluate a line and return it
 */
function evaluate (
    string $line,
    string $prefix = ''
): int
{
    // Go through the line by characters
    // Expand it so that we have clear words
    $line = str_replace('(', '( ',  $line);
    $line = str_replace(')', ' )', $line);
    // Reduce all the spacing
    $line = str_replace('  ', ' ', $line);

    print($prefix.'Evaluating '.$line."\n");
    $result = 0;
    $line_parts = explode(' ', $line);

    // We need to actually get the purest form of all the input
    $pure = [];

    for ($index = 0; $index < count($line_parts); $index++) {
        $part = $line_parts[$index];
        if (is_numeric($part)) {
            $pure[] = intval($part);
        }

        if (in_array($part, ['+', '-', '*', '/'])) {
            $pure[] = $part;
        }

        if ($part === '(') {
            // It's beginning of a subexpression
            // Find the matching side
            $count = 1;
            for ($j = $index + 1; $j < count($line_parts); $j++) {
                $matching_part = $line_parts[$j];
                if ($matching_part === '(') {
                    $count += 1;
                }
                if ($matching_part === ')') {
                    $count -= 1;
                }

                if ($count === 0) {
                    break;
                }
            }

            // The subexpression is from $index to $j
            $copy = $line_parts;
            // print($result);
            $value = evaluate(implode(' ', array_splice($copy, $index + 1, $j - $index - 1)), '  '.$prefix);
            // Record this
            $pure[] = $value;

            // Skipp thing
            $index = $j;

            // print($line_parts[$index]);
        }
    }

    // Now, calculate with pure
    while (count($pure) > 1) {
        $copy = [];
        if (in_array('+', $pure)) {
            $first_plus = array_search('+', $pure);

            if ($first_plus > 2) {
                foreach(range(0, $first_plus - 2) as $i) {
                    // Must all be number and *
                    $copy[] = $pure[$i];
                }
            }
            // Calculate the plus
            $copy[] = $pure[$first_plus - 1] + $pure[$first_plus + 1];

            if ($first_plus < count($pure) - 2) {
                //  Things left to copy
                foreach(range($first_plus + 2, count($pure) - 1) as $i) {
                    // Must all be number and *
                    $copy[] = $pure[$i];
                }
            }
        } else {
            // Now that we have only multiplication left, just do it
            $result = 1;
            foreach ($pure as $val) {
                if (is_int($val)) {
                    $result *= $val;
                }
            }

            $copy = [$result];
        }
        $pure = $copy;
    }
    $result = $pure[0];
    print($prefix.$line.' = '.$result."\n");
    return $result;
}

$sum = 0;
// Open the input file and read the numbers
$handle = @fopen(INPUT_FILE, "r");
if (!$handle) {
    // Failed to open the file
    throw new Exception ('Failed opening input file '.INPUT_FILE);
} else {
    while (($buffer = fgets($handle)) !== false) {
        // Clean up the string
        $input = trim($buffer);
        $value = evaluate($input);

        // print($input." = ".$value."\n");

        $sum += $value;
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

print('Answer to part 2: '.$sum."\n");
