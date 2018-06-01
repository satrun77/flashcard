MooFlashCard
=============
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c60d35bc-17f3-40b7-9b17-f504b8a62270/mini.png)](https://insight.sensiolabs.com/projects/c60d35bc-17f3-40b7-9b17-f504b8a62270)
[![Build Status](https://travis-ci.org/satrun77/MooFlashCard.svg?branch=master)](https://travis-ci.org/satrun77/MooFlashCard)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/satrun77/MooFlashCard/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/satrun77/MooFlashCard/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/satrun77/MooFlashCard/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/satrun77/MooFlashCard/?branch=master)

This package provides model for card & category to organise simple flash card system.
It provide an API end-point to query for cards or categories.

### Installation

Install the package via composer:

```bash
composer require moo/flashcard
```

Rebuild the cached package manifest
  
```bash
artisan package:discover 
```

Update your database with the package schema.

```bash
artisan migrate
```

#### DONE!

### API Usage

##### Query Categories

Request all categories
``` 
/api/categories
```

Request categories by search query
``` 
/api/categories?filter[custom]=search_query
```

##### Query Cards

Request all cards
``` 
/api/cards
```

Request cards by search query
``` 
/api/cards?filter[custom]=search_query
```

Request cards & include category details for each card
``` 
/api/cards?include=category
```

Request cards with pagination
``` 
/api/cards?page=1&limit=20
```

Request all card by id
``` 
/api/cards/{id}
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

### Demo
- You can view my personal use of this package - [Demo](http://flashcard.my.geek.nz/).

### License

This package is under the MIT license. View the [LICENSE.md](LICENSE.md) file for the full copyright and license information.
