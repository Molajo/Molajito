=======
Molajito Render Package
=======

[![Build Status](https://travis-ci.org/Molajo/Render.png?branch=master)](https://travis-ci.org/Molajo/Molajito)

Molajito is a template environment for frontend developers who want to focus on rendered output, not programming.

## Working Example

A working example of a website that has a home page, blog, post, contact, and about us page is
available within this package. Just download the Molajito package and copy it to a site
that you can access via a browser. Then, configure an Apache Host to post to the
[ .dev / Sample / Public ](https://github.com/Molajo/Molajito/tree/master/.dev/Sample/Public) folder.
No database is required since the example retrieves data from files.


```php

<?php
/**
* Bootstrap
*
* @package Molajo
* @copyright 2014 Amy Stephen. All rights reserved.
* @license http://www.opensource.org/licenses/mit-license.html MIT License
*/
include $molajito_base . '/vendor/autoload.php';

if (! defined('PHP_VERSION_ID')) {
    $version = explode('.', phpversion());
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}
/** Simulates the Route process** /
include __DIR__ . '/Route.php';

/** Adds data to the $this->runtime_data object */
include __DIR__ . '/RuntimeData.php';

/** Defines the location of views and data files */
include __DIR__ . '/Input.php';

/** Dependency Injection for Molajito */
include __DIR__ . '/MolajitoFactoryMethod.php';

$class = 'Molajo\\Factories\\MolajitoFactoryMethod';
$factory = new $class(
    $molajito_base,
    $theme_base_folder,
    $view_base_folder,
    $posts_base_folder,
    $author_base_folder,
    $post_model_registry,
    $author_model_registry
);

/** Initiates Molajito */
$molajito = $factory->instantiateClass();

/** Pass in the Theme and Data ($this->runtime_data) */
$rendered_page = $molajito->render($theme_base_folder, $data);

/** Pass $rendered_page off to your response class */
echo $rendered_page;

```

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

    `{I page=<? $this->runtime_data->page_name ?> I}`

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

    `{I Sidebar I}`

If `type=` is omitted from the `include statement`, Molajito first searches for a `position`
with that name. If a `position` with that name is not found, Molajito next search for a
like named `template view`.

You can define which `template views` are associated with a position on the page



```php

{I Sidebar I}

```


### Template

**Template** `{I template=Templatename I}`
* Molajito looks for a `template view` with the name `Templatename`.

An example of a typical `template view` follows.

```php

<h3>
    <a href="<?= $this->row->current_url ?>">
        <?= $this->row->title ?>
    </a>
</h3>

<h6>{T Written by T}:
    <a href="<?= $this->runtime_data->route->contact ?>">
        <?= $this->row->author ?>
    </a>
    {T on T} <?= $this->row->published ?>.
</h6>

<?php if (isset($this->row->video) && $this->row->video !== '') { ?>
    {I template=video link=<?= $this->row->video ?> I}
<?php } ?>

<?= $this->row->content; ?>


```


```php


### Wrap

**Wrap** `{I template=Templatename wrap=Wrapname I}`
* To wrap a `template view`, add the `wrap=Wrapname` element to the `include statement`.

**Header.phtml file**

```php

<footer class="<?= $class ?>">

```

**Body.phtml file**

```php


echo $this->row->content;

```

**Footer.phtml file**

```php

</footer>
```
