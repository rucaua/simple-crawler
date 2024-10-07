<?php

namespace common\interfaces;

interface CrawlerResultInterface
{
    public function getHttpStatusCode(): int;

    public function getResponseTime(): int;

    public function getExternalLinksCount(): int;

    public function getInternalLinks(): array;

    public function getImagesCount(): int;

    public function WordsCount(): int;
}