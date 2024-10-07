<?php

namespace common\models;

enum UrlStatus: int
{
    case NEW = 10;
    case IN_PROGRESS = 20;
    case CRAWLED = 30;
    case FAILED = 40;

    const labels = [
        10 => 'New',
        20 => 'in progress',
        30 => 'Crawled',
        40 => 'Failed',
    ];


    public function getLabel(): string
    {
        return self::labels[$this->value];
    }
}
