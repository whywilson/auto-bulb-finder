jQuery(function ($) {
    var questions = $('#selects')
    var vehicleBulbList = document.getElementById("bulb_result");
    var appPromotion = document.getElementById("app_promotion");

    function refreshSelects() {
        var selects = questions.find('select');

        // Improve the selects with the Chose plugin
        selects.chosen({ width: '100%' });

        var isMobile = false; //initiate as false

        if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) ||
            /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0, 4))) {

            $('.chosen-search').hide();
        }

        // Listen for changes
        selects.unbind('change').bind('change', function () {
            var selected = $(this).find('option').eq(this.selectedIndex);
            // Look up the data-connection attribute
            var connection = selected.data('connection');
            // Removing the li containers that follow (if any)
            selected.closest('#selects div').nextAll().remove();

            if (connection) {
                fetchSelect(connection);
            }
        });
    }

    function replaceAll(string, search, replace) {
        return string.split(search).join(replace);
    }

    var working = false;

    function fetchSelect(val) {
        if (working) {
            return false;
        }
        working = true;

        yearselected = $('#year').val();
        makeselected = $('#make').val();
        modelselected = $('#model').val();
        submodelselected = $('#submodel').val();
        bodytypeselected = $('#bodytype').val();
        qualifierselected = $('#qualifier').val();

        if (!val) {
            updateYearMakeModel('yearSelect');
            refreshSelects();
            working = false;
        } else {
            $.ajax({
                url: window.location.origin + "/wp-admin/admin-ajax.php",
                type: 'POST',
                data: {
                    'action': 'auto_bulb_finder',
                    'fn': 'query_vehicle',
                    'query': {
                        key: val,
                        year: yearselected,
                        make: makeselected,
                        model: modelselected,
                        submodel: submodelselected,
                        bodytype: bodytypeselected,
                        qualifier: qualifierselected,
                        country: navigator.language,
                    }
                },
                success: function (response) {
                    var r = JSON.parse(response);

                    var connection, options = '';

                    let node = document.getElementById("bulb-size-list-content");
                    if (node) {
                        if (node.parentNode) {
                            node.parentNode.removeChild(node);
                        }
                    }

                    try {
                        if (r.id == 'vehicle') {
                            working = false
                            bulb_result = '<div id="bulb-size-list-content" class="accordion">'
                            for (var location in r.items) {
                                var data = r.items[location]
                                bulb_result += '<div class="accordion-item">'

                                if (data['products'].length > 0) {
                                    bulb_result += '<h2 class="accordion-title accordion-title-with-inner active" href="#" style="text-transform: capitalize;text-align: left;" ><button class="toggle">'
                                    bulb_result += '<i class="icon-arrow-right"></i>'
                                    bulb_result += '</button><span>' + replaceAll(location, '_', ' ') + '</span>'
                                    bulb_result += '<span class="abfinder-bulb-size">' + data['size'] + '</span>'
                                    bulb_result += '</h2>'

                                    //Inner Item
                                    bulb_result += '<div id="' + r.id + '-' + location + '" class="accordion-inner" style="transform: translateX(0px);"> '
                                    bulb_result += data['html'];
                                    bulb_result += '</div>'
                                } else {
                                    bulb_result += '<h2 class="accordion-title accordion-title-no-inner" href="#"  style="text-transform: capitalize;text-align: left;" ><button class="toggle">'
                                    bulb_result += '<i class="icon-arrow-right"></i>'
                                    bulb_result += '</button><span>' + replaceAll(location, '_', ' ') + '</span>'
                                    bulb_result += '<span class="abfinder-bulb-size">' + data['size'] + '</span>'
                                    bulb_result += '</h2>'
                                }

                                bulb_result += '</div>'
                            }

                            bulb_result += '</div>'
                            $(bulb_result).hide().appendTo(vehicleBulbList).fadeIn('slow')

                            vehicleBulbList = document.querySelector('#bulb_result')
                            vehicleBulbList.style.display = 'block'
                            if (appPromotion) {
                                appPromotion.style.display = 'block'
                            }
                        } else {
                            vehicleBulbList = document.querySelector('#bulb_result')
                            vehicleBulbList.style.display = 'none'
                            if (appPromotion) {
                                appPromotion.style.display = 'none'
                            }
                            removeAllChildNodes(vehicleBulbList)

                            if (r.key == 'yearSelect') {
                                function reverseObject(object) {
                                    var newObject = {};
                                    var keys = [];
                                    for (var key in object) {
                                        keys.push(key);
                                    }
                                    for (var i = keys.length - 1; i >= 0; i--) {
                                        var value = object[keys[i]];
                                        newObject[keys[i]] = value;
                                    }
                                    keys.reverse();
                                    return keys;
                                }
                                newObjects = reverseObject(r.items);

                                $.each(newObjects, function (k, v) {
                                    options += '<option value="' + v + '" data-connection="makeSelect">' + v + '</option>';
                                });
                            } else {
                                var items
                                if (r.items == "string") {
                                    items = JSON.parse(r.items)
                                } else {
                                    items = r.items
                                }
                                // console.log(items)
                                $.each(items, function (k, v) {
                                    connection = '';
                                    if (v) {
                                        connection = 'data-connection="' + v[0] + '"';
                                    }
                                    options += '<option value="' + k + '" ' + connection + '>' + v[1] + '</option>';
                                });
                            }

                            if (r.defaultText) {
                                options = '<option>' + r.defaultText + '</option>' + options;
                            }

                            if (val == 'parttypeSelect' || val == 'qualifierSelect') {
                                $('<div class="cell">\
                            <select id="' + r.select + '" data-placeholder="' + r.defaultText + '">\
                                ' + options + '\
                            </select>\
                            <span class="divider"></span>\
                        </div>').hide().appendTo(questions).fadeIn('slow');
                            } else {
                                $('<div class="cell medium-3">\
                                <select id="' + r.select + '" data-placeholder="' + r.defaultText + '">\
                                    ' + options + '\
                                </select>\
                                <span class="divider"></span>\
                            </div>').hide().appendTo(questions).fadeIn('slow');
                            }

                            updateYearMakeModel(r.key);
                            refreshSelects();
                            $('#' + r.select).trigger("chosen:open").chosen({ width: '100%' });
                        }
                    } catch (error) {
                        console.log("Error " + error)
                    }

                    working = false;
                },
                complete: function () {
                    updateAccordion()
                },
                error: function (r) {
                    console.log('error: ' + r)
                }
            })
        }
    }

    function updateAccordion() {
        jQuery(".accordion-title.accordion-title-with-inner").each(function () {
            jQuery(this)
                .off("click")
                .on("click", function (t) {
                    if (jQuery(this).next().is(":hidden")) {
                        jQuery(this)
                            .toggleClass("active")
                            .next()
                            .slideDown(200);
                    } else {
                        jQuery(this)
                            .parent()
                            .parent()
                            .find(".accordion-title.accordion-title-with-inner")
                            .addClass("active")
                            .next()
                            .slideUp(200)
                    }
                });

            if (jQuery(this).next().is(":hidden") && jQuery(this)[0].classList.contains("active")) {
                jQuery(this).click()
            }
        });
        jQuery(".accordion-inner a").each(function () {
            jQuery(this).attr("target", "_blank");
        }
        );
    }

    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    if (urlParams.has('vid')) {
        loadProductByVid(urlParams.get('vid'))
    }

    function loadProductByVid(vid) {
        $.ajax({
            url: window.location.origin + "/wp-admin/admin-ajax.php",
            type: 'POST',
            data: {
                'action': 'auto_bulb_finder',
                'fn': 'query_vehicle_by_vid',
                'vid': vid,
                country: navigator.language
            },
            success: function (response) {
                var r = JSON.parse(response);
                // console.log(r)

                let node = document.getElementById("bulb-size-list-content");
                if (node) {
                    if (node.parentNode) {
                        node.parentNode.removeChild(node);
                    }
                }
                try {
                    if (r.id == 'vehicle') {
                        working = false
                        bulb_result = '<div id="bulb-size-list-content" class="accordion">'
                        for (var location in r.items) {
                            var data = r.items[location]
                            bulb_result += '<div class="accordion-item">'

                            if (data['products'].length > 0) {
                                bulb_result += '<h2 class="accordion-title accordion-title-with-inner active" href="#" style="text-transform: capitalize;text-align: left;" ><button class="toggle">'
                                bulb_result += '<i class="icon-arrow-right"></i>'
                                bulb_result += '</button><span>' + replaceAll(location, '_', ' ') + '</span>'
                                bulb_result += '<span style="float: right;">' + data['size'] + '</span>'
                                bulb_result += '</h2>'

                                //Inner Item
                                bulb_result += '<div id="' + r.id + '-' + location + '" class="accordion-inner" style="transform: translateX(0px);"> '
                                bulb_result += data['html'];
                                bulb_result += '</div>'
                            } else {
                                bulb_result += '<h2 class="accordion-title accordion-title-no-inner" href="#"  style="text-transform: capitalize;text-align: left;" ><button class="toggle">'
                                bulb_result += '<i class="icon-arrow-right"></i>'
                                bulb_result += '</button><span>' + replaceAll(location, '_', ' ') + '</span>'
                                bulb_result += '<span style="float: right;">' + data['size'] + '</span>'
                                bulb_result += '</h2>'
                            }

                            bulb_result += '</div>'
                        }

                        bulb_result += '</div>'
                        $(bulb_result).hide().appendTo(vehicleBulbList).fadeIn('slow')

                        vehicleBulbList = document.querySelector('#bulb_result')
                        vehicleBulbList.style.display = 'block'
                        appPromotion.style.display = 'block'
                    }
                } catch (error) {
                    console.log("Result Error " + error)
                }

                working = false;
            },
            complete: function () {
                updateAccordion()
            },
            error: function (r) {
                console.log('error: ' + r)
            }
        })
    }

    var introduction = jQuery("#introduction")[0]
    if (introduction && introduction.innerHTML.length > 0) {
        jQuery("#introduction")[0].style.display = "block"
    }

    var videos = jQuery("#videos")[0]
    if (videos && videos.innerHTML.length > 20) {
        jQuery("#videos")[0].style.display = "block"
    }

    var bulb_result = jQuery("#bulb_result")[0]
    if (bulb_result && bulb_result.innerHTML.length > 20) {
        jQuery("#bulb_result")[0].style.display = "block"
    }

    var reviews = jQuery("#reviews")[0]
    if (reviews && reviews.innerHTML.length > 20) {
        jQuery("#reviews")[0].style.display = "block"
    }

    var promotion = jQuery("#promotion")[0]
    if (promotion && promotion.innerHTML.length > 20) {
        promotion.style.display = "block"
    }

    function removeAllChildNodes(parent) {
        while (parent.firstChild) {
            parent.removeChild(parent.firstChild);
        }
    }

    function removeAllChildByClass(className) {
        const node = document.getElementsByClassName(className)
        for (var i = 0; i < node.length; i++) {
            node[i].textContent = ''
        }
    }

    function updateYearMakeModel(val) {
        if (val == 'yearSelect') {
            $('<div class="cell medium-3">\
                <select class="start-select" placeholder="Make" disabled>\
                <option value="Make">Make</option>\
                </select>\
                <span class="divider"></span>\
            </div>').hide().appendTo(questions).fadeIn('slow');

            $('<div class="cell medium-3">\
                <select class="start-select" placeholder="Model" disabled>\
                <option value="Model">Model</option>\
                </select>\
                <span class="divider"></span>\
            </div>').hide().appendTo(questions).fadeIn('slow');
            $('<div class="cell medium-3">\
            <select class="start-select" style="text-align: center;"  disabled>\
            <option>Search</option>\
            </select>\
            <span class="divider"></span>\
        </div>').hide().appendTo(questions).fadeIn('slow');
        }
        if (val == 'makeSelect') {
            $('<div class="cell medium-3">\
                <select class="start-select" placeholder="Model" disabled>\
                <option value="Model">Model</option>\
                </select>\
                <span class="divider"></span>\
            </div>').hide().appendTo(questions).fadeIn('slow');
            $('<div class="cell medium-3">\
            <select class="start-select" style="text-align: center;"  disabled>\
            <option>Search</option>\
            </select>\
            <span class="divider"></span>\
        </div>').hide().appendTo(questions).fadeIn('slow');
        }
    }
    $(document).ajaxStart(function () {
        $('#preloader').show();
    }).ajaxStop(function () {
        $('#preloader').delay(800).hide();
    });
    fetchSelect('');
});

function getProductQuickView(product) {
    jQuery(".accordion-title").each(function () {
        jQuery(function ($) {
            $.ajax({
                url: window.location.origin + "/wp-admin/admin-ajax.php",
                type: 'POST',
                data: {
                    'action': 'flatsome_quickview',
                    'product': product
                },
                success: function (r) {
                    var modal = document.getElementById("quick-view-modal");
                    var modalProduct = document.getElementById("modal-content-product");
                    modalProduct.innerHTML = r
                    var span = document.getElementsByClassName("close")[0];
                    span.onclick = function () {
                        modal.style.display = "none";
                    }
                    window.onclick = function (event) {
                        if (event.target == modal) {
                            modal.style.display = "none";
                        }
                    }
                    modal.style.display = "block";
                },
                complete: function () {
                    $('#preloader').delay(800).hide();
                },
                error: function (r) {
                    console.log('error: ' + r)
                    $('#preloader').delay(800).hide();
                }
            })
        });
    })
}