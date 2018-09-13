<?php

namespace RebelCode\Bookings\Module\FuncTest;

use Dhii\Modular\Module\ModuleInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Bookings\Module\BookingLogicModule;
use RebelCode\Modular\Testing\ModuleTestCase;

/**
 * Tests the booking logic module.
 *
 * @see   BookingLogicModule
 *
 * @since [*next-version*]
 */
class BookingLogicModuleTest extends ModuleTestCase
{
    /**
     * Returns the path to the module main file.
     *
     * @since [*next-version*]
     *
     * @return string The file path.
     */
    public function getModuleFilePath()
    {
        return __DIR__ . '/../../module.php';
    }

    /**
     * Tests the `setup()` method to assert whether the resulting container contains the config.
     *
     * @since [*next-version*]
     */
    public function testSetupConfig()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasConfig(
            'booking_logic',
            [
                'booking_status_transitions'  => [],
                'booking_event_state_machine' => [
                    'event_name_format' => 'booking_transition',
                ],
            ],
            $module
        );
    }

    /**
     * Tests the `booking_factory` service to assert if it can be retrieved from the container and if its type is
     * correct.
     *
     * @since [*next-version*]
     */
    public function testSetupBookingFactory()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService('booking_factory', 'Dhii\Data\StateAwareFactoryInterface', $module, [
            /* Add mocked dependency services here */
        ]);
    }

    /**
     * Tests the `map_factory` service to assert if it can be retrieved from the container and if its type is correct.
     *
     * @since [*next-version*]
     */
    public function testSetupMapFactory()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService('map_factory', 'Dhii\Collection\MapFactoryInterface', $module, [
            /* Add mocked dependency services here */
        ]);
    }

    /**
     * Tests the `booking_transitioner` service to assert if it can be retrieved from the container and if its type is
     * correct.
     *
     * @since [*next-version*]
     */
    public function testSetupBookingTransitioner()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService('booking_transitioner', 'Dhii\Data\TransitionerInterface', $module, [
            'booking_logic/status_transitions'      => [],
            'booking_logic/transition_event_format' => '',
            'event_manager'                         => $this->mockEventManager(),
        ]);
    }

    /**
     * Tests the `booking_transitioner_state_machine_factory` service to assert if it can be retrieved from the
     * container and if its type is correct.
     *
     * @since [*next-version*]
     */
    public function testSetupBookingTransitionerStateMachineFactory()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService(
            'booking_transitioner_state_machine_factory',
            'Dhii\State\StateMachineFactoryInterface',
            $module,
            [
                'booking_logic/status_transitions'      => [],
                'booking_logic/transition_event_format' => '',
                'event_manager'                         => $this->mockEventManager(),
            ]
        );
    }

    /**
     * Tests the `booking_transition_event_factory` service to assert if it can be retrieved from the container and if
     * its type is correct.
     *
     * @since [*next-version*]
     */
    public function testSetupBookingTransitionEventFactory()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService('booking_transition_event_factory', 'Dhii\Event\EventFactoryInterface', $module, [
            /* Add mocked dependency services here */
        ]);
    }
}
