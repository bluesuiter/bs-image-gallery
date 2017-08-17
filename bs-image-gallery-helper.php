<?php

if (!function_exists('handlePostData'))
{
    function handlePostData($key)
    {
        if (isset($_POST[$key]))
        {
            if (!is_array($_POST[$key]))
            {
                return htmlspecialchars(trim($_POST[$key]));
            }
            else
            {
                $out = [];
                if(!empty($_POST[$key]))
                {
                    foreach ($_POST[$key] as $k => $v)
                    {
                        $out[$k] = htmlspecialchars(trim($v));
                    }
                    return $out;
                }
            }
        }
    }
}


if(!function_exists('_bsigLodFile'))
{
    function _bsigLodFile($file)
    {
        try
        {
            if(file_exists($file))
            {
                require_once($file);
                return true;
            }
        }
        catch(Excepion $e)
        {
            echo 'Error : Function _sopLodLib creating an error.';
            return false;
        }
    }
}


if(!function_exists('checkArrayValue'))
{
    function checkArrayValue($arr, $key)
    {
        if (isset($arr[$key]) && !empty($arr[$key]))
        {
            return $arr[$key];
        }
        return false;
    }
}