/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function checkAuthentication() {
    setTimeout(function () {
        $.ajax({url: core.getURL('authenticationURL', {}), success: function (data) {
                if (data.isTimeout) {
                    window.location.href = data.backURL;
                } else {
                    //Setup the next poll recursively
                    checkAuthentication();
                }
            }, dataType: "json"});
    }, 10000);
})();