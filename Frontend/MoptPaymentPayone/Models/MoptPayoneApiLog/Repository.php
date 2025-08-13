<?php

/**
 * $Id: $
 */

namespace Shopware\CustomModels\MoptPayoneApiLog;

use Shopware\Components\Model\ModelRepository;
use Shopware\Plugins\Community\Frontend\MoptPaymentPayone\Components\Payone\PayoneRequest;
use Shopware\Plugins\Community\Frontend\MoptPaymentPayone\Components\Payone\PayoneResponse;

/**
 * Transaction Log Repository
 */
class Repository extends ModelRepository
{

    public function save(PayoneRequest $request, PayoneResponse $response)
    {
        $apiLog = new MoptPayoneApiLog();

        if ($response instanceof PayoneRequest) {
            $apiLog->setRequest($request->params['request']);
        } else {
            $apiLog->setRequest($request->params['request']);
            $apiLog->setResponse($response->getStatus());
            if ($request->params['mode'] == 'live') {
                $apiLog->setLiveMode(true);
            } else {
                $apiLog->setLiveMode(false);
            }
            $apiLog->setMerchantId($request->params['mid']);
            $apiLog->setPortalId($request->params['portalid']);
            $apiLog->setCreationDate(date('Y-m-d\TH:i:sP'));
            $apiLog->setRequestDetails($request->__toString());
            $apiLog->setResponseDetails($response->__toString());
            $apiLog->setTransactionId($response->get('txid'));
        }

        Shopware()->Models()->persist($apiLog);
        Shopware()->Models()->flush();
        return true;
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
