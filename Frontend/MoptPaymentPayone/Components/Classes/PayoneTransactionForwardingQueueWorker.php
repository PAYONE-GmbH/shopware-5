<?php

use Doctrine\ORM\ORMException;
use Shopware\Components\Model\ModelManager;
use Shopware\CustomModels\MoptPayoneTransactionForwardQueue\MoptPayoneTransactionForwardQueue;
use Shopware\CustomModels\MoptPayoneTransactionForwardQueue\Repository as MoptPayoneTransactionForwardQueueRepository;

class Mopt_PayoneTransactionForwardingQueueWorker
{
    /**
     * @var Mopt_PayoneHelper
     */
    private $helper;

    public function __construct()
    {
        $this->helper = new Mopt_PayoneHelper();
    }

    /**
     * Push an element to the queue
     *
     * @param string $request
     * @param Zend_Http_Response $response
     * @param $tx_id
     * @param $post
     * @param $url
     * @param $payoneConfig
     */
    public function queuePush($request, $response, $tx_id, $post, $url, $payoneConfig)
    {
        $jsonPost = json_encode($post);

        $log_msg = [
            'Add notification to queue',
            'tx_action=' . $post['txaction'],
            'tx_id=' . $post['txid'],
            'post=' . $jsonPost,
        ];
        $this->helper->forwardLog($log_msg, $payoneConfig);

        $queueEntry = new MoptPayoneTransactionForwardQueue();
        $queueEntry->setRequest((string) $request);
        $queueEntry->setResponse((string) $response);
        $queueEntry->setTransactionId($tx_id);
        $queueEntry->setJsonPost($jsonPost);
        $queueEntry->setNumtries('0');
        $queueEntry->setEndpoint($url);
        try {
            Shopware()->Models()->persist($queueEntry);
            Shopware()->Models()->flush();
        } catch (ORMException $e) {
        }
    }

    public function processQueue()
    {
        /** @var ModelManager $modelManager */
        $modelManager = Shopware()->Models();
        /** @var MoptPayoneTransactionForwardQueueRepository $queueRepository */
        $queueRepository = $modelManager->getRepository(MoptPayoneTransactionForwardQueue::class);

        $builder = $queueRepository->createQueryBuilder('MoptPayoneTransactionForwardQueue');
        $notifications = $builder
            ->select(['MoptPayoneTransactionForwardQueue'])
            ->getQuery()
            ->getResult()
        ;

        /** @var MoptPayoneTransactionForwardQueue $notification */
        foreach ($notifications as $notification) {
            $jsonPost = $notification->getJsonPost();
            $post = json_decode($jsonPost, true);

            $paymentId = $post['paymentID'];
            $payoneConfig = Shopware()
                ->Plugins()
                ->Frontend()
                ->MoptPaymentPayone()
                ->Application()
                ->MoptPayoneMain()
                ->getPayoneConfig($paymentId, true);

            $log_msg = [
                'Process notification from queue',
                'tx_action=' . $post['txaction'],
                'tx_id=' . $post['txid'],
                'post=' . $jsonPost,
            ];
            $this->helper->forwardLog($log_msg, $payoneConfig);

            $transactionResult = $this->helper->forwardTransactionStatus(
                $notification->getEndpoint(),
                $post,
                $post['txaction'],
                $post['txid'],
                $payoneConfig
            );

            if ($transactionResult['success']) {
                try {
                    $modelManager->remove($notification);
                    $modelManager->flush();
                } catch (ORMException $e) {
                    $log_msg = [
                        'Error removing successful notification from queue',
                        'tx_action=' . $post['txaction'],
                        'tx_id=' . $post['txid'],
                    ];
                    $this->helper->forwardLog($log_msg, $payoneConfig);
                }
            } else if (!array_key_exists('error_msg', $transactionResult)) {
                $notification->setNumtries($notification->getNumtries() + 1);
                $notification->setResponse($transactionResult['response']);
//                $notification->setRequest($transactionResult['request']);
                try {
                    $modelManager->persist($notification);
                    $modelManager->flush();
                } catch (ORMException $e) {
                    $log_msg = [
                        'Error increasing failed notification in queue',
                        'tx_action=' . $post['txaction'],
                        'tx_id=' . $post['txid'],
                    ];
                    $this->helper->forwardLog($log_msg, $payoneConfig);
                }
            } else {
                $log_msg = [
                    'Failed to initialize request client',
                    'error_msg=' . $transactionResult['error_msg'],
                    'url=' . $transactionResult['url'],
                    'tx_action=' . $post['txaction'],
                    'tx_id=' . $post['txid'],
                    'post=' . $post,
                ];
                $this->helper->forwardLog($log_msg, $payoneConfig);
            }
        }
    }
}
