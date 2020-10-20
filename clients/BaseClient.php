<?php
/**
 * Copyright (c) 2020. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace common\components\clients;

use backend\Helpers\PaginationResponseReader;
use common\components\ResponseDataProvider;
use common\helpers\HPagination;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

abstract class BaseClient implements CRUDInterface
{
  use  CRUDTrait;

  protected $baseUri;

  protected $client;

  /**
   * @param string $uri
   * @param array $params
   * @return ResponseInterface
   */
  protected function sendPostRequest($uri, $params = [])
  {
    $client = $this->getClient();
    $options = [
      'form_params' => $params
    ];

    return $client->post($uri, $options);
  }

  /**
   * @param string $uri
   * @param array $params
   * @return ResponseInterface
   */
  protected function sendRawPostRequest($uri, $params, $type = 'post')
  {
    $client = $this->getClient();
    $options = [
      'body' => Json::encode($params),
      'headers' => ['Content-Type' => 'application/json']
    ];
    Yii::info(['Send RAW:' . $this->baseUri . $uri, print_r($params, true)]);
    if ($type === 'post') {
      return $client->post($uri, $options);
    } else {
      return $client->get($uri, $options);
    }
  }

  /**
   * @param string $uri
   * @param array $params
   * @return PromiseInterface
   */
  protected function sendPostAsyncRequest($uri, $params = [])
  {
    $client = $this->getClient();
    Yii::info(['Send POST Async:' . $this->baseUri . '- ' . $uri, $params]);
    return $client->postAsync($uri, $params);
  }

  /**
   * @param string $uri
   * @param array $params
   * @return PromiseInterface
   */
  protected function sendGetAsyncRequest($uri, $params = [])
  {
    $client = $this->getClient();
    Yii::info(['Send GET Async:' . $this->baseUri . '- ' . $uri, $params]);
    return $client->getAsync($uri, $params);
  }

  /**
   * @param string $uri
   * @param array $params
   * @return ResponseInterface
   */
  protected function sendGetRequest($uri, $params = [])
  {
    if (!empty($params)) {
      $uri .= '?' . http_build_query($params);
    }
    $client = $this->getClient();
    Yii::info(['Send GET:' . $this->baseUri . '- ' . $uri, $params]);
    return $client->get($uri);
  }

  /**
   *
   * @param string $uri
   * @param array $params
   * @return ResponseInterface
   */
  public function sendPutRequest($uri, $params = [])
  {
    $client = $this->getClient();
    return $client->put($uri, ['json' => $params]);
  }

  /**
   * @param $result
   * @return array|mixed
   */
  public static function checkResultLog($result)
  {
    if (isset($result['status']) && $result['status'] === 'ok' && isset($result['data'])) {
      return $result['data'];
    } else {
      Yii::warning(['Response  http client:' . self::class, print_r($result, true)]);
    }
    return [];
  }

  public static function createArrayDataProvider($data, $forName = 'data', $forNameItems = 'items')
  {

//    HPagination::convertToDataProvider($data);

    if (empty($data)) {
      return $provider = new ResponseDataProvider([
        'allModels' => [],
      ]);
    }
    if ($forName) {
      $dataResult = $data[$forName];
    } else {
      $dataResult = $data;
    }
    $paginationModel = HPagination::getPaginationModel($data, HPagination::$pagination_name);
    $items = ArrayHelper::getValue($dataResult, $forNameItems, []);
    if (!empty($pk)) {
      $items = ArrayHelper::index($items, $pk);
    }
    return $provider = new ResponseDataProvider([
      'allModels' => $items,
      'pagination' => $paginationModel,
      //@todo сортировку првоерить и добавить обработку
      /* 'sort' => [
        'attributes' => ['id', 'name'],
        ], */
    ]);
  }

  /**
   * @param array $params
   * @param string $url
   * @return mixed|null
   * @throws Exception
   */
  protected function baseIndex(array $params, string $url)
  {
    $response = $this->sendGetRequest($url, $params);
    $code = $response->getStatusCode();
    $reason = $response->getReasonPhrase();

    if ($code == 200) {
      $data = Json::decode($response->getBody()->getContents());
      $this->checkResultLog($data);
      return $data ?? null;
    }

    throw new Exception($reason, $code);
  }

  /**
   * @return Client
   */
  protected function getClient(): Client
  {
    if ($this->client) {
      return $this->client;
    } else {
      return new Client(["base_uri" => $this->baseUri, 'verify' => true]);
    }

  }

}
