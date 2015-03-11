# given-php
Proudly inspired by [rspec-given from *the* Jim Weirich](https://github.com/jimweirich/rspec-given) and [phpspec](https://github.com/phpspec/phpspec).

## Basic features
```php
<?php namespace spec\Foo;

use Foo;

return describe(Foo::class, with('constructorParam1', 'constructorParam2'), function () {
  given('constructorParam1', function () { return 'scalarValue'; });
  // $barProphecy will be an instance of PhpSpec\Prophecy\ObjectProphecy
  given('constructorParam2', function (Bar $barProphecy) { return $barProphecy->reveal(); });
  given(function (Bar $barProphecy) { $barProphecy->baz()->willReturn(3); });
  
  // Isolate further statements
  context('when isolated', function () {
    given(function (Bar $barProphecy) { $barProphecy->required()->shouldBeCalled(); });
  
    // $that is the instance of the object under specification
    when(function (Foo $that) { $that->isolated(); });
    when('result', function (Foo $that) { return $that->passToBar('required'); });
    
    // Assertions are (for now) made with simple conditions
    then(function ($result) { return $result === 3; });
  }
}
```

## Run the specs

```
$ vendor/bin/given-php run
```

## Todos

- Refactor the code and have a real flow for the SpecRunner
 - ~~Add a real runner (using [Symfony/Command](https://github.com/symfony/command) and maybe [thephpleague/flysystem](https://github.com/thephpleague/flysystem))~~
 - Add a real formatter
- Handling errors with then(failed()) and then(failedWith('ExceptionType'))
- Transform PHPErrors into Exceptions
- Add pending status if then() is empty
- Add skip status with then($callback, bool|callable $skip = false)
- Add formats
 - Simple dot [.XF] notation
 - Pretty
 - Null
- Add reporters
 - JUnit format
- Handling output with then(showed('expected output'))
- ? Add test/code generation (phpspec alike)
