<?php


namespace ComposerWorkspacesPlugin;

use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Package\Loader\ArrayLoader;
use Composer\Package\PackageInterface;
use ComposerWorkspacesPlugin\Composer\UnversionedJsonPackageLoader;

class WorkspaceRootRegistry
{
    /** @var WorkspaceRoot[] */
    static $registry = [];

    /** @var IOInterface */
    protected $io;

    /**
     * WorkspaceRootFactory constructor.
     * @param IOInterface $io
     */
    public function __construct(IOInterface $io)
    {
        $this->io = $io;
    }

    /**
     * @param $directory string
     * @param PackageInterface|null $package
     * @return WorkspaceRoot
     */
    public function createWorkspaceRoot($directory = null, PackageInterface $package = null)
    {
        if (!$directory) {
            $directory = getcwd();
        }

        $key = $directory;

        if ($this->isRegistered($key)) {
            return self::$registry[$key];
        }

        if (!$package) {
            $package = $this->loadPackage($directory);
        }

        $config = WorkspaceRootConfig::fromPackage($package);
        $config->validate();

        $workspaceRoot = new WorkspaceRoot($this->io, $directory);
        $workspaceRoot
            ->setGlobs($config->getGlobs())
            ->scanWorkspaces();

        self::$registry[$key] = $workspaceRoot;

        return $workspaceRoot;
    }

    /**
     * @param $path
     * @return bool
     */
    protected function isRegistered($path)
    {
        return array_key_exists($path, self::$registry);
    }

    /**
     * @param $directory
     * @return PackageInterface
     */
    protected function loadPackage($directory)
    {
        $loader = new UnversionedJsonPackageLoader(new ArrayLoader());

        return $loader->load(new JsonFile("$directory/composer.json"));
    }


}
