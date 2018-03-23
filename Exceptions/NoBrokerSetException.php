<?php

namespace Widicorp\KafkaBundle\Exceptions;

/**
 * Class NoBrokerSetException
 * @package Widicorp\KafkaBundle\Exceptions
 *
 * A class to handle exceptions when trying to use a constructed entity without having set a broker
 */
class NoBrokerSetException extends \Exception
{
    protected $message = 'No broker set';
}
