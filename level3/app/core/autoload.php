<?php

spl_autoload_register(function ($class) {
        include '../' . str_replace('\\', DIRECTORY_SEPARATOR, $class . '.php');
});