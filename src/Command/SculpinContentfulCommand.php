<?php

declare(strict_types=1);

namespace AirtonZanon\SculpinContentfulBundle\Command;

use Contentful\Delivery\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;


final class SculpinContentfulCommand extends Command
{
    private Client\ClientInterface $contetful;

    protected function configure()
    {
        $contentfulSpaceId = getenv('contentful_space_id');
        $contentfulToken = getenv('contentful_token');

        $this
            ->setName('contentful:fetch')
            ->setDescription('Fetch Contentful data.')
            ->setHelp("The <info>contentful:fetch</info> command fetches contentful data and create files locally.")
            ->setContentfulClient(new Client($contentfulToken, $contentfulSpaceId));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entries = $this->contetful->getEntries()->getItems();

        foreach ($entries as $entry) {
            $filesystem = new Filesystem();

            $contentType = strtolower($entry->getSystemProperties()->getContentType()->getName());
            $filePath = $this->createPath($contentType, $entry['date'], $entry['title']);

            $filesystem->dumpFile(
                $filePath,
                $this->createContent($entry['language'], $entry['date'], $entry['title'], $entry['contentMarkdown'])
            );

            $output->writeln("Created file: " . $filePath );
        }

        return Command::SUCCESS;
    }

    private function createPath(string $type, \DateTimeImmutable $date, string $title): string
    {
        return "source/_" . $type . "/" . $date->format('Y-m-d') . "-" . $this->normalizeTitle($title) . '.md';
    }

    private function createContent(string $language, \DateTimeImmutable $date, string $title, string $body): string
    {
        return <<<EOL
---
createdAt: {$date->format('Y-m-d')}
title: {$title}
language: {$language}
---

{$body}
EOL;
    }

    private function normalizeTitle($title): string
    {
        $currentLocale = setlocale(LC_ALL, 0);
        setlocale(LC_ALL, 'en_US.utf8');

        $cleanTitle = strtolower($title);
        $cleanTitle = iconv('UTF-8', 'ASCII', $cleanTitle);
        $cleanTitle = preg_replace("/[^a-z0-9]+/", "-", $cleanTitle);

        setlocale(LC_ALL, $currentLocale);
        return $cleanTitle;
    }

    public function setContentfulClient(Client\ClientInterface $client): self
    {
        $this->contetful = $client;

        return $this;
    }
}
