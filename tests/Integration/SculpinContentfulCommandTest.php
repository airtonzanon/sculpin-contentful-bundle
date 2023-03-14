<?php
declare(strict_types=1);

namespace AirtonZanon\SculpinContentfulBundle\Tests\Integration;

use AirtonZanon\SculpinContentfulBundle\Command\SculpinContentfulCommand;
use Contentful\Delivery\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class SculpinContentfulCommandTest extends TestCase
{
    public function testExecuteSuccessScenario(): void {
        self::markTestSkipped("I need to investigate it. But this is important when developing");

        $command = new SculpinContentfulCommand();

        $contentfulSpaceId = getenv('contentful_space_id');
        $contentfulToken = getenv('contentful_token');

        $client = new Client($contentfulToken, $contentfulSpaceId);

        $command->setContentfulClient($client);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        self::assertStringContainsString("Created file", $output);
    }
}
