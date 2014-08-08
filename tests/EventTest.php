<?php

use PagerDuty\Event;

class EventTest extends PHPUnit_Framework_TestCase {

    public function testGettersSetters() {
        $evt = new Event();

        $expected = 'PagerDuty\Event';
        $key      = '12345678901234567890123456789012';
        $this->assertInstanceOf( $expected, $evt->setServiceKey( $key ) );
        $this->assertEquals( $key, $evt->getServiceKey() );

        $this->assertInstanceOf( $expected, $evt->setDescription( 'desc' ) );
        $this->assertEquals( 'desc', $evt->getDescription() );

        $this->assertInstanceOf( $expected, $evt->setIncidentKey( 'ikey' ) );
        $this->assertEquals( 'ikey', $evt->getIncidentKey() );

        $this->assertInstanceOf( $expected, $evt->setClient( 'client' ) );
        $this->assertEquals( 'client', $evt->getClient() );

        $this->assertInstanceOf( $expected, $evt->setClientUrl( 'url' ) );
        $this->assertEquals( 'url', $evt->getClientUrl() );

        $this->assertInstanceOf( $expected, $evt->setDetails( array() ) );
        $this->assertEquals( array(), $evt->getDetails() );

        $this->assertInstanceOf( $expected, $evt->setDetail( 'detail', 1 ) );
        $this->assertEquals( array( 'detail' => 1 ), $evt->getDetails() );
        $this->assertEquals( null, $evt->getDetail( 'newkey' ) );
        $this->assertEquals( 1, $evt->getDetail( 'detail' ) );

        $this->assertInstanceOf( $expected, $evt->setApiUrl( 'test' ) );
        $this->assertEquals( 'test', $evt->getApiUrl() );

        $this->assertInstanceOf( $expected, $evt->setTimeout( 1 ) );
        $this->assertEquals( 1, $evt->getTimeout() );
    }

    public function testToArray() {
        $initObj = array(
            "service_key"  => "12345678901234567890123456789012",
            "incident_key" => "testKey"
        );

        $evt = new Event($initObj);
        $this->assertEquals( $initObj, $evt->toArray() );

        $evt->setDescription( 'desc' );
        $evt->setIncidentKey( null );

        $initObj['description'] = 'desc';
        unset($initObj['incident_key']);

        $this->assertEquals( $initObj, $evt->toArray() );
    }

    /**
     * @expectedException PagerDuty\EventException
     */
    public function testShortServiceKeyException() {
        $evt = new Event();
        $evt->setServiceKey( 'a' );
    }

    /**
     * @expectedException PagerDuty\EventException
     */
    public function testTriggerServiceKeyException() {
        $evt = new Event();
        $evt->trigger();
    }

    /**
     * @expectedException PagerDuty\EventException
     */
    public function testTriggerDescriptionException() {
        $evt = new Event();
        $evt->setServiceKey( '12345678901234567890123456789012' );
        $evt->trigger();
    }

    public function testTriggerSendHit() {
        $evt = new Event();
        $evt->setApiUrl( 'test' );
        $evt->setServiceKey( '12345678901234567890123456789012' );
        $evt->setDescription( 'desc' );
        $evt->trigger();
    }

    /**
     * @expectedException PagerDuty\EventException
     */
    public function testAcknowledgeServiceKeyException() {
        $evt = new Event();
        $evt->acknowledge();
    }

    /**
     * @expectedException PagerDuty\EventException
     */
    public function testAcknowledgeIncidentException() {
        $evt = new Event();
        $evt->setServiceKey( '12345678901234567890123456789012' );
        $evt->acknowledge();
    }

    public function testAcknowledgeSendHit() {
        $evt = new Event();
        $evt->setApiUrl( 'test' );
        $evt->setServiceKey( '12345678901234567890123456789012' );
        $evt->setIncidentKey( 'ikey' );
        $evt->acknowledge();
    }

    /**
     * @expectedException PagerDuty\EventException
     */
    public function testResolveServiceKeyException() {
        $evt = new Event();
        $evt->resolve();
    }

    /**
     * @expectedException PagerDuty\EventException
     */
    public function testResolveIncidentException() {
        $evt = new Event();
        $evt->setServiceKey( '12345678901234567890123456789012' );
        $evt->resolve();
    }

    public function testResolveSendHit() {
        $evt = new Event();
        $evt->setApiUrl( 'test' );
        $evt->setServiceKey( '12345678901234567890123456789012' );
        $evt->setIncidentKey( 'ikey' );
        $evt->resolve();
    }

}
