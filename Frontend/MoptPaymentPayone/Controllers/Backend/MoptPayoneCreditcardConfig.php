<?php

/**
 * backend controller for payone creditcard config editing
 *
 * $Id: $
 */
class Shopware_Controllers_Backend_MoptPayoneCreditcardConfig extends Shopware_Controllers_Backend_Application
{

    protected $model = 'Shopware\CustomModels\MoptPayoneCreditcardConfig\MoptPayoneCreditcardConfig';
    protected $alias = 'moptPayoneCreditcardConfig';

    protected function getListQuery()
    {
        $builder = parent::getListQuery();

        $builder->leftJoin('moptPayoneCreditcardConfig.locale', 'locale');
        $builder->addSelect(array('locale'));
        $builder->leftJoin('moptPayoneCreditcardConfig.shop', 'shop');
        $builder->addSelect(array('shop'));

        return $builder;
    }

    protected function getDetailQuery($id)
    {
        $builder = parent::getDetailQuery($id);

        $builder->leftJoin('moptPayoneCreditcardConfig.locale', 'locale');
        $builder->addSelect(array('locale'));
        $builder->leftJoin('moptPayoneCreditcardConfig.locale', 'shop');
        $builder->addSelect(array('shop'));

        return $builder;
    }

    /**
     * extend save method to catch validation errors
     *
     * @param array $data
     * @return array
     */
    public function save($data)
    {
        $actionName = $this->Request()->getActionName();
        $errors = array();

        if ($data['shopId'] === 0) {
            $errors[] = array(
                'property' => Shopware()->Snippets()->getNamespace('backend/mopt_payone_creditcard_config/main')
                    ->get('shop'),
                'message' => Shopware()->Snippets()->getNamespace('backend/mopt_payone_creditcard_config/main')
                    ->get('shopValidationError', 'Bitte einen Shop auswählen', true)
            );
        }

        if ($data['errorLocaleId'] === 0) {
            $data['errorLocaleId'] = 74;
        }

        $sql = 'SELECT id FROM s_plugin_mopt_payone_creditcard_config WHERE shop_id = ?';
        $datasetId = Shopware()->Db()->fetchOne($sql, $data['shopId']);
        
        if ($datasetId && $actionName === 'create') {
            $errors[] = array(
                'property' => Shopware()->Snippets()->getNamespace('backend/mopt_payone_creditcard_config/main')
                    ->get('shop'),
                'message' => Shopware()->Snippets()->getNamespace('backend/mopt_payone_creditcard_config/main')
                    ->get('shopExistsError', 'Für diesen Shop existiert schon eine Konfiguration', true)
            );
        }


        if (!empty($errors)) {
            return array('success' => false, 'violations' => $errors);
        }
        
        return parent::save($data);
    }
}
