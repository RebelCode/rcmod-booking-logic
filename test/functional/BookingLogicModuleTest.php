<?php

namespace RebelCode\Bookings\Module\FuncTest;

use Dhii\Config\ConfigFactoryInterface;
use Dhii\Data\Container\ContainerFactoryInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Bookings\Module\BookingLogicModule as TestSubject;
use ReflectionClass;
use ReflectionException;
use Xpmock\TestCase;

/**
 * Functionally tests the module class.
 *
 * @since [*next-version*]
 */
class BookingLogicModuleTest extends TestCase
{
    /**
     * The test subject FQN.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_FQN = 'RebelCode\Bookings\Module\BookingLogicModule';

    /**
     * Creates the test subject instance.
     *
     * @since [*next-version*]
     *
     * @param array $methods The methods to mock.
     *
     * @return MockObject|TestSubject
     */
    protected function createTestSubject(array $methods = [])
    {
        $builder = $this->getMockBuilder(static::TEST_SUBJECT_FQN)
                        ->setMethods($methods)
                        ->disableOriginalConstructor();

        return $builder->getMockForAbstractClass();
    }

    /**
     * Creates a mock config factory instance.
     *
     * @since [*next-version*]
     *
     * @return MockObject|ConfigFactoryInterface
     */
    protected function createConfigFactory()
    {
        return $this->getMockBuilder('Dhii\Config\ConfigFactoryInterface')
                    ->setMethods(['make'])
                    ->getMockForAbstractClass();
    }

    /**
     * Creates a mock container factory instance.
     *
     * @since [*next-version*]
     *
     * @return MockObject|ContainerFactoryInterface
     */
    protected function createContainerFactory()
    {
        return $this->getMockBuilder('Dhii\Data\Container\ContainerFactoryInterface')
                    ->setMethods(['make'])
                    ->getMockForAbstractClass();
    }

    /**
     * Creates a mock composite container factory instance.
     *
     * @since [*next-version*]
     *
     * @return MockObject|ContainerFactoryInterface
     */
    protected function createCompositeContainerFactory()
    {
        return $this->getMockBuilder('Dhii\Data\Container\ContainerFactoryInterface')
                    ->setMethods(['make'])
                    ->getMockForAbstractClass();
    }

    /**
     * Tests the constructor to assert whether the internal initialization method is invoked.
     *
     * @since [*next-version*]
     *
     * @throws ReflectionException If an error occurred while reflecting the test subject.
     */
    public function testConstructor()
    {
        // Constructor args
        $key     = uniqid('key-');
        $deps    = [
            uniqid('dep1-'),
            uniqid('dep2-'),
            uniqid('dep3-'),
        ];
        $cnfgFac = $this->createConfigFactory();
        $cntrFac = $this->createContainerFactory();
        $compFac = $this->createCompositeContainerFactory();

        // Create test subject
        $subject = $this->createTestSubject(['_initModule']);

        // Expect internal initialization method to be called
        $subject->expects($this->once())
                ->method('_initModule')
                ->with($key, $deps, $cnfgFac, $cntrFac, $compFac);

        // Call constructor
        $reflect = new ReflectionClass($subject);
        $reflect->getConstructor()->invoke($subject, $key, $deps, $cnfgFac, $cntrFac, $compFac);
    }

    /**
     * Tests the setup method to assert whether the config and services are correctly read and returned in a container.
     *
     * @since [*next-version*]
     */
    public function testSetup()
    {
        // Constructor args
        $key     = uniqid('key-');
        $deps    = [
            uniqid('dep1-'),
            uniqid('dep2-'),
            uniqid('dep3-'),
        ];
        $cnfgFac = $this->createConfigFactory();
        $cntrFac = $this->createContainerFactory();
        $compFac = $this->createCompositeContainerFactory();

        // Mock config and services
        $config   = [
            uniqid('config1-') => [
                uniqid('sub-config1-') => uniqid('value1-'),
                uniqid('sub-config2-') => uniqid('value2-'),
            ],
            uniqid('config2-') => uniqid('value3-'),
        ];
        $services = [
            uniqid('service1-') => function () {
            },
            uniqid('service2-') => function () {
            },
            uniqid('service3-') => function () {
            },
        ];
        // Mock return container
        $container = $this->getMockBuilder('Dhii\Data\Container\ContainerInterface')
                          ->getMockForAbstractClass();

        // Create test subject
        $subject = $this->createTestSubject(['_loadPhpConfigFile', '_setupContainer']);
        $reflect = $this->reflect($subject);
        // Initialize it
        $reflect->_initModule($key, $deps, $cnfgFac, $cntrFac, $compFac);

        // Mock config and services file path constants
        define('RC_BOOKING_LOGIC_MODULE_CONFIG', uniqid('config-file-'));
        define('RC_BOOKING_LOGIC_MODULE_SERVICES', uniqid('services-file-'));

        // Expect and mock the loading of the config and services files
        $subject->expects($this->exactly(2))
                ->method('_loadPhpConfigFile')
                ->withConsecutive([RC_BOOKING_LOGIC_MODULE_CONFIG], [RC_BOOKING_LOGIC_MODULE_SERVICES])
                ->willReturnOnConsecutiveCalls($config, $services);
        // Expect the container to be set up
        $subject->expects($this->once())
                ->method('_setupContainer')
                ->with($config, $services)
                ->willReturn($container);

        // Run setup
        $subject->setup();
    }
}
