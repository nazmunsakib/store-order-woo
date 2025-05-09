<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9b8ce7371908d42bbeb6545facdfed72
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'StoreOrderWoo\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'StoreOrderWoo\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9b8ce7371908d42bbeb6545facdfed72::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9b8ce7371908d42bbeb6545facdfed72::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit9b8ce7371908d42bbeb6545facdfed72::$classMap;

        }, null, ClassLoader::class);
    }
}
