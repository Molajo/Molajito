=======
Molajito Render Package
=======

[![Build Status](https://travis-ci.org/Molajo/Render.png?branch=master)](https://travis-ci.org/Molajo/Molajito)

Molajito is a template environment for frontend developers who want to focus on rendered output, not programming.

## Basic usage:

Molajito uses the following for rendering output:

* [Themes](https://github.com/Molajo/Molajito#theme) provide the starting point for rendering,
typically defines CSS and JS statements.
* [Page](https://github.com/Molajo/Molajito#page) Views define various layouts for the site,
for example, blog, post, contact, and home page layouts.
* [Page](https://github.com/Molajo/Molajito#page) Views define various layouts for the site,
for example, blog, post, contact, and home page layouts.
* [Template](https://github.com/Molajo/Molajito#template) Views used to render one specific block for the page,
for example a post or an author profile.
* [Wrap](https://github.com/Molajo/Molajito#wrap) Views rendered output from a template view in a specific manner,
for example, it might enclose the template output in content-specific HTML5 element,
like `<article>`, `<footer>`, or `<header>`. It can also be used to render a block with a certain visual effect.
* [Positions](https://github.com/Molajo/Molajito#position) define a block associated with one or more `template views`.

### Theme

Themes drive the rendering process.

Molajito injects a data object called `$this->runtime_data` into the Theme. You can add data elements
to the object.

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
    {I Navbar I}
    {I Breadcrumbs I}
    {I page=<?= $this->runtime_data->page_name ?> I}
</div>
    {I Footer I}
</body>
</html>

```

### Include Statements

Molajito uses `include statements` to define where specific views should be rendered. It
 can process include statements defined within templates, too, so you can create reusable templates
  that are referenced in many places in order to keep your views [DRY](http://en.wikipedia.org/wiki/Don%27t_repeat_yourself)
  and easy to maintain.

The Include syntax is simple.
 * `{I` marks the start of an include statement
 * `type=` Set to `position`, `template`, `page` or `wrap`. If omitted, Molajito first assumes it is a `position`, and then looks for a like-named `template`
 * `Name` identifies the name of the View of the type specified.
 * `I}` marks the end of an include statement

Examples:
* Position `{I Positionname I}`
* Page `{I page=<? $this->row->page_name ?> I}`
* Template `{I template=Templatename wrap=Wrapname I}`

 If `type=` is omitted, Molajito treats it as a `position`. If the `position` is not found,
 Molajito uses it as a `template`.


### Page

```php

<html>
<head>
    <title><?= $this->row->title; ?></title>
</head>

<body>

<?= $this->row->content_text; ?>

<?= $this->row->content_text; ?>

</body>
</html>


```

