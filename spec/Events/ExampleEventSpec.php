<?php namespace spec\GivenPHP\Events;

use GivenPHP\Events\ExampleEvent;

return describe(ExampleEvent::class, with('result'), function () {
    given('result', function () { return true; });
    then(function (ExampleEvent $that) { return $that->getResult() === true; });
});