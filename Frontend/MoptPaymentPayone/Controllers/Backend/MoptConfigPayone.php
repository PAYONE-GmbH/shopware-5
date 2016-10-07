<?php

/**
 * backend controller for payone configuration
 *
 * $Id: $
 */
class Shopware_Controllers_Backend_MoptConfigPayone extends Shopware_Controllers_Backend_ExtJs
{

    // payone config repository
    protected $repository = null;

    /**
     * Disable template engine for all actions
     */
    public function preDispatch()
    {
        if (!in_array($this->Request()->getActionName(), array('index', 'load'))) {
            $this->Front()->Plugins()->Json()->setRenderer(true);
        }
    }

    /**
     * get payone config repository
     *
     * @return \Shopware\CustomModels\MoptPayoneConfig\Repository
     */
    public function getRepository()
    {
        if ($this->repository === null) {
            $this->repository = Shopware()->Models()->getRepository(
                'Shopware\CustomModels\MoptPayoneConfig\MoptPayoneConfig'
            );
        }
        return $this->repository;
    }

    /**
     * get global payone config
     */
    public function getConfigAction()
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(0, true);
        $data = $this->getIsoCodesForCountries($data);

        $this->View()->assign(array(
            "success" => true,
            "data" => $data,
            "totalCount" => count($data)));
    }

    /**
     * Controller action which can be called over an ajax request.
     * This function can be used to update an existing payment configuration
     */
    public function updateConfigAction()
    {
        $this->View()->assign(
            $this->saveConfig()
        );
    }

    /**
     * saves submitted payment config or calls deleteConfigAction if reset is requested
     *
     * @return mixed
     */
    public function saveConfigAction()
    {
        $data = $this->Request()->getParams();
        if (isset($data['type']) && $data['type'] == 'reset' && $data['paymentId'] != 0) {
            return $this->deleteConfigAction($data);
        }
        $data = $this->validateFormData($data);
        $data = $this->validateCountries($data);

        if (!$data) {
            $errorMessage = Shopware()->Snippets()->getNamespace('backend/MoptPaymentPayone/errorMessages')
                    ->get('wrongCountryIsoCodes', 'Die konfigurierten Ländercodes sind fehlerhaft.', true);
            $this->View()->assign(array(
                'success' => false,
                'data' => $errorMessage
            ));
            return;
        }

        $config = $this->get('MoptPayoneMain')->getPayoneConfig($data['paymentId'], true, false);

        if ($data['paymentId'] == $config->getPaymentId()) {
            $config->fromArray($data);
        } else {
            $config = new Shopware\CustomModels\MoptPayoneConfig\MoptPayoneConfig();
            $config->setData($data);
        }

        Shopware()->Models()->persist($config);
        Shopware()->Models()->flush();
        $message = Shopware()->Snippets()->getNamespace('backend/MoptPaymentPayone/messages')
                ->get('successfulSaved', 'Erfolgreich gespeichert', true);

        $this->View()->assign(array(
            'success' => true,
            'data' => $message
        ));
    }

    /**
     * delete separate config
     * does not delete global config
     *
     * @param array $data
     */
    protected function deleteConfigAction($data)
    {
        $data = $this->get('MoptPayoneMain')->getPayoneConfig(
            $data['paymentId'],
            true,
            false
        );

        if ($data->getPaymentId() != 0) {
            Shopware()->Models()->remove($data);
            Shopware()->Models()->flush();
            $message = Shopware()->Snippets()->getNamespace('backend/MoptPaymentPayone/messages')
                    ->get('successfulReset', 'Erfolgreich zurückgesetzt', true);
        } else {
            $message = Shopware()->Snippets()->getNamespace('backend/MoptPaymentPayone/errorMessages')
                    ->get('noConfigForThisPaymentMethod', 'Diese Zahlart besitzt keine spezielle Konfiguration', true);
        }

        $this->View()->assign(array(
            "success" => true,
            "data" => $message
        ));
    }

    /**
     * validate checkbox data send via payone configuration form
     *
     * @param array $data
     * @return array $data
     */
    protected function validateFormData($data = array())
    {

        $fields = array(
            'consumerscoreAgreementActive',
            'consumerscoreNoteActive',
            'consumerscoreAbtestActive',
            'consumerscoreAgreementActive',
            'liveMode',
            'submitBasket',
            'adresscheckActive',
            'adresscheckLiveMode',
            'consumerscoreLiveMode',
            'consumerscoreActive',
            'checkCc',
            'showAccountnumber',
            'showBic',
            'mandateActive',
            'mandateDownloadEnabled',
            'paypalEcsActive',
            'payolutionB2bmode',
            'showSofortIbanBic',
        );

        foreach ($fields as $field) {
            $data[$field] = ($data[$field] == 'off' || $data[$field] == false || $data[$field] == 'false') ? 0 : 1;
        }

        if (!$data['adresscheckFailureMessage']) {
            $data['adresscheckFailureMessage'] = Shopware()->Snippets()
                    ->getNamespace('frontend/MoptPaymentPayone/errorMessages')
                    ->get('addresscheckErrorMessage', 'Bitte überprüfen Sie die Adresse', true);
        }

        if (!$data['consumerscoreNoteMessage']) {
            $data['consumerscoreNoteMessage'] = Shopware()->Snippets()
                    ->getNamespace('frontend/MoptPaymentPayone/messages')
                    ->get('consumerscoreNoteMessage', 'Es wird eine Bonitätsprüfung durchgeführt.', true);
        }

        if (!$data['consumerscoreAgreementMessage']) {
            $data['consumerscoreAgreementMessage'] = Shopware()->Snippets()
                    ->getNamespace('frontend/MoptPaymentPayone/messages')
                    ->get('consumerscoreAgreementMessage', 'Stimmen Sie der Bonitätsprüfung zu?', true);
        }

        return $data;
    }

    /**
     * get all payments and some additional information
     * used to fill payment store
     */
    public function getPaymentsAction()
    {
        $builder = Shopware()->Models()->createQueryBuilder();
        $data = $builder->select('a.id, a.description, a.name')
                        ->from('Shopware\Models\Payment\Payment a')
                        ->where('a.name LIKE \'mopt_payone__%\'')
                        ->getQuery()->getArrayResult();

        foreach ($data as $dataKey => $dataValue) {
            $data[$dataKey]['description'] = $dataValue['description'] . ' - ' . $dataValue['name'];
            if ($this->moptDoesSeparateConfigExists($dataValue['id'])) {
                $data[$dataKey]['configSet'] = 1;
            } else {
                $data[$dataKey]['configSet'] = 0;
            }
        }

        array_unshift($data, array('id' => null, 'description' => 'Alle (global)', 'name' => '', 'configSet' => 1));
        $this->View()->assign(array(
            "success" => true,
            "data" => $data,
            "totalCount" => count($data)));
    }

    /**
     * get pament config action, returns config according to submitted payment id
     */
    public function getPaymentConfigAction()
    {
        $data = $this->Request()->getParams();

        foreach ($data['filter'] as $filter) {
            if ($filter['property'] === 'payment_id') {
                $data = $this->moptGetPaymentConfig($filter['value']);
                $this->View()->assign(array('success' => true, 'data' => $data));
            }
        }
    }

    /**
     * retrieve all possible payment states a order may have for transaction status mapping
     */
    public function readPaymentStateAction()
    {
        $builder = Shopware()->Models()->createQueryBuilder();
        $data = $builder->select('a.id, a.description')
                        ->from('Shopware\Models\Order\Status a')
                        ->where('a.group = \'payment\'')
                        ->getQuery()->getArrayResult();

        $this->View()->assign(array('data' => $data, 'success' => true));
    }

    /**
     * get config for given payment id
     * and add extra information for some payment methods for special form treatment
     *
     * @param string $paymentId
     */
    protected function moptGetPaymentConfig($paymentId)
    {
        $data = Shopware()->Plugins()->Frontend()->MoptPaymentPayone()
                        ->Application()->MoptPayoneMain()->getPayoneConfig($paymentId, true, true);
        $paymentHelper = Shopware()->Plugins()->Frontend()->MoptPaymentPayone()
                        ->Application()->MoptPayoneMain()->getPaymentHelper();

        //getPaymentName
        $builder = Shopware()->Models()->createQueryBuilder();
        $paymentData = $builder->select('a.name')
                        ->from('Shopware\Models\Payment\Payment a')
                        ->where('a.id = ?1')
                        ->setParameter(1, $paymentId)
                        ->getQuery()->getOneOrNullResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        if ($paymentData && $paymentData['name']) {
            if ($paymentHelper->isPayonePaymentMethod($paymentData['name'])) {
                $data['extra'] = 'p1';
            }
            if ($paymentHelper->isPayoneDebitnote($paymentData['name'])) {
                $data['extra'] = 'debit';
            }
            if ($paymentHelper->isPayoneKlarna($paymentData['name'])) {
                $data['extra'] = 'klarna';
            }
            if ($paymentHelper->isPayoneCreditcardNotGrouped($paymentData['name'])) {
                $data['extra'] = 'cc';
            }
            if ($paymentHelper->isPayonePaypal($paymentData['name'])) {
                $data['extra'] = 'paypal';
            }
            if ($paymentHelper->isPayonePaydirekt($paymentData['name'])) {
                $data['extra'] = 'paydirekt';
            }
            if ($paymentHelper->isPayonePayolutionDebitNote($paymentData['name'])) {
                $data['extra'] = 'payolution_debitnote';
            }
            if ($paymentHelper->isPayonePayolutionInvoice($paymentData['name'])) {
                $data['extra'] = 'payolution_invoice';
            }
            if ($paymentHelper->isPayonePayolutionInstallment($paymentData['name'])) {
                $data['extra'] = 'payolution_installment';
            }            
        }

        $data = $this->getIsoCodesForCountries($data);

        return $data;
    }

    /**
     * checks if a separate config exists for given payment id
     *
     * @param string $paymentId
     * @return boolean
     */
    protected function moptDoesSeparateConfigExists($paymentId)
    {
        $sql = 'SELECT `id` FROM `s_plugin_mopt_payone_config` WHERE payment_id = ?';
        $result = Shopware()->Db()->fetchOne($sql, $paymentId);

        if ($result === false) {
            return false;
        } else {
            return true;
        }
    }

    protected function validateCountries($configData)
    {
        if (!empty($configData['adresscheckBillingCountries'])) {
            $countries = $this->getIdsFromIsoCodes($configData['adresscheckBillingCountries']);

            if (in_array(false, $countries)) {
                $configData['adresscheckBillingCountries'] = false;
            } else {
                $configData['adresscheckBillingCountries'] = implode(',', $countries);
            }
        }

        if (!empty($configData['adresscheckShippingCountries'])) {
            $countries = $this->getIdsFromIsoCodes($configData['adresscheckShippingCountries']);

            if (in_array(false, $countries)) {
                $configData['adresscheckShippingCountries'] = false;
            } else {
                $configData['adresscheckShippingCountries'] = implode(',', $countries);
            }
        }

        if ($configData['adresscheckBillingCountries'] === false || $configData['adresscheckShippingCountries'] === false) {
            return false;
        } else {
            return $configData;
        }
    }

    protected function getIdsFromIsoCodes($countries)
    {
        $helper = $this->get('MoptPayoneMain')->getHelper();

        $countries = explode(',', rtrim($countries, ','));

        foreach ($countries as $key => $country) {
            $countries[$key] = $helper->getCountryIdFromIso(trim($country));
        }

        return $countries;
    }

    protected function getIsoCodesForCountries($configData)
    {
        $helper = $this->get('MoptPayoneMain')->getHelper();

        if (!empty($configData['adresscheckBillingCountries'])) {
            $countries = explode(',', $configData['adresscheckBillingCountries']);
            foreach ($countries as $key => $country) {
                $countries[$key] = $helper->getCountryIsoFromId($country);
            }

            $configData['adresscheckBillingCountries'] = implode(',', $countries);
        }

        if (!empty($configData['adresscheckShippingCountries'])) {
            $countries = explode(',', $configData['adresscheckShippingCountries']);
            foreach ($countries as $key => $country) {
                $countries[$key] = $helper->getCountryIsoFromId($country);
            }
            $configData['adresscheckShippingCountries'] = implode(',', $countries);
        }

        return $configData;
    }
}
