<?php
/**
 * Copyright (c) 2020. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace api\actions\base;

use common\components\exceptions\AccountNotFoundException;
use Yii;
use yii\db\ActiveRecordInterface;
use yii\web\NotFoundHttpException;

/**
 * ViewAction implements the API endpoint for returning the detailed information about a model.
 *
 * For more details and usage information on ViewAction, see the [guide article on rest controllers](guide:rest-controllers).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ViewAction extends Action
{
  /**
   * Displays a model.
   * @param string $id the primary key of the model.
   * @return ActiveRecordInterface the model being displayed
   */
  public function run($id)
  {

    try {
      $model = $this->findModel($id);
    } catch (NotFoundHttpException $e) {
      throw new AccountNotFoundException('Not found ' . $id);
    }
    if ($this->checkAccess) {
      call_user_func($this->checkAccess, $this->id, $model);
    }
    return $model;
  }
}
