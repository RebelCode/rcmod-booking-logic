<?php

return [
    'booking_logic' => [
        /*
         * A map of booking statuses as keys to transition maps as values.
         * The transition maps should have transitions as keys mapping to destination booking statuses as values.
         *
         * @since [*next-version*]
         */
        'booking_status_transitions' => [],

        /*
         * Configuration for the event state machine service.
         *
         * @since [*next-version*]
         */
        'booking_event_state_machine' => [
            /*
             * The sprintf-style format for the name of the events triggered by the event state machine.
             *
             * @since [*next-version*]
             */
            'event_name_format' => 'booking_transition',
        ],
    ],
];
