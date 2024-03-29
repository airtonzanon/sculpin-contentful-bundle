<?php
declare(strict_types=1);

namespace AirtonZanon\SculpinContentfulBundle\Tests\Unit;

use AirtonZanon\SculpinContentfulBundle\Command\SculpinContentfulCommand;
use Contentful\Core\Resource\ResourceArray;
use Contentful\Delivery\Client;
use Contentful\Delivery\Resource\ContentType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class SculpinContentfulCommandTest extends TestCase
{
    private ContentType|MockObject $contentTypeMock;

    /**
     * @dataProvider  dpDifferentTitles
     */
    public function testExecuteSuccessScenario(
        string $title,
        string $language,
        string $expectedFileName
    ): void {
        $command = new SculpinContentfulCommand();
        $contentfulMock = $this->getMockBuilder(Client\ClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contentTypeMock = $this->getMockBuilder(ContentType::class)->disableOriginalConstructor()->getMock();

        $contentfulMock->expects($this->once())
            ->method('getEntries')
            ->willReturn(new ResourceArray($this->getFixture($title, $language), 1, 1, 1));

        $this->assertSame("contentful:fetch", $command->getName());

        $command->setContentfulClient($contentfulMock);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertEquals("Created file: source/_til/" . $expectedFileName . "\n", $output);
    }

    public static function dpDifferentTitles(): array
    {
        return [
            "pt-br" => ["Métricas caçador mamão", "pt-BR", "2021-05-20-mtricas-caador-mamo.md"],
            "en-us" => ["Fake entry", "en-US", "2021-05-20-fake-entry.md"],
        ];
    }

    private function getFixture(string $title, string $language): array
    {
        $this->contentTypeMock->method('getName')->willReturn('til');

        return [
            new FakeEntry([
                'date' => new \DateTimeImmutable('2021-05-20'),
                'title' => $title,
                'language' => $language,
                'environment' => "test",
                'space' => '123space',
                'contentMarkdown' => <<<EOL
### Markdown Title

* Item 1
* Item 2
EOL,], [
                'type' => 'Entry',
                'id' => '123-id',
                'createdAt' => '2021-10-01',
                'updatedAt' => '2021-10-01',
                'environment' => "test",
                'space' => '123space',
                'contentType' => $this->contentTypeMock,
            ])];
    }
}
