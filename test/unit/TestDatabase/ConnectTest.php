<?php

/**
 * @group regression
 * @covers Database::connect
 * User: dinies
 * Date: 11/04/16
 * Time: 18.12
 */
class ConnectTest extends AbstractTest
{
    protected $reflector;
    protected $property;

    public function setUp()
    {
        parent::setUp();
        $this->reflectedClass = Database::obtain();
        $this->reflector = new ReflectionClass($this->reflectedClass);
        $this->reflectedClass->close();
        $this->property = $this->reflector->getProperty('instance');
        $this->property->setAccessible(true);
        $this->property->setValue($this->reflectedClass, null);
    }

    public function tearDown()
    {
        $this->reflectedClass = Database::obtain("localhost", "unt_matecat_user", "unt_matecat_user", "unittest_matecat_local");
        $this->reflectedClass->close();
        startConnection();
    }

    /**
     * @group regression
     * @covers Database::connect
     */
    public function test_connect_connected()
    {
        /**
         * @var Database
         */
        $instance_after_reset = $this->reflectedClass->obtain("localhost", "unt_matecat_user", "unt_matecat_user", "unittest_matecat_local");
        $instance_after_reset->connect();
        $connection = $this->reflector->getProperty('connection');
        $connection->setAccessible(true);
        $current_value = $connection->getValue($instance_after_reset);

        $this->assertNotNull($current_value);
        $this->assertTrue($current_value instanceof PDO);
    }

    /**
     * @group regression
     * @covers Database::connect
     */
    public function test_connect_not_connected()
    {
        $this->reflectedClass->obtain("localhost", "unt_matecat_user", "unt_matecat_user", "unittest_matecat_local");
        $connection = $this->reflector->getProperty('connection');
        $connection->setAccessible(true);
        $current_value = $connection->getValue($this->reflectedClass);
        $this->assertNull($current_value);

    }

    /**
     * @group regression
     * @covers Database::connect
     */
    public function test_connect_different_hash_between_two_PDO_objects()
    {
        /**
         * @var Database
         */
        $instance_after_first_reset = $this->reflectedClass->obtain("localhost", "unt_matecat_user", "unt_matecat_user", "unittest_matecat_local");
        $instance_after_first_reset->connect();
        $connection = $this->reflector->getProperty('connection');
        $connection->setAccessible(true);
        $current_value_first_PDO = $connection->getValue($instance_after_first_reset);
        $hash_first_PDO = spl_object_hash($current_value_first_PDO);
        $instance_after_first_reset->close();
        $this->property->setValue($instance_after_first_reset, null);
        /**
         * @var Database
         */
        $instance_after_second_reset = $instance_after_first_reset->obtain("localhost", "unt_matecat_user", "unt_matecat_user", "unittest_matecat_local");
        $instance_after_second_reset->connect();
        $current_value_second_PDO = $connection->getValue($instance_after_second_reset);
        $hash_second_PDO = spl_object_hash($current_value_second_PDO);

        $this->assertNotEquals($hash_first_PDO, $hash_second_PDO);
    }

}