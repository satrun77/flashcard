# Public API (RESTful) #

## /api/card ##

### `GET` /api/card.{_format} ###

_Search for a card._

Search for a card.

#### Requirements ####

**_format**

  - Requirement: html|json

#### Filters ####

query:

  * Requirement: 
  * Description: Search query
  * Default: 

pageLink:

  * Requirement: boolean
  * Description: To indicate whether to include a link to the card page (html format only).
  * Default: 1

shareLink:

  * Requirement: boolean
  * Description: To indicate whether to include the share buttons (twitter & google+) (html format only).
  * Default: 1

popup:

  * Requirement: boolean
  * Description: To indicate whether to the html is to be displayed as a popup box. This will include 'popup class name and close button' (html format only).
  * Default: 1


## /api/cards ##

### `GET` /api/cards.{_format} ###

_Returns a paginated list of cards._

Returns a paginated list of cards.

#### Requirements ####

**_format**

  - Requirement: html|json

#### Filters ####

page:

  * Requirement: \d+
  * Description: Page number.
  * Default: 1

limit:

  * Requirement: \d+
  * Description: Max number of cards to return.
  * Default: 20

query:

  * Requirement: 
  * Description: Limit result by query
  * Default: 


## /api/cards/random ##

### `GET` /api/cards/random.{_format} ###

_Returns a random selection of cards._

Returns a random selection of cards.

#### Requirements ####

**_format**

  - Requirement: html|json

#### Filters ####

limit:

  * Requirement: \d+
  * Description: Max number of cards to return.
  * Default: 30



# Public pages #

## / ##

### `GET` / ###

_The home page._

#### Requirements ####

**_format**

  - Requirement: html


## /{slug} ##

### `GET` /{slug} ###

_Displays a card details._

#### Requirements ####

**_format**

  - Requirement: html
**slug**

  - Requirement: [a-zA-Z0-9-_]+
  - Type: string
  - Description: The slug value of a card.
