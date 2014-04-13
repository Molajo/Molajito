=======
Molajito Render Package
=======

[![Build Status](https://travis-ci.org/Molajo/Render.png?branch=master)](https://travis-ci.org/Molajo/Molajito)

Molajito is a template environment that empowers frontend developers with full control over rendered output, making it
*as easy as possible* to move from HTML mock-ups to "render ready" views.

* Escapes all data prior to rendering view
* Handles row `looping`, feeding one row at a time to views

## Simple example:


Molajito uses the following types of Views for rendering output:

* **Themes** - base information for rendered output, including CSS and JS
* **Page Views** - useful for defining different types of page layouts, like blog, post, contact, home, etc.
* **Template Views** - renders one specific layout, for example a post or an author profile
* **Wrap Views** - wraps the rendered output for a template view in a specific manner, for example, in an HTML 5
footer or to build a certain visual chrome

### Theme

In this [example Theme](https://github.com/Molajo/Molajito/blob/master/.dev/Sample/Public/Foundation5/Index.phtml#L1),
the Foundation 5

```php

<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= $this->runtime_data->parameters->site_title ?></title>
    <link rel="stylesheet" href="/Foundation5/webicons/webicons.css"/>
    <link rel="stylesheet" href="/Foundation5/css/foundation.css"/>
    <link rel="stylesheet" href="/Foundation5/css/custom.css"/>
    <script src="/Foundation5/js/vendor/modernizr.js"></script>
</head>
<body>
<div class="page-wrap">
    {I Navbar I}
    {I Breadcrumbs I}
    {I page=<?= $this->row->page_name; ?> I}
</div>
    {I Footer I}
<script src="/Foundation5/js/vendor/jquery.js"></script>
<script src="/Foundation5/js/foundation.min.js"></script>
<script src="/Foundation5/js/foundation/foundation.orbit.js"></script>
<script src="/Foundation5/js/foundation/foundation.reveal.js"></script>
<script src="/Foundation5/js/foundation/foundation.clearing.js"></script>
<script>
    $(document).foundation();
</script>
</body>
</html>


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

