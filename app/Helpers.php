<?php

if (!function_exists('asset_dir')) {
    function asset_dir($path, $secure = null)
    {
        $url = config('app.asset_url') . $path;

        if ($secure) {
            str_replace(['http://', 'https://', $url]);
        }

        return $url;
    }
}

if (!function_exists('media')) {
    function media($path, $secure = null)
    {
        $url = config('app.media_url') . $path;

        if ($secure) {
            str_replace(['http://', 'https://', $url]);
        }

        return $url;
    }
}

if (!function_exists('keep')) {
    function keep($key, $callback)
    {
        if (!\Illuminate\Support\Facades\Config::has("app.{$key}")){
            \Illuminate\Support\Facades\Config::set("app.{$key}", $callback());
        }

        return \Illuminate\Support\Facades\Config::get("app.{$key}");
    }
}
