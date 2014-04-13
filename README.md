=======
Molajito Render Package
=======

[![Build Status](https://travis-ci.org/Molajo/Render.png?branch=master)](https://travis-ci.org/Molajo/Molajito)

Molajito is a template environment for frontend developers who want to focus on rendered output, not programming.

## How it works:

Molajito starts by including a [Theme](https://github.com/Molajo/Molajito#theme) file
 as rendered output.

The `theme` contains [Include Statements](https://github.com/Molajo/Molajito#include-statements)
 discovered by Molajito during parsing and used by Molajito to identify what
 `view` is to be rendered at that location.

Molajito uses three different types of `views':
* [Page](https://github.com/Molajo/Molajito#page) views define layouts.
A site typically has different types of layouts and the page view is useful for that purpose.
 Molajito does not pass data into the `page view`, it only includes the `page view` file.

* [Template](https://github.com/Molajo/Molajito#template) Views define one specific area of
 the page, for example a `template view` could render a navigation menu, a blog post, or
  an author profile. Molajito passes in data to the `template view` in support of the rendering
  process.

* [Wrap](https://github.com/Molajo/Molajito#wrap) views `wrap` the rendered output from a
`template view` in a specific manner. For example, a wrap might enclose the template output
in an `<article>`, `<footer>`, or `<header>` content-specific HTML5 element.  A wrap might
also be used to achieve a certain visual effect for the content, for example by including
a message in a div with an `alert` class.

You can also use `include statements` to define
[positions](https://github.com/Molajo/Molajito#position) which are placeholders that can be
associated with one or more `template views`. For example, you might want a `sidebar position`
for a blog that can be configured by site builders.

### Theme

Themes are the first rendered output and therefore drive the rendering process.
* [Themes](https://github.com/Molajo/Molajito#theme) provide the starting point for rendering and
typically define CSS and JS statements.

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

The Include syntax is simple `{I name=Value I}`:
 * `{I` marks the start of an include statement
 * `type=` Set to `template` or `page`.
 If omitted, Molajito first assumes it is a `position`, and then looks for a like-named `template`.
 * `Name` identifies the name of the View associated with the type specified.
 * `I}` marks the end of an include statement

Page `{I page=<? $this->runtime_data->page_name ?> I}`
* The page value is automatically passed in via `$this->runtime_data->page_name` to the Theme.

Position `{I Positionname I}`
* If `type=` is omitted, Molajito treats it as a `position`, first. But, if `Positionname` is
not found, Molajito looks for a `template view` with that name.

Template `{I template=Templatename I}`
* Molajito looks for a `template view` with the name `Templatename`.

Wrap `{I template=Templatename wrap=Wrapname I}`
* To wrap a `template view`, add the `wrap=Wrapname` element to the `include statement`.

Extra attributes `{I template=Templatename class=current,error dog=food I}` can be added to
the `include statement` by adding named pair attributes.


Notes:

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

