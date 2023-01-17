{preloader}
<div id="abf-block" style="background: transparent;">
    {content}
    <div id="selects" class="row" style="margin: 1px;">
        <div class="cell medium-3" > <select id="year" data-placeholder="Year">
                <option>Year</option>
                <option value="2022" data-connection="makeSelect">2022</option>
                <option value="2021" data-connection="makeSelect">2021</option>
                <option value="2020" data-connection="makeSelect">2020</option>
                <option value="2019" data-connection="makeSelect">2019</option>
                <option value="2018" data-connection="makeSelect">2018</option>
                <option value="2017" data-connection="makeSelect">2017</option>
                <option value="2016" data-connection="makeSelect">2016</option>
                <option value="2015" data-connection="makeSelect">2015</option>
                <option value="2014" data-connection="makeSelect">2014</option>
                <option value="2013" data-connection="makeSelect">2013</option>
                <option value="2012" data-connection="makeSelect">2012</option>
                <option value="2011" data-connection="makeSelect">2011</option>
                <option value="2010" data-connection="makeSelect">2010</option>
                <option value="2009" data-connection="makeSelect">2009</option>
                <option value="2008" data-connection="makeSelect">2008</option>
                <option value="2007" data-connection="makeSelect">2007</option>
                <option value="2006" data-connection="makeSelect">2006</option>
                <option value="2005" data-connection="makeSelect">2005</option>
                <option value="2004" data-connection="makeSelect">2004</option>
                <option value="2003" data-connection="makeSelect">2003</option>
                <option value="2002" data-connection="makeSelect">2002</option>
                <option value="2001" data-connection="makeSelect">2001</option>
                <option value="2000" data-connection="makeSelect">2000</option>
                <option value="1999" data-connection="makeSelect">1999</option>
                <option value="1998" data-connection="makeSelect">1998</option>
                <option value="1997" data-connection="makeSelect">1997</option>
                <option value="1996" data-connection="makeSelect">1996</option>
                <option value="1995" data-connection="makeSelect">1995</option>
                <option value="1994" data-connection="makeSelect">1994</option>
                <option value="1993" data-connection="makeSelect">1993</option>
                <option value="1992" data-connection="makeSelect">1992</option>
                <option value="1991" data-connection="makeSelect">1991</option>
                <option value="1990" data-connection="makeSelect">1990</option>
                <option value="1989" data-connection="makeSelect">1989</option>
                <option value="1988" data-connection="makeSelect">1988</option>
                <option value="1987" data-connection="makeSelect">1987</option>
                <option value="1986" data-connection="makeSelect">1986</option>
                <option value="1985" data-connection="makeSelect">1985</option>
                <option value="1984" data-connection="makeSelect">1984</option>
                <option value="1983" data-connection="makeSelect">1983</option>
                <option value="1982" data-connection="makeSelect">1982</option>
                <option value="1981" data-connection="makeSelect">1981</option>
                <option value="1980" data-connection="makeSelect">1980</option>
                <option value="1979" data-connection="makeSelect">1979</option>
                <option value="1978" data-connection="makeSelect">1978</option>
                <option value="1977" data-connection="makeSelect">1977</option>
                <option value="1976" data-connection="makeSelect">1976</option>
                <option value="1975" data-connection="makeSelect">1975</option>
                <option value="1974" data-connection="makeSelect">1974</option>
                <option value="1973" data-connection="makeSelect">1973</option>
                <option value="1972" data-connection="makeSelect">1972</option>
                <option value="1971" data-connection="makeSelect">1971</option>
                <option value="1970" data-connection="makeSelect">1970</option>
                <option value="1969" data-connection="makeSelect">1969</option>
                <option value="1968" data-connection="makeSelect">1968</option>
                <option value="1967" data-connection="makeSelect">1967</option>
                <option value="1966" data-connection="makeSelect">1966</option>
                <option value="1965" data-connection="makeSelect">1965</option>
                <option value="1964" data-connection="makeSelect">1964</option>
                <option value="1963" data-connection="makeSelect">1963</option>
                <option value="1962" data-connection="makeSelect">1962</option>
                <option value="1961" data-connection="makeSelect">1961</option>
                <option value="1960" data-connection="makeSelect">1960</option>
            </select>
        </div>
    </div>
</div>

<div id="bulb_result" style="background: white; display:none">
    <div id="bulb-size-list-content"></div>
</div>
<div id="app-promotion" style="display: none;padding: 10px;">
    {app-promotion}
</div>
<div id="promotion" style="display: none;padding: 10px;">
    {promotion}
</div>

<div id="quick-view-modal" class="modal" style="display:none">
    <span class="close">&times;</span>
    <div class="modal-content product-lightbox lightbox-content">
        <p id="modal-content-product"></p>
    </div>
</div>


<script>
	jQuery(".accordion-title").each(function() {
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
                    .find(".accordion-title")
                    .addClass("active")
                    .next()
                    .slideUp(200)
            }
        });
});
</script>