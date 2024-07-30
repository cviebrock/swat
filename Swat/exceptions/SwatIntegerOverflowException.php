<?php

/**
 * Thrown when a integer causes an arithmetic/buffer overflow.
 *
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatIntegerOverflowException extends OverflowException
{
    /**
     * Sign.
     *
     * The sign of the integer, either positive or negative
     *
     * @var int
     */
    protected $sign;

    /**
     * Creates a new invalid type exception.
     *
     * @param string $message the message of the exception
     * @param int    $code    the code of the exception
     * @param int    $sign    the sign of the integer, either positive or
     *                        negative
     */
    public function __construct($message = null, $code = 0, $sign = 1)
    {
        parent::__construct($message, $code);

        $this->sign = $sign;
    }

    /**
     * Gets the sign of the integer.
     *
     * @return int the sign of the integer, either positive or negative
     */
    public function getSign()
    {
        return $this->sign;
    }
}
