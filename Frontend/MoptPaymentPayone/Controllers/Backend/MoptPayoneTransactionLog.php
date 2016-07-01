<?php

/**
 * $Id: $
 */
class Shopware_Controllers_Backend_MoptPayoneTransactionLog extends Shopware_Controllers_Backend_ExtJs
{

  /**
   * assigns transaction log entries to view
   */
    public function getTransactionLogsAction()
    {
        $start = $this->Request()->get('start');
        $limit = $this->Request()->get('limit');

        $builder = Shopware()->Models()->createQueryBuilder();
        $builder->select('log')
            ->from('Shopware\CustomModels\MoptPayoneTransactionLog\MoptPayoneTransactionLog log');

        $order = (array) $this->Request()->getParam('sort', array());

        if ($order) {
            foreach ($order as $ord) {
                $builder->addOrderBy('log.' . $ord['property'], $ord['direction']);
            }
        } else {
            $builder->addOrderBy('log.creationDate', 'DESC');
        }

        $builder->addOrderBy('log.creationDate', 'DESC');

        $builder->setFirstResult($start)->setMaxResults($limit);

        $result = $builder->getQuery()->getArrayResult();
        $total  = Shopware()->Models()->getQueryCount($builder->getQuery());

        $this->View()->assign(array('success' => true, 'data'    => $result, 'total'   => $total));
    }

  /**
   * set detail data to specified log entry to view
   *
   * @return mixed
   */
    public function getDetailDataAction()
    {
        $request = $this->Request();
        $result  = array();

        if (!$id = $request->get('id')) {
            return;
        }

        $log = Shopware()->Models()
            ->getRepository('Shopware\CustomModels\MoptPayoneTransactionLog\MoptPayoneTransactionLog')->find($id);

        foreach ($log->getDetails() as $key => $value) {
            $result[] = array('name'  => $key, 'value' => $value);
        }

        $this->View()->assign(array('success' => true, 'data'    => $result));
    }

  /**
   * search transaction logs for search term and assign result to view
   */
    public function getSearchResultAction()
    {
        $filters = $this->Request()->get('filter');

        $builder = Shopware()->Models()->createQueryBuilder();
        $builder->select('log')
            ->from('Shopware\CustomModels\MoptPayoneTransactionLog\MoptPayoneTransactionLog log');

        foreach ($filters as $filter) {
            if ($filter['property'] == 'search' && !empty($filter['value'])) {
                $builder->where($builder->expr()->orx($builder->expr()->like('log.details', $builder->expr()->literal(
                    '%' . $filter['value'] . '%'
                ))));
            }
        }

        $builder->setMaxResults(20);
        $result = $builder->getQuery()->getArrayResult();
        $total  = Shopware()->Models()->getQueryCount($builder->getQuery());

        $this->View()->assign(array('success' => true, 'data'    => $result, 'total'   => $total));
    }
}
