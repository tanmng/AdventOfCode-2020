<?php
/*
 * part2.php
 *
 * Main program for Advent calendar 2020 problem 22
 * Part 2
 */

ini_set('memory_limit', '2048M');

const INPUT_FILE = 'input.txt';
const DEBUG = false;
const P1_WIN = true;
const P2_WIN = false;

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

$original_decks = [
    $player1_deck,
    $player2_deck,
];
/* print_r($player1_deck); */
/* print_r($player2_deck); */
$final_deck = []; // A way so that we can get the final deck of the winner

// A function to perform the recursive combat
// Return the true if P1 wins and false if p2 wins
function recursive_combat(
    array $deck1,
    array $deck2,
    int $layer,
    bool $final = false
): bool
{
    global $final_deck;
    $previous_decks = [];
    $counter = 0;
    // print_r($deck1);
    while (true) {
        // Check if we have found these deck before
        foreach ($previous_decks as $previous_deck) {
            if ($previous_deck === $deck1 || $previous_deck === $deck2) {
                // Same configuration as earlier
                // P1 instantly wins
                return P1_WIN;
            }
        }

        // Record previous decks
        $previous_decks[] = $deck1;
        $previous_decks[] = $deck2;

        if (DEBUG) {
            print('-- Round '.($counter + 1).' (Game '.$layer.') --'."\n");
            print("Before:\n");
            print('P1: '.implode(', ', $deck1)."\n");
            print('P2: '.implode(', ', $deck2)."\n");
        }


        // Deal
        $player1_card = array_shift($deck1);
        $player2_card = array_shift($deck2);

        if (count($deck1) >= $player1_card && count($deck2) >= $player2_card) {
            // We have enough cars for recursive
            $copy1 = $deck1;
            $copy2 = $deck2;
            $result = recursive_combat(array_splice($copy1, 0, $player1_card), array_splice($copy2, 0, $player2_card), $layer + 1);
        } else {
            $result = $player1_card > $player2_card;
        }

        if ($result) {
            // P1 wins
            $deck1 = array_merge($deck1, [$player1_card, $player2_card]);
        } else {
            // P2 wins
            $deck2 = array_merge($deck2, [$player2_card, $player1_card]);
        }

        $counter += 1;
        if (DEBUG) {
            print("Afterward:\n");
            print('P1: '.implode(', ', $deck1)."\n");
            print('P2: '.implode(', ', $deck2)."\n");
        }

        if (count($deck1) === 0 || count($deck2) === 0) {
            // Done
            break;
        }
    }

    if (count($deck1) === 0) {
        // P2 wins
        if ($final) {
            // print_r($deck2);
            $final_deck = $deck2;
        }
        return P2_WIN;
    } else {
        // P1 wins
        if ($final) {
            // print_r($deck1);
            $final_deck = $deck1;
        }
        return P1_WIN;
    }
}
$previous_decks = [];

// Play the game
$counter = 0;

$result = recursive_combat($player1_deck, $player2_deck, 1, true);

print($result? 'P1' : 'P2');

/* print_r($player1_deck); */
/* print_r($player2_deck); */

$ans = 0;
foreach (array_reverse($final_deck) as $index => $value) {
    $ans += ($index + 1) * $value;
}
print('Answer to part 2: '.$ans."\n");
