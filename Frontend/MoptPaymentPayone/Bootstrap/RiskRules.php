<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This class handles:
 * installment, uninstallment
 *
 *
 * PHP version 5
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU General Public License (GPL 3)
 * that is bundled with this package in the file LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Payone to newer
 * versions in the future. If you wish to customize Payone for your
 * needs please refer to http://www.payone.de for more information.
 *
 * @category        Payone
 * @package         Payone Payment Plugin for Shopware 5
 * @subpackage      Installer
 * @copyright       Copyright (c) 2016 <kontakt@fatchip.de> - www.fatchip.com
 * @author          Stefan Müller <stefan.mueller@fatchip.de>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.fatchip.com
 */

namespace Shopware\Plugins\MoptPaymentPayone\Bootstrap;

use Shopware\Models\Payment\RuleSet;
use Shopware\Models\Payment\Payment;

/**
 * Class RiskRules
 *
 * creates risk rules for payment methods.
 */
class RiskRules
{
    /**
     * MoptPaymentPayone Plugin Bootstrap Class
     * @var \Shopware_Plugins_Frontend_MoptPaymentPayone_Bootstrap
     */
    private $plugin;

    /**
     * RiskRules constructor.
     */
    public function __construct()
    {
        $this->plugin = Shopware()->Plugins()->Frontend()->MoptPaymentPayone();
    }

    /**
     * Create risk rules.
     *
     * @see createComputopRiskRule
     *
     * @throws \Exception
     * @return void
     */
    public function createRiskRules()
    {
        $this->createPayoneRiskRule('mopt_payone__fin_payone_secured_invoice',
            'ORDERVALUELESS', '9.99', '', '', 4);
        $this->createPayoneRiskRule('mopt_payone__fin_payone_secured_invoice',
            'ORDERVALUEMORE', '1500.01', '', '', 4);
        $this->createPayoneRiskRule('mopt_payone__fin_payone_secured_invoice',
            'BILLINGLANDISNOT', 'AT', 'BILLINGLANDISNOT', 'DE', 4);
        $this->createPayoneRiskRule('mopt_payone__fin_payone_secured_invoice',
            'CURRENCIESISOISNOT', 'EUR', '', '', 4);

        $this->createPayoneRiskRule('mopt_payone__fin_payone_secured_installment',
            'ORDERVALUELESS', '199.99', '', '', 4);
        $this->createPayoneRiskRule('mopt_payone__fin_payone_secured_installment',
            'ORDERVALUEMORE', '3500.01', '', '', 4);
        $this->createPayoneRiskRule('mopt_payone__fin_payone_secured_installment',
            'BILLINGLANDISNOT', 'AT', 'BILLINGLANDISNOT', 'DE', 4);
        $this->createPayoneRiskRule('mopt_payone__fin_payone_secured_installment',
            'CURRENCIESISOISNOT', 'EUR', '', '', 4);

        $this->createPayoneRiskRule('mopt_payone__fin_payone_secured_directdebit',
            'ORDERVALUELESS', '9.99', '', '', 4);
        $this->createPayoneRiskRule('mopt_payone__fin_payone_secured_directdebit',
            'ORDERVALUEMORE', '1500.01', '', '', 4);
        $this->createPayoneRiskRule('mopt_payone__fin_payone_secured_directdebit',
            'BILLINGLANDISNOT', 'AT', 'BILLINGLANDISNOT', 'DE', 4);
        $this->createPayoneRiskRule('mopt_payone__fin_payone_secured_directdebit',
            'CURRENCIESISOISNOT', 'EUR', '', '', 4);
    }

    /**
     * remove risk rules belonging to $paymentname
     *
     * @param $paymentName
     * @throws \Exception
     * @return void
     */
    public function removeRiskRules($paymentName)
    {
        /** @var \Shopware\Components\Model\ModelManager $manager */
        $manager = $this->plugin->get('models');
        /** @var $payment Payment */
        $payment = Shopware()->Models()->getRepository(Payment::class)->findOneBy(['name' => $paymentName]);
        if ($payment) {
            $rules = $payment->getRuleSets();
            foreach ($rules as $rule) {
                $manager->remove($rule);
                $manager->flush($rule);
            }
            $payment->setRuleSets(null);
            $manager->persist($payment);
            $manager->flush($payment);
        }
    }

    /**
     * Create risk rules.
     *
     * @see RuleSet
     *
     * @param string $paymentName payment method to restrict
     * @param string $rule1
     * @param string $value1
     * @param string $rule2
     * @param string $value2
     * @param int $numRules
     *
     * @throws \Exception
     * @return void
     */
    private function createPayoneRiskRule($paymentName, $rule1, $value1, $rule2 = '', $value2 = '', $numRules = 1)
    {
        /** @var \Shopware\Components\Model\ModelManager $manager */
        $manager = $this->plugin->get('models');
        $payment = $this->getPaymentObjByName($paymentName);

        $rules = [];
        $valueRule = new RuleSet();
        $valueRule->setRule1($rule1);
        $valueRule->setValue1($value1);
        $valueRule->setRule2($rule2);
        $valueRule->setValue2($value2);
        $valueRule->setPayment($payment);
        $rules[] = $valueRule;

        if ($payment->getRuleSets() === null ||
            $payment->getRuleSets()->count() < $numRules) {
            $payment->setRuleSets($rules);
            foreach ($rules as $rule) {
                $manager->persist($rule);
            }
            $manager->flush($payment);
        }
    }

    /**
     * return payment object by payment name.
     *
     * @param string $paymentName payment method name
     *
     * @return Payment|null
     */
    private function getPaymentObjByName($paymentName)
    {
        /** @var Payment $result */
        $result = $this->plugin->Payments()->findOneBy(
            [
                'name' => [
                    $paymentName,
                ]
            ]
        );
        return $result;
    }
}
