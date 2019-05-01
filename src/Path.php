<?php


namespace ComposerWorkspacesPlugin;

class Path
{

    /**
     * @param string ...$paths
     * @return string
     */
    static function join(...$paths)
    {
        return self::normalize(implode(DIRECTORY_SEPARATOR, $paths));
    }

    /**
     * @param string $path
     * @return string
     */
    static function normalize($path)
    {
        return rtrim($path, '\\/');
    }

}
