Feature: As a customer
  I need to login with amazon
  So that I can pay with my amazon account

  Scenario: login with amazon links amazon account to customer account if already logged in
    Given there is a customer "existing@example.com"
    And "existing@example.com" has never logged in with amazon
    And "existing@example.com" is logged in
    When I login with amazon as "existing@example.com"
    Then "existing@example.com" is associated with an amazon account





