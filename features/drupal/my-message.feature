Feature: My Message

  Verify that the "My Message" route correctly handles user roles.

  Scenario: Verified unauthenticated users
    When I am on "/my-message"
    Then the response status code should be 403
    And the response should contain "You are not authorized to access this page."
    And the response should not contain "You are logged in"
    And the response should not contain "You are special"
    And the response should not contain "You have yet another privilege"

  @api
  Scenario: Verify users with "my super secret privilege"
    Given I am logged in as a user with the "my super secret privilege" permission
    When I am on "/my-message"
    Then the response status code should be 200
    And the response should contain "You are logged in"
    And the response should contain "You are special"
    And the response should not contain "You have yet another privilege"

  @api
  Scenario: Verify users with "yet another privilege"
    Given I am logged in as a user with the "yet another privilege" permission
    When I am on "/my-message"
    Then the response status code should be 200
    And the response should contain "You are logged in"
    And the response should not contain "You are special"
    And the response should contain "You have yet another privilege"


# CONSOLIDATED RULE FOR ALL 200 status permissions:

  @api
  Scenario Outline:
    Given I am logged in as a user with the "<permission>" permission
    When I am on "/my-message"
    Then the response status code should be 200
    And the response should <logged in> "You are logged in"
    And the response should <special> "You are special"
    And the response should <yet another> "You have yet another privilege"

    Scenarios:
    | permission                                           | logged in | special     | yet another |
    | access content                                       | contain   | not contain | not contain |
    | my super secret privilege                            | contain   | contain     | not contain |
    | yet another privilege                                | contain   | not contain | contain     |
    | my super secret privilege, yet another privilege     | contain   | contain     | contain     |
