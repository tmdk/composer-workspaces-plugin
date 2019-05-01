<?php


namespace ComposerWorkspacesPlugin\Composer;

use Composer\Json\JsonFile;
use Composer\Package\Loader\LoaderInterface;
use Composer\Package\PackageInterface;

class UnversionedJsonPackageLoader
{

    /** @var LoaderInterface $loader */
    private $loader;

    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @param string|JsonFile $json A filename, json string or JsonFile instance to load the package from
     * @return PackageInterface
     */
    public function load($json)
    {
        $config = [];

        if ($json instanceof JsonFile) {
            $config = $json->read();
        } elseif (file_exists($json)) {
            $config = JsonFile::parseJson(file_get_contents($json), $json);
        } elseif (is_string($json)) {
            $config = JsonFile::parseJson($json);
        }

        $config['version'] = $config['version'] ?? 'dev-master';

        return $this->loader->load($config);
    }
}
