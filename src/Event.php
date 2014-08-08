<?php

namespace PagerDuty;

class EventException extends \Exception {
}

/**
 * Class Event
 * A class for interacting with the pagerduty events api:
 * http://developer.pagerduty.com/documentation/integration/events
 * @package PagerDuty
 */
class Event {
    const API_URL = 'https://events.pagerduty.com/generic/2010-04-15/create_event.json';

    private $_apiUrl = null; //allows overriding of the API_URL
    private $_timeout = 10; //seconds
    private $_eventAry = array(
        'service_key'  => null,
        'description'  => null,
        'incident_key' => null,
        'client'       => null,
        'client_url'   => null,
        'details'      => null
    );

    public function __construct( $ary = null ) {
        if( is_null( $ary ) ) {
            return;
        }
        if( !is_array( $ary ) ) {
            throw new EventException("Expected an array");
        }

        foreach( array_keys( $this->_eventAry ) as $k ) {
            if( isset($ary[$k]) ) {
                $this->_eventAry[$k] = $ary[$k];
            }
        }
    }

    protected function _get( $key, $def = null ) {
        return isset($this->_eventAry[$key]) ? $this->_eventAry[$key] : $def;
    }

    protected function _set( $key, $val ) {
        $this->_eventAry[$key] = $val;
        return $this;
    }

    /**
     * Required for all events
     * The GUID of one of your "Generic API" services. This is the "service
     * key" listed on a Generic API's service detail page.
     * @return null|String
     */
    public function getServiceKey() {
        return $this->_get( 'service_key' );
    }

    /**
     * Required for all events
     * The GUID of one of your "Generic API" services. This is the "service
     * key" listed on a Generic API's service detail page.
     * @param string $key
     * @return $this
     * @throws EventException
     */
    public function setServiceKey( $key ) {
        if( !is_string( $key ) || strlen( $key ) !== 32 ) {
            throw new EventException("Service key must be a 32 character GUID string");
        }
        return $this->_set( 'service_key', $key );
    }

    /**
     * Required for trigger
     * A short description of the problem that led to this event. This field
     * (or a truncated version) will be used when generating phone calls, SMS
     * messages and alert emails. It will also appear on the incidents tables
     * in the PagerDuty UI. The maximum length is 1024 characters.
     * @return null|string
     */
    public function getDescription() {
        return $this->_get( 'description' );
    }

    /**
     * Required for trigger
     * A short description of the problem that led to this event. This field
     * (or a truncated version) will be used when generating phone calls, SMS
     * messages and alert emails. It will also appear on the incidents tables
     * in the PagerDuty UI. The maximum length is 1024 characters.
     * @param string $description
     * @return $this
     * @throws EventException
     */
    public function setDescription( $description ) {
        if( !is_string( $description ) || strlen( $description ) > 1024 ) {
            throw new EventException("Description must be a string that is shorter than 1024 characters");
        }
        return $this->_set( 'description', $description );
    }

    /**
     * Required for acknowledge and resolve. Optional for trigger.
     * Identifies the incident to we are working with. If there's no open
     * (i.e. unresolved) incident with this key, a new one will be created. If
     * there's already an open incident with a matching key, this event will be
     * appended to that incident's log. The event key provides an easy way to
     * "de-dup" problem reports. If this field isn't provided, PagerDuty will
     * automatically open a new incident with a unique key.
     * @return null|string
     */
    public function getIncidentKey() {
        return $this->_get( 'incident_key' );
    }

    /**
     * Required for acknowledge and resolve. Optional for trigger.
     * Identifies the incident to we are working with. If there's no open
     * (i.e. unresolved) incident with this key, a new one will be created. If
     * there's already an open incident with a matching key, this event will be
     * appended to that incident's log. The event key provides an easy way to
     * "de-dup" problem reports. If this field isn't provided, PagerDuty will
     * automatically open a new incident with a unique key.
     * @param $key
     * @return $this
     */
    public function setIncidentKey( $key ) {
        return $this->_set( 'incident_key', $key );
    }

    /**
     * Only relevant to trigger [optional]
     * The name of the monitoring client that is triggering this event.
     * Text used for the link of the triggered event
     * @return null|string
     */
    public function getClient() {
        return $this->_get( 'client' );
    }

    /**
     * Only relevant to trigger [optional]
     * The name of the monitoring client that is triggering this event.
     * Text used for the link of the triggered event
     * @param $client
     * @return $this
     */
    public function setClient( $client ) {
        return $this->_set( 'client', $client );
    }

    /**
     * Only relevant to trigger [optional]
     * The URL of the monitoring client that is triggering this event.
     * URL that is linked to in the triggered event
     * @return null|string
     */
    public function getClientUrl() {
        return $this->_get( 'client_url' );
    }

    /**
     * Only relevant to trigger [optional]
     * The URL of the monitoring client that is triggering this event.
     * URL that is linked to in the triggered event
     * @param $url
     * @return $this
     */
    public function setClientUrl( $url ) {
        return $this->_set( 'client_url', $url );
    }

    /**
     * Optional
     * An arbitrary JSON object containing any data you'd like included in the
     * incident log.
     * @return null|array
     */
    public function getDetails() {
        return $this->_get( 'details', array() );
    }

    /**
     * Optional
     * An arbitrary JSON object containing any data you'd like included in the
     * incident log.
     * @param $details
     * @return $this
     */
    public function setDetails( $details ) {
        return $this->_set( 'details', $details );
    }

    /**
     * Convenience method for interacting with the details array/object
     * @param string $key
     * @return mixed
     */
    public function getDetail( $key ) {
        $details = $this->getDetails();
        if( isset($details[$key]) ) {
            return $details[$key];
        }
        return null;
    }

