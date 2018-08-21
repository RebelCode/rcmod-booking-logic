<?php

use Dhii\Collection\CountableMapFactory;
use Psr\Container\ContainerInterface;
use RebelCode\Bookings\BookingTransitioner;
use RebelCode\Bookings\Module\TransitionEventFactory;
use RebelCode\Bookings\StateAwareBookingFactory;
use RebelCode\State\EventStateMachineFactory;

return [
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
            $c->get('booking_logic/transition_event_format')
        );
    },
    /**
     * The factory for booking transitioner state machine to be able to create transition events.
     *
     * @since [*next-version*]
     */
    'booking_transition_event_factory'           => function (ContainerInterface $c) {
        return new TransitionEventFactory();
    },
];
