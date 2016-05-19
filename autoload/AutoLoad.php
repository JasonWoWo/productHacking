<?php

class AutoLoad
{
    /**
     * @param \Composer\Autoload\ClassLoader $loader
     */
    public static function autoLoader($loader)
    {
        $map = require __DIR__ . "/autoload_namespace.php";
        foreach ($map as $nameSpace => $path) {
            $loader->set($nameSpace, $path);
        }
    }
}
AutoLoad::autoLoader($ComposerClassLoader);