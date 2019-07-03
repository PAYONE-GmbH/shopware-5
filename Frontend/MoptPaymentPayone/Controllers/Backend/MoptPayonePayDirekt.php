<?php
/**
 * backend controller for payone PayDirekt  editing
 *
 * $Id: $
 */

class MoptPayonePayDirekt extends Shopware_Controllers_Backend_Application
{
    protected $model = 'Shopware\CustomModels\MoptPayonePayDirekt\MoptPayonePayDirekt';
    protected $alias = 'moptPayonePayDirekt';

    protected function getListQuery()
    {
        $builder = parent::getListQuery();

        $builder->leftJoin('moptPayonePayDirekt.localeId', 'localeId');
        $builder->addSelect(array('locale'));

        return $builder;
    }

    protected function getDetailQuery($id)
    {
        $builder = parent::getDetailQuery($id);

        $builder->leftJoin('moptPayonePayDirekt.locale', 'locale');

        $builder->addSelect(array('locale'));

        return $builder;
    }
}