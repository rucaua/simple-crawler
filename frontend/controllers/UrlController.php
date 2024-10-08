<?php

namespace frontend\controllers;

use common\models\Attempt;
use common\models\AttemptSearch;
use common\models\Url;
use common\models\UrlSearch;
use frontend\models\UrlForm;
use yii\bootstrap5\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\filters\AjaxFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * UrlController implements the CRUD actions for Url model.
 */
class UrlController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                        'create' => ['POST'],
                    ],
                ],
                'ajax' => [
                    'class' => AjaxFilter::class,
                    'only' => ['create', 'validate','view-logs'],
                ],
            ]
        );
    }

    public function actions(): array
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ]
        ];
    }

    /**
     * Lists all Url models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UrlSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'form' => new UrlForm(),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionValidate()
    {
        $model = new UrlForm();
        $request = \Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    /**
     * Displays a single Url model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id)
    {
        $model = $this->findUrlModel($id);
        $dataProvider = new ActiveDataProvider([
            'query' => $model->getAttempts(),
        ]);
        return $this->render('view', [
            'model' => $model,
            'attemptDataProvider' => $dataProvider
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionViewLogs(int $id)
    {
        $model = $this->findAttemptModel($id);
        $dataProvider = new ActiveDataProvider([
            'query' => $model->getAttemptLogs(),
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);

        return $this->renderAjax('_logs', [
            'form' => new UrlForm(),
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return false[]
     * @throws Exception
     */
    public function actionCreate(): array
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new UrlForm();
        return ['success' => $this->request->isPost && $model->load($this->request->post()) && $model->create()];
    }

    /**
     * Deletes an existing Url model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findUrlModel($id)->delete();

        return $this->redirect(['index']);
    }


    /**
     * Finds the Url model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Url the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findUrlModel(int $id): Url
    {
        if (($model = Url::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    /**
     * Finds the Attempt model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Attempt the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findAttemptModel(int $id): Attempt
    {
        if (($model = Attempt::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
