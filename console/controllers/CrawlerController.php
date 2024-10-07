<?php

namespace console\controllers;

use common\components\crawler\CrawlerComponent;
use common\models\Url;
use yii\base\ErrorException;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Exception;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 */
class CrawlerController extends Controller
{
    /**
     * @return int
     * @throws ErrorException
     * @throws Exception
     * @throws \Throwable
     */
    public function actionRun(): int
    {
        if ($model = Url::getNextUrl()) {
            /* @var $crawler CrawlerComponent */
            $crawler = \Yii::$app->crawler;
            try {
                $model->finishAttempt(
                    $crawler->run($model->startAttempt()->url)
                );
            } catch (\Exception $exception) {
                $model->currentAttempt->log($exception->getMessage());
            }
        }
        return ExitCode::OK;
    }

}
