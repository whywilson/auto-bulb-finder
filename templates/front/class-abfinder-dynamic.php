<?php

use ABFinder\Helper\ABFinder_Database; ?>

<?php include ABFINDER_PLUGIN_FILE . 'templates/front/class-abfinder-loader.php'; ?>
<div id="abf-block" style="background: transparent;">
    <?php
    if ($content) {
        if (strpos($content, 'h1') !== false  || strpos($content, 'h2') !== false) {
        } else if (strlen(trim($content)) > 0) {
            $content = '<h2 style="text-align: center;margin-top: 6px;">' . $content . '</h2>';
        }

        echo wp_kses_post($content);
    }
    ?>
    <div id="selects" class="row" style="margin: 1px;">
        <div class="cell medium-3"> <select id="year" data-placeholder="Year">
                <option>Year</option>
                <?php
                $abfinderDb = new ABFinder_Database();
                $localVehhicleYears = $abfinderDb->query_local_vehicle_years();
                $localYears = array();
                foreach ($localVehhicleYears as $localVehhicleYear) {
                    $localYears[] = intval($localVehhicleYear['year']);
                }

                $abf_search_result_priority = get_option("abf_search_result_priority", 0);

                if ($abf_search_result_priority == 0) {
                    $yearRange = array_unique(array_merge($localYears));
                } else {
                    $yearRange = range(2022, 1960);
                    $yearRange = array_unique(array_merge($localYears, $yearRange));
                }

                rsort($yearRange);

                foreach ($yearRange as $year) {
                    echo wp_kses('<option value="' . $year . '" data-connection="makeSelect" data-select="year">' . $year . '</option>', array('option' => array('value' => array(), 'data-connection' => array())));
                }

                ?>
            </select>
        </div>
    </div>
</div>

<div id="bulb_result" style="background: white; display:none">
    <div id="bulb-size-list-content"></div>
</div>
<div id="app_promotion" style="display: none;padding: 10px;">
    <?php
    $appPromotion = get_option("app_promotion_html", abfinder_get_default_app_promotion_html());
    if ($appPromotion) {
        echo wp_kses_post($appPromotion);
    }
    ?>
</div>

<style>
    .icon-arrow-right:before {
        content: " ";
        padding: 10px;
        background: url(<?php echo ABFINDER_PLUGIN_URL . 'assets/images/arrow-right.svg' ?>) center center no-repeat;
        width: 10px;
    }
</style>

