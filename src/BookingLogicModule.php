<?php

namespace RebelCode\Bookings\Module;

use Dhii\Collection\CountableMapFactory;
use Dhii\Data\Container\ContainerFactoryInterface;
use Dhii\Exception\InternalException;
use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\ContainerInterface;
use RebelCode\Bookings\BookingFactory;
use RebelCode\Bookings\BookingTransitioner;
use RebelCode\Bookings\FactoryStateMachineTransitioner;
use RebelCode\Bookings\StateAwareBookingFactory;
use RebelCode\Modular\Module\AbstractBaseModule;
use RebelCode\Sessions\SessionGeneratorFactory;
use RebelCode\State\EventStateMachine;
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
                'booking_transitioner'                       => function (ContainerInterface $c) {
                    return new BookingTransitioner(
                        $c->get('booking_logic/state_machine/status_transitions'),
                        $c->get('booking_transitioner_state_machine_factory'),
                        $c->get('booking_factory')
                    );
                },
                'booking_transitioner_state_machine_factory' => function (ContainerInterface $c) {
                    return new EventStateMachineFactory(
                        $c->get('event_manager'),
                        $c->get('event_factory'),
                        $c->get('booking_logic/state_machine/transition_event_format')
                    );
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
