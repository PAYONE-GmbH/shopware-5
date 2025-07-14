 function check_script_loaded(glob_var) {
        if(typeof(glob_var) !== 'undefined') {
            if (typeof paylaDcs.init !== 'function') {
                setTimeout(function() {
                    check_script_loaded(glob_var)
                }, 100)
            } else {
                var paylaDcsT = paylaDcs.init("{$BSPayoneSecuredMode}", "{$BSPayoneSecuredToken}");
                console.log(paylaDcsT);
                tokenElem = document.getElementById('{$id}_token');
                tokenElem.setAttribute('value', paylaDcsT)
                console.log('Token:' + paylaDcsT);
            }
        } else {
            setTimeout(function() {
                check_script_loaded(glob_var)
            }, 100)
        }
    }
    check_script_loaded('paylaDcs');