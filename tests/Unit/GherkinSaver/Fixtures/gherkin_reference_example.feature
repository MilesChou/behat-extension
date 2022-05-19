Feature: Listing command
  In order to change the structure of the folder I am currently in
  As a UNIX user
  I need to be able see the currently available files and folders there

  Scenario: Listing two files in a directory
    Given I am in a directory "test"
    And I have a file named "foo"
    And I have a file named "bar"
    When I run "ls"
    Then I should get:
      """
      bar
      foo
      """
