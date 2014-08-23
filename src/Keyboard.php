<?php
/**
 * Copyright (c) Scott Driscoll
 */

namespace SD\ConsoleHelper;

/**
 * Helper class to read keystrokes without needing the enter key being pressed
 *
 * @author Scott Driscoll <scott.driscoll@opensoftdev.com>
 */
class Keyboard
{
    /**
     * @var string
     */
    const RIGHT_ARROW = '→';

    /**
     * @var string
     */
    const LEFT_ARROW = '←';

    /**
     * @var string
     */
    const UP_ARROW = '↑';

    /**
     * @var string
     */
    const DOWN_ARROW = '↓';

    /**
     * Used when arrow keys are pressed
     */
    const CONTROL_KEY = '';

    /**
     * Reads one key from the keyboard buffer without waiting for the enter key to be pressed.
     * If no keys have been pressed, null is returned.
     * Arrow keys are supported, returning the above characters.
     *
     * @return string|null
     */
    public function readKey()
    {
        $key = $this->nonblockingRead();
        if (null === $key) {
            return null;
        }

        if ($key == self::CONTROL_KEY) {
            // throw away next character
            $this->nonblockingRead();
            switch ($this->nonblockingRead()) {
                case 'A':
                    $key = self::UP_ARROW;
                    break;
                case 'B':
                    $key = self::DOWN_ARROW;
                    break;
                case 'C':
                    $key = self::RIGHT_ARROW;
                    break;
                case 'D':
                    $key = self::LEFT_ARROW;
                    break;
                default:
                    $key = null;
                    break;
            }
        }

        return $key;
    }

    /**
     * Reads from a stream without waiting for a \n character.
     *
     * @return string|null
     */
    private function nonblockingRead()
    {
        $read = [STDIN];
        $write = [];
        $except = [];
        $result = stream_select($read, $write, $except, 0);

        if ($result === false || $result === 0) {
            return null;
        }

        return stream_get_line(STDIN, 1);
    }
}
