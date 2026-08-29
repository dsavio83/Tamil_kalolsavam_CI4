<?php

if (!function_exists('connectionLink')) {
    function connectionLink()
    {
        $link = mysqli_connect("localhost", "root", "", "tk2025") or die($link);
        return $link;
    }
}
