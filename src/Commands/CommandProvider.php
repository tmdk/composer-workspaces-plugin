<?php


namespace ComposerWorkspacesPlugin\Commands;


use Composer\Command\BaseCommand;
use Composer\Plugin\Capability\CommandProvider as CommandProviderInterface;
use ComposerWorkspacesPlugin\Plugin;

class CommandProvider implements CommandProviderInterface
{
    /** @var Plugin */
    protected $plugin;

    public function __construct($args)
    {
        list('plugin' => $this->plugin) = $args;
    }

    /**
     * Retrieves an array of commands
     *
     * @return BaseCommand[]
     */
    public function getCommands()
    {
        if ($this->plugin->isWorkspaceRoot()) {
            return [
                new ListCommand($this->plugin),
                new WorkspaceCommand($this->plugin),
                new InitCommand($this->plugin),
                new BootstrapCommand($this->plugin),
            ];
        }

        return [
            new InitCommand($this->plugin)
        ];
    }
}
