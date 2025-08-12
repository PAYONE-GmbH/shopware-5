<?php

use Shopware\Components\CSRFWhitelistAware;
use Shopware\Plugins\Community\Frontend\MoptPaymentPayone\Components\Payone\PayoneEnums;
use Shopware\Plugins\Community\Frontend\MoptPaymentPayone\Components\Payone\PayoneRequest;

class Shopware_Controllers_Backend_MoptPayoneAmazonPay extends Shopware_Controllers_Backend_Application implements CSRFWhitelistAware
{

    protected $model = 'Shopware\CustomModels\MoptPayoneAmazonPay\MoptPayoneAmazonPay';
    protected $alias = 'MoptPayoneAmazonPay';

    /**
     * PayoneMain
     * @var Mopt_PayoneMain
     */
    protected $moptPayoneMain = null;

    public function getWhitelistedCSRFActions()
    {
        $returnArray = array(
            'downloadConfigs',
            'saveAmazonPayConfigs',
        );
        return $returnArray;
    }

    public function downloadConfigAction()
    {
        $configId = $this->Request()->getParam('configId');

        /**
         * @var $config \Shopware\CustomModels\MoptPayoneAmazonPay\MoptPayoneAmazonPay
         */
        $config = $this->getManager()->find(
            $this->model,
            $configId
        );

        /**
         * @var $paymentAmazonPayId \Shopware\Models\Payment\Payment
         */
        $paymentAmazonPayId = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment')->findOneBy(
            array('name' => 'mopt_payone__ewallet_amazon_pay')
        )->getId();

        try {
            $amazonPayProfile = $this->requestAmazonPayConfigFromApi($paymentAmazonPayId);
        } catch (Exception $e) {
            $this->View()->assign(array(
                'success' => false,
                'error' => $e->getMessage()
            ));
        }

        if (isset($amazonPayProfile)) {
            $config->fromArray($amazonPayProfile);
            $this->getManager()->persist($config);
            $this->getManager()->flush($config);
            $this->View()->assign(array('success' => true, 'error' => ''));
        }
    }

    public function downloadConfigsAction()
    {
        $this->Front()->Plugins()->Json()->setRenderer(true);
        $builder = Shopware()->Models()->createQueryBuilder();
        $data = $builder->select('a')
            ->from('\Shopware\CustomModels\MoptPayoneAmazonPay\MoptPayoneAmazonPay', 'a')
            ->getQuery()->getArrayResult();

        foreach ($data as $dataItem) {
            try {

                /**
                 * @var $config \Shopware\Models\Payment\Payment
                 */
                $paymentamazonpayId = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment')->findOneBy(
                    array('name' => 'mopt_payone__ewallet_amazon_pay')
                )->getId();

                $amazonPayProfile = $this->requestAmazonPayConfigFromApi($paymentamazonpayId);
                if (!$amazonPayProfile) {
                    $errorElem[] = $dataItem['id'];
                } else {
                    /**
                     * @var $config \Shopware\CustomModels\MoptPayoneAmazonPay\MoptPayoneAmazonPay
                     */
                    $config = $this->getManager()->find(
                        $this->model,
                        $dataItem['id']
                    );

                    $config->fromArray($amazonPayProfile);
                    $this->getManager()->persist($config);
                    $this->getManager()->flush($config);
                }
            } catch (Exception $e) {
                $errorElem[] = $dataItem['id'];
            }
        }

        $data['errorElem'] = $errorElem;
        $data['status'] = 'success';
        $encoded = json_encode($data);
        echo $encoded;
        exit();
    }

    public function saveAmazonPayConfigsAction()
    {
        $this->Front()->Plugins()->Json()->setRenderer(true);
        $params = $this->Request()->getParams();
        unset($params['module']);
        unset($params['controller']);
        unset($params['action']);

        $amazonPayRepo = Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneAmazonPay\MoptPayoneAmazonPay'
        );
        $amazonPayConfigs = $amazonPayRepo->findAll();
        $shopRepo = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop');

        // Remove all configs that don't exist in the form data
        foreach ($amazonPayConfigs as $amazonPayConfig) {
            $configStillExists = false;
            foreach ($params['row'] as $amazonPayConfigFromJSON) {
                if ($amazonPayConfig->getId() == $amazonPayConfigFromJSON['id']) {
                    $configStillExists = true;
                }
            }
            if ($configStillExists === false) {
                $this->getManager()->remove($amazonPayConfig);
            }
        }

        // Update or create all configs that exist in the form data
        foreach ($params['row'] as $dataset) {
            $config = $this->getManager()->find(
                $this->model,
                $dataset['id']
            );

            if (!$config) {
                $config = new \Shopware\CustomModels\MoptPayoneAmazonPay\MoptPayoneAmazonPay;
            }
            if (!$dataset['id']) {
                $dataset['id'] = 0;
            }
            $dataset['buttonLanguage'] = 'none';
            $dataset['shop'] = $shopRepo->findOneBy(array('id' => $dataset['shopId']));;

            $config->fromArray($dataset);
            $this->getManager()->persist($config);
        }
        $this->getManager()->flush($config);
        $data['errorElem'] = '';
        $data['status'] = 'success';
        $encoded = json_encode($data);
        echo $encoded;
        exit();
    }

    /**
     * Returns the payment plugin config data.
     *
     * @return Shopware_Plugins_Frontend_MoptPaymentPayone_Bootstrap
     */
    public function Plugin()
    {
        return Shopware()->Plugins()->Frontend()->MoptPaymentPayone();
    }

    protected function requestAmazonPayConfigFromApi($paymentAmazonPayId)
    {
        $this->moptPayoneMain = $this->Plugin()->Application()->MoptPayoneMain();
        $config = $this->moptPayoneMain->getPayoneConfig($paymentAmazonPayId);
        $clearingType = PayoneEnums::WALLET;
        $walletType = PayoneEnums::AMAZONPAY;
        try {
            $profileResponse = $this->buildAndCallAmazonPayProfile(
                $config,
                $clearingType,
                $walletType
            );
        } catch (Exception $e) {
        }

        if (isset($profileResponse) && $profileResponse->getStatus() === PayoneEnums::OK ) {
            $payData = $profileResponse->getRatepayPayDataArray();
            return $payData;
        }  else {
            throw new Exception($profileResponse->getErrorMessage());
        }
    }

    /**
     * @param $config
     * @param $clearingType
     * @param $walletType
     * @return mixed
     */
    protected function buildAndCallAmazonPayProfile(
        $config,
        $clearingType,
        $walletType
    ) {
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize($config['paymentId']);
        $params['api_version'] = '3.10';

        $request = new PayoneRequest(PayoneEnums::GenericpaymentAction_genericpayment, $params);
        $params['add_paydata[action]'] = PayoneEnums::AMAZON_GETCONFIGURATION;
        $params['clearingtype'] = $clearingType;
        $params['wallettype'] = $walletType;
        // set currency here to prevent a mapping exception
        $params['currency'] = 'EUR';
        $response = $request->request(PayoneEnums::GenericpaymentAction_genericpayment, $params);
        return $response;
    }

    protected function getListQuery()
    {
        $builder = parent::getListQuery();

        $builder->leftJoin('MoptPayoneAmazonPay.shop', 'shop');
        $builder->addSelect(array('shop'));

        return $builder;
    }

    protected function getDetailQuery($id)
    {
        $builder = parent::getDetailQuery($id);

        $builder->leftJoin('MoptPayoneAmazonPay.shop', 'shop');

        $builder->addSelect(array('shop'));

        return $builder;
    }
}