<script>
    jQuery(function($) {
        var questions = $('#selects')
        var vehicleBulbList = document.getElementById("bulb_result");
        var appPromotion = document.getElementById("app_promotion");

        function refreshSelects() {
            var selects = questions.find('select');

            selects.chosen({
                width: '100%'
            });

            var isMobile = false;
            if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) ||
                /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0, 4))) {

                $('.chosen-search').hide();
            }

            selects.unbind('change').bind('change', function() {
                var selected = $(this).find('option').eq(this.selectedIndex);
                var connection = selected.data('connection');
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
            finalMakeSelectorKey = $('#make option:selected').data('select');
            makeselected = $('#make').val();
            finalModelSelectorKey = $('#model option:selected').data('select');
            modelselected = $('#model').val();
            finalSubmodelSelectorKey = $('#submodel option:selected').data('select');
            submodelselected = $('#submodel').val();
            finalBodytypeSelectorKey = $('#bodytype option:selected').data('select');
            bodytypeselected = $('#bodytype').val();
            finalQualifierSelectorKey = $('#qualifier option:selected').data('select');
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
                            [finalMakeSelectorKey]: makeselected,
                            [finalModelSelectorKey]: modelselected,
                            [finalSubmodelSelectorKey]: submodelselected,
                            [finalBodytypeSelectorKey]: bodytypeselected,
                            [finalQualifierSelectorKey]: qualifierselected,
                            country: navigator.language,
                        }
                    },
                    success: function(response) {
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
                                        bulb_result += '<h2 class="accordion-item-title accordion-item-title-with-inner active" href="#" style="text-transform: capitalize;display: flex; justify-content: space-between; align-items: center;"><div><button class="toggle">'
                                        bulb_result += '<i class="icon-arrow-right"></i>'
                                        bulb_result += '</button><span>' + replaceAll(location, '_', ' ') + '</span></div>'
                                        bulb_result += '<span class="abfinder-bulb-size">' + data['size'] + '</span>'
                                        bulb_result += '</h2>'

                                        //Inner Item
                                        bulb_result += '<div id="' + r.id + '-' + location + '" class="accordion-inner" style="transform: translateX(0px);"> '
                                        bulb_result += data['html'];
                                        bulb_result += '</div>'
                                    } else {
                                        bulb_result += '<h2 class="part-item accordion-item-title accordion-item-title-no-inner" href="#"  style="text-transform: capitalize;display: flex; justify-content: space-between; align-items: center;" ><div><button class="toggle">'
                                        bulb_result += '<i class="icon-arrow-right"></i></button><span>' + replaceAll(location, '_', ' ') + '</span></div>'
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

                                    $.each(newObjects, function(k, v) {
                                        options += '<option value="' + v + '" data-connection="makeSelect" data-select="year">' + v + '</option>';
                                    });
                                } else {
                                    var items
                                    if (r.items == "string") {
                                        items = JSON.parse(r.items)
                                    } else {
                                        items = r.items
                                    }
                                    // console.log(items)
                                    $.each(items, function(k, v) {
                                        connection = '';
                                        if (v) {
                                            connection = 'data-connection="' + v[0] + '" data-select="' + v[2] + '"';
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
                                $('#' + r.select).trigger("chosen:open").chosen({
                                    width: '100%'
                                });
                            }
                        } catch (error) {
                            console.log("Error " + error)
                        }

                        working = false;
                    },
                    complete: function() {
                        updateAccordion()
                    },
                    error: function(r) {
                        console.log('error: ' + r)
                    }
                })
            }
        }

        function updateAccordion() {
            jQuery(".accordion-item-title.accordion-item-title-with-inner").each(function() {
                jQuery(this)
                    .off("click")
                    .on("click", function(t) {
                        if (jQuery(this).next().is(":hidden")) {
                            jQuery(this)
                                .toggleClass("active")
                                .next()
                                .slideDown(200);
                        } else {
                            jQuery(this)
                                .parent()
                                .parent()
                                .find(".accordion-item-title.accordion-item-title-with-inner")
                                .addClass("active")
                                .next()
                                .slideUp(200)
                        }
                    });

                if (jQuery(this).next().is(":hidden") && jQuery(this)[0].classList.contains("active")) {
                    jQuery(this).click()
                }
            });
            jQuery(".accordion-inner a").each(function() {
                jQuery(this).attr("target", "_blank");
            });
        }

        function removeAllChildNodes(parent) {
            while (parent.firstChild) {
                parent.removeChild(parent.firstChild);
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
        $(document).ajaxStart(function() {
            $('#preloader').show();
        }).ajaxStop(function() {
            $('#preloader').delay(800).hide();
        });
        fetchSelect('');

        jQuery(".accordion-item-title").each(function() {
            jQuery(this)
                .off("click.accordion")
                .on("click.accordion", function(t) {
                    if (jQuery(this).next().is(":hidden")) {
                        jQuery(this)
                            .toggleClass("active")
                            .next()
                            .slideDown(200, function() {
                                /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(
                                        navigator.userAgent
                                    ) &&
                                    jQuery.scrollTo(jQuery(this).prev(), {
                                        duration: 300,
                                        offset: -100
                                    });
                            });
                    } else {
                        jQuery(this)
                            .parent()
                            .parent()
                            .find(".accordion-item-title")
                            .addClass("active")
                            .next()
                            .slideUp(200)
                    }
                });
        });
    });
</script>