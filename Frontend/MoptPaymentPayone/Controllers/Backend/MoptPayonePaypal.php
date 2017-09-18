<?php

/**
 * backend controller for payone paypal button editing
 *
 * $Id: $
 */
class Shopware_Controllers_Backend_MoptPayonePaypal extends Shopware_Controllers_Backend_Application
{

    protected $model = 'Shopware\CustomModels\MoptPayonePaypal\MoptPayonePaypal';
    protected $alias = 'moptPayonePaypal';

    protected function getListQuery()
    {
        $builder = parent::getListQuery();
 
        $builder->leftJoin('moptPayonePaypal.locale', 'locale');
        $builder->addSelect(array('locale'));
 
        return $builder;
    }
 
    protected function getDetailQuery($id)
    {
        $builder = parent::getDetailQuery($id);
 
        $builder->leftJoin('moptPayonePaypal.locale', 'locale');
 
        $builder->addSelect(array('locale'));
 
        return $builder;
    }
}
