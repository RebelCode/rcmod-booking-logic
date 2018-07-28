<?php

namespace RebelCode\Bookings\Module;

use Dhii\Collection\CountableMapFactory;
use Dhii\Data\Container\ContainerFactoryInterface;
use Dhii\Exception\InternalException;
use Dhii\Factory\GenericCallbackFactory;
use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\ContainerInterface;
use RebelCode\Bookings\BookingTransitioner;
use RebelCode\Bookings\StateAwareBookingFactory;
use RebelCode\Modular\Module\AbstractBaseModule;
use RebelCode\State\EventStateMachineFactory;
use RebelCode\State\TransitionEvent;

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
                /**
                 * Factory for creating bookings; specifically, state-aware bookings.
                 *
                 * @since [*next-version*]
                 */
                'booking_factory'                            => function (ContainerInterface $c) {
                    return new StateAwareBookingFactory(
                        $c->get('map_factory')
                    );
                },
                /**
                 * Factory for creating maps.
                 *
                 * @since [*next-version*]
                 */
                'map_factory'                                => function (ContainerInterface $c) {
                    return new CountableMapFactory();
                },
                /**
                 * The booking transitioner instance.
                 *
                 * @since [*next-version*]
                 */
                'booking_transitioner'                       => function (ContainerInterface $c) {
                    return new BookingTransitioner(
                        $c->get('booking_logic/status_transitions'),
                        $c->get('booking_transitioner_state_machine_factory'),
                        $c->get('booking_factory')
                    );
                },
                /**
                 * The factory for creating state machines for the booking transitioner during transitions.
                 *
                 * @since [*next-version*]
                 */
                'booking_transitioner_state_machine_factory' => function (ContainerInterface $c) {
                    return new EventStateMachineFactory(
                        $c->get('event_manager'),
                        $c->get('booking_transition_event_factory'),
                        $c->get('booking_logic/state_machine/transition_event_format')
                    );
                },
                /**
                 * The factory for booking transitioner state machine to be able to create transition events.
                 *
                 * @since [*next-version*]
                 */
                'booking_transition_event_factory'           => function (ContainerInterface $c) {
                    return new GenericCallbackFactory(function ($config) {
                        $name       = $this->_containerGet($config, 'name');
                        $transition = $this->_containerGet($config, 'transition');

                        $target = $this->_containerHas($config, 'target')
                            ? $this->_containerGet($config, 'target')
                            : null;
                        $params = $this->_containerHas($config, 'params')
                            ? $this->_containerGet($config, 'params')
                            : null;

                        return new TransitionEvent($name, $transition, $target, $params);
                    });
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
