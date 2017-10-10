{if (isset($consumerscoreNoteMessage) || isset($consumerscoreAgreementMessage))}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel">
        {s namespace='frontend/MoptPaymentPayone/payment' name='confirmConsumerScoreCheckTitle'}Bonitätsprüfung Bestätigen{/s}
    </h4>
</div>
<div class="modal-body">
    {if isset($consumerscoreNoteMessage)}
        <p style="padding: 0 25px 0 0;" class="none">
            {$consumerscoreNoteMessage}
        </p>
    {/if}
    {if isset($consumerscoreAgreementMessage)}
        <p style="padding: 25px 0 25px 0;" class="none">
            {$consumerscoreAgreementMessage}
        </p>
    {/if}
</div>
<div class="modal-footer">
    {if isset($consumerscoreAgreementMessage)}
        <button type="button" class="btn btn-default" onclick="doNotCheckConsumerScore();" data-dismiss="modal">
            {s namespace='frontend/MoptPaymentPayone/payment' name='disagreeButtonLabel'}Nicht zustimmen{/s}
        </button>
        <button type="button" class="btn btn-primary" onclick="checkConsumerScore();">
            {s namespace='frontend/MoptPaymentPayone/payment' name='agreeButtonLabel'}Zustimmen{/s}
        </button>
    {/if}
</div>
{/if}
<script type="text/javascript">
    <!--
  function checkConsumerScore() {
        jQuery.post('{url controller="moptAjaxPayone" action="checkConsumerScore" forceSecure}', function (response) {
            if (response == 'true')
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
        jQuery.post('{url controller="moptAjaxPayone" action="doNotCheckConsumerScore" forceSecure}', function (response) {
            if (response == 'true')
            {
                window.location = "{url controller=account action=savePayment sTarget=checkout forceSecure}";
            }
            else
            {
                window.location = "{url controller=account action=payment sTarget=checkout forceSecure}";
            }
        });
    }

    {if !isset($consumerscoreAgreementMessage) && isset($consumerscoreNoteMessage)}
    setTimeout(function () {
        checkConsumerScore();
    }, 3000);
    {/if}
    {if !isset($consumerscoreAgreementMessage) && !isset($consumerscoreNoteMessage)}
    setTimeout(function () {
        checkConsumerScore();
    }, 1);
    {/if}
    // -->
</script>