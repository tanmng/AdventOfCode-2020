<?php
/*
 * lib.php
 *
 * Library of things we use in all parts of problem 2
 */

class InvalidCommandException extends Exception {}
class InvalidMemoryForCommandException extends Exception {}

/*
 * A class to represent a command
 */
class Command
{
    /*
     * Command values
     */
    private const COMMAND_ADD = 1;
    private const COMMAND_MULTIPLY = 2;
    private const COMMAND_HALT = 99;

    /*
     * Check whether a command number is valid (according to the rule of our
     * computer
     */
    private function validateCommandNumber (
        int $command
    ): bool
    {
        return in_array($command, [
            self::COMMAND_ADD,
            self::COMMAND_MULTIPLY,
            self::COMMAND_HALT
        ]);
    }

    public function __construct
    (
        array $input_parts
    )
    {
        // Make sure that the input_parts contain enough
        if (count($input_parts) < 1 || count($input_parts) > 4) {
                throw new InvalidCommandException('Invalid number of parts for a command '.print_r($input_parts, true));
        }

        // Validate that all parts of the input has to be number
        foreach ($input_parts as $part) {
            if (!is_numeric($part)) {
                throw new InvalidCommandException('Command contains parts that are not numeric '.print_r($input_parts, true));
            }
        }

        // Get the value of the command number
        $potential_command_number = intval($input_parts[0]);

        // Make sure to validate the command number
        if (!$this->validateCommandNumber($potential_command_number)) {
            throw new InvalidCommandException('Invalid command number '.$potential_command_number);
        }

        // Store the command number
        $this->opcode = $potential_command_number;

        if ($this->opcode === self::COMMAND_HALT) {
            // It's a command to halt, nothing needed further
            return;
        } else {
            // It's either an add or multiply command
            if (count($input_parts) !== 4) {
                throw new InvalidCommandException('Not enough parts for command '.print_r($input_parts));
            }

            // Store the parts of the command
            $this->operant1 = intval($input_parts[1]);
            $this->operant2 = intval($input_parts[2]);
            $this->target_position = intval($input_parts[3]);
        }
    }

    /*
     * Apply the command onto the memory as provided
     *
     * Return whether we should continue execution
     */
    public function execute(
        array &$memory
    ): bool
    {
        if ($this->opcode === self::COMMAND_HALT) {
            return false;
        }

        // Make sure that the operant values are present and are numeric
        if ($this->operant1 > count($memory) || !is_numeric($memory[$this->operant1])) {
            throw new InvalidMemoryForCommandException('The memory provided for command does not have first operant or it is of invalid type');
        }
        if ($this->operant2 > count($memory) || !is_numeric($memory[$this->operant2])) {
            throw new InvalidMemoryForCommandException('The memory provided for command does not have second operant or it is of invalid type');
        }
        if ($this->target_position > count($memory)) {
            // We don't have a position to write to
            throw new InvalidMemoryForCommandException('The memory does not a cell for the target position');
        }

        // Here we assume everything passed -> proceed
        $result = null;
        switch ($this->opcode) {
        case self::COMMAND_ADD:
            $result = $memory[$this->operant1] + $memory[$this->operant2];
            break;
        case self::COMMAND_MULTIPLY:
            $result = $memory[$this->operant1] * $memory[$this->operant2];
            break;
        default:
            throw new InvalidCommandException('Invalid command number '.$this->opcode);
            break;
        }

        // Store the result into the memory
        $memory[$this->target_position] = $result;
        return true;
    }
}

class InvalidMachineStateException extends Exception {}
class MachineExecutionExceedLimit extends Exception {}
class MachineProgramCounterExeedLimit extends Exception {}
class MemoryOutOfRangeException extends Exception {}

/*
 * Class represent a machine
 */
class Machine
{
    private $memory;

    private const COMMAND_CHUNK = 4;    // How many element of the memory we should chunk into commands
    private const MAX_CYCLE_COUNT = 1000;   // How many command/instruction we should execute before halting
    private const PART_DELIMITER = ',';

    public function __construct (
        string $input_string
    )
    {
        // Break the input string into parts
        $parts = explode(self::PART_DELIMITER, $input_string);

        if (count($parts) === 0) {
            // The input doesn't contain anything
            throw new InvalidMachineStateException('Input state for machine does NOT contain anything '.$input_string);
        }

        $this->memory = [];

        // Parse the parts and store it
        foreach ($parts as $part) {
            if (!is_numeric($part)) {
                throw new InvalidMachineStateException('One part of the machine state is not numeric '.$part);
            }

            // Store it
            $this->memory[] = intval($part);
        }
    }

    /*
     * Run the machine until it reach either the halt or the limit
     * Return a status of whether the machine ran without issues
     */
    public function runUntilHalt(
        int $limit = self::MAX_CYCLE_COUNT,
        bool $debug = false
    ): bool
    {
        $counter = 0;

        while (true) {
            // Chunk the current memory into parts
            $memory_chunks = array_chunk($this->memory, self::COMMAND_CHUNK);

            if ($counter >= count($memory_chunks)) {
                // We exceeded the memory
                throw new MachineProgramCounterExeedLimit('Program counter '.$counter.' exceeded the memory of our machine');
            }
            // Execute the command
            // Create the command
            $command = new Command($memory_chunks[$counter]);
            // Execute the command
            $continue = $command->execute($this->memory);

            if ($debug) {
                print($this."\n");
            }

            if (!$continue) {
                // We just reached a halt
                return true;
            }

            // Increase counter
            $counter += 1;

            if ($counter >= $limit) {
                throw new MachineExecutionExceedLimit('Machine executed for too long '.$counter);
            }
        }
    }

    public function __toString() {
        return implode(self::PART_DELIMITER, $this->memory);
    }

    /*
     * Return the output of the machine
     */
    public function output (): int {
        return $this->get(0);
    }

    /*
     * Set the value at any index in our memory
     */
    public function set(
        int $position,
        int $value
    )
    {
        if ($position > count($this->memory)) {
            throw new MemoryOutOfRangeException('Position '.$position.' is out of range for our machine');
        }

        $this->memory[$position] = $value;
    }

    /*
     * Set the value at any index in our memory
     */
    public function get(
        int $position
    ): int
    {
        if ($position > count($this->memory)) {
            throw new MemoryOutOfRangeException('Position '.$position.' is out of range for our machine');
        }

        return $this->memory[$position];
    }
}
