Change log
==============

## Version 2.0.0
- Migrate the package to Laravel. Symfony will always be amazing framework :heart:
- Feature: added color field to category model.
- API response is only in JSON format.
- Support php 7.1+

## Version 1.1.0
- Upgrade bootstrap to 3.3.5 and use LESS.
- Feature: filter card by category in homepage.
- Support symfony 2.7.
- Bug fixes.

## Version 1.0.3
- Bug fixes.
- New test cases.
- Code quality changes.

## Version 1.0.2
- Rename property "is_active" in Card and Category entity to "active".
- Compatible with v1.0.2 MooFlashCardAdminBundle

## Version 1.0.1
- Minor changes to composer.json
- Minor change to doc files.
- Added travis-ci support.

## Version 1.0.0
- Command line tool to create cards and categories.
- Webpage to display a card's details and the ability to share the card using twitter or google+ buttons.
- The Home page lists cards with the ability to search.
- RESTful API with the following features:
    - Get all cards in paginated results.
    - Search for cards in paginated results.
    - Search for a card.
    - Return a random selection of cards.
