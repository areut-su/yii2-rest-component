<?php
/**
 * Copyright (c) 2020. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace api\actions\base;

use common\components\exceptions\ExceptionValidate;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\ActiveRecordInterface;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

/**
 * CreateAction implements the API endpoint for creating a new model from the given data.
 *
 * For more details and usage information on CreateAction, see the [guide article on rest controllers](guide:rest-controllers).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CreateAction extends Action
{
  /**
   * @var string the scenario to be assigned to the new model before it is validated and saved.
   */
  public $scenario = Model::SCENARIO_DEFAULT;
  /**
   * @var string the name of the view action. This property is needed to create the URL when the model is successfully created.
   */
  public $viewAction = 'view';


  /**
   * Creates a new model.
   * @return ActiveRecordInterface the model newly created
   * @throws ServerErrorHttpException if there is any error when creating the model
   * @throws InvalidConfigException
   */
  public function run()
  {
    if ($this->checkAccess) {
      call_user_func($this->checkAccess, $this->id);
    }

    /* @var $model ActiveRecord */
    $model = new $this->modelClass([
      'scenario' => $this->scenario,
    ]);

    $model->load(Yii::$app->getRequest()->getBodyParams(), '');
    if ($model->save()) {
      $response = Yii::$app->getResponse();
      $response->setStatusCode(201);
      $id = implode(',', array_values($model->getPrimaryKey(true)));
      $response->getHeaders()->set('Location', Url::toRoute([$this->viewAction, 'id' => $id], true));

    } elseif ($model->hasErrors()) {
      throw new ExceptionValidate($model->errors);
    }
    return $model;
  }
}
