# GivenPHP

**GivenPHP** is a test framework library that allows your Unit Tests to be defined cleanly.

Please refer to the tests to get and idea how to use this.

*Note: This is still in active development and is not even ready for alpha release.*

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
    given('page', 10);
    when('turning the page', 'newPage', function ($book) {
      return $book->turnPage();
    });
    then('$newPage should be 11', function ($newPage) {
      return $newPage === 11;
    })
  })
});
```

### Running tests


Tests can be run with the GivenPHP cli


Example: running all tests in directory test
```
bin/givenphp test
```

Example: running a specific test called cat.php
```
bin/givenphp test/cat.php
```

Getting help with the cli

```
bin/givenphp --help
```


### Reporters

#### Default reporter

By default tests are output with a dot for each passing test and a capital F for each failing test.
If tests fail, failure summaries are printed at the end of test running.

#### TAP reporter

GivenPHP supports [TAP](http://en.wikipedia.org/wiki/Test_Anything_Protocol) compatible test output.
To specify test output to be in TAP format, use the -r or --reporter flag.
```
bin/givenphp --reporter tap test
```

### Test terms
#### describe
#### context
#### given
#### when
#### then

## Further examples
