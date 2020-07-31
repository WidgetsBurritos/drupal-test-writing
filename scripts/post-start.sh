#!/usr/bin/env bash

tables = $(mysql -e 'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = "db" AND table_name = "watchdog" LIMIT 1' | sed -n 2p)
if [[ tables -gt 0 ]]; then
  echo "Drupal site detected!"
else
  echo "Drupal not installed. Installing database..."
  gzip -dc /var/www/html/snapshot/dump.sql.gz | mysql db

  echo "Downloading PHP dependencies..."
  cd /var/www/html
  composer install

  echo "Downloading Node dependencies..."
  cd /var/www/html/web/core
  yarn install
fi
