<?php

use Doctrine\ORM\ORMException;
use Shopware\CustomModels\MoptPayoneTransactionForwardQueue\MoptPayoneTransactionForwardQueue;
use Shopware\CustomModels\MoptPayoneTransactionForwardQueue\Repository as MoptPayoneTransactionForwardQueueRepository;

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
    public function queuePush($request, $response, $tx_id, $rawPost, $url)
    {
        $queueEntry = new MoptPayoneTransactionForwardQueue();
        $queueEntry->setRequest($request);
        $queueEntry->setResponse($response);
        $queueEntry->setTransactionId($tx_id);
        $queueEntry->setJsonPost($rawPost);
        $queueEntry->setNumtries('1');
        $queueEntry->setEndpoint($url);
        try {
            Shopware()->Models()->persist($queueEntry);
            Shopware()->Models()->flush();
        } catch (ORMException $e) {
        }
    }

    public function processQueue()
    {
        $helper = new Mopt_PayoneHelper();
        $zendHttpClient = $helper->initRequestClient();

        /** @var MoptPayoneTransactionForwardQueueRepository $queueRepository */
        $queueRepository = Shopware()->Models()->getRepository(MoptPayoneTransactionForwardQueue::class);

        $builder = $queueRepository->createQueryBuilder('MoptPayoneTransactionForwardQueue');
        $resultSet = $builder
            ->select(['MoptPayoneTransactionForwardQueue'])
            ->getQuery()
            ->getArrayResult()
        ;
        foreach ($resultSet as $entry) {

        }

        return $resultSet;
    }
}
