<?php


namespace ComposerWorkspacesPlugin;


use Composer\Json\JsonFile;
use Composer\Package\CompletePackage;
use Composer\Package\Loader\ArrayLoader;
use Composer\Package\PackageInterface;
use ComposerWorkspacesPlugin\Composer\UnversionedJsonPackageLoader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

class Workspace
{
    /** @var string */
    protected $path;
    /** @var CompletePackage */
    protected $package;
    /** @var string */
    protected $workspaceRootPath;
    /** @var Filesystem */
    protected $filesystem;

    /**
     * Workspace constructor.
     * @param string $path
     * @param string $workspaceRootPath
     * @param PackageInterface $package
     */
    public function __construct($path, $workspaceRootPath, $package)
    {
        $this->path = $path;
        $this->workspaceRootPath = $workspaceRootPath;
        $this->package = $package;
        $this->filesystem = new Filesystem();
    }

    /**
     * @param SplFileInfo $composerFile
     * @param string $workspaceRootPath
     * @return Workspace
     */
    static function fromFile(SplFileInfo $composerFile, $workspaceRootPath)
    {
        $jsonFile = new JsonFile($composerFile);
        $loader = new UnversionedJsonPackageLoader(new ArrayLoader());
        $path = dirname($composerFile->getRealPath());

        return new self($path, $workspaceRootPath, $loader->load($jsonFile));
    }

    /**
     * @return string
     */
    public function getAbsolutePath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getRelativePath(): string
    {
        return $this->getPathRelativeTo($this->workspaceRootPath);
    }

    /**
     * @param string $path
     * @return string
     */
    public function getPathRelativeTo($path): string
    {
        return Path::normalize($this->filesystem->makePathRelative($this->path, $path));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->package->getName();
    }

    /**
     * @return string
     */
    public function getComposerFilePath()
    {
        return "$this->path/composer.json";
    }

}
