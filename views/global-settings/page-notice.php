<style>

    #wsw-notice-support-view{

        margin-top: 10px;

        padding: 10px 10px 10px 10px;

        border-color: rgba(0, 0, 0, 0.22);

        border-width: 1px;

        border-style: solid;

        border-radius: 2px;

        margin-left: 10px;

    }

    .wsw-support-click-common {

        display: inline;

        position: relative;

    }





    .grow {

        display: inline-block;

        -webkit-transition-duration: 0.2s;

        transition-duration: 0.2s;

        -webkit-transition-property: -webkit-transform;

        transition-property: transform;

        -webkit-tap-highlight-color: rgba(0, 0, 0, 0);

        -webkit-transform: translateZ(0);

        -ms-transform: translateZ(0);

        transform: translateZ(0);

        box-shadow: 0 0 1px rgba(0, 0, 0, 0);

    }



    .grow:hover {

        -webkit-transform: scale(1.3);

        -ms-transform: scale(1.3);

        transform: scale(1.3);

    }





</style>

<script>

    jQuery(document).ready(function(){

        jQuery( '#wsw-notice-support-close' ).click( function (event) {

            jQuery("#wsw-notice-support-view").hide();



            var data = {

                action:'wsw_set_support_time'

            };



            jQuery.post(ajax_object.ajax_url, data, function(respond) {



            });

            return false;

        } );



        jQuery( '#wsw-notice-support-click' ).click( function (event) {



                if(document.getElementById('chk_author_linking'))        document.getElementById('chk_author_linking').checked = true;



                var data = {

                    action:'wsw_set_support_link'

                };



                jQuery.post(ajax_object.ajax_url, data, function(respond) {

                    jQuery("#wsw_support_title_1").hide();

                    jQuery("#wsw_support_title_2").show();

                    jQuery("#wsw_support_title_3").hide();

                });



        } );



    });



</script>



<div class="updated" id="wsw-notice-save-view" style="display: none; margin-left: 10px;">

<p>Save Settings Successfully.</p>

</div>

<div class="updated" id="wsw-build-sitemap-view" style="display: none; margin-left: 10px;">

    <p>Build Sitemap Successfully.</p>

</div>

<div class="updated" id="wsw-notice-support-view" style="<?php



    if(WSW_Main::$settings['chk_author_linking'] == '0'){



        if((time() - WSW_Main::$settings['wsw_initial_dt']) >= 24 * 60 * 60){

        //if((time() - WSW_Main::$settings['wsw_initial_dt']) >= 1){



        }

        else{

            echo 'display: none;';

        }

    }

    else {

        echo 'display: none;';

    }



?>">



    <div class="wsw-support-click-title wsw-support-click-common" id="wsw_support_title_1">Thank you for using

        <a href="<?php  $url = admin_url();

        echo $url . 'admin.php?page=wsw_dashboard_page';?>">SEO Wizard Plugin</a>,  if you enjoy our plugin please activate the author link credit by clicking

        <a href="#" id="wsw-notice-support-click"> OK.</a>



    </div>

    <div class="wsw-support-click-title wsw-support-click-common" id="wsw_support_title_2" style="display: none;">Thank you for supporting our plugin, the link has been placed in your footer.</div>

    <div style="float: right;" id="wsw_support_title_3">

        <small><a href="#" id="wsw-notice-support-close"> Hide this Message</a> </small>

    </div>



</div>