<?php

use Psr\Container\ContainerInterface;
use RebelCode\Bookings\Module\BookingLogicModule;

return function (ContainerInterface $c) {
    return new BookingLogicModule(
        [
            'key'                => 'booking_logic',
            'dependencies'       => [],
            'module_path'        => __DIR__,
            'config_file_path'   => __DIR__ . '/config.php',
            'services_file_path' => __DIR__ . '/services.php',
        ],
        $c->get('config_factory'),
        $c->get('container_factory'),
        $c->get('composite_container_factory')
    );
};
