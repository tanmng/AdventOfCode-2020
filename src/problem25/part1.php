<?php
/*
 * part1.php
 *
 * Main program for Advent calendar 2020 problem 25
 * Part 1
 */

ini_set('memory_limit', '2048M');

const N = 20201227;   // This is a prime
const m = 7;
const CARD_PUB = 15628416;  // This is 7^e1 mod N
const DOOR_PUB = 11161639;  // This is 7^e2 mod N

// We need fo find e1 or e2
// Hmmmmm
