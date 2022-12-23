<?php

namespace Shopware\CustomModels\MoptPayoneAmazonPay;

use Shopware\Components\Model\ModelRepository;

/**
 * Payone Config Repository
 */
class Repository extends ModelRepository
{

    public function getConfig($asArray = true)
    {
        return $this->getConfigById(0, $asArray);
    }

    public function getConfigById($configId, $asArray = true)
    {
        $builder = $this->getEntityManager()->createQueryBuilder();
        $builder->select('c')
            ->from('Shopware\CustomModels\MoptPayoneAmazonPay\MoptPayoneAmazonPay', 'c')
            ->where('c.paymentId = ?1')
            ->setParameter(1, $configId);

        $hydrationMode = $asArray ? \Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY : \Doctrine\ORM\AbstractQuery::HYDRATE_OBJECT;
        $result        = $builder->getQuery()->getSingleResult($hydrationMode);

        return $result;
    }
}
