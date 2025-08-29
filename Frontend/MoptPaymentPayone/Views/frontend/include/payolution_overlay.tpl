{namespace name='frontend/MoptPaymentPayone/payment'}
<div id="{$id}_overlay" class="js--modal content" style="width:78%; height:auto; display: none; opacity: 0.9; margin: 0 auto;">
    <a href="#" onclick="{$id}RemoveOverlay();return false;" style="float:right;font-weight:bold;">{s name="closeWindow"}Fenster schliessen{/s}</a><br><br>
    {$moptCreditCardCheckEnvironment.moptPayolutionInformation.overlaycontent}
</div>
<div id="{$id}_overlay_bg" class="js--overlay is--open" style="opacity: 0.8; display: none"></div>