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
use common\helpers\HArray;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use yii\db\ActiveRecordInterface;
use yii\db\ExpressionInterface;
use yii\db\QueryInterface;
use yii\web\NotFoundHttpException;

class ListDataAction extends Action
{
  /**
   * @var $modelSearch string
   */
  public $modelSearch;
  public $scenario = Model::SCENARIO_DEFAULT;
  public $search_like = ['name'];

  private $name_type_result = '_type_result';
  const TYPE_ARRAY = 1;
  const TYPE_ROW = 3;

  /**
   * Displays a model.
   * @return ActiveRecordInterface the model being displayed
   * @throws InvalidConfigException
   */
  public function run()
  {
    $type_result = (int)Yii::$app->request->get($this->name_type_result, 1);
    /* @var $modelClass ActiveRecord */
    /* @var $model Model */
    $modelClass = $this->modelClass;
    $modelSearch = $this->modelSearch;
    $params = [];

    if (defined($modelSearch . "::SCENARIO_LIST")) {
      $model = Yii::createObject([
        'class' => $modelSearch
      ]);
      if (defined(get_class($model) . "::SCENARIO_LIST")) {
        $model->scenario = $modelSearch::SCENARIO_LIST;
      }

      $model->load(Yii::$app->request->get(), '');

      if ($model->validate()) {
        $params = HArray::filterEmptyValue($model->getAttributes($model->safeAttributes()));
      }
    }
    return $modelClass::listData($params, $type_result);
  }
}
