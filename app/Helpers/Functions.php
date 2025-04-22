<?php

// function assets_url($uri, $default_path = 'web')
// {
//     return url($default_path . DIRECTORY_SEPARATOR . $uri);
// }

if (!function_exists('assets_url')) {
    function assets_url($uri, $default_path = 'storage')
    {
        return url($default_path . '/' . ltrim($uri, '/'));
    }
}