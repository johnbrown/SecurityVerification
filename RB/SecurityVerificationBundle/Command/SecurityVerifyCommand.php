<?php

namespace RB\SecurityVerificationBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Class SecurityVerifyCommand
 * @package RB\SecruityVerificationBundle\Command
 * @author  John Brown <brown.john@gmail.com>
 */
class SecurityVerifyCommand extends ContainerAwareCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('rb:security:print')
            ->setDescription('Prints out the security associated with routes')
            ->setHelp("");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $service = $this->getContainer()->get('rb.security_verify');
        $info = $service->getSecurityInfo();
        $output->write($info);
    }
}