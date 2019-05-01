<?php


namespace ComposerWorkspacesPlugin;

use Composer\IO\IOInterface;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class WorkspaceRoot
{

    /** @var IOInterface */
    protected $io;

    /** @var Workspace[] */
    protected $workspaces = [];

    /** @var string[] */
    protected $globs = [];

    /** @var string */
    protected $path;

    /** @var Filesystem */
    protected $filesystem;

    /**
     * WorkspaceRoot constructor.
     * @param IOInterface $io
     * @param string $path
     */
    public function __construct(IOInterface $io, $path)
    {
        $this->io = $io;
        $this->path = realpath($path);
        $this->filesystem = new Filesystem();
    }

    public function scanWorkspaces()
    {
        $finder = new Finder();

        /** @var SplFileInfo[] $composerFiles */
        $composerFiles = $finder
            ->files()
            ->in($this->globs)
            ->name('composer.json')
            ->depth(0);

        foreach ($composerFiles as $composerFile) {
            try {
                $workspace = Workspace::fromFile($composerFile, $this->path);
                $name = $workspace->getName();

                if (!$name) {
                    throw new Exception("No 'name' field found in $composerFile");
                }

                $this->workspaces[$name] = $workspace;
            } catch (Exception $exception) {
                $this->io->writeError(
                    '<warning>Skipped ' . $composerFile->getPath() . ': could not load package.' .
                    $exception->getMessage() . '</warning>');
            }
        }
    }

    /**
     * @param string[] $globs
     * @return WorkspaceRoot
     */
    public function setGlobs(array $globs): WorkspaceRoot
    {
        $this->globs = array_map(function ($glob) {
            return Path::join($this->path, $glob);
        }, $globs);

        return $this;
    }

    /**
     * @param string $name
     * @return Workspace|null
     */
    public function getWorkspaceByName($name)
    {
        return $this->hasWorkspace($name) ? $this->workspaces[$name] : null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasWorkspace($name)
    {
        return array_key_exists($name, $this->workspaces);
    }

    /** @return Workspace[] */
    public function getWorkspaces()
    {
        return $this->workspaces;
    }

    /**
     * @param string $path
     * @return string
     */
    public function getPathRelativeTo($path)
    {
        return Path::normalize($this->filesystem->makePathRelative($this->path, $path));
    }

    public function resolveWorkspace($path)
    {
        $path = Path::normalize($path);
        foreach ($this->workspaces as $workspace) {
            if ($path === $workspace->getAbsolutePath()) {
                return $workspace;
            }
        }

        return null;
    }

}
