# GivenPHP

**GivenPHP** is a test framework that allows your Unit Tests to be defined cleanly.

## Requirements

* PHP 5.4.0 is required but using the latest version of PHP is highly recommended

## Installation

To install you need [composer](https://getcomposer.org/) installed.
Add the following line to your composer.json files dependency section:
```
"given-php/given-php": "dev-master"
```
and then run:
```
composer update
```
from the command line

## Usage

### A basic test definition

Test files must be named with a leading `test_`

in `test_book.php`
```php
describe('A Book', function () {
  context('turning a page', function () {
    given('book', new Book); 
    when('setting the current page to 10', function($book) { 
      $book->setPage(10); 
    });
    when('turning the page', 'newPage', function ($book) {
      return $book->turnPage();
    });
    then('the book should be on page 11', function ($newPage) {
      return $newPage === 11;
    });
  })
});
```

### Running tests


Tests can be run with the GivenPHP cli


Example: running all tests in directory test
```
bin/givenphp test
```

Example: running a specific test called test_cat.php
```
bin/givenphp test/test_cat.php
```

Getting help with the cli

```
bin/givenphp --help
```

### General test style of GivenPHP

GivenPHP loosely follows an [arrange, act, assert](http://www.arrangeactassert.com/why-and-what-is-arrange-act-assert/) style of structuring tests. 
The `given` statement is the equivalent of `arrange`, `when` is synoymous with `act` and `then` is used in place of
`assert`. This dsl helps to enforce the writing of clean, clear, well structured tests.

Defining tests with GivenPHP follows the following pattern:
- `describe` addition
- `context` simple addition of 2 values
- `given` x = 1
- `given` y = 2
- `when` I add x and y
- `then` I can expect the result to equal 3


### Context description with `describe`

Each test file should define a single `describe` block. This allows you to provide some 
descriptive context around what you are about to test. Note: you cannot nest `describe` blocks inside each other. 1 per file only.

**example:**

```php
describe('A house on a hill', function () {
  //write tests that test the properties of the house on the hill
})
```

### Defining a `context`

The files `describe` block should have 1 or more `context` blocks that should then be used to describe how the way the thing being
tested behaves under different conditions. In a mathematics example, if we are using `describe` to test addition. We might have any number of different contexts we want to test addition under. Perhaps the addition of 2 values, the addition of 3 values, etc. 

You may even nest `context` blocks within `context` blocks as you see fit.

**example:**

```php
describe('addition', function () {
  //...
  context('addition of 2 values', function () {
    //...
  });
  context('addition of 3 values', function () {
    //...
    context('...', function () {
      //...
    });
  });
  //...
});
```

### Variable declarations with `given`

Setting up variables happens at the `given` stage of the `context`
Call `given` passing it a string representing the name of the variable you want to assign as the first
parameter and the value to assign as the second parameter. The variable you assign can be injected into `when` and `then` blocks in the callback.

**example:**

```php
//$x and $y will be available for use in subsequent `when` and `then` blocks
given('x', 1);
given('y', 2);
```
You can optionally add a label to give some context to your variable definition

**example:**

```php
given('a variable `x` is set to 1', 'x', 1);
```

### Operations on your variables with `when`

After using `given` statements to setup variables you can then perform operations on them in a `when` block before making assertions about the result in a `then` block. Anything you have defined in a `given` block can be injected into a `when` block in by adding it as a parameter to the `when` callback.

**example:**

```php
//we inject $x and $y into the callback function...
when('adding x and y together', 'result', function ($x, $y) {
  //and what we return here will be availble in later `when` and `then` blocks as $result
  return $x + $y;
});
```

### Assertions with `then`

Assertions happen in the `then` block of the tests. In order to make an assertion you can either return true, false or make use of a third party assertion library such as [php-expect](https://github.com/wscoble/php-expect)

**example:**

```php
then('I can expect the result to equal 3', function ($result) {
  return $result === 3;
});

//or

then('I can expect the result to equal 3', function ($result) {
  Expect::a($result)->toBeEqualTo(3);
});
```

### Catching errors with `fails` and `failsWith`

If you are testing code the might error or throw an exception, you can use `fails` and `failsWith` to make 
assertions about whats happening.

**example:**

```php
when(function() {
  throw new Exception;
});
then(fails());
then(failsWith('Exception'));
```

### Reporters

You can control the output of your tests by setting the reporter. Currently a default dot matrix style reporter is
available and a tap reporter can be set

#### Default reporter

By default tests are output with a dot for each passing test and a capital F for each failing test.
If tests fail, failure summaries are printed at the end of test running.

#### TAP reporter

GivenPHP supports [TAP](http://en.wikipedia.org/wiki/Test_Anything_Protocol) compatible test output.
To specify test output to be in TAP format, use the -r or --reporter flag.
```
bin/givenphp --reporter tap test
```

### API

#### describe

describe($label, $callback);

$label {string} - context description
$callback {closure function}

#### context

context($label, $callback);

$label {string} - context description
$callback {closure function}

#### given

given([$label], $variableName, $callback);

$label {string} - [optional] context description
$variableName {string} - variable to assign
$callback {closure function}

#### when

when([$label], [$variableName], $callback);

$label {string} - [optional] context description
$variableName {string} - [optional] variable to assign
$callback {closure function}

#### then

then([$label], $callback);

$label {string} - [optional] context description
$callback {closure function}

#### fails

fails();

#### failsWith

failsWith($exception);

$exception {string} - name of exception class

## Contributing

Please follow the PSR-2 standard.

## TODO List

- [ ] Output Handling (through then(outputs()))
