<?php

namespace RebelCode\Bookings\Module\UnitTest;

use Dhii\Config\ConfigFactoryInterface;
use Dhii\Data\Container\ContainerFactoryInterface;
use Dhii\Data\Container\ContainerGetCapableTrait;
use Dhii\Data\Container\ContainerHasCapableTrait;
use Dhii\Data\Container\ContainerListGetCapableTrait;
use Dhii\Data\Container\ContainerListHasCapableTrait;
use Dhii\Data\Container\CreateContainerExceptionCapableTrait;
use Dhii\Data\Container\CreateNotFoundExceptionCapableTrait;
use Dhii\Data\Container\NormalizeKeyCapableTrait;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Exception\CreateOutOfRangeExceptionCapableTrait;
use Dhii\Exception\InternalException;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Modular\Module\ModuleInterface;
use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Exception;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Container\ContainerInterface;
use RebelCode\Bookings\Module\BookingLogicModule as TestSubject;
use ReflectionClass;
use ReflectionException;
use Xpmock\TestCase;

/**
 * Unit tests the module.
 *
 * @since [*next-version*]
 */
class BookingLogicModuleTest extends TestCase
{
    /* @since [*next-version*] */
    use ContainerListGetCapableTrait;

    /* @since [*next-version*] */
    use ContainerListHasCapableTrait;

    /* @since [*next-version*] */
    use ContainerGetCapableTrait;

    /* @since [*next-version*] */
    use ContainerHasCapableTrait;

    /* @since [*next-version*] */
    use NormalizeKeyCapableTrait;

    /* @since [*next-version*] */
    use NormalizeStringCapableTrait;

    use NormalizeIterableCapableTrait;

    /* @since [*next-version*] */
    use CreateNotFoundExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateContainerExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateOutOfRangeExceptionCapableTrait;

    /* @since [*next-version*] */
    use StringTranslatingTrait;

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
     * Creates a mock container instance.
     *
     * @since [*next-version*]
     *
     * @param array $data The data for the container.
     *
     * @return MockObject|ContainerInterface
     */
    protected function createContainer(array $data)
    {
        $mock = $this->getMockBuilder('Psr\Container\ContainerInterface')
                     ->setMethods(['get', 'has'])
                     ->getMockForAbstractClass();

        $mock->method('get')->willReturnCallback(function ($key) use ($data) {
            return $this->_containerGet($data, $key);
        });

        $mock->method('has')->willReturnCallback(function ($key) use ($data) {
            return $this->_containerHas($data, $key);
        });

        return $mock;
    }

    /**
     * Creates a mock composite container instance.
     *
     * @since [*next-version*]
     *
     * @param array $containers The containers to include in the composite container.
     *
     * @return MockObject|ContainerInterface
     */
    protected function createCompositeContainer(array $containers)
    {
        $mock = $this->getMockBuilder('Psr\Container\ContainerInterface')
                     ->setMethods(['get', 'has'])
                     ->getMockForAbstractClass();

        $mock->method('get')->willReturnCallback(function ($key) use ($containers) {
            return $this->_containerListGet($key, $containers);
        });

        $mock->method('has')->willReturnCallback(function ($key) use ($containers) {
            return $this->_containerListHas($key, $containers);
        });

        return $mock;
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
     *
     * @throws InternalException If the module failed to setup.
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

        // Load the module file for the file path constants, without saving the module callback
        require RCMOD_BOOKING_LOGIC_MODULE_FILE;

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

    /**
     * Tests the module loading, setup and execution to assert whether the module can run without problems.
     *
     * @since [*next-version*]
     *
     * @throws Exception
     */
    public function testModule()
    {
        // A mock for the container that should be provided by whatever loads the module
        $loaderContainer = $this->createContainer([
            'config_factory'              => $this->createConfigFactory(),
            'container_factory'           => $this->createContainerFactory(),
            'composite_container_factory' => $this->createCompositeContainerFactory(),
        ]);

        /* Load module main file and get module callback */
        /* @var $module ModuleInterface */
        $callback = require RCMOD_BOOKING_LOGIC_MODULE_FILE;
        $module   = call_user_func_array($callback, [$loaderContainer]);

        // Setup module to get container
        $moduleContainer = $module->setup();

        // Mock container merging
        $container = $this->createCompositeContainer([
            $moduleContainer,
            $loaderContainer,
        ]);

        try {
            // Run module
            $module->run($container);
        } catch (Exception $exception) {
            $this->fail('Module failed to run without exceptions.');

            throw $exception;
        }
    }
}
