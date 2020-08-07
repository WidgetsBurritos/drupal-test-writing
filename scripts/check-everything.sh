#!/usr/bin/env bash

# Ensure drupal is properly bootstrapped.
echo "Attempting to load Drupal:"
cd /var/www/html
drush status bootstrap | grep -cq Successful
if [ $? -eq 0 ]
then
  echo "✓ Success"
else
  echo "× Failure"
  exit 1
fi

# Attempt to run tests
echo "Attempting to run Drupal tests:"
php ./web/core/scripts/run-tests.sh --sqlite /tmp/a.sqlite --die-on-fail my_testing_module > /dev/null
if [ $? -eq 0 ]
then
  echo "✓ Success"
else
  echo "× Failure"
  exit 2
fi

# Attempt to run behat tests
echo "Attempting to run behat tests:"
behat > /dev/null
if [ $? -eq 0 ]
then
  echo "✓ Success"
else
  echo "× Failure"
  exit 3
fi

# Attempt to run nightwatch.js tests
echo "Attempting to run nightwatch.js tests:"
cd /var/www/html/web/core
yarn test:nightwatch ../modules/custom/my_testing_module/tests/src/Nightwatch > /dev/null
if [ $? -eq 0 ]
then
  echo "✓ Success"
else
  echo "× Failure"
  exit 4
fi
