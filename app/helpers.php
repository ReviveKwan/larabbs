<?php

function route_class()
{
    return str_replace('.', '-', \Illuminate\Routing\Route::currentRouteName());
}
