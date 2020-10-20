<?php
/**
 * Copyright (c) 2020. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace common\services;


use common\traits\PopulatedTrait;
use yii\helpers\ArrayHelper;

class ServiceRelationV2
{
  use PopulatedTrait;

  private $dataArray = [];
  /**
   * @var array
   *
   * $this->options = [
   * Company::class => [
   * 'classRelation' => Company::class,
   * 'class_key' => 'id',
   * 'class_model' => [],
   * 'data_key' => 'payment_id',
   * 'data_alias' => 'company',
   *
   * //   не обзятельно    отключили, тк не исользутеся  'data_filter' => ['payment_type' => 'company'],
   * * 'class_ids' => [],
   * ]
   * ];
   */
  private $options = [];


  public static function create(array $data)
  {
    $m = new self();
    $m->dataArray = $data;
    return $m;
  }

  /**
   *
   * $arrayOptions = [
   * Company::class => [
   * 'classRelation' => Company::class,
   * 'class_key' => 'id',
   * 'class_ids' => [],
   * 'class_model' => [],
   * 'data_key' => 'payment_id',
   * 'data_alias' => 'company',
   * ]
   * ];
   * добавляет к масиву данные  из текущей БД
   * @param array $arrayOptions
   * @return array
   */
  public function setExtraModels(array $arrayOptions)
  {
    $this->options = $arrayOptions;
    $this->setClassIds();
    foreach ($this->options as &$itemOption) {
      $itemOption['class_model'] = $this->findObject($itemOption['classRelation'], $itemOption['class_key'], $itemOption['class_ids']);
    }
    $this->mixData();

    return $this->getDataArray();
  }


  /**
   * @param string $name_param
   * @return mixed|null
   */
  protected function findObject($classRelation, $class_key, $ids)
  {
    return $records = ArrayHelper::index(call_user_func([$classRelation, 'findAll'], [$class_key => array_unique($ids)]), $class_key);
  }

  /**
   * заполняем массив Для выборки свзяей
   */
  private function setClassIds()
  {
    $data_key = 'data_key';
    foreach ($this->dataArray as $itemData) {
      foreach ($this->options as $key => &$itemO) {
        if (isset($itemO[$data_key])) {
          $data_key_val = ArrayHelper::getValue($itemData, $itemO[$data_key]);
          if ($data_key_val !== null) {
            $itemO['class_ids'][] = $data_key_val;
          }
        }
      }
    }

  }

  /**
   *  добавлеяем данные(найденыне даныне ) в массив data (выхо)
   *
   */
  public function mixData()
  {
    $data_key = 'data_key';

    foreach ($this->dataArray as $key => &$itemData) {
      foreach ($this->options as &$itemO) {
        if (isset($itemO['data_alias'])) {
          if (isset($itemO['class_model'])) {
            $data_key_val = ArrayHelper::getValue($itemData, $itemO[$data_key]);
            if ($data_key_val === null) {
              $itemData[$itemO['data_alias']] = [];
            } else {
              $itemData[$itemO['data_alias']] = $itemO['class_model'][$data_key_val] ?? [];
            }

          }
        }
      }

    }
  }

  /**
   * @return array
   */
  public function getDataArray(): array
  {
    return $this->dataArray;
  }

  /**
   * проверят что в наличчи есть второе значение
   * @param $itemData
   * @param $itemO
   * @return bool
   */
  private function isFilterData($itemData, $itemO)
  {
    $flag = true;
    if (isset($itemO['data_filter'])) {
      foreach ($itemO['data_filter'] as $keyFilter => $itemFilter) {
        $flag = $flag && isset($itemData['keyFilter']) ? ($itemData['keyFilter'] === $itemFilter) : true;

      }
    }
    return true;

  }


}