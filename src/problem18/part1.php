<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 18
 * Part 1
 */


const INPUT_FILE = 'input.txt';

function calculate (
    int $operant1,
    int $operant2,
    string $operation
)
{
    switch ($operation) {
    case '+':
        return $operant1 + $operant2;
        break;
    case '-':
        return $operant1 - $operant2;
        break;
    case '*':
        return $operant1 * $operant2;
        break;
    case '/':
        return $operant1 / $operant2;
        break;
    }
}


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
    $last_operator = '';
    $result = 0;
    $line_parts = explode(' ', $line);
    for ($index = 0; $index < count($line_parts); $index++) {
        $part = $line_parts[$index];
        if (is_numeric($part)) {
            // It's a simple number
            if ($index === 0) {
                // First number we see
                $result = intval($part);
            } else {
                // Calculate this
                $result = calculate($result, intval($part), $last_operator);
            }
        }

        if (in_array($part, ['+', '-', '*', '/'])) {
            $last_operator = $part;
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
            if ($index === 0) {
                $result = $value;
            } else {
                $result = calculate($result, $value, $last_operator);
                print($prefix.'  result = '.$result."\n");
            }

            // Skipp thing
            $index = $j;

            // print($line_parts[$index]);
        }
    }
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

print('Answer to part 1: '.$sum."\n");
