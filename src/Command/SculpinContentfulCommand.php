<?php

declare(strict_types=1);

namespace AirtonZanon\SculpinContentfulBundle\Command;

use Contentful\Delivery\Client;
use Sculpin\Core\Console\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;


final class SculpinContentfulCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('contentful:fetch')
            ->setDescription('Fetch Contentful data.')
            ->setHelp("The <info>contentful:fetch</info> command fetches contentful data and create files locally.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $contentfulSpaceId = getenv('contentful_space_id');
        $contentfulToken = getenv('contentful_token');

        $contetful = new Client($contentfulToken, $contentfulSpaceId);

        $entries = $contetful->getEntries()->getItems();

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
    }

    private function createPath(string $type, \DateTimeImmutable $date, string $title): string
    {
        return "source/_" . $type . "/" . $date->format('Y-m-d') . "-" . $this->normalizeTitle($title) . '.md';
    }

    private function createContent(string $language, \DateTimeImmutable $date, string $title, string $body): string
    {
        $content = <<<EOL
---
createdAt: {$date->format('Y-m-d')}
title: {$title}
language: {$language}
---

{$body}
EOL;
        return $content;
    }

    private function normalizeTitle($title): string
    {
        // Get Current Locale
        $currentLocale = setlocale(LC_ALL, 0);
        // Change to UTF-8 Locale
        setlocale(LC_ALL, 'en_US.utf8');
        // Lowercase
        $title_clean = strtolower($title);
        // Remove Accents by converting UTF-8 to ASCII
        $title_clean = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $title_clean);
        // Replace space and non alphanumeric with dash
        $title_clean = preg_replace("/[^a-z0-9]+/", "-", $title_clean);
        // Restore locale to default value
        setlocale(LC_ALL, $currentLocale);
        return $title_clean;
    }
}
