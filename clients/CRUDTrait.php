<?php
/**
 * Copyright (c) 2020. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace common\components\clients;


use common\errormanager\exception\ExceptionRemoteAPI;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Yii;
use yii\base\Model;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;

/**
 * @property  BaseClient $this
 */
trait CRUDTrait
{
  protected $last_response;
  protected $name_type_result = '_type_result';
  protected $clientController;

  /**
   * @param string $controller
   * @param array $params
   * @return mixed
   * @throws Exception
   */
  public function create(string $controller, array $params)
  {
    /**
     * @var  $response ResponseInterface
     */
    $response = $this->sendRawPostRequest($controller
      . 'create', $params);
    $code = $response->getStatusCode();
    $reason = $response->getReasonPhrase();
    if ($code == 200) {
      return $this->last_response = Json::decode($response->getBody()->getContents());
    }

    throw new Exception($reason, $code);
  }

  /**
   * @param string $controller
   * @param array $params
   * @return mixed
   * @throws Exception
   */
  public function view(string $controller, array $params)
  {
    /**
     * @var  $response ResponseInterface
     */

    $response = $this->sendGetRequest($controller . 'view', $params);
    $code = $response->getStatusCode();
    $reason = $response->getReasonPhrase();
    if ($code == 200) {
      return $this->last_response = Json::decode($response->getBody()->getContents());
    }

    throw new Exception($reason, $code);
  }

  /**
   * @param $controller
   * @param array $params
   * @return mixed
   * @throws Exception
   */
  public function delete(string $controller, array $params)
  {
    /**
     * @var  $response ResponseInterface
     */
    $response = $this->sendGetRequest($controller . 'delete', $params);
    $code = $response->getStatusCode();
    $reason = $response->getReasonPhrase();
    if ($code == 200) {
      $this->last_response = Json::decode($response->getBody()->getContents());
      if ($this->last_response['status_code'] == 204) {
        return true;
      }
      return false;
    }

    throw new Exception($reason, $code);
  }

  /**
   * @param string $controller
   * @param array $params_GET
   * @param array $params_POST
   * @return mixed
   * @throws Exception
   */
  public function update(string $controller, array $params_GET, array $params_POST)
  {
    /**
     * @var  $response ResponseInterface
     */

    $uri = '';
    if (!empty($params_GET)) {
      $uri .= '?' . http_build_query($params_GET);
    }

    $response = $this->sendRawPostRequest($controller . 'update' . $uri, $params_POST);
    $code = $response->getStatusCode();
    $reason = $response->getReasonPhrase();
    if ($code == 200) {
      return $this->last_response = Json::decode($response->getBody()->getContents());
    }
    throw new Exception($reason, $code);
  }

  /**
   * обработчик ошибок
   * @param $controller
   * @param array $params
   * @return mixed|null
   * @throws Exception
   */
  public function index(string $controller, array $params)
  {
    /**
     * @var  $response ResponseInterface
     */

    $response = $this->sendGetRequest($controller . 'index', $params);
    $code = $response->getStatusCode();
    $reason = $response->getReasonPhrase();
    $contents = $response->getBody()->getContents();
    if ($code == 200) {
      $data = Json::decode($contents);
      $this->checkResultLog($data);
      return $data ?? null;
    }
    throw new Exception($reason . '-' . $contents, $code);
  }

  /**
   * @param Model $model
   * @return bool
   * @throws ExceptionRemoteAPI
   * @throws NotFoundHttpException
   */
  public function loadResult(Model $model)
  {
    $response = $this->last_response;
    if ($response['status'] === 'error') {
      if ($response['status_code'] === 422 && is_array($response['data'])) {
        $model->addErrors($response['data']);
        $model->addError('service_err', 'Ошибка сервиса:' . $response['service']);
      } else if ($response['status_code'] == 404) {
        throw  new  NotFoundHttpException();
      } else {
        throw  new  ExceptionRemoteAPI($response, $response['message'] ?? '');
      }
      return false;
    } else {
      if ($model->scenario === null && defined(get_class($model) . "::SCENARIO_LOAD")) {
        $model->scenario = $model::SCENARIO_LOAD;
      }
      if ($model->load($response['data'], '')) {
        return true;
      }
      $model->addError('service_err', 'Ошибка загрузки данных');
      return false;
    }

  }

  /**
   * @param string $controller
   * @param array $params
   * @param int $type
   * @return array
   * @throws Exception
   */
  public function listData(string $controller, array $params, int $type = 1)
  {
    /**
     * @var  $response ResponseInterface
     */
    $response = $this->sendGetRequest($controller
      . 'list-data', $params + [$this->name_type_result => $type]);

    $code = $response->getStatusCode();
    $reason = $response->getReasonPhrase();
    if ($code == 200) {
      $this->last_response = Json::decode($response->getBody()->getContents());
      if (isset($this->last_response['data']) && is_array($this->last_response['data'])) {
        return $this->last_response['data'];
      } else {
        Yii::error('Не верный формат ответа:' . print_r($this->last_response, true));
      }
    }
    throw new Exception($reason, $code);

  }

}