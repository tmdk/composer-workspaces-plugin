<?php


namespace ComposerWorkspacesPlugin\Commands;

use Composer\Config\JsonConfigSource;
use Composer\Json\JsonFile;
use ComposerWorkspacesPlugin\Workspace;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

class BootstrapCommand extends BaseWorkspaceCommand
{

    protected function configure()
    {
        $this
            ->setName('workspaces:bootstrap')
            ->setDescription('Bootstraps workspace packages.')
            ->setHelp(
                <<<EOT
The <info>bootstrap</info> command configures all found workspace packages and links them to the workspace root.
EOT
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->isWorkspaceRoot()) {
            return $this->notifyWorkspacesDisabled();
        }

        $workspaceRoot = $this->getWorkspaceRoot();

        foreach ($workspaceRoot->getWorkspaces() as $workspace) {
            $config = new JsonConfigSource(new JsonFile($workspace->getComposerFilePath()));
            $this->addWorkspaceRootProperty($config, $workspace);
        }

        $application = $this->getApplication();

        // prevents runInWorkspace from exiting after running the command
        $application->setAutoExit(false);

        $requireCommand = new StringInput('require tmdk/composer-workspaces-plugin=' . $this->getPluginVersion());

        $exitCode = 0;

        foreach ($workspaceRoot->getWorkspaces() as $workspace) {
            $exitCode = max($exitCode, $this->runInWorkspace($workspace->getName(), $requireCommand, $output));
        }

        return $exitCode;
    }

    protected function addWorkspaceRootProperty(JsonConfigSource $config, Workspace $workspace)
    {
        $workspaceRoot = $this->getWorkspaceRoot();
        $config->addProperty('extra.workspace-root', $workspaceRoot->getPathRelativeTo($workspace->getAbsolutePath()));
    }

}
