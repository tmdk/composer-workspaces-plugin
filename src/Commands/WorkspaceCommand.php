<?php


namespace ComposerWorkspacesPlugin\Commands;


use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

class WorkspaceCommand extends BaseWorkspaceCommand
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        if (!$this->isWorkspaceRoot()) {
            return $this->notifyWorkspacesDisabled();
        }

        $args = $this->parseInput($input);

        // show help for this command if no command was found
        if (count($args) < 3) {
            return parent::run($input, $output);
        }

        list(, $workspace) = $args;

        $rewrittenInput = preg_replace('/\bworkspace\s+' . preg_quote($workspace, '/') . '/', '', (string)$input, 1);

        $workspace = trim($workspace, "'\"");

        return $this->runInWorkspace($workspace, new StringInput($rewrittenInput), $output);
    }

    /**
     * @param InputInterface $input
     * @return array
     */
    protected function parseInput(InputInterface $input)
    {
        // extract real command name
        $tokens = preg_split('{\s+}', (string)$input);
        $args = [];
        foreach ($tokens as $token) {
            if ($token) {
                // don't allow options between arguments
                if ($token[0] === '-') {
                    break;
                }
                $args[] = $token;
                if (count($args) >= 3) {
                    break;
                }
            }
        }

        return $args;
    }

    /**
     * {@inheritDoc}
     */
    public function isProxyCommand()
    {
        return true;
    }

    protected function configure()
    {
        $this
            ->setName('workspace')
            ->setDescription('Run commands in the context of a workspace package.')
            ->setDefinition(array(
                new InputArgument('workspace', InputArgument::REQUIRED, ''),
                new InputArgument('command-name', InputArgument::REQUIRED, ''),
                new InputArgument('args', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, ''),
            ))
            ->setHelp(
                <<<EOT
Run commands in the context of a workspace package.
EOT
            );
    }

}
