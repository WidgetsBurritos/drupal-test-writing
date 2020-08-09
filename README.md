# Drupal Test Writing

This is a test D9 site which can be used for practicing test writing and running. This is to be used in tandem with the [Drupal Testing Crash Course](https://2020.drupalcampcolorado.org/trainings/drupal-testing-crash-course) training at DrupalCamp Colorado.

## Dependencies

- Docker 18.06+ - [Instructions for installing Docker on your local system](https://docs.docker.com/engine/install/)
- Latest version of DDEV-Local - [Instructions for installing DDEV on your local system](https://ddev.readthedocs.io/en/stable/).

_Note: If you want to improve performance of test running on MacOS, follow the [instructions for using the new experimental mutagen functionality in Docker Desktop with DDEV-Local](https://github.com/drud/ddev/issues/2278)._

## Getting Started

1. Clone this repo locally. (Note: You may want to consider forking this repo instead of directly cloning if you wish to open pull requests)
    ```bash
    git clone git@github.com:WidgetsBurritos/drupal-test-writing.git
    ```

    If you run into a permission denied error with the command above, try using https instead:
    ```bash
    git clone https://github.com/WidgetsBurritos/drupal-test-writing.git
    ```

    Then you will need to change into that directory:
    ```bash
    cd drupal-test-writing
    ```

2. Start DDEV-Local
    ```bash
    ddev start
    ```

    The very first time `ddev start` is run, it will do a few different things, which can be found in [scripts/post-start.sh](scripts/post-start.sh):

    1. Install a very simple D9 website, based on the snapshot located at `snapshot/dump.sql.gz`.
        Any subsequent `ddev start` runs will keep your database intact, unless you manually remove it.
    2. Install all PHP dependencies via composer.
    3. Inject a `.env` and `phpunit.xml` file in your `web/core` directory, based on the templates in `templates/core.env` and `templates/core.phpunit.xml` respectively. In a real project, you would want to ensure those files are handled properly and securely. See `web/core/.env.example` and `web/core/phpunit.xml.dist` for more information about those files.
    4. Install all Node.js dependencies inside the `web/core` directory. This is needed for nightwatch tests to run.
    5. Clear the Drupal cache.

3. Open the site in your browser:
    ```bash
    ddev launch
    ```
4. You can then use the following sample credentials to test out various roles:

    1. `admin`/`admin` - Has all the privileges
    2. `bobby`/`bobby` - Has the `My Super Secret Privilege` permission
    3. `carol`/`carol` - Has the `Yet Another Privilege` permission
    4. `david`/`david` - Has no roles or permissions set

5. Verify everything is running properly:

    ```bash
    ddev composer check:everything
    ```

    If everything is working correctly, you should see a response like this:

    > Attempting to load Drupal:\
    > ✓ Success\
    > Attempting to run Drupal tests:\
    > ✓ Success\
    > Attempting to run behat tests:\
    > ✓ Success\
    > Attempting to run nightwatch.js tests:\
    > \- Connecting to chromedriver on port 9515...\
    > ℹ Connected to chromedriver on port 9515 (226ms).\
    > ✓ Success

## What We're Testing

Most of what we will be working with is located in a custom module called
[_my_testing_module_](web/modules/custom/my_testing_module).

This module is intentionally broken for the sake of demonstrating test-driven development.

This module _should_ do the following when you navigate to `/my-message`:
  1. Shows a message for any authenticated users that says: "You are logged in"
      - It's actually showing "You _might be_ logged in" instead.
  2. Shows a message for users with the _my super secret privilege_ permission that says: "You are super special."
      - It's actually showing "You _aren't all that_ special." instead. Well that's not very nice.
  3. Shows a message for users with the _yet another privilege_ permission that says "You have yet another privilege."
      - This one is working as expected.
  4. If multiple scenarios apply, it should show all of the above messages.
      - It's actually only showing one of these messages.
  5. If a user is not logged in, they should get an access forbidden error.
      - It's actually showing them the message shown to authenticated users.

## Test Runners

### PHPUnit (PHP Testing)

[PHPUnit](https://phpunit.de/) is a tool for testing PHP functionality in Drupal.

There are a [few different ways to run PHPUnit tests](https://www.drupal.org/docs/testing/phpunit-in-drupal/running-phpunit-tests). This training uses the `core/scripts/run-tests.sh` method, as that is what DrupalCI uses.

#### Run all PHPUnit tests:
If you want to run all PHPUnit-based tests for your module, you can do so with one of the following commands:

- *From inside the DDEV-Local container (i.e. after running `ddev ssh`):*
  ```bash
  php core/scripts/run-tests.sh --color --verbose --sqlite /tmp/a.sqlite my_testing_module
  ```
- *From outside the DDEV-Local container:*
  ```bash
  ddev exec php core/scripts/run-tests.sh --color --verbose --sqlite /tmp/a.sqlite my_testing_module
  ```

_Note: We're using the `--sqlite` flag. When the test runner bootstraps Drupal, it will save results in a sqlite database, that we're just storing in the `/tmp` directory for now. Alternatively we could download the SimpleTest module (contrib as of D9) and use mysql DB instead._

#### Run specific PHPUnit tests:

Running every test, all the time, can sometimes take a while. If you want to focus on a specific test you can do so by using the `--class` flag instead:

- *From inside the DDEV-Local container (i.e. after running `ddev ssh`):*
  ```bash
  php core/scripts/run-tests.sh --color --verbose --sqlite /tmp/a.sqlite --class 'Drupal\Tests\my_testing_module\Functional\MyFunctionalTest'
  ```
- *From outside the DDEV-Local container:* (note the double-quotes on the `ddev exec` command)
  ```bash
  ddev exec "php core/scripts/run-tests.sh --color --verbose --sqlite /tmp/a.sqlite --class 'Drupal\Tests\my_testing_module\Functional\MyFunctionalTest'"
  ```

### Nightwatch.js (Javascript Testing)

[Nightwatch.js](https://nightwatchjs.org/) is a tool used for javascript testing in Drupal.

To add support for nightwatch testing in DDEV-Local, a [docker-compose.chromedriver.yml]() file must be added into your `.ddev` directory.

Read Matt Glaman's [Running Drupal's Nightwatch test suite on DDEV](https://glamanate.com/blog/running-drupals-nightwatch-test-suite-ddev) article for more information about setting this up.

#### Run all Nightwatch.js tests

- *From inside the DDEV-Local container (i.e. after running `ddev ssh`):*
  ```bash
  cd /var/www/html/web/core
  yarn test:nightwatch ../modules/custom/my_testing_module/tests/src/Nightwatch
  ```

- *From outside the DDEV-Local container (_Note: You have to specify which directory to run inside using the `-d` flag, and all other paths are relative to that_):*
  ```bash
  ddev exec -d /var/www/html/web/core yarn test:nightwatch ../modules/custom/my_testing_module/tests/src/Nightwatch
  ```

#### Run specific Nightwatch.js tests

- *From inside the DDEV-Local container (i.e. after running `ddev ssh`):*
```bash
cd /var/www/html/web/core
yarn test:nightwatch ../modules/custom/my_testing_module/tests/src/Nightwatch/MyNightwatchTest.js
```
- *From outside the DDEV-Local container (_Note you have to specify which directory to run inside using the `-d` flag, and all other paths are relative to that_):*
```bash
ddev exec -d /var/www/html/web/core yarn test:nightwatch ../modules/custom/my_testing_module/tests/src/Nightwatch/MyNightwatchTest.js
```

### Behat (Behavioral Testing w/ Cucumber)

[Behat](https://docs.behat.org/en/latest/) is a tool for behavorial testing in Drupal.

#### Run all behat tests
- *From inside the DDEV-Local container (i.e. after running `ddev ssh`):*
```bash
cd /var/www/html && behat
```
- *From outside the DDEV-Local container (_Note you have to specify which directory to run inside using the `-d` flag, and all other paths are relative to that_):*
```bash
ddev exec -d /var/www/html behat
```

#### Run specific behat tests
- *From inside the DDEV-Local container (i.e. after running `ddev ssh`):*
```bash
cd /var/www/html && behat features/drupal/cache.feature
```
- *From outside the DDEV-Local container (_Note you have to specify which directory to run inside using the `-d` flag, and all other paths are relative to that_):*
```bash
ddev exec -d /var/www/html behat
```
