<?php

namespace common\components\crawler;

use common\interfaces\CrawlerResultInterface;
use CurlHandle;
use yii\base\Component;
use yii\base\Exception;

class CrawlerComponent extends Component
{

    /**
     * @var int limit redirects to prevent infinite redirects. Set to 0 to disable redirects at all
     */
    public int $maxRedirects = 10;

    /**
     * @var int the maximum number of seconds to allow cURL functions to execute.
     */
    public int $timeout = 10;

    private CurlHandle|null $curl = null;
    private string $body = '';

    /**
     * @param string $url
     * @return self
     * @throws Exception
     */
    private function initCurl(string $url): self
    {
        if ($this->curl = curl_init($url)) {
            return $this;
        }
        throw new Exception('cUrl initialization error');
    }


    private function getCurl(): CurlHandle
    {
        if ($this->curl === null) {
            throw new Exception('cUrl initialization error');
        }
        return $this->curl;
    }


    private function curlClose(): void
    {
        if ($this->curl !== null) {
            curl_close($this->curl);
            $this->curl = null;
        }
    }

    // Close cURL session

    /**
     * @return self
     */
    private function setCurlOptions(): self
    {
        $config = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_HEADER, true);
        curl_setopt($this->curl, CURLOPT_USERAGENT, $config);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, $this->maxRedirects !== 0);
        curl_setopt($this->curl, CURLOPT_MAXREDIRS, $this->maxRedirects);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->timeout);
        return $this;
    }

    /**
     * @throws Exception
     */
    private function getResponseTime(): int
    {
        return curl_getinfo($this->getCurl(), CURLINFO_TOTAL_TIME_T);
    }

    /**
     * @throws Exception
     */
    private function getHttpCode(): int
    {
        return curl_getinfo($this->getCurl(), CURLINFO_HTTP_CODE);
    }

    /**
     * @return self
     * @throws Exception
     */
    private function exec(): self
    {
        if ($response = curl_exec($this->getCurl())) {
            $headerSize = curl_getinfo($this->getCurl(), CURLINFO_HEADER_SIZE);
            $this->body = substr($response, $headerSize);
        }

        return $this;
    }


    private function getLinks(): array
    {
        preg_match_all('/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>/i', $this->body, $matches);
        return $matches[1];
    }


    private function getImages(): array
    {
        preg_match_all('/<img\s+[^>]*src=["\']?([^"\'>]+)["\']?/i', $this->body, $images);
        return $images[0];
    }


    private function getWordCount(): int
    {
        return str_word_count(strip_tags($this->body));
    }


    /**
     * @throws Exception
     */
    public function run(string $url): CrawlerResultInterface
    {
        $this->initCurl($url)->setCurlOptions()->exec();

        $result = new CrawlerResult(
            $url,
            $this->getHttpCode(),
            $this->getResponseTime(),
            $this->getLinks(),
            $this->getImages(),
            $this->getWordCount(),
        );

        $this->curlClose();

        return $result;
    }
}
