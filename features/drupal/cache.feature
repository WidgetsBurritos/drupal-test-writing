@api
Feature: Cache

Scenario:
  When I visit "/"
  Then the response should contain "Welcome to Drush Site-Install"
  And the cache tag "|http_response|" is present
  And the cache context "|url.path.is_front|" is present
