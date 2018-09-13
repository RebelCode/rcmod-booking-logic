<?php

namespace RebelCode\Bookings\Module;

use Dhii\Exception\InternalException;
use Psr\Container\ContainerInterface;
use RebelCode\Modular\Module\AbstractBaseModule;

/**
 * Module class for the booking logic module.
 *
 * @since [*next-version*]
 */
class BookingLogicModule extends AbstractBaseModule
{
    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @throws InternalException
     */
    public function setup()
    {
        $config = $this->_getConfig();

        return $this->_setupContainer(
            $this->_loadPhpConfigFile($config->get('config_file_path')),
            $this->_loadPhpConfigFile($config->get('services_file_path'))
        );
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function run(ContainerInterface $c = null)
    {
    }
}
