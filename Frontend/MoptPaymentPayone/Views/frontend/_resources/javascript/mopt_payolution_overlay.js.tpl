function {$id}DisplayOverlay()
{
    document.getElementById('{$id}_overlay').style.display = "block";
    document.getElementById('{$id}_overlay_bg').style.display = "block";
}

function {$id}RemoveOverlay()
{
    document.getElementById('{$id}_overlay').style.display = "none";
    document.getElementById('{$id}_overlay_bg').style.display = "none";
}