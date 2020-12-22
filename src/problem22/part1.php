<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 22
 * Part 1
 */

ini_set('memory_limit', '2048M');

const INPUT_FILE = 'input.txt';

$player1_deck = [];
$player2_deck = [];
$index = 0;

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
            $index = 1;
            continue;
        }

        if (is_numeric($input)) {
            // A number
            switch ($index) {
            case 0:
                $player1_deck[] = intval($input);
                break;
            case 1:
                $player2_deck[] = intval($input);
                break;
            }
        }
    }
    if (!feof($handle)) {
         print('Finished reading the input file');
    }
}

$total_card_count = count($player1_deck) + count($player2_deck);

print_r($player1_deck);
print_r($player2_deck);

// Play the game
$counter = 0;

while (true) {
    if (count($player1_deck) === 0 || count($player2_deck) === 0) {
        // Done
        break;
    }

    $player1_card = array_shift($player1_deck);
    $player2_card = array_shift($player2_deck);

    if ($player1_card > $player2_card) {
        // Player 1 wins
        $player1_deck = array_merge($player1_deck, [$player1_card, $player2_card]);
    } else {
        // P2 wins
        $player2_deck = array_merge($player2_deck, [$player2_card, $player1_card]);
    }
}

/* print_r($player1_deck); */
/* print_r($player2_deck); */

$ans = 0;
foreach (array_reverse(array_merge($player1_deck, $player2_deck)) as $index => $value) {
    $ans += ($index + 1) * $value;
}

print('Answer to part 1: '.$ans."\n");
