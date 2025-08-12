<?php

/**
 * $Id: $
 */

namespace Shopware\CustomModels\MoptPayoneTransactionLog;

use Shopware\Components\Model\ModelRepository;

/**
 * Transaction Log Repository
 */
class Repository extends ModelRepository
{

    public function save($request, $response)
    {
        $transactionLog = new MoptPayoneTransactionLog();

        $transactionLog->setStatus($request->get('txaction'));
        if ($request->get('mode') === 'live') {
            $transactionLog->setLiveMode(true);
        } else {
            $transactionLog->setLiveMode(false);
        }
        $transactionLog->setPortalId((int)$request->get('portalid'));
        $transactionLog->setCreationDate(date('Y-m-d\TH:i:sP'));
        $transactionLog->setUpdateDate(date('Y-m-d\TH:i:sP'));
        $transactionLog->setTransactionDate(date('Y-m-d\TH:i:sP', $request->get('txtime')));

        $transactionLog->setTransactionId((int)$request->get('txid'));
        $transactionLog->setOrderNr($request->get('reference'));
        $transactionLog->setSequenceNr($request->get('sequencenumber'));
        $transactionLog->setPaymentId(Shopware()->Config()->mopt_payone__paymentId);
    
        if (is_null($request->get('receivable'))) {
            $transactionLog->setClaim(0);
        } else {
            $transactionLog->setClaim($request->get('receivable'));
        }
    
        if (is_null($request->get('balance'))) {
            $transactionLog->setBalance(0);
        } else {
            $transactionLog->setBalance($request->get('balance'));
        }
    
        $transactionLog->setDetails($this->buildParamDetails($request, $response));

        Shopware()->Models()->persist($transactionLog);
        Shopware()->Models()->flush();
        return true;
    }

  /**
   *
   *
   * @param type $response
   * @return type
   */
    protected function buildParamDetails($request, $response)
    {
        $details = array_merge($request->toArray(), array('response_state' => $response->getStatus()));
        ksort($details);
        return $details;
    }

  /**
   * Returns an instance of the \Doctrine\ORM\Query object which selects a list of all mails.
   * @param $transactionId
   * @return \Doctrine\ORM\Query
   */
    public function getTransactionQuery($transactionId)
    {
        $builder = $this->getTransactionQueryBuilder($transactionId);
        return $builder->getQuery();
    }

  /**
   * Helper function to create the query builder for the "getTransactionQuery" function.
   * @param $transactionId
   * @return \Doctrine\ORM\QueryBuilder
   */
    public function getTransactionQueryBuilder($transactionId)
    {
        $builder = $this->getEntityManager()->createQueryBuilder();
        $builder->select(array('id', 'transactionId'))
            ->from(
                'Shopware\CustomModels\MoptPayoneTransactionLog\MoptPayoneTransactionLog',
                'MoptPayoneTransactionLog'
            )
            ->where('MoptPayoneTransactionLog.transactionId = ?1')
            ->setParameter(1, $transactionId);
        return $builder;
    }
}
