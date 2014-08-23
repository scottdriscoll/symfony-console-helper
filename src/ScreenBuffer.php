<?php
/**
 * Copyright (c) Scott Driscoll
 */

namespace SD\ConsoleHelper;

/**
 * @author Richard Bunce <richard.bunce@opensoftdev.com>
 */
class ScreenBuffer
{
    /**
     * @var array
     */
    private $screen;

    /**
     * @var int
     */
    private $height;

    /**
     * @var int
     */
    private $width;

    /**
     * @param int $width
     * @param int $height
     */
    public function initialize($width = 100, $height = 30)
    {
        $this->screen = array();
        $this->height = $height;
        $this->width = $width;
        for ($i = 0; $i < $height; $i++) {
            for ($j = 0; $j < $width; $j++) {
                $this->screen[$i][$j] = new ScreenBufferUnit();
            }
        }
    }

    /**
     * @param string $value
     */
    public function clearScreen($value = ' ')
    {
        foreach ($this->screen as $row) {
            foreach ($row as $unit) {
                $unit->setNext($value);
            }
        }
    }

    public function nextFrame()
    {
        foreach ($this->screen as $row) {
            foreach ($row as $unit) {
                $unit->nextFrame();
            }
        }
    }

    /**
     * @param int $x
     * @param int $y
     * @param string $value
     * @param string $color
     * @param string $backgroundColor
     * @return boolean
     */
    public function putNextValue($x, $y, $value, $color = null, $backgroundColor = null)
    {
        if ($x < 0 || $x >= $this->width || $y < 0 || $y >= $this->height) {
            return false;
        }

        $format = '';

        if ($color) {
            $format = sprintf('fg=%s', $color);
        }

        if ($color && $backgroundColor) {
            $format .= ';';
        }

        if ($backgroundColor) {
            $format .= sprintf('bg=%s', $backgroundColor);
        }

        if (!empty($format)) {
            $formatBegin = sprintf('<%s>', $format);
            $formatEnd = sprintf('</%s>', $format);
        } else {
            $formatBegin = '';
            $formatEnd = '';
        }

        $this->screen[$y][$x]->setNext(sprintf('%s%s%s', $formatBegin, $value, $formatEnd));
    }

    /**
     * @param int $x
     * @param int $y
     * @param array $values
     * @param string $color
     * @param string $backgroundColor
     */
    public function putArrayOfValues($x, $y, array $values, $color = null, $backgroundColor = null)
    {
        foreach ($values as $yi => $value) {
            if (is_array($value)) {
                foreach ($value as $xi => $element) {
                    if ($x + $xi < 0 || $x + $xi >= $this->width || $y + $yi < 0 || $y + $yi >= $this->height) {
                        continue;
                    }
                    $this->putNextValue($x + $xi, $y + $yi, $element, $color, $backgroundColor);
                }
            } else {
                for ($i = 0; $i < strlen($value); $i++) {
                    if ($x + $i < 0 || $x + $i >= $this->width || $y < 0 || $y >= $this->height) {
                        continue;
                    }
                    $this->putNextValue($x + $i, $y + $yi, $value[$i], $color, $backgroundColor);
                }
            }
        }
    }

    /**
     * @param OutputHelper $output
     */
    public function paintChanges(OutputHelper $output)
    {
        foreach ($this->screen as $y => $row) {
            foreach ($row as $x => $unit) {
                if ($unit->hasChanged()) {
                    $output->moveCursorUp(100);
                    $output->moveCursorFullLeft();
                    if ($y > 0) {
                        $output->moveCursorDown($y);
                    }
                    if ($x > 0) {
                        $output->moveCursorRight($x);
                    }
                    $output->write($unit->getNext());
                }
            }
        }
    }
}
