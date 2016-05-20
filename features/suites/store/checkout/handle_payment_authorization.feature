Feature: As a customer
  I need to authorize a payment from my amazon account
  So that I can pay for goods

  Background:
    Given I login with amazon as "existing@example.com"
    And there is a valid product in my basket
    And I go to the checkout
    And I select a shipping address from my amazon account
    And I select a valid shipping method
    And I go to billing
    And I select a payment method from my amazon account

  Scenario: customer authorizes payment for an order
    Given I place my order
    Then "existing@example.com" should have placed an order
    And there should be an open authorization for the last order for "existing@example.com"
    And amazon should have an open authorization for the last order for "existing@example.com"

  Scenario: customer authorizes payment for an order charged on order placement
    Given orders are charged for at order placement
    And I place my order
    Then "existing@example.com" should have placed an order
    And there should be a closed capture for the last order for "existing@example.com"
    #And there should be a paid invoice for the last order for "existing@example.com"
    #And amazon should have a closed capture for the last order for "existing@example.com"