<?php

use Psr\Container\ContainerInterface;
use RebelCode\Bookings\Module\BookingLogicModule;

// Define constants only once
if (!defined('RC_BOOKING_LOGIC_MODULE_KEY')) {
    define('RC_BOOKING_LOGIC_MODULE_KEY', 'booking_logic');
    define('RC_BOOKING_LOGIC_MODULE_DIR', __DIR__);
    define('RC_BOOKING_LOGIC_MODULE_CONFIG', __DIR__ . DIRECTORY_SEPARATOR . 'config.php');
    define('RC_BOOKING_LOGIC_MODULE_SERVICES', __DIR__ . DIRECTORY_SEPARATOR . 'services.php');
}

return function(ContainerInterface $c) {
    return new BookingLogicModule(
        RC_BOOKING_LOGIC_MODULE_KEY,
        [],
        $c->get('config_factory'),
        $c->get('container_factory'),
        $c->get('composite_container_factory')
    );
};
