<?php

namespace RebelCode\Bookings\Module\FuncTest;

use Dhii\Data\Container\Exception\ContainerException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Bookings\Module\TransitionEventFactory as TestSubject;
use stdClass;
use Xpmock\TestCase;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class TransitionEventFactoryTest extends TestCase
{
    /**
     * The FQN of the test subject.
     */
    const TEST_SUBJECT_FQN = 'RebelCode\Bookings\Module\TransitionEventFactory';

    /**
     * Creates an instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @param array $methods
     *
     * @return MockObject|TestSubject
     */
    public function createInstance(array $methods = [])
    {
        return $this->getMockBuilder(static::TEST_SUBJECT_FQN)
                    ->setMethods($methods)
                    ->getMockForAbstractClass();
    }

    /**
     * Tests the make method to assert whether the created event instance has the correct data as provided in the
     * config.
     *
     * @since [*next-version*]
     */
    public function testMake()
    {
        $subject = $this->createInstance();

        $name       = uniqid('name-');
        $params     = [
            uniqid('param1-') => uniqid('value1-'),
            uniqid('param2-') => uniqid('value2-'),
            uniqid('param3-') => uniqid('value3-'),
        ];
        $target     = new stdClass();
        $transition = uniqid('transition-');

        $event = $subject->make([
            'name'       => $name,
            'params'     => $params,
            'target'     => $target,
            'transition' => $transition,
        ]);

        $this->assertEquals($name, $event->getName(), 'Created event has incorrect name.');
        $this->assertEquals($transition, $event->getTransition(), 'Created event has incorrect transition.');
        $this->assertEquals($params, $event->getParams(), 'Created event has incorrect params.');
        $this->assertEquals($target, $event->getTarget(), 'Created event has incorrect target.');
    }

    /**
     * Tests the make method with only the required config to assert whether the defaults are correctly used for the
     * remaining optional config.
     *
     * @since [*next-version*]
     */
    public function testMakeRequiredOnly()
    {
        $subject = $this->createInstance();

        $name       = uniqid('name-');
        $transition = uniqid('transition-');

        $event = $subject->make([
            'name'       => $name,
            'transition' => $transition,
        ]);

        $this->assertEquals($name, $event->getName(), 'Created event has incorrect name.');
        $this->assertEquals($transition, $event->getTransition(), 'Created event has incorrect transition.');
        $this->assertEquals([], $event->getParams(), 'Created event has incorrect params.');
        $this->assertEquals(null, $event->getTarget(), 'Created event has incorrect target.');
    }

    /**
     * Tests the make method with config that is missing the required event name to assert whether a proper exception
     * is thrown.
     *
     * @since [*next-version*]
     */
    public function testMakeMissingName()
    {
        $this->setExpectedException('Dhii\Factory\Exception\CouldNotMakeExceptionInterface');

        $subject    = $this->createInstance();
        $params     = [
            uniqid('param1-') => uniqid('value1-'),
            uniqid('param2-') => uniqid('value2-'),
            uniqid('param3-') => uniqid('value3-'),
        ];
        $target     = new stdClass();
        $transition = uniqid('transition-');

        $subject->make([
            'params'     => $params,
            'target'     => $target,
            'transition' => $transition,
        ]);
    }

    /**
     * Tests the make method with config that is missing the required transition to assert whether a proper exception
     * is thrown.
     *
     * @since [*next-version*]
     */
    public function testMakeMissingTransition()
    {
        $this->setExpectedException('Dhii\Factory\Exception\CouldNotMakeExceptionInterface');

        $subject = $this->createInstance();
        $name    = uniqid('name-');
        $params  = [
            uniqid('param1-') => uniqid('value1-'),
            uniqid('param2-') => uniqid('value2-'),
            uniqid('param3-') => uniqid('value3-'),
        ];
        $target  = new stdClass();

        $subject->make([
            'name'   => $name,
            'params' => $params,
            'target' => $target,
        ]);
    }

    /**
     * Tests the make method with an empty config to assert whether an appropriate exception is thrown.
     *
     * @since [*next-version*]
     */
    public function testMakeEmptyConfig()
    {
        $this->setExpectedException('Dhii\Factory\Exception\CouldNotMakeExceptionInterface');

        $subject = $this->createInstance();
        $subject->make([]);
    }

    /**
     * Tests the make method with config that contains an invalid transition to assert whether an appropriate
     * exception is thrown.
     *
     * @since [*next-version*]
     */
    public function testMakeInvalidTransition()
    {
        $this->setExpectedException('Dhii\Factory\Exception\CouldNotMakeExceptionInterface');

        $subject = $this->createInstance();
        $subject->make([
            'name'       => uniqid('name-'),
            'transition' => new stdClass(),
        ]);
    }

    /**
     * Tests the make method to assert whether an appropriate factory exception is thrown when a container exception
     * is thrown while reading from the config.
     *
     * @since [*next-version*]
     */
    public function testMakeContainerException()
    {
        $this->setExpectedException('Dhii\Factory\Exception\FactoryExceptionInterface');

        $subject = $this->createInstance(['_containerGet']);
        $subject->method('_containerGet')->willThrowException(
            new ContainerException()
        );

        $name       = uniqid('name-');
        $params     = [
            uniqid('param1-') => uniqid('value1-'),
            uniqid('param2-') => uniqid('value2-'),
            uniqid('param3-') => uniqid('value3-'),
        ];
        $target     = new stdClass();
        $transition = uniqid('transition-');

        $subject->make([
            'name'       => $name,
            'params'     => $params,
            'target'     => $target,
            'transition' => $transition,
        ]);
    }
}
