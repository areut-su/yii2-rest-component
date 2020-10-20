<?php
/**
 * Copyright (c) 2020. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace common\components\clients;


use Exception;
use yii\base\Model;

interface CRUDInterface
{

  const TYPE_ARRAY = 1;
  const TYPE_ROW = 3;

  public function create(string $controller, array $params);

  public function view(string $controller, array $params);

  /**
   * @param $controller
   * @param array $params
   * @return mixed
   * @throws Exception
   */
  public function delete(string $controller, array $params);

  public function update(string $controller, array $params_GET, array $params_POST);

  /**
   * @param $controller
   * @param array $params
   * @return mixed|null
   * @throws Exception
   */
  public function index(string $controller, array $params);

  public function loadResult(Model $model);

  public function listData(string $controller, array $params, int $type = 1);


}