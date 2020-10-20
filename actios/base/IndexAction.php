<?php
/**
 * Copyright (c) 2020. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace api\actions\base;

use common\helpers\HPagination;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;

class IndexAction extends \yii\rest\IndexAction
{
  /**
   * @var $modelSearch  InterfaceSearch
   */
  public $modelSearch;

  public $serializer = [
    'class' => 'yii\rest\Serializer',
    'collectionEnvelope' => 'items',
    'linksEnvelope' => '_links',
    'metaEnvelope' => 'pagination',
    'preserveKeys' => true,
  ];
//  public $serializer = 'yii\rest\Serializer';

  /**
   * @return mixed|ActiveDataProvider
   * @throws InvalidConfigException
   */
  public function run()
  {
    $result = $this->serializeData(parent::run());
    $result = $this->filterResult($result);
    return $result;
  }


  protected function afterRun()
  {
    parent::afterRun();
  }

  /**
   * @param $data
   * @return mixed
   * @throws InvalidConfigException
   */
  protected function serializeData($data)
  {
    return Yii::createObject($this->serializer)->serialize($data);
  }

  protected function filterResult($result)
  {
    if (isset($result['_links'])) {
      unset($result['_links']);
    }
    HPagination::convertFromDataProvider($result);
    return $result;

  }

  /**
   * @return ActiveDataProvider
   * @throws InvalidConfigException
   */
  protected function prepareDataProvider()
  {
    $m = Yii::createObject(
      $this->modelSearch
    );

    $requestParams = Yii::$app->getRequest()->getBodyParams();
    if (empty($requestParams)) {
      $requestParams = Yii::$app->getRequest()->getQueryParams();
    }
    if (method_exists($m, 'search')) {
      return $m->search($requestParams);
    }
    throw  new InvalidConfigException('modelSearch must have method search');
  }


}