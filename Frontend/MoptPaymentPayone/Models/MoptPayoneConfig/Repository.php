<?php

namespace Shopware\CustomModels\MoptPayoneConfig;

use Shopware\Components\Model\ModelRepository;

/**
 * Payone Config Repository
 */
class Repository extends ModelRepository
{

  public function getGlobalConfig($asArray = true)
  {
    return $this->getConfigByPaymentId(0, $asArray);
  }

  public function getConfigByPaymentId($paymentId, $asArray = true)
  {
    $builder = $this->getEntityManager()->createQueryBuilder();
    $builder->select('c')
            ->from('Shopware\CustomModels\MoptPayoneConfig\MoptPayoneConfig c')
            ->where('c.paymentId = ?1')
            ->setParameter(1, $paymentId);

    $hydrationMode = $asArray ? \Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY : \Doctrine\ORM\AbstractQuery::HYDRATE_OBJECT;
    $result        = $builder->getQuery()->getOneOrNullResult($hydrationMode);


    //if empty result, load global config
    if ($paymentId && !$result)
    {
      $result = $this->getGlobalConfig($asArray);
    }

    return $result;
  }

}