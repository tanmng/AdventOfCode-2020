<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 25
 * Part 1
 */

ini_set('memory_limit', '2048M');

const P = 20201227;   // This is a prime
const g = 7;

# The 2 public keys
const CARD_PUB = 15628416;  // This is g^a mod P
const DOOR_PUB = 11161639;  // This is g^b mod P

// We need fo find a or b
$val = 1;
$result = 1;
while (true) {
    $val = ($val * g) % P;
    $result = ($result * DOOR_PUB) % P;
    if ($val === CARD_PUB) {
        break;
    }
}

print('Answer to part 1: '.$result."\n");
