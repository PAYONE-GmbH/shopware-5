<?php

/**
 * $Id: $
 */

namespace Shopware\CustomModels\MoptPayoneApiLog;

use Shopware\Components\Model\ModelRepository;

use \Payone_Api_Persistence_Interface;

/**
 * Transaction Log Repository
 */
class Repository extends ModelRepository implements \Payone_Api_Persistence_Interface
{

  const KEY = 'p1_shopware_api';

  /**
   * @return string
   */
  public function getKey()
  {
    return self::KEY;
  }

  public function save(\Payone_Api_Request_Interface $request, \Payone_Api_Response_Interface $response)
  {
    $apiLog = new \Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog();

    //special transaction status handling
    if ($response instanceof \Payone_TransactionStatus_Request_Interface)
    {
      $apiLog->setRequest(get_class($response));
    }
    else
    {
      $apiLog->setRequest($request->getRequest());
      $apiLog->setResponse($response->getStatus());
      if ($request->getMode() == 'live')
      {
        $apiLog->setLiveMode(true);
      }
      else
      {
        $apiLog->setLiveMode(false);
      }
      $apiLog->setMerchantId($request->getMid());
      $apiLog->setPortalId($request->getPortalid());
      $apiLog->setCreationDate(date('Y-m-d\TH:i:sP'));
      $apiLog->setRequestDetails($request->__toString());
      $apiLog->setResponseDetails($response->__toString());
    }

    Shopware()->Models()->persist($apiLog);
    Shopware()->Models()->flush();
  }

  /**
   * @param Payone_Api_Request_Interface $request
   * @param Exception
   * @return boolean
   */
  public function saveException(\Payone_Api_Request_Interface $request, \Exception $ex)
  {
    
  }

  /**
   * Helper function to create the query builder
   * @return \Doctrine\ORM\QueryBuilder
   */
  public function getApiLogQueryBuilder()
  {
    $builder = $this->getEntityManager()->createQueryBuilder();
    $builder->select(array('m.id', 'm.request', 'm.response', 'm.liveMode', 'm.merchantId', 
        'm.portalId', 'm.creationDate', 'm.requestDetails', 'm.responseDetails'))
            ->from('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog', 'm');
    return $builder;
  }

}