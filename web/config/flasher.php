<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Inject Assets
    |--------------------------------------------------------------------------
    |
    | This option determines whether PHPFlasher should automatically inject
    | its assets (CSS and JavaScript) into your HTML response. Since we are
    | bundling assets with Vite, this should be set to false.
    |
    */

    'inject_assets' => false,

    /*
    |--------------------------------------------------------------------------
    | Scripts
    |--------------------------------------------------------------------------
    |
    | This option allows you to specify custom JavaScript files to be loaded
    | by PHPFlasher. We leave this empty to prevent any default scripts
    | from being loaded, as we are handling them via the frontend bundle.
    |
    */

    'scripts' => [],

    /*
    |--------------------------------------------------------------------------
    | Styles
    |--------------------------------------------------------------------------
    |
    | This option allows you to specify custom CSS files to be loaded by
    | PHPFlasher. We leave this empty to prevent any default styles from
    | being loaded, as we are handling them via the frontend bundle.
    |
    */

    'styles' => [],

];
