jQuery(function ($) {
    abfCodeLoader = $('#abf_code_loader')[0]
    btnSubmitCode = $('#abf_code_submit')[0]
    statusValue = $('.abf-status span')[0];
    validValue = $('.valid-date')[0];

    function submitCode() {
        abf_license = $('#abf_code').val()
        if (abf_license == "") {
            $('#site_notice')[0].innerHTML = '<div class="notice notice-error is-dismissible"> <p> License code is empty. </p> </div>'
            return
        }

        abfCodeLoader.style.display = 'inline'
        btnSubmitCode.disabled = true
        $.ajax({
            url: window.location.origin + "/wp-admin/admin-ajax.php",
            type: 'POST',
            data: {
                'action': 'auto_bulb_finder',
                'fn': 'get_token',
                'code': abf_license
            },
            success: function (response) {
                var r = JSON.parse(response)
                console.log(r)
                if (r) {
                    try {
                        if (r.status == 1) {
                            $('.abf-status').addClass("valid")
                            statusValue.innerHTML = "Available"
                            validValue.innerHTML = r.expired
                            $('#site_notice')[0].innerHTML = '<div class="notice notice-success is-dismissible"> <p> ' + r.msg + '</p> </div>'
                            location.reload()
                        } else {
                            $('.abf-status').addClass("invalid")
                            statusValue.innerHTML = "Unavailable"
                            $('#site_notice')[0].innerHTML = '<div class="notice notice-error is-dismissible"> <p> ' + r.msg + '</p> </div>'
                        }
                    } catch (error) {
                        console.log('error: ' + error)
                        $('#site_notice')[0].innerHTML = '<div class="notice notice-error is-dismissible"> <p> ' + error + '</p> </div>'
                    }
                }
            },
            complete: function () {
                abfCodeLoader.style.display = 'none'
                btnSubmitCode.disabled = false
            },
            error: function (r) {
                console.log('error: ' + r)
            }
        })
    }

    function revokeCode() {
        statusValue.disabled = true
        $.ajax({
            url: window.location.origin + "/wp-admin/admin-ajax.php",
            type: 'POST',
            data: {
                'action': 'auto_bulb_finder',
                'fn': 'revoke_token'
            },
            success: function (response) {
                var r = JSON.parse(response)
                console.log(r)
                alert(r.msg)
                location.reload()
            },
            complete: function () {
                statusValue.disabled = false
            },
            error: function (r) {
                console.log('error: ' + r)
            }
        })
    }

    // statusValue.addEventListener("click", revokeCode, false)

    if (btnSubmitCode) {
        btnSubmitCode.addEventListener("click", submitCode, false)
    }

    loader = $('#abf_loader')[0]
    buttonSaveSetting = $('#save_abf_settings')[0]
    settingResult = $('#save_abf_settings_result')[0]

    function save_settings() {
        appPromotionHtml = $('#app-promotion')[0].value
        enableVehiclePost = $('#enable_vehicle_post')[0].checked
        searchResultPriority = $('#search-result-priority')[0].value

        loader.style.display = 'inline'
        buttonSaveSetting.disabled = true
        $.ajax({
            url: window.location.origin + "/wp-admin/admin-ajax.php",
            type: 'POST',
            data: {
                'action': 'auto_bulb_finder',
                'fn': 'save_settings',
                'names': ["app_promotion_html", "enable_vehicle_post", "abf_search_result_priority"],
                'values': [appPromotionHtml, enableVehiclePost, searchResultPriority]
            },
            success: function (r) {
                console.log(r)
                settingResult.innerHTML = 'Success'
                setTimeout(() => {
                    settingResult.innerHTML = ""
                }, 2000);
            },
            complete: function () {
                loader.style.display = 'none'
                buttonSaveSetting.disabled = false
            },
            error: function (r) {
                console.log('error: ' + r)
                settingResult.innerHTML = "Failed"
                setTimeout(() => {
                    settingResult.innerHTML = ""
                }, 2000);

            }
        })
    }

    buttonSaveSetting.addEventListener("click", save_settings, false)

    vehiclePostCheckbox = document.getElementById("enable_vehicle_post")
    vehiclePostIntro = document.getElementById("vehiclePostTypeIntro");
    vehiclePostCheckbox.addEventListener('change', e => {
        if (e.target.checked) {
            vehiclePostIntro.style.display = 'block'
        } else {
            vehiclePostIntro.style.display = 'none'
        }
    });
});