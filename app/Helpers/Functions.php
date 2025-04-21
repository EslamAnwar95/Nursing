,؟<?php

function assets_url($uri, $default_path = 'web')
{
    return url($default_path . DIRECTORY_SEPARATOR . $uri);
}
