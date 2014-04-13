=======
Molajito Render Package
=======

[![Build Status](https://travis-ci.org/Molajo/Render.png?branch=master)](https://travis-ci.org/Molajo/Molajito)

Molajito is a template environment that empowers frontend developers with full control over rendered output, making it
*as easy as possible* to move from HTML mock-ups to "render ready" views.

* Escapes all data prior to injecting view with data
* Handles row `looping`, injecting the view one row

## Basic usage:

Molajito uses the following for rendering output:

* `Themes` base information for rendered output, including CSS and JS
* `Page Views` use to define different types of page layouts, like blog, post, contact, home, etc.
* `Template Views` renders one specific layout, for example a post or an author profile
* `Wrap Views` wraps rendered output from a template view in a specific manner, for example, it might
 enclose the template output in content-specific HTML5 element, like `<article>`, `<footer>`, `<header>`,
 `<nav>`, or `<section>`. It can also be used to render a block with a certain visual effect.
 * `Positions` is a way to define a block that is associated with one or more `template views`.


### Theme

Themes drive the rendering process.

Molajito injects two data objects into the theme:
 * `$this->runtime_data` an object you can use to define configuration data
 * `$this->row` an object which contains the `page_name`, linking to the page view

```php

&lt;!doctype html&gt;
&lt;html class="no-js" lang="en"&gt;
&lt;head&gt;
    &lt;meta charset="utf-8"/&gt;
    &lt;meta name="viewport" content="width=device-width, initial-scale=1.0"/&gt;
    &lt;title&gt;&lt;?= $this->;runtime_data->parameters->site_title ?>&lt;/title&gt;
    &lt;link rel="stylesheet" href="/css/custom.css"/&gt;
&lt;/head&gt;
&lt;body&gt;
&lt;div class="page-wrap"&gt;
    {I Navbar I}
    {I Breadcrumbs I}
    {I page=&lt;?= $this->row->page_name ?&gt; I}
&lt;/div&gt;
    {I Footer I}
&lt;/body&gt;
&lt;/html&gt;

```

### Include Statements

Molajito uses `include statements` to define where specific views should be rendered.

The syntax is simple. The following is used to define a `position`. But, if a like named `position`
does not exist, Molajito will select a `template` with the same name.

```php

<?php
    {I Templatename I}
```

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

