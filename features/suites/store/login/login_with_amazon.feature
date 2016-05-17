Feature: As a customer
  I need to login with amazon
  So that I can pay with my amazon account

  @javascript
  Scenario: login with amazon button is available on the login page
    Given I go to login
    Then I see a login with amazon button on the login page

  @javascript
  Scenario: login with amazon button is available on the registration page
    Given I go to register
    Then I see a login with amazon button on the registration page

  @javascript
  Scenario: login with amazon links amazon account to customer account if already logged in
    Given there is a customer "magentoexisting@example.com"
    And "magentoexisting@example.com" has never logged in with amazon
    And "magentoexisting@example.com" is logged in
    And there is a valid product in my basket
    And I login with amazon on the basket page as "existing@example.com"
    Then "magentoexisting@example.com" is associated with an amazon account