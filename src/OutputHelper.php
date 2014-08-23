<?php
/**
 * Copyright (c) Scott Driscoll
 */

namespace SD\ConsoleHelper;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Scott Driscoll <scott.driscoll@opensoftdev.com>
 */
class OutputHelper
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var string
     */
    private $string;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function clear()
    {
        $this->string = '';
    }

    public function dump()
    {
        $this->output->write($this->string);
    }

    /**
     * @param string $msg
     */
    public function write($msg)
    {
        $this->string .= $msg;
    }

    /**
     * @param string $msg
     */
    public function writeln($msg)
    {
        $this->string .= "$msg\n";
    }

    /**
     * @param int $lines
     */
    public function moveCursorDown($lines)
    {
        $this->string .= sprintf("\033[%dB", $lines);
    }

    /**
     * @param int $lines
     */
    public function moveCursorUp($lines)
    {
        $this->string .= sprintf("\033[%dA", $lines);
    }

    /**
     * @param int $spaces
     */
    public function moveCursorRight($spaces)
    {
        $this->string .= sprintf("\033[%dC", $spaces);
    }

    public function moveCursorFullLeft()
    {
        $this->string .= "\x0D";
    }

    public function disableKeyboardOutput()
    {
        shell_exec('stty -icanon -echo');
    }

    public function hideCursor()
    {
        printf("\e[?25l");
    }

    static public function showCursor()
    {
        echo sprintf("\033[%dB", 50);
        echo "\x0D";
        shell_exec('stty icanon echo');
        printf("\e[?25h");
    }
}

register_shutdown_function('\SD\ConsoleHelper\OutputHelper::showCursor');
