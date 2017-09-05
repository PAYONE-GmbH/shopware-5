{extends file='parent:frontend/checkout/payment.tpl'}

{block name="frontend_index_content"}

    <div id="payment" class="grid_20" style="margin:10px 0 10px 20px;width:959px;height:900px;">
        <h2 class="headingbox_dark largesize">{se name="PaymentHeader" namespace="frontend/checkout/payment"}Bitte führen Sie nun die Zahlung durch:{/se}</h2>
        <iframe src="{$gatewayUrl}"
                scrolling="yes"
                style="height:900px;"
                width="100%" height="100%" frameborder="0" border="0" id="payment_frame">
        </iframe>
        <div id="payment_loader" class="ajaxSlider" style="height:100px;border:0 none;display:none">
            <div class="loader" style="width:80px;margin-left:-50px;">
                {s name="PaymentInfoWait" namespace="frontend/checkout/payment"}Bitte warten...{/s}
            </div>
        </div>
    </div>

    <div class="doublespace">&nbsp;</div>

    <div class="actions">
        <a class="btn" href="{url controller=checkout action=confirm}">
            {s name='backToConfirmPage' namespace='frontend/MoptPaymentPayone/payment'}Zurück zu Prüfen und Bestellen{/s}
        </a>
    </div>

{/block}