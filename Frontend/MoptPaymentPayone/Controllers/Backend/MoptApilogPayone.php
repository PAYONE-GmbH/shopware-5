<?php

/**
 * $Id: $
 */
class Shopware_Controllers_Backend_MoptApilogPayone extends Shopware_Controllers_Backend_ExtJs
{

  /**
   *  get logs action, loads log entries with paging
   */
  public function getApilogsAction()
  {
    $start = $this->Request()->get('start');
    $limit = $this->Request()->get('limit');

    //Get the value itself
    if ($this->Request()->get('filter'))
    {
      $filter      = $this->Request()->get('filter');
      $filter      = $filter[count($filter) - 1];
      $filterValue = $filter['value'];
    }

    $builder = Shopware()->Models()->createQueryBuilder();
    $builder->select(
            'log.id as id', 'log.request as request', 'log.response as response', 
            'log.liveMode as liveMode', 'log.merchantId as merchantId', 'log.portalId as portalId', 
            'log.creationDate as creationDate', 'log.requestDetails as requestDetails', 
            'log.responseDetails as responseDetails'
    )->from('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog', 'log');


    //order data
    $order = (array) $this->Request()->getParam('sort', array());
    if ($order)
    {
      foreach ($order as $ord)
      {
        $builder->addOrderBy('log.' . $ord['property'], $ord['direction']);
      }
    }
    else
    {
      $builder->addOrderBy('log.creationDate', 'DESC');
    }

    if ($filterValue)
    {
      $builder->where('log.merchantId = ?1')->setParameter(1, $filterValue);
    }

    $builder->setFirstResult($start)->setMaxResults($limit);

    $result = $builder->getQuery()->getArrayResult();

    $result = $this->addArrayRequestResponse($result);

    $total = Shopware()->Models()->getQueryCount($builder->getQuery());

    $this->View()->assign(array('success' => true, 'data'    => $result, 'total'   => $total));
  }

  /**
   * grid data action, returns api call details
   */
  public function getGridDataAction()
  {
    $type = $this->Request()->get('type');

    $builder = Shopware()->Models()->createQueryBuilder();
    $builder->select('log.id as id', 'log.requestDetails as requestDetails', 'log.responseDetails as responseDetails')
            ->from('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog', 'log')
            ->where('log.id = ?1')
            ->setParameter(1, $this->Request()->get('id'));

    $result = $builder->getQuery()->getArrayResult();

    $result = $this->addArrayRequestResponse($result);

    $total = Shopware()->Models()->getQueryCount($builder->getQuery());
    $this->View()->assign(array('success' => true, 'data'    => $result[0][$type . 'Array'], 'total'   => $total));
  }

  /**
   * helper method, extracts response/request data
   *
   * @param string $result
   * @return array 
   */
  protected function addArrayRequestResponse($result)
  {
    if (!empty($result))
    {

      foreach ($result as $key => $entry)
      {
        $request = array();
        $response = array();

        $dataRequest = explode('|', $entry['requestDetails']);

        foreach ($dataRequest as $value)
        {
          $tmp       = explode('=', $value);
          $request[] = array('name'  => $tmp[0], 'value' => $tmp[1]);
        }

        $dataResponse = explode('|', $entry['responseDetails']);
        foreach ($dataResponse as $value)
        {
            $tmp        = explode('=', $value);
            $response[] = array('name'  => $tmp[0], 'value' => $tmp[1]);
          }

        $result[$key]['requestArray']  = $request;
        $result[$key]['responseArray'] = $response;
      }
    }
    return $result;
  }

  /**
   * controller action, returns log data 
   */
  public function controllerAction()
  {
    $start = $this->Request()->get('start');
    $limit = $this->Request()->get('limit');

    //order data
    $order = (array) $this->Request()->getParam('sort', array());
    //Get the value itself
    if ($this->Request()->get('filter'))
    {
      $filter      = $this->Request()->get('filter');
      $filter      = $filter[count($filter) - 1];
      $filterValue = $filter['value'];
    }

    $builder = Shopware()->Models()->createQueryBuilder();
    $builder->select(
            'log.id as id', 'log.request as request', 'log.response as response', 
            'log.liveMode as liveMode', 'log.merchantId as merchantId', 'log.portalId as portalId', 
            'log.creationDate as creationDate', 'log.requestDetails as requestDetails', 
            'log.responseDetails as responseDetails'
    )->from('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog', 'log');

    if ($filterValue)
    {
      $builder->where('log.merchant_id = ?1')->setParameter(1, $filterValue);
    }
    $builder->addOrderBy($order);

    $builder->setFirstResult($start)->setMaxResults($limit);

    $result = $builder->getQuery()->getArrayResult();
    $total  = Shopware()->Models()->getQueryCount($builder->getQuery());


    $this->View()->assign(array('success' => true, 'data'    => $result, 'total'   => $total));
  }

  /**
   * assigns search result data to view object 
   */
  public function getSearchResultAction()
  {
    $filters = $this->Request()->get('filter');

    $builder = Shopware()->Models()->createQueryBuilder();
    $builder->select(
            'log.id as id', 'log.request as request', 'log.response as response', 
            'log.liveMode as liveMode', 'log.merchantId as merchantId', 'log.portalId as portalId', 
            'log.creationDate as creationDate', 'log.requestDetails as requestDetails', 
            'log.responseDetails as responseDetails'
    )->from('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog', 'log');

    foreach ($filters as $filter)
    {
      if ($filter['property'] == 'search' && !empty($filter['value']))
      {
        $builder->where($builder->expr()->orx($builder->expr()->like('log.requestDetails', $builder->expr()->literal(
                                        '%' . $filter['value'] . '%')), 
                $builder->expr()->like('log.responseDetails', $builder->expr()->literal(
                                        '%' . $filter['value'] . '%'))
                ));
      }
      elseif ($filter['property'] == 'searchtrans' && !empty($filter['value']))
      {
        $builder->where($builder->expr()->orx($builder->expr()->like('log.responseDetails', $builder->expr()->literal(
                                        '%txid=' . $filter['value'] . '%'))));
      }
    }

    $builder->setMaxResults(20);
    $result = $builder->getQuery()->getArrayResult();
    $total  = Shopware()->Models()->getQueryCount($builder->getQuery());

    $this->View()->assign(array('success' => true, 'data'    => $result, 'total'   => $total));
  }

}