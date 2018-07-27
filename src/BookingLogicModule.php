<?php

namespace RebelCode\Bookings\Module;

use Dhii\Collection\CountableMapFactory;
use Dhii\Data\Container\ContainerFactoryInterface;
use Dhii\Exception\InternalException;
use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\ContainerInterface;
use RebelCode\Bookings\BookingFactory;
use RebelCode\Bookings\FactoryStateMachineTransitioner;
use RebelCode\Bookings\StateAwareBookingFactory;
use RebelCode\Modular\Module\AbstractBaseModule;
use RebelCode\Sessions\SessionGeneratorFactory;
use RebelCode\State\EventStateMachineFactory;

/**
 * Module class for the booking logic module.
 *
 * @since [*next-version*]
 */
class BookingLogicModule extends AbstractBaseModule
{
    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable         $key                  The module key.
     * @param string[]|Stringable[]     $dependencies         The module dependencies.
     * @param ContainerFactoryInterface $configFactory        The config factory.
     * @param ContainerFactoryInterface $containerFactory     The container factory.
     * @param ContainerFactoryInterface $compContainerFactory The composite container factory.
     */
    public function __construct(
        $key,
        $dependencies,
        $configFactory,
        $containerFactory,
        $compContainerFactory
    ) {
        $this->_initModule($key, $dependencies, $configFactory, $containerFactory, $compContainerFactory);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @throws InternalException
     */
    public function setup()
    {
        return $this->_setupContainer(
            $this->_loadPhpConfigFile(RC_BOOKING_LOGIC_MODULE_CONFIG),
            [
                'booking_factory'                            => function (ContainerInterface $c) {
                    return new StateAwareBookingFactory(
                        $c->get('map_factory')
                    );
                },
                'map_factory'                                => function (ContainerInterface $c) {
                    return new CountableMapFactory();
                },
                'booking_transitioner'          => function (ContainerInterface $c) {
                    return new FactoryStateMachineTransitioner(
                        $c->get('booking_state_machine_provider'),
                        $c->get('booking_factory')
                    );
                },
                'booking_state_machine_factory' => function (ContainerInterface $c) {
                    return new EventStateMachineFactory();
                },
                'session-generator-factory'     => function (ContainerInterface $c) {
                    return new SessionGeneratorFactory();
                },
            ]
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
