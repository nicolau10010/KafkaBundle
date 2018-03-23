<?php
namespace Widicorp\KafkaBundle;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Widicorp\KafkaBundle\DependencyInjection\CompilerPass\LoggerPass;

/**
 * Class WidicorpKafkaBundle
 */
class WidicorpKafkaBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new LoggerPass(), PassConfig::TYPE_OPTIMIZE);
    }
}
