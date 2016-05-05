<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <title>Houses from Ibiza</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="Content-Style-Type" content="text/css">
    <link href="css/common-styles.css" rel="stylesheet" type="text/css">
    <link href="css/jquery-ui.css" rel="stylesheet" type="text/css">
    <script src="js/jquery.js" type="text/javascript"></script>
    <script src="js/jquery-ui.js" type="text/javascript"></script>
    <script src="js/order.js" type="text/javascript"></script>
    <script type="text/javascript" src="http://api.recaptcha.net/js/recaptcha_ajax.js"></script>
    <script type="text/javascript">
    // <![CDATA[
        $(function(){
            $('.showCollapse').click(function(){$(this).next().toggle();}).hover(function(){$(this).addClass('hover');},function(){$(this).removeClass('hover');});
            $('.collapse').click(function(){this.style.display="none";});
            
            $('#dateArriveShowId').datepicker({
                minDate: new Date(),
                dateFormat: 'DD, dd-M-yy',
                showOn: 'both',
                altField: '#dateArriveId',
                altFormat: 'yy-mm-dd',
                buttonImage: 'css/images/calendar.gif',
                buttonImageOnly: true,
                onSelect: function(selectedDate) {
                    var option = "minDate";
                    var instance = $(this).data("datepicker");
                    var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate);

                    $('#dateDepartureShowId').datepicker("option", option, date);
                }
            });
            $('#dateDepartureShowId').datepicker({
                minDate: new Date(),
                dateFormat: 'DD, dd-M-yy',
                showOn: 'both',
                altField: '#dateDepartureId',
                altFormat: 'yy-mm-dd',
                buttonImage: 'css/images/calendar.gif',
                buttonImageOnly: true,
                onSelect: function(selectedDate) {
                    var option = "maxDate";
                    var instance = $(this).data("datepicker");
                    var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
                    
                    $('#dateArriveShowId').datepicker("option", option, date);
                }
            });
        });
    // ]]>
    </script>
</head>
<body>
    <div class="layout">
        <div class="head">
            <div class="siteName">Houses from Ibiza</div>
            <div class="contacts">
                <span class="showCollapse">Contacts</span>
                <div class="collapse">
                    <div class="phone"> tel: +3 <span>(460)</span> 630-94-12</div>
                    <div class="mails">
                        <a href="mailto:rpr@ctv.es" title="send mail to rpr@ctv.es">rpr@ctv.es</a><br/>
                        <a href="mailto:gemcug@bk.ru" title="send mail to gemcug@bk.ru">gemcug@bk.ru</a>
                    </div>
                </div>
            </div>
            <div class="logo">
            	<a href="/index.html" title="index page"><img src="img/logo.png" alt=""/></a>
                <div class="flags">
                    <img src="img/england.gif" alt=""/>
                    <a href="formorder-es.php" title="spanish version"><img src="img/spain.gif" alt=""/></a>
                </div>
            </div>
        </div>
        <form id="orderForm" action="order.php" method="post" onsubmit="order_form_submit(this.action); return false;">
            
            <div class="sets">
            	<ul class="messageSet"></ul>
            	<ul class="errorSet"></ul>
            </div>
            <input type="hidden" name="lang" value="en"/>
            <div class="column">
                <div class="control">
                    <label for="houseId"><span class="require">*</span> House</label>
                    <select id="houseId" name="houseId">
                        <option value="1">Daniela</option>
                        <option value="2">Delfin</option>
                    </select>
                </div>
                
                <div class="control">
                    <label for="firstNameId"><span class="require">*</span> First Name</label><input id="firstNameId" type="text" name="firstName" value=""/>
                </div>
                <div class="control">
                    <label for="lastNameId"><span class="require">*</span> Last Name</label><input id="lastNameId" type="text" name="lastName"/>
                </div>
                <div class="control">
                    <label for="commentId"><span class="require">*</span> Comment</label><textarea id="commentId" name="comment" cols="30" rows="5"></textarea>
                </div>
            </div>
            <div class="column">
                <div class="control">
                    <label for="emailId"><span class="require">*</span> E-Mail</label><input id="emailId" type="text" name="Email"/>
                </div>
                <div class="control">
                    <input id="dateArriveId" type="hidden" name="dateArrive"/>
                    <label for="dateArriveShowId"><span class="require">*</span> Date Arrive</label><input id="dateArriveShowId" type="text"/>
                </div>
                <div class="control">
                    <input id="dateDepartureId" type="hidden" name="dateDeparture"/>
                    <label for="dateDepartureShowId"><span class="require">*</span> Date Departure</label><input id="dateDepartureShowId" type="text"/>
                </div>
                <div id="captcha" class="control">                
                <?php
                    require_once('_php/libs/recaptcha/recaptchalib.php');
                    require_once('_php/config.php');
                    
                    # the response from reCAPTCHA
                    $resp = null;
                    # the error code from reCAPTCHA, if any
                    $error = null;
			
                    echo recaptcha_get_html(PUBLICKEY);
                ?>
                </div>
            </div>
            <div class="control button">
                <input class="button ui-corner-all" onmouseover="$(this).addClass('hover')" onmouseout="$(this).removeClass('hover')" type="submit" value="Send order"/>
            </div>
        </form>
    </div>
    <div class="footer">
        <div class="seaandhouse">
            <strong>SeaAndHouse © 2010</strong>
        </div>
    </div>
    
    <script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-16440227-1']);
	  _gaq.push(['_trackPageview']);

	  (function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>
	
</body>
</html>