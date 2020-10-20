<?php
/**
 * Copyright (c) 2020. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace api\actions\base;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class DeleteAction extends Action
{
  /**
   * Deletes a model.
   * @param mixed $id id of the model to be deleted.
   * @return array
   * @throws ServerErrorHttpException on failure.
   * @throws NotFoundHttpException
   */
  public function run($id)
  {
    $model = $this->findModel($id);

    if ($this->checkAccess) {
      call_user_func($this->checkAccess, $this->id, $model);
    }

    if ($model->delete() === false) {
      throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
    }

    Yii::$app->getResponse()->setStatusCode(204);
    return [
      'id' => $model->id,
    ];
  }
}
