<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Image Driver
    |--------------------------------------------------------------------------
    |
    | Driver used by Laravel's Image component (Illuminate\Image). Both drivers
    | are powered by intervention/image. Imagick is used when the extension is
    | installed (better with large photos); otherwise GD.
    |
    | Supported: "gd", "imagick"
    |
    */

    'default' => env('IMAGE_DRIVER', extension_loaded('imagick') ? 'imagick' : 'gd'),

];
