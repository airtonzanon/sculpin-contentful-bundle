<?php

declare(strict_types=1);

namespace AirtonZanon\SculpinContentfulBundle;

use AirtonZanon\SculpinContentfulBundle\Command\SculpinContentfulCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SculpinContentfulBundle extends Bundle
{
    public function registerCommands(Application $application)
    {
        $application->add(new SculpinContentfulCommand());
    }
}
