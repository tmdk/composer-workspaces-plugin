<?php


namespace ComposerWorkspacesPlugin\Commands;

use Composer\Config\JsonConfigSource;
use Composer\Factory;
use Composer\Json\JsonFile;
use ComposerWorkspacesPlugin\WorkspaceRootConfig;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends BaseWorkspaceCommand
{

    protected function configure()
    {
        $this
            ->setName('workspaces:init')
            ->setDescription('Initialize workspace configuration for this package.')
            ->setDefinition(array(
                new InputOption('packages', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                    'Path to workspace packages (wildcards allowed)'),
            ))
            ->setHelp(
                <<<EOT
The <info>init</info> command configures workspaces for this package.
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
        $io = $this->getIO();

        $composerFile = new JsonFile(Factory::getComposerFile());
        $configSource = new JsonConfigSource($composerFile);

        $workspaceRootConfig = WorkspaceRootConfig::fromJsonFile($composerFile);
        $workspaceRootConfig->setGlobs($input->getOption('packages'));

        $configSource->addProperty('extra.workspaces', $workspaceRootConfig->toArray());

        $io->writeError('<info>Configuration added to composer.json</info>');

        return 0;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = $this->getIO();
        $packages = $input->getOption('packages') ?: 'packages/*';
        $packages = $io->ask(
            'Path to workspace packages (wildcards allowed) [<comment>' . $packages . '</comment>]: ',
            $packages
        );
        $input->setOption('packages', $packages);
    }
}
