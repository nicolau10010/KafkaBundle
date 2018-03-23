<?php

namespace Widicorp\KafkaBundle\Exceptions;

/**
 * Class EntityNotSetException
 * @package Widicorp\KafkaBundle\Exceptions
 *
 * A class to handle exceptions when trying to use an entity not set
 */
class EntityNotSetException extends \Exception
{
    protected $message = 'Entity not set';
}
