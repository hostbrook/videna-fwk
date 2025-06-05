<?php

/**
 * Helpers, global available functions
 * Videna MVC Micro-Framework
 * 
 * @license Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @author HostBrook <support@hostbrook.com>
 */


/*
 * Returns enviroment value from $_ENV[] and $_SERVER[] with a correct type
 */
function env($name) 
{
    if (isset($_ENV[$name]))  return $_ENV[$name];

    if (isset($_SERVER[$name])) return $_SERVER[$name];

    return null;
}


/*
 * Escaping output to prevent XSS attacks
 */
function esc($value, 
            $flags = ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401,
            $encoding = 'UTF-8',
            $double_encode = true) 
{
    return htmlspecialchars($value, $flags, $encoding, $double_encode);
}