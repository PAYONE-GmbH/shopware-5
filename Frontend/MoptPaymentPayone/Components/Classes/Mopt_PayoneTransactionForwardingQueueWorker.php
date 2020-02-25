<?php

use Doctrine\ORM\ORMException;
use Shopware\CustomModels\MoptPayoneTransactionForwardQueue\MoptPayoneTransactionForwardQueue;

class Mopt_PayoneTransactionForwardingQueueWorker
{
    /**
     * Push an element to the queue
     *
     * @param $request
     * @param $response
     * @param $tx_id
     * @param $url
     */
    public function queuePush($request, $response, $tx_id, $url) {
        $queueEntry = new MoptPayoneTransactionForwardQueue();
        $queueEntry->setRequest($request);
        $queueEntry->setResponse($response);
        $queueEntry->setTransactionId($tx_id);
        $queueEntry->setNumtries('1');
        $queueEntry->setEndpoint($url);
        try {
            Shopware()->Models()->persist($queueEntry);
            Shopware()->Models()->flush();
        } catch (ORMException $e) {
        }
    }

    public function queueProcessNext() {

    }
}
