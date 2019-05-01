<?php


namespace ComposerWorkspacesPlugin\Commands;

use Composer\Command\BaseCommand;
use Composer\Console\Application;
use ComposerWorkspacesPlugin\Plugin;
use ComposerWorkspacesPlugin\WorkspaceRoot;
use Exception;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BaseWorkspaceCommand extends BaseCommand
{

    /** @var Plugin */
    protected $plugin;

    public function __construct(Plugin $plugin, string $name = null)
    {
        $this->plugin = $plugin;
        parent::__construct($name);
    }

    /**
     * @return bool
     */
    public function isWorkspaceRoot()
    {
        return $this->plugin->isWorkspaceRoot();
    }

    /**
     * @return int
     */
    public function notifyWorkspacesDisabled()
    {
        $this->getIO()->writeError('<comment>Workspaces are not enabled for this package.</comment>');
        return 0;
    }

    /**
     * @param string $workspaceName
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    public function runInWorkspace($workspaceName, $input, $output)
    {
        $oldWorkingDirectory = getcwd();
        $workspaceRoot = $this->getWorkspaceRoot();

        if (!$workspaceRoot->hasWorkspace($workspaceName)) {
            throw new RuntimeException('Workspace "' . $workspaceName . '" does not exist');
        }

        $workspace = $workspaceRoot->getWorkspaceByName($workspaceName);

        /** @var Application $application */
        $application = $this->getApplication();

        $exitCode = 1;

        try {
            try {
                chdir($workspace->getAbsolutePath());
            } catch (Exception $e) {
                throw new RuntimeException(
                    'Could not switch to workspace directory "' . $workspace->getRelativePath() . '"', 0, $e);
            }

            $this->getIO()->writeError('<info>Changed current directory to ' . $workspace->getRelativePath() . '</info>');

            $application->resetComposer();

            $exitCode = $application->run($input, $output);
        } finally {
            chdir($oldWorkingDirectory);
        }

        return $exitCode;
    }

    /**
     * @return WorkspaceRoot
     */
    public function getWorkspaceRoot()
    {
        return $this->plugin->getWorkspaceRoot();
    }

    /**
     * @return string
     */
    public function getPluginVersion() {
        return $this->plugin->getVersion();
    }

}
