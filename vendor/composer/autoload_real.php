<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitff3ff43131a8f466791ff0967c5cfb75
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInitff3ff43131a8f466791ff0967c5cfb75', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitff3ff43131a8f466791ff0967c5cfb75', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitff3ff43131a8f466791ff0967c5cfb75::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
