=======
Molajito Render Package
=======

[![Build Status](https://travis-ci.org/Molajo/Render.png?branch=master)](https://travis-ci.org/Molajo/Molajito)

Molajito is a template environment for frontend developers who want to focus on rendered output, not programming.

## Basic usage:

Molajito starts by including the `Theme` file as rendered output. Next, it parses the output
for `include statements` which are used to identify and render views. The rendered output for
each view is injected in place of its associated include statement and the combined results are
again parsed for `include statements`. This process continues until the parsing process yields no
results, or the maximum number of times the process is allowed has been reached. When complete,
the rendered page is returned to the application.

uses the following for rendering output:

* [Themes](https://github.com/Molajo/Molajito#theme) provide the starting point for rendering and
typically define CSS and JS statements.
* [Page](https://github.com/Molajo/Molajito#page) views define various layouts for the site,
for example, you might have a different page for a blog or a post or a home page.
* [Include Statements](https://github.com/Molajo/Molajito#include-statements) are tokens which define
the type, name and placement of views.
* [Template](https://github.com/Molajo/Molajito#template) Views define one specific block for the page,
for example a post is rendered from a template view, as is an author profile.
* [Wrap](https://github.com/Molajo/Molajito#wrap) views `wrap` the rendered output from a template view
in a specific manner. For example, a wrap might enclose the template output in content-specific HTML5 element,
like `<article>`, `<footer>`, or `<header>`. A wrap might also be used to achieve a certain visual effect.
* [Positions](https://github.com/Molajo/Molajito#position) can be used to define a block
that can be associated with one or more `template views`. For example, you might want a `sidebar position`
for a blog that can be configured by site builders differently.

### Theme

Themes are the first rendered output and therefore drive the rendering process.

Molajito injects a data object called `$this->runtime_data` into the Theme.
It contains the `$this->runtime_data->page_name` value used to specify the page include statement in
the example, below. You can add data elements to the `$this->runtime_data`, as needed.
In this example `site_name` is available within the data object and used for rendering `title`.

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

Molajito uses `include statements` to define where specific views should be rendered.
`Include statements` defined within templates, too, so you can create reusable templates
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

