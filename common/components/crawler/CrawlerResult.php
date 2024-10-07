<?php

namespace common\components\crawler;

use common\interfaces\CrawlerResultInterface;

class CrawlerResult implements CrawlerResultInterface
{
    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    public function getResponseTime(): int
    {
        return $this->responseTime;
    }

    public function getInternalLinks(): array
    {
        return $this->internalLinks;
    }

    public function getExternalLinksCount(): int
    {
        return count($this->externalLinks);
    }

    public function getImagesCount(): int
    {
        return count($this->images);
    }

    public function WordsCount(): int
    {
        return $this->words;
    }

    public function setLinks(array $links): void
    {
        foreach ($links as $link) {
            $linkHost = parse_url($link, PHP_URL_HOST);
            $baseDomain = parse_url($this->url, PHP_URL_HOST);
            if ($linkHost === null) {
                $this->internalLinks[] = parse_url($this->url, PHP_URL_SCHEME) . '://' . $baseDomain . $link;
            } else if ($linkHost === $baseDomain) {
                $this->internalLinks[] = $link;
            } else {
                $this->externalLinks[] = $link;
            }
        }
    }

    public function __construct(
        public readonly string $url,
        public readonly int $httpStatusCode,
        public readonly int $responseTime,
        private readonly array $links = [],
        public array $images = [],
        public ?int $words = null,
        private array $externalLinks = [],
        private array $internalLinks = [],
    ) {
        $this->setLinks($this->links);
    }
}