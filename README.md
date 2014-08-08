PagerDuty
=========

Library for interacting with PagerDuty REST API

currently only implements Events/Integration API:

http://developer.pagerduty.com/documentation/integration/events

Composer Install:
-----------------
`"nmcquay/pagerduty": "0.1.*"`

Examples:
=========

Events:
-------
```
$evt = new \PagerDuty\Event();
// service key found at https://<your subdomain>.pagerduty.com/services
$evt->setServiceKey('32 char GUID') 
    ->setDescription('an example description')
    //incident key will get set automatically by pagerduty response if not set here
    //->setIncidentKey('example001') //optional
    ->setClient('Example Client') //optional
    ->setClientUrl('http://www.example.com') //optional
    ->setDetails( array('test' => 1) ) //optional
    //setDetail will add/alter a key to the details object
    ->setDetail( 'appended Detail key', 'with a value' ); //optional
$resp = $evt->trigger();

var_dump( $resp, $evt->toArray(), $evt->getIncidentKey() );

//now assuming everything worked, you should have triggered a pagerduty event
//we can acknowledge the event:
$resp = $evt->acknowledge();
var_dump( $resp );

//and we can resolve the event:
$resp = $evt->resolve();
var_dump( $resp );

// acknowledge() and resolve() require an incident_key to exist before 
// calling them

// You can also initialize an event using the same JSON format pageduty
// accepts (as a PHP array):
$evtB = new \PagerDuty\Event( array(    
      "service_key" => "32 char GUID",
      "incident_key" => "srv01/HTTP",
      "description" => "FAILURE on machine srv01.example.com",
      "client" => "Sample Monitoring Service",
      "client_url" => "https://monitoring.example.com",
      "details" => array(
          "ping time" => "1500ms",
          "load avg" => "0.75"
      )
) );
$evtB->trigger();
```

