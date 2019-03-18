<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5b3d7beef8292e1010b3a535c4d0d893
{
    public static $files = array (
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        'ca1cca1354b2a9fc796c7bab93d8f1dc' => __DIR__ . '/..' . '/krak/fn/src/fn.php',
        'da31daf3a9c2e9b8e493e2cd671ab345' => __DIR__ . '/..' . '/krak/fn/src/curried.generated.php',
        '49cace45680775b7a466e44b5f5b9390' => __DIR__ . '/..' . '/krak/fn/src/consts.generated.php',
        '34b267f5345746f164e2703cceadce96' => __DIR__ . '/..' . '/krak/fn/src/consts.ns.generated.php',
        '4c7cc18da1771aba346601648f7528cf' => __DIR__ . '/..' . '/krak/fn/src/generate.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Contracts\\' => 18,
            'Symfony\\Component\\Translation\\' => 30,
        ),
        'K' => 
        array (
            'Krak\\Fn\\' => 8,
        ),
        'C' => 
        array (
            'Carbon\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Contracts\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/contracts',
        ),
        'Symfony\\Component\\Translation\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/translation',
        ),
        'Krak\\Fn\\' => 
        array (
            0 => __DIR__ . '/..' . '/krak/fn/src',
        ),
        'Carbon\\' => 
        array (
            0 => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon',
        ),
    );

    public static $prefixesPsr0 = array (
        's' => 
        array (
            'stringEncode' => 
            array (
                0 => __DIR__ . '/..' . '/paquettg/string-encode/src',
            ),
        ),
        'P' => 
        array (
            'PHPHtmlParser' => 
            array (
                0 => __DIR__ . '/..' . '/paquettg/php-html-parser/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5b3d7beef8292e1010b3a535c4d0d893::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5b3d7beef8292e1010b3a535c4d0d893::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit5b3d7beef8292e1010b3a535c4d0d893::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
