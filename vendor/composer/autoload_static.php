<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7f2401f0214852afd5437bca9591f17c
{
    public static $prefixLengthsPsr4 = array (
        'V' => 
        array (
            'Videna\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Videna\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7f2401f0214852afd5437bca9591f17c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7f2401f0214852afd5437bca9591f17c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit7f2401f0214852afd5437bca9591f17c::$classMap;

        }, null, ClassLoader::class);
    }
}
