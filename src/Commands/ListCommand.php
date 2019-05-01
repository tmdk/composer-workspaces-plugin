<?php


namespace ComposerWorkspacesPlugin\Commands;


use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends BaseWorkspaceCommand
{
    protected function configure()
    {
        $this
            ->setName('workspaces:list')
            ->setDescription('List workspace packages.')
            ->setHelp(
                <<<EOT
List workspace packages.
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->isWorkspaceRoot()) {
            return $this->notifyWorkspacesDisabled();
        }

        $workspaces = $this->getWorkspaceRoot()->getWorkspaces();

        if (count($workspaces) < 1) {
            $output->writeln(['<comment>No workspaces found</comment>', '']);
            return 0;
        }

        $table = new Table($output);
        $table->setStyle('compact');
        $tableStyle = $table->getStyle();
        $tableStyle->setCellRowContentFormat('%s  ');
        $table->setHeaders(array('Name', 'Path'));

        foreach ($this->getWorkspaceRoot()->getWorkspaces() as $workspace) {
            $table->addRow(array(
                $workspace->getName(),
                $workspace->getRelativePath(),
            ));
        }

        $table->render();

        return 0;
    }

}
