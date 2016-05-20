Feature: As a customer
  I want to register with Amazon after I have purchased something using Amazon Payment

  Background:
    Given Login with Amazon is disabled

  @javascript @revert-m2-config
  Scenario:
    Given I login with Amazon as "existing@example.com" on product page
    Then I should be redirected to the Basket

  @javascript @revert-m2-config
  Scenario:
    Given there is a valid product in my basket
    And I login with amazon on the basket page as "existing@example.com"
    And I go to the checkout
    And I provide the "existing@example.com" email in the shipping form
    And I select a shipping address from my amazon account
    And I select a valid shipping method
    And I go to billing
    And I place my order
    And I can create a new Amazon account on the success page with email "existing@example.com"
    Then "existing@example.com" is associated with an amazon account
