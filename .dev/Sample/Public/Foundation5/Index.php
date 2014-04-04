<?php
/**
 * Foundation 5 Sample Theme for Molajito
 *
 * @package    Molajito
 * @link       Molajito
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
?>
<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Molajito | Foundation 5 | Welcome</title>
      <link rel="stylesheet" href="<?= $page_url; ?>/Foundation5/webicons/webicons.css" />
      <link rel="stylesheet" href="<?= $page_url; ?>/Foundation5/css/foundation.css" />
      <script src="<?= $page_url; ?>/Foundation5/js/vendor/modernizr.js"></script>
  </head>
  <body>
<?php
include FOLDER_BASE . '/Views/Foundation5/Templates/Navbar/Custom.phtml';
if ($page === 'Home') {
} else {
    include FOLDER_BASE . '/Views/Foundation5/Templates/Breadcrumbs/Header.phtml';
    include FOLDER_BASE . '/Views/Foundation5/Templates/Breadcrumbs/Body.phtml';
    include FOLDER_BASE . '/Views/Foundation5/Templates/Breadcrumbs/Footer.phtml';
}
if (file_exists(FOLDER_BASE . '/Views/Foundation5/Pages/' . $page . '/Index.phtml')) :
    include FOLDER_BASE . '/Views/Foundation5/Pages/' . $page . '/Index.phtml';
else :
    include FOLDER_BASE . '/Views/Foundation5/Pages/Home/Index.phtml';
endif;
include FOLDER_BASE . '/Views/Foundation5/Templates/Footer/Custom.phtml';
?>
<script src="<?= $page_url; ?>/Foundation5/js/vendor/jquery.js"></script>
<script src="<?= $page_url; ?>/Foundation5/js/foundation.min.js"></script>
<script src="<?= $page_url; ?>/Foundation5/js/foundation/foundation.orbit.js"></script>
<script src="<?= $page_url; ?>/Foundation5/js/foundation/foundation.reveal.js"></script>
<script src="<?= $page_url; ?>/Foundation5/js/foundation/foundation.clearing.js"></script>
<script>
    $(document).foundation();
</script>
  </body>
</html>