    /**
     * Convenience method for interacting with the details array/object
     * @param string $key
     * @param mixed $val
     * @return $this
     */
    public function setDetail( $key, $val ) {
        $details       = $this->getDetails();
        $details[$key] = $val;
        return $this->setDetails( $details );
    }

    /**
     * Returns the trigger api url
     * @return string
     */
    public function getApiUrl() {
        return is_null( $this->_apiUrl ) ? self::API_URL : $this->_apiUrl;
    }

    /**
     * Convenience method that allow you to override the default api url
     * @param string $url
     * @return $this
     */
    public function setApiUrl( $url ) {
        $this->_apiUrl = $url;
        return $this;
    }

    /**
     * Request timeout period defined in seconds, 0 for no timeout
     * @return int
     */
    public function getTimeout() {
        return $this->_timeout;
    }

    /**
     * Request timeout period defined in seconds, 0 for no timeout
     * @param int $timeout
     * @return $this
     */
    public function setTimeout( $timeout ) {
        $this->_timeout = $timeout;
        return $this;
    }

    /**
     * returns the minimal array needed to express the event to the pagerduty
     * api
     * @param string|null $eventType
     * @return array
     */
    public function toArray( $eventType = null ) {
        $data = array(
            'service_key' => $this->getServiceKey()
        );
        if( $eventType ) {
            $data['event_type'] = $eventType;
        }
        if( $this->getDescription() ) {
            $data['description'] = $this->getDescription();
        }
        if( $this->getIncidentKey() ) {
            $data['incident_key'] = $this->getIncidentKey();
        }
        if( count( $this->getDetails() ) > 0 ) {
            $data['details'] = $this->getDetails();
        }

        if( $eventType === 'trigger' ) {
            if( $this->getClient() ) {
                $data['client'] = $this->getClient();
            }
            if( $this->getClientUrl() ) {
                $data['client_url'] = $this->getClientUrl();
            }
        }

        return $data;
    }

    /**
     * @param array $data
     * @return array
     * @throws EventException
     */
    protected function _send( $data ) {
        if( $this->getApiUrl() === 'test' ) {
            return array();
        }

        $header = array(
            'Content-type' => 'application/json'
        );
        $handle = curl_init();
        curl_setopt( $handle, CURLOPT_URL, $this->getApiUrl() );
        curl_setopt( $handle, CURLOPT_POST, true );
        curl_setopt( $handle, CURLOPT_HTTPHEADER, $header );
        curl_setopt( $handle, CURLOPT_POSTFIELDS, json_encode( $data ) );
        curl_setopt( $handle, CURLOPT_HEADER, true );
        curl_setopt( $handle, CURLOPT_RETURNTRANSFER, true );

        $timeout = $this->getTimeout();
        if( $timeout ) {
            curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, $timeout );
            curl_setopt( $handle, CURLOPT_TIMEOUT, $timeout );
        }

        $resp   = curl_exec( $handle );
        $errNum = curl_errno( $handle );
        $error  = curl_error( $handle );

        $headerLength = curl_getinfo( $handle, CURLINFO_HEADER_SIZE );
        $httpCode     = curl_getinfo( $handle, CURLINFO_HTTP_CODE );
        curl_close( $handle );

        if( $errNum ) {
            throw new EventException($error);
        }
        // If HTTP response is not 200, throw exception
        if( $httpCode !== 200 ) {
            throw new EventException('Unexpected HTTP response code: ' . $httpCode);
        }

        $jsonStr = substr( $resp, $headerLength );
        $json    = json_decode( $jsonStr, true );
        if( !$json ) {
            throw new EventException('Invalid json response format: ' . $jsonStr);
        }
        if( isset($json['incident_key']) ) {
            $this->setIncidentKey( $json['incident_key'] );
        }

        return $json;
    }

    /**
     * Trigger a new event or update an existing event
     * Example Response:
     * {
     *     "status": "success",
     *     "message": "Event processed",
     *     "incident_key": "srv01/HTTP"
     * }
     * @return array
     * @throws EventException
     */
    public function trigger() {
        if( !$this->getServiceKey() ) {
            throw new EventException("A service_key must be provided before triggering an event");
        }
        if( !$this->getDescription() ) {
            throw new EventException("A description must be provided before triggering an event");
        }

        return $this->_send( $this->toArray( 'trigger' ) );
    }

    /**
     * Acknowledge an existing event
     * Example Response:
     * {
     *     "status": "success",
     *     "message": "Event processed",
     *     "incident_key": "srv01/HTTP"
     * }
     * @return array
     * @throws EventException
     */
    public function acknowledge() {
        if( !$this->getServiceKey() ) {
            throw new EventException("A service_key must be provided before acknowledging an event");
        }
        if( !$this->getIncidentKey() ) {
            throw new EventException("An incident_key must be provided before acknowledging an event");
        }

        return $this->_send( $this->toArray( 'acknowledge' ) );
    }

    /**
     * Resolve an existing event
     * Example Response:
     * {
     *     "status": "success",
     *     "message": "Event processed",
     *     "incident_key": "srv01/HTTP"
     * }
     * @return array
     * @throws EventException
     */
    public function resolve() {
        if( !$this->getServiceKey() ) {
            throw new EventException("A service_key must be provided before resolving an event");
        }
        if( !$this->getIncidentKey() ) {
            throw new EventException("An incident_key must be provided before resolving an event");
        }

        return $this->_send( $this->toArray( 'resolve' ) );
    }
}
