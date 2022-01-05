<?php

    if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly

    // Accepted languages (code => display_name)
    // get codes from https://docs.oracle.com/cd/E13214_01/wli/docs92/xref/xqisocodes.html
    $cfg['langs'] = array(
                    'de'=>'Deutsch',
                    'en'=>'English',
                    'fr'=>'Français',
                );

    // Templates to exclude
    $cfg['exclude'] = array();

    // Set to 1 to use prettyURLs e.g. http://www.yoursite.com/en/products/
    $cfg['prettyurls'] = 1;

    // Set to 1 if the language used by the visitor's browser is a factor in choosing the current language
    $cfg['use_browser_lang'] = 0;
