<?php

namespace Widicorp\KafkaBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class LoggerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->getParameter('widicorp_kafka.logger.enabled')) {
            return;
        }

        try {
            $logger = $container->findDefinition($container->getParameter('widicorp_kafka.logger.service'));
        } catch (ServiceNotFoundException $e) {
            throw new \InvalidArgumentException('Logger service for widikafka is not defined');
        }

        $container->getDefinition('widicorp_kafka.command.consumer_topic')
            ->addMethodCall('setLogger', [$logger]);
    }
}
