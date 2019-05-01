<?php


namespace ComposerWorkspacesPlugin;

use Composer\Package\PackageInterface;
use RuntimeException;

class WorkspaceConfig
{

    /** @var string */
    protected $workspaceRoot;

    /**
     * Config constructor.
     * @param string $workspaceRoot
     */
    public function __construct($workspaceRoot)
    {
        $this->workspaceRoot = $workspaceRoot;
    }

    /**
     * @param PackageInterface $package
     * @return WorkspaceConfig
     */
    public static function fromPackage(PackageInterface $package)
    {
        $config = new self($package->getExtra()['workspace-root'] ?? '');

        $config->validate();

        return $config;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        if (empty($this->workspaceRoot)) {
            throw new RuntimeException("No workspace root specified");
        }

        if (!file_exists("$this->workspaceRoot/composer.json")) {
            throw new RuntimeException("Invalid workspace root specified: composer.json not found");
        }

        return true;
    }

    /**
     * @return string
     */
    public function getWorkspaceRootDirectory()
    {
        return realpath($this->workspaceRoot);
    }

}
