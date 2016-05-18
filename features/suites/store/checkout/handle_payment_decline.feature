Feature: As a customer
  I need to be notified when I am unable to pay with amazon
  So that I can pay via alternative means

  Background:
    Given I login with amazon as "existing@example.com"
    And there is a valid product in my basket
    And I go to the checkout
    And I select a shipping address from my amazon account
    And I select a valid shipping method
    And I go to billing
    And I select a payment method from my amazon account

  Scenario: payment authorization receives a hard decline as it was rejected by amazon
    Given I am requesting authorization on a payment that will be rejected
    When I place my order
    Then I should be notified that my payment was rejected
    And the amazon wallet widget should be removed
    And I should be logged out of amazon
    And "existing@example.com" should not have placed an order
    And I should be able to select an alternative payment method

  Scenario: payment authorization receives a hard decline due to transaction timeout
    Given I am requesting authorization on a payment that will timeout
    When I place my order
    Then I should be notified that my payment was rejected
    And the amazon wallet widget should be removed
    And I should be logged out of amazon
    And my amazon order should be cancelled
    And "existing@example.com" should not have placed an order
    And I should be able to select an alternative payment method
