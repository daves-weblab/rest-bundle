<?php

namespace DavesWeblab\RestBundle\Exception;

use Exception;

/**
 * Class InvalidNamespaceException
 * @package RestBundle\Exception
 *
 * Exception thrown to indicate an invalid namespace, detected during runtime.
 */
class InvalidNamespaceException extends \RuntimeException
{
    const DEFAULT_MESSAGE = "The given namespace is invalid.";

    /** @var string $validNamespace the valid namespace to display to the user */
    private $validNamespace;

    /**
     * InvalidNamespaceException constructor.
     *
     * @param string $validNamespace the valid namespace to display to the user
     * @inheritdoc
     */
    public function __construct(string $validNamespace, $message = "", $code = 0, Exception $previous = null)
    {
        $this->validNamespace = $validNamespace;
        $message = $this->computeMessage($message);
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param string $message the message to use instead of self::DEFAULT_MESSAGE
     * @return string the default message to display to the user.
     */
    private function computeMessage($message)
    {
        $message = $message ?: self::DEFAULT_MESSAGE;
        return "$message You can only use objects of type $this->validNamespace";
    }
}