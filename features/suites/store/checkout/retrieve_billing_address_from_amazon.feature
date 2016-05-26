Feature: As a customer
  I need to provide a billing address to the merchant

  @javascript
  Scenario:
    Given I login with amazon as "existing@example.com"
    And there is a valid product in my basket
    And I go to the checkout
    And I select a shipping address from my amazon account
    And I select a valid shipping method
    And I go to billing
    When I select a payment method from my amazon account
    Then the billing address for my payment method should be displayed
