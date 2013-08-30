MooFlashCardBundle
=============

The MooFlashCardBundle is a Symfony2 bundle for a simple flash card system.

## License

This bundle is under the MIT license. View the [LICENSE.md](LICENSE.md) file for the full copyright and license information.

## Features

#### Version 1.0.0
- Command line tool to create cards and categories.
- Webpage to display a card's details and the ability to share the card using twitter or google+ buttons.
- The Home page lists cards with the ability to search.
- RESTful API with the following features:
    - Get all cards in paginated results.
    - Search for cards in paginated results.
    - Search for a card.
    - Return a random selection of cards.

## Demos
- Use of the RESTful API to search for a card. [Demo 1](http://jamandcheese-on-phptoast.com/flashcard/example.html)
- FlashCardBundle [Demo 2](http://jamandcheese-on-phptoast.com/flashcard)

## Installation (5 steps)

### 1. Download MooFlashCardBundle with composer.

Add the following to your composer.json:

```yml
{
    "require": {
        "moo/flashcard-bundle": "*"
    }
}
```

Install the bundle by executing the following command:

``` bash
$ php composer.phar update moo/flashcard-bundle
```

### 2. Add the bundle configurations.

Open your application base configuration file `app/config/config.yml` and add the following to the imports section.

```yml
imports:
    # ....
    - { resource: "@MooFlashCardBundle/Resources/config/config.yml" }
```

** (optional) ** Open your application test configuration file `app/config/config_test.yml` and add the following to the imports section. This is needed for the bundle test cases.

```yml
imports:
    # ....
    - { resource: "@MooFlashCardBundle/Resources/config/config_test.yml" }
```

Open your application rounting file `app/config/routing.yml` and add the following to end of the file.

```yml
moo_flashcard:
    resource: "@MooFlashCardBundle/Resources/config/routing.yml"
    prefix: /flashcard
```

### 3. Enable the bundle in your application kernel.

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new JMS\SerializerBundle\JMSSerializerBundle(),
        new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
        new FOS\RestBundle\FOSRestBundle(),
        new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
        new Moo\FlashCardBundle\MooFlashCardBundle(),
    );

    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        // ...
        $bundles[] = new Liip\FunctionalTestBundle\LiipFunctionalTestBundle();
    }
}
```

### 4. Publish bundle assets for Development and Production environments.

```bash
$ php app/console assets:install web --symlink
$ php app/console assetic:dump
```

### 5. Upgrade your database with the bundle schema.

This step creates 3 database tables (card, card_view, card_category).

#### ORM: execute the followng:

```bash
$ php app/console doctrine:schema:update --force
```

## DONE!

- You can access the homepage of the bundle from `http://yoursite.com/app_dev.php/flashcard`
- You can view the RESTful API documentations from `http://yoursite.com/app_dev.php/flashcard/api/doc`
