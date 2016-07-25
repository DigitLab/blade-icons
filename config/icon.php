<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Icon SVG Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your icon SVGs.
    |
    */

    'paths' => [
        realpath(base_path('resources/svgs')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled Icon Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled icon templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    */

    'compiled' => realpath(storage_path('framework/icons')),

];