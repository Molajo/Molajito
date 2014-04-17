=======
Molajito Render Package
=======

[![Build Status](https://travis-ci.org/Molajo/Molajito.png?branch=master)](https://travis-ci.org/Molajo/Molajito)

Molajito is a template environment for frontend developers who want to focus on markup, not programming.  It
locates views, injects escaped data, integrates with Event handling, offers translation support for language
strings, and more.

## Sample

A working example of a website that has a
[Home](https://raw.githubusercontent.com/Molajo/Molajito/master/Sample/Screenshots/Home.png),
[Blog](https://raw.githubusercontent.com/Molajo/Molajito/master/Sample/Screenshots/Blog.png),
[Post](https://raw.githubusercontent.com/Molajo/Molajito/master/Sample/Screenshots/Post.png),
[Contact](https://raw.githubusercontent.com/Molajo/Molajito/master/Sample/Screenshots/Contact.png),
[About](https://raw.githubusercontent.com/Molajo/Molajito/master/Sample/Screenshots/About.png),
page is available within this package. To view the sample,
configure an Apache Host to this
[ Sample / Public ](https://github.com/Molajo/Molajito/tree/master/Sample/Public) folder and
access the Host using your browser.

## Basic Process:

Molajito initiates the rendering process by including
a [Theme](https://github.com/Molajo/Molajito#theme) file as rendered output. The `theme` contains
[Include Statements](https://github.com/Molajo/Molajito#include-statements) discovered by Molajito
during parsing and used to identify what `view` is to be rendered at that location.

Molajito uses three different types of `views':

* [Page](https://github.com/Molajo/Molajito#page) views define layouts.
A site typically has different layouts and the page view is useful for that purpose.
 Molajito does not pass data into the `page view`, it only includes the `page view` file.

* [Template](https://github.com/Molajo/Molajito#template) views define one specific area of
 the page, for example a `template view` could render a navigation menu, a blog post, or
  an author profile. Molajito passes in data to the `template view` in support of the rendering
  process.

* [Wrap](https://github.com/Molajo/Molajito#wrap) views *wrap* the rendered output from a
`template view` in a specific manner. For example, a wrap might enclose the template output
in an `<article>`, `<footer>`, or `<header>` content-specific HTML5 element.  A wrap might
also be used to achieve a certain visual effect for the content, for example by including
a message in a div with an `alert` class.

You can also use `include statements` to define
[positions](https://github.com/Molajo/Molajito#position) which are placeholders that can be
associated with one or more `template views`. For example, you might want a `sidebar position`
for a blog that can be configured by site builders.

### Theme

Themes are the first rendered output and therefore drive the rendering process. Typically, a
`theme` defines necessary CSS and JS statements.

Molajito injects a data object called `$this->runtime_data` into the Theme.
As can be seen in the following example, Molajito passes in the `$this->runtime_data->page_name`
value used in the page include statement.

You can add data the `$this->runtime_data` so that the data are available for rendering.
In this example, `site_name` is used to render `title`.

In the `theme` below, you will find `include statements` for `page`, `template` and `wrap` views.

```html

<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= $this->runtime_data->site_title ?></title>
    <link rel="stylesheet" href="/css/custom.css"/>
</head>
<body>
<div class="page-wrap">
    {I template=Navbar wrap=Nav I}
    {I Breadcrumbs I}
    {I page=<?= $this->runtime_data->page_name ?> I}
</div>
    {I Footer wrap=Nav I}
</body>
</html>

```

### Include Statements

Molajito uses `include statements` to define where specific views should be rendered.
`Include statements` can be defined within any `theme` or `template` which helps to create reusable templates
  referenced in many places in order to keep your views [DRY](http://en.wikipedia.org/wiki/Don%27t_repeat_yourself)
  and easy to maintain. Molajito continues parsing rendered output after rendering each view until
  no more `include statements` are found.

The Include syntax is simple `{I type=Name I}`:
 * `{I` marks the start of an include statement;
 * `type=` set to `template` or `page`;
 If omitted, Molajito first assumes it is a `position`, and then looks for a like-named `template`;
 * `Name` identifies the view associated with the type specified;
 * `I}` marks the end of an include statement.

Extra attributes `{I template=Templatename class=current,error dog=food I}` can be added to
the `include statement` by adding named pair attributes.


### Page

    {I page=<? $this->runtime_data->page_name ?> I}

`Themes` are typically where `page views` are defined. The `page_name` is passed into
 the `theme` via the `$this->runtime_data->page_name` object. However, a `page view` could
 be defined anywhere. What is important to remember is that Molajito simple includes the
 `page view` file without passing in data.

Following is an example of a `page view` for a blog post. The page layout calls for three
`template views`: a post, comments, and a paging template. The page layout also includes
a `sidebar` position. If there is no position with the name `sidebar`, Molajito searches for
a like-named `template view.'

```php

<div class="row">
    <div class="large-9 push-3 columns">
        {I template=Post I}
        {I template=Comments I}
        {I template=Paging I}
    </div>
    <div class="large-3 pull-9 columns">
        {I Sidebar I}
    </div>
</div>


```

### Position

    {I Sidebar I}

If `type=` is omitted from the `include statement`, Molajito first searches for a `position`
with that name. If a `position` with that name is not found, Molajito next search for a
like named `template view`.

You can define which `template views` are associated with a position by defining
the values in an array for $type = 'page' or $type = 'theme'. Molajo will check for positions
in that sequence. If positions are found, Molajito inserts `(I template=Name I}` values
for each `template view` defined.

    $this->runtime_data->render->extension->$type->parameters->positions


### Template

    {I template=Templatename I}

Molajito injects data into the `Template view` for support in rendering. As with the `Theme`,
 Molajito passes in the `$this->runtime_data` object which you can use to ensure specific
 data is available for rendering.

There are two ways to configure a `template view`.

1. Custom.php - Molajito will inject the `view` with `$this->query_results` and `this->runtime_data`,
but the view must handle looping, if needed.

2. Header.php, Body.php, and Footer.php - Molajito will inject `Body.php` with `$this->row` and
`$this->runtime_data` one time for each row. If `Header.php` exists, it will get the first row.
 If `Footer.php` exists, it will get the last row.

An example of a typical `template view` Body.php file follows.

```php

<h3>
    <a href="<?= $this->row->current_url ?>">
        <?= $this->row->title ?>
    </a>
</h3>

<h6>Written by:
    <a href="<?= $this->runtime_data->route->contact ?>">
        <?= $this->row->author ?>
    </a>
    on <?= $this->row->published ?>.
</h6>

<?php if (isset($this->row->video) && $this->row->video !== '') { ?>
    {I template=video link=<?= $this->row->video ?> I}
<?php } ?>

<?= $this->row->content; ?>


```

### Wrap

    {I template=Templatename wrap=Wrapname I}

To wrap a `template view`, add the `wrap=Wrapname` element to the `include statement`.

A typical `Wrap view` requires three files each of which Molajito will inject the `view` with
`$this->row` (containing the rendered output from the `template view`) and `this->runtime_data`.

```php

<footer class="<?= $class ?>">
    echo $this->row->content;
</footer>

```


### Localization

    {T Translate this string T}

Molajito supports localization using language strings and translations. Simply create a
[token](https://github.com/Molajo/Molajito/blob/master/Sample/Views/Foundation5/Templates/Blog/Body.phtml#L17)
like this example and inject the translate class [with an array of language strings and translations]
(https://github.com/Molajo/Molajito/blob/master/Sample/Bootstrap/Index.php#L20) or add an Translate Adapter for your
Translation class.

```

## Requirements and Compliance
 * PHP application framework independent, no dependencies
 * Requires PHP 5.4, or above
 * Uses [Semantic Versioning](http://semver.org/)
 * Compliant with:
    * [PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md) Basic Coding Standards
    * [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) Coding Style
    * [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md) Autoloader
 * [phpDocumentor2] (https://github.com/phpDocumentor/phpDocumentor2)
 * [phpUnit Testing] (https://github.com/sebastianbergmann/phpunit)
 * Author [AmyStephen](http://twitter.com/AmyStephen)
 * [Travis Continuous Improvement] (https://travis-ci.org/Molajo/Molajito)
 * Listed on [Packagist] (https://packagist.org/packages/molajo/molajito) and installable using [Composer] (http://getcomposer.org/)
 * Please use github to submit [pull requests](https://github.com/Molajo/Molajito/pulls) and [features](https://github.com/Molajo/Molajito/issues)
 * Licensed under the MIT License - see the `LICENSE` file for details
