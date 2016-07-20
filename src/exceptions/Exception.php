<?php

namespace eznio\db\exceptions;

class Exception extends \Exception
{
    /** @var \Exception */
    protected $innerException;

    /**
     * @return \Exception
     */
    public function getInnerException()
    {
        return $this->innerException;
    }

    /**
     * @param \Exception $innerException
     */
    public function setInnerException($innerException)
    {
        $this->innerException = $innerException;
    }

}
