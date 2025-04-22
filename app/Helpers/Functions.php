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

function media_storage_url($file, ?string $conversion = null): string
{
    $id = $file->id;
    $name = $file->name;

    $path = $conversion
        ? "storage/app/public/{$id}/conversions/{$name}-{$conversion}.jpg"
        : "storage/app/public/{$id}/{$file->file_name}";

    return asset("nursing-app/{$path}");
}

function UR_exists($url){
    $headers=get_headers($url);
    return stripos($headers[0],"200 OK")?true:false;
}