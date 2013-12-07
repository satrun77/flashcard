Command Lines
==============

## 1. Create a new card category.

```bash
$ php app/console flashcard:category:create
```

#### Usage:
```
 flashcard:category:create [--active] title [desc] [parent]
```

#### Arguments:
```
 title                 The category title.
 desc                  The category description.
 parent                The parent category ID.
```

#### Options:
```
 --active              If set, the category is going to be active.
```

## 2. Create a new card.

```bash
$ php app/console flashcard:card:create
```

#### Usage:
```
 flashcard:card:create [--active] title content category [keywords] [description] [slug]
```

#### Arguments:
```
 title                 The title of the card.
 content               The content of the card.
 category              The category ID the card is belong to.
 keywords              Comma seperated keywords for the metadata tag.
 description           The metadata description.
 slug                  The url slug of the card.
```

#### Options:
```
 --active              If set, the card is going to be active.
```
