<?php
namespace IMAG\SimpleCacheBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption
    ;

use Symfony\Component\Console\Output\OutputInterface;

class CacheClearCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('simplecache:cache-clear')
            ->setDescription('Clears the cache')
            ->addOption('entire-cache', null, InputOption::VALUE_NONE, 'If set, doesn\'t check the cache lifetime.')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entireCache = $input->getOption('entire-cache');
        $manager = $this->getContainer()->get('imag_simple_cache.cache_manager');

        if (true === $entireCache) {
            $removed = $manager->clearCache();
        } else {
            $removed = $manager->clearExpired();
        }

        foreach ($removed as $item) {
            $output->writeln("[CC]\t<comment>$item</comment>");
        }
        
        $output->writeln("\n<info>Cache clear successfully</info>");
    }
}