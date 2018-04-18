<?php

namespace RebelCode\Bookings\Module;

use Dhii\Data\Container\ContainerFactoryInterface;
use Dhii\Exception\InternalException;
use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\ContainerInterface;
use RebelCode\Bookings\BookingFactory;
use RebelCode\Bookings\BookingInterface;
use RebelCode\Bookings\FactoryStateMachineTransitioner;
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
        $this->_initModule($key, $dependencies, $containerFactory, $configFactory, $compContainerFactory);
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
                'booking_factory'                => function (ContainerInterface $c) {
                    return new BookingFactory();
                },
                'booking_transitioner'           => function (ContainerInterface $c) {
                    return new FactoryStateMachineTransitioner(
                        $c->get('booking_state_machine_provider'),
                        $c->get('booking_factory')
                    );
                },
                'booking_state_machine_provider' => function (ContainerInterface $c) {
                    return function (BookingInterface $booking, $transition) use ($c) {
                        return $c->get('booking_state_machine_factory')->make(
                            [
                                'event_manager'     => $c->get('event_manager'),
                                'initial_state'     => $booking->getStatus(),
                                'transitions'       => $c->get('booking_logic/booking_status_transitions'),
                                'event_name_format' => $c->get('booking_logic/booking_event_state_machine/event_name_format'),
                                'target'            => $booking,
                            ]
                        );
                    };
                },
                'booking_state_machine_factory'  => function (ContainerInterface $c) {
                    return new EventStateMachineFactory();
                },
                'session-generator-factory'      => function (ContainerInterface $c) {
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
