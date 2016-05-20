<?php

class Shopware_Controllers_Backend_MoptPayonePayment extends Shopware_Controllers_Backend_ExtJs
{

  /**
   * create a copy of the given payment
   * 
   * @throws Exception
   */
  public function moptPayoneDuplicatePaymentAction()
  {
    $request = $this->Request();

    try
    {
      //get id
      $paymentId = $request->getParam('id');

      if (!$payment = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment')->find($paymentId))
      {
        $exceptionMessage = Shopware()->Snippets()->getNamespace('backend/MoptPaymentPayone/errorMessages')
                ->get('paymentNotFound', 'Zahlart nicht gefunden', true);
        throw new Exception($exceptionMessage);
      }

      if (!$this->moptDuplicatePayment($payment))
      {
        $exceptionMessage = Shopware()->Snippets()->getNamespace('backend/MoptPaymentPayone/errorMessages')
                ->get('paymentDuplicationFailed', 'Duplizieren der Zahlart fehlgeschlagen', true);
        throw new Exception($exceptionMessage);
      }

      $response = array('success' => true);
    }
    catch (Exception $e)
    {
      $response = array('success' => false, 'error_message' => $e->getMessage());
    }

    $this->View()->assign($response);
  }

  /**
   * copy all properties and create duplicate payment
   * 
   * @param object $payment
   * @return boolean
   */
  protected function moptDuplicatePayment($payment)
  {
    $duplicatedPayment = new \Shopware\Models\Payment\Payment();

    $duplicatedPayment->setName($this->moptCreateUniquePaymentName($payment->getName()));

    $duplicatedPayment->setDescription($payment->getDescription());
    $duplicatedPayment->setTemplate($payment->getTemplate());
    $duplicatedPayment->setAdditionalDescription($payment->getAdditionalDescription());
    $duplicatedPayment->setPosition(200);
    $duplicatedPayment->setActive(false);
    $duplicatedPayment->setAction($payment->getAction());
    $duplicatedPayment->setPluginId($payment->getPluginId());
    $duplicatedPayment->setSource(1);

    try
    {
      Shopware()->Models()->persist($duplicatedPayment);
      Shopware()->Models()->flush();
    }
    catch (Exception $e)
    {
      return false;
    }
    
    return true;
  }

  /**
   * create a name for the duplicated payment method which does not exist
   * 
   * @param string $paymentName
   * @return string|boolean
   */
  protected function moptCreateUniquePaymentName($paymentName)
  {
    $newName = $paymentName . '_';

    for ($i = 1; $i < 100; $i++)
    {
      $newName = $newName . $i;

      if (!$payment = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment')
              ->findOneBy(array('name' => $newName)))
      {
        return $newName;
      }
    }

    return false;
  }

}