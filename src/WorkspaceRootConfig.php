<?php


namespace ComposerWorkspacesPlugin;

use Composer\Json\JsonFile;
use Composer\Package\PackageInterface;
use RuntimeException;

class WorkspaceRootConfig
{

    /** @var array */
    protected $config;

    /**
     * Config constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param PackageInterface $package
     * @return WorkspaceRootConfig
     */
    public static function fromPackage(PackageInterface $package)
    {
        return new self($package->getExtra()['workspaces'] ?? []);
    }

    public static function fromJsonFile(JsonFile $file)
    {
        $config = $file->read();
        return new self($config['extra']['workspaces'] ?? []);
    }

    /**
     * @return bool
     */
    public function validate()
    {
        $packages = $this->config['packages'] ?? [];

        if (!(is_string($packages) || is_array($packages) && count($packages))) {
            throw new RuntimeException("No workspace packages specified");
        }

        return true;
    }

    /**
     * @return string[]
     */
    public function getGlobs()
    {
        return (array)$this->config['packages'];
    }

    /**
     * @param array $globs
     * @return WorkspaceRootConfig
     */
    public function setGlobs($globs = [])
    {
        $this->config['packages'] = $globs;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->config;
    }

}
