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
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\ActiveRecordInterface;
use yii\web\ServerErrorHttpException;

/**
 * UpdateAction implements the API endpoint for updating a model.
 *
 * For more details and usage information on UpdateAction, see the [guide article on rest controllers](guide:rest-controllers).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class UpdateAction extends Action
{
  /**
   * @var string the scenario to be assigned to the model before it is validated and updated.
   */
  public $scenario = Model::SCENARIO_DEFAULT;


  /**
   * Updates an existing model.
   * @param string $id the primary key of the model.
   * @return ActiveRecordInterface the model being updated
   * @throws ServerErrorHttpException if there is any error when updating the model
   */
  public function run($id)
  {
    /* @var $model ActiveRecord */
    $model = $this->findModel($id);

    if ($this->checkAccess) {
      call_user_func($this->checkAccess, $this->id, $model);
    }

    $model->scenario = $this->scenario;
    $model->load(Yii::$app->getRequest()->getBodyParams(), '');
    if ($model->save() === false && !$model->hasErrors()) {
      throw new ExceptionValidate($model->errors);
//      throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
    }

    return $model;
  }
}
