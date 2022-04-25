<?php require_once( 'couch/cms.php' ); ?>
  <cms:template title='Startseite' order='100' icon='home'/><cms:minify><cms:embed 'head/og.html' />
  <!doctype html>
  <html lang="de">
  <head>
    <meta charset="utf-8">
    <title><cms:show meta_title/></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="<cms:show meta_desc />">
    <link rel="stylesheet" href="<cms:addhash>/assets/css/main.min.css</cms:addhash>">
    <cms:embed 'head/favicon.html' />
    <meta property="og:title"       content="<cms:show meta_title />" >
    <meta property="og:description" content="<cms:show meta_desc />" >
    <meta property="og:image"       content="<cms:show meta_img />" >
    <meta property="og:url"         content="<cms:show meta_url />" >
    <meta property="og:type"        content="website" >
    <meta property="og:locale"      content="de_DE" >
  </head>
  <body>

  <div class="page">

  </div>
  </body>
  </html></cms:minify>
<?php COUCH::invoke(); ?>