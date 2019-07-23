<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\Services\ParseService;

class ParseCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:parse';

    /**
     * @var ParseService
     */
    protected $parseService;

    /**
     * ParseCommand constructor.
     * @param ParseService $parseService
     */
    public function __construct(ParseService $parseService)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor
        $this->parseService = $parseService;

        parent::__construct();
    }

    protected function configure()
    {

        $this
            ->setName('args')
            ->setDescription('Describe args behaviors')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('site', 's',  InputOption::VALUE_REQUIRED),
                    new InputOption('depth', 'd', InputOption::VALUE_OPTIONAL),
                    new InputOption('count_pages', 'p', InputOption::VALUE_OPTIONAL),
                ])
            );
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pageCount = !is_null($input->getOption('count_pages')) ? $input->getOption('count_pages') : 0;
        $this->parseService->crawl($input->getOption('site'), $input->getOption('depth'), $pageCount);
    }
}