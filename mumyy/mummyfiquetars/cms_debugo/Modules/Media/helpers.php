<?php

if (! function_exists('getPathImage')) {
    function getPathImage($file)
    {
    	return Config('asgard.media.config.files-path') . $file->filename;
    }
}

if (! function_exists('getPathThumbImage')) {
    function getPathThumbImage($file, $thumbnail = 'mediumThumb')
    {
        $filename = $file->filename;
        $name = substr($filename, 0, strrpos($filename, '.'));
        $ext = substr($filename, strrpos($filename, '.') + 1);
        return Config('asgard.media.config.files-path') . $name . '_' . $thumbnail . '.' .$ext;
    }
}

if (! function_exists('convertLinkS3ToHttp')) {
    function convertLinkS3ToHttp($url){
        if(substr($url, 0,5) == 'https'){
            return str_replace('https', 'http', substr($url,0,5)) . substr($url,5);
        }
        return $url;
    }
}

if (! function_exists('get_url_query')) {
    function get_url_query($url, $url_query, $data){
        $arr = [];
        $split_query = explode('&', $url_query);
        foreach ($split_query as $key => $item) {
            if($item){
                $tmp = explode('=', $item);
                if(sizeof($tmp > 1)){
                    $query = [$tmp[0] => $tmp[1]];
                    $arr = array_merge($arr, $query);
                }
            }

        }
        $str = [];
        $arr = array_merge($arr, $data);
        foreach ($arr as $key => $item) {
            $str[] = $key.'='.$item;
        }
        // dd($url);
        return $url . '?' . implode('&', $str);

    }
}