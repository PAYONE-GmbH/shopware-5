<?php

/**
 * $Id: $
 */
class Mopt_PayoneConfig
{
  /**
   * standard valid IPs, add load balancer IP here if any problems occur
   *
   * @var array 
   */
  protected $validIPs = array(
      '213.178.72.196',
      '213.178.72.197',
      '217.70.200.*',
      '185.60.20.*',
      );


  /**
   * return array with configured valid IPs to accept transaction feedback from
   * 
   * @return array
   */
  public function getValidIPs()
  {
    return $this->validIPs;
  }
  
}
