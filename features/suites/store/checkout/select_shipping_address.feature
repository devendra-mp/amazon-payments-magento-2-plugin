Feature: As a customer
  I need to select a shipping address
  So that I can receive my goods

  Scenario: customer logged into amazon sees shipping widget
    Given I login with amazon as "amazoncustomer@example.com"
    And there is a valid product in my basket
    When I go to the checkout
    Then the amazon shipping widget should be displayed
    And the standard shipping form should not be displayed

  Scenario: customer not logged into amazon sees shipping form
    Given there is a valid product in my basket
    When I go to the checkout
    Then the standard shipping form should be displayed
    And the amazon shipping widget should not be displayed

  Scenario: shipping address is selected from amazon
    Given I login with amazon as "amazoncustomer@example.com"
    And there is a valid product in my basket
    And I go to the checkout
    When I select a shipping address from my amazon account
    And I select a valid shipping method
    And I go to billing
    Then the current basket for "amazoncustomer@example.com" should have my amazon shipping address
