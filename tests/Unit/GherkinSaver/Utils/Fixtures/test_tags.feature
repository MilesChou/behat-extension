@billing @bicker @annoy
Feature: Verify billing

  @important
  Scenario: Missing product description

  Scenario: Several products

  @billing @eating
  Scenario Outline: eating
    Given there are <start> cucumbers
    When I eat <eat> cucumbers
    Then I should have <left> cucumbers

    @foo
    Examples:
      | start | eat | left |
      | 12    | 5   | 7    |

    @bar @baz
    Examples:
      | start | eat | left |
      | 20    | 5   | 15   |
