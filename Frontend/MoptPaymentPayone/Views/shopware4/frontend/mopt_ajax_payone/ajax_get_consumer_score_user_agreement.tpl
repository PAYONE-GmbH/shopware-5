<div>
  <div class="heading">
    <h2>
      {s namespace='frontend/MoptPaymentPayone/payment' name='confirmConsumerScoreCheckTitle'}Bonitätsprüfung Bestätigen{/s}
    </h2>
  </div>
  <div style="padding: 25px;">
    <p style="padding: 0 25px 0 0;" class="none">
      {$consumerscoreNoteMessage}
    </p>
    <p style="padding: 25px 0 25px 0;" class="none">
      {$consumerscoreAgreementMessage}
    </p>

    <input style="margin-left: 5px;" class="button-middle large right" type="submit" onclick="checkConsumerScore();" 
           value="{s namespace='frontend/MoptPaymentPayone/payment' name='agreeButtonLabel'}Zustimmen{/s}"/>
    <input style="margin-bottom: 25px;" class="button-middle large right" type="submit" 
           onclick="doNotCheckConsumerScore();" 
           value="{s namespace='frontend/MoptPaymentPayone/payment' name='disagreeButtonLabel'}Nicht zustimmen{/s}"/>
  </div>
</div>

<script type="text/javascript">
  <!--
  function checkConsumerScore() {
    jQuery.post('{url controller="moptAjaxPayone" action="checkConsumerScore" forceSecure}', function(response)
    {
      if (response === 'true')
      {
        window.location = "{url controller=account action=savePayment sTarget=checkout forceSecure}";
      }
      else
      {
        window.location = "{url controller=account action=payment sTarget=checkout forceSecure}";
      }
    });
  }

  function doNotCheckConsumerScore() {
    jQuery.post('{url controller="moptAjaxPayone" action="doNotCheckConsumerScore" forceSecure}', function(response)
    {
      if (response === 'true')
      {
        window.location = "{url controller=account action=savePayment sTarget=checkout forceSecure}";
      }
      else
      {
        window.location = "{url controller=account action=payment sTarget=checkout forceSecure}";
      }
    });
  }
  // -->
</script>