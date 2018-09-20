[![Latest Stable Version](https://poser.pugx.org/mtawil/converter/v/stable.png)](https://packagist.org/packages/mtawil/converter)
[![Latest Unstable Version](https://poser.pugx.org/mtawil/converter/v/unstable.png)](https://packagist.org/packages/mtawil/converter)
[![Build Status](https://scrutinizer-ci.com/g/mtawil/converter/badges/build.png?b=master)](https://scrutinizer-ci.com/g/mtawil/converter/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mtawil/converter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mtawil/converter/?branch=master)
[![License](https://poser.pugx.org/mtawil/converter/license.svg)](https://packagist.org/packages/mtawil/converter)
[![Total Downloads](https://poser.pugx.org/mtawil/converter/downloads.png)](https://packagist.org/packages/mtawil/converter)


Converter
=========
A set of classes to translate a text **from HTML to BBCode** and **from BBCode to Markdown**.

The HTML to BBCode converter, in particular, has not been created with the intent to convert an entire HTML page, but 
just to convert a set of HTML tags used to format articles and blog posts in the HTML era, when Markdown and BBcode 
didn't exist at all.

The special BBCode to Markdown converter is able to convert: bold, italic, underline and strikethrough texts, lists, 
urls, images, quotes and even code blocks (snippets).


Composer Installation
---------------------

To install Converter, you first need to install [Composer](http://getcomposer.org/), a Package Manager for
PHP, following these few [steps](http://getcomposer.org/doc/00-intro.md#installation-nix):

```sh
curl -s https://getcomposer.org/installer | php
```

You can run this command to easily access composer from anywhere on your system:

```sh
sudo mv composer.phar /usr/local/bin/composer
```


Converter Installation
----------------------
Once you have installed Composer, it's easy install Converter.

Run the following command in your project root dir:
```sh
composer require mtawil/converter
```


Usage
-----
There are two classes: `HTMLConverter` and `BBCodeConverter`. The first class may be used to convert from HTML to
BBCode, while the second one is used to convert from BBCode to Markdown.

HTML to BBCode conversion:

```php
$converter = new Converter\HTMLConverter($text, $id);
echo $converter->toBBCode();
```

BBCode to Markdown conversion:

```php
$converter = new Converter\BBCodeConverter($text, $id);
echo $converter->toMarkdown();
```

In both cases the `$id` is optional. When provided is used in case an exception is raised during the conversion process.


Documentation
-------------
The documentation can be generated using [Doxygen](http://doxygen.org). A `Doxyfile` is provided for your convenience.


Requirements
------------
- PHP 5.4.0 or above.


Authors
-------
Filippo F. Fadda - <filippo.fadda@programmazione.it> - <http://www.linkedin.com/in/filippofadda>

[Mohammad @Taweel](https://twitter.com/mtawil7) - <https://www.linkedin.com/in/maltawil>


License
-------
Converter is licensed under the Apache License, Version 2.0 - see the LICENSE file for details.
