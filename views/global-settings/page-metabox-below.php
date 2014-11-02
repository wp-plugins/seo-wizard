
<style>
    .z-tabs.nested-tabs > .z-container > .z-content > .z-content-inner {padding-top: 10px;}
</style>
<script>
    jQuery(document).ready(function ($) {
        /* jQuery activation and setting options for parent tabs with id selector*/
        $("#tabbed-nav").zozoTabs({
            rounded: false,
            multiline: true,
            theme: "white",
            size: "medium",
            responsive: true,
            animation: {
                effects: "slideH",
                easing: "easeInOutCirc",
                duration: 500
            },
            defaultTab: "tab1"
        });

        /* jQuery activation and setting options for all nested tabs with class selector*/
        $(".nested-tabs").zozoTabs({
            position: "top-left",
            theme: "red",
            style: "underlined",
            rounded: false,
            shadows: false,
            defaultTab: "tab1",
            animation: {
                easing: "easeInOutCirc",
                effects: "slideV",
                duration: 350
            },
            size: "medium"
        });

        $( "#wsw_event_date" ).datepicker(
                {
                    dateFormat: 'yy-mm-dd',
                    constrainInput: true
                }
            );
        jQuery("#dashboard-page-post").show();
    });
</script>

<div id="dashboard-page-post" style="display: none; margin-right: 10px;">
    <span id="seowizard-post-id" class="wsw-ui-hidden"><?php echo $wsw_post_id; ?></span>

        <!-- Zozo Tabs Start-->
        <div id="tabbed-nav">

            <!-- Tab Navigation Menu -->
            <ul>
                <li><a>Settings<span></span></a></li>
                <li><a>Rich Snippets<span></span></a></li>
                <li><a>Social SEO<span></span></a></li>
                <li><a onclick='show_page_score();'>Page Analysis<span></span></a></li>

            </ul>

            <!-- Content container -->
            <div>
                <!-- General Settings -->
                <div>
                    <!-- Zozo Tabs nested (Overview) Start-->
                    <div class="wsw-post-settings">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">SEO Keyword</label>
                            <div class="col-sm-9">
                                <label style="width: 100%;">
                                    <input type="text" id="wsw_keyword_value" value="<?php echo $wsw_keyword_value; ?>" style="width: 60%;">
                                </label><br>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php echo 'Use META Keyword'; ?></label>
                            <div class="col-sm-9">
                                <label style="width: 100%;">
                                    <input type="checkbox" id="wsw_is_meta_keyword" <?php echo ($wsw_is_meta_keyword =='1')?'checked':''?>>
                                    Allow SEO Wizard to automatically use below as META keyword tags.
                                </label>
                                <label style="width: 100%;"><input type="radio" name="wsw_keyword_type" value="categories" <?php echo ($wsw_meta_keyword_type =='categories')?'checked':''?>> <?php echo 'Use Post Categories'; ?></label><br>
                                <label style="width: 100%;"><input type="radio" name="wsw_keyword_type" value="tags" <?php echo ($wsw_meta_keyword_type =='tags')?'checked':''?>> <?php echo 'Use Post Tags'; ?></label>
                                <label style="width: 100%;"><input type="radio" name="wsw_keyword_type" value="keywords" <?php echo ($wsw_meta_keyword_type =='keywords')?'checked':''?>> <?php echo 'Use SEO Keyword'; ?></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php echo 'Use META title tag'; ?></label>
                            <div class="col-sm-9">
                                <label style="width: 100%;">
                                    <input type="checkbox" id="wsw_is_meta_title" <?php echo ($wsw_is_meta_title =='1')?'checked':''?>>
                                    Allow SEOWizard to automatically use the text below as META title tag.
                                </label>
                                <label style="width: 100%;">
                                    <input type="text" id="wsw_meta_title" value="<?php echo $wsw_meta_title; ?>" style="width: 60%;">
                                </label><br>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php echo 'Use META description tag'; ?></label>
                            <div class="col-sm-9">
                                <label style="width: 100%;">
                                    <input type="checkbox" id="wsw_is_meta_description" <?php echo ($wsw_is_meta_description =='1')?'checked':''?> >
                                    Allow SEOWizard to automatically use the text below as META description tag.
                                </label>
                                <label style="width: 100%;">
                                    <textarea style="width: 60%;"  id="wsw_meta_description"><?php echo $wsw_meta_description; ?></textarea>
                                </label><br>
                            </div>
                        </div>
                        <div class="form-group" style="display: none;">
                            <label class="col-sm-3 control-label"><?php echo 'Override keyword detection in sentences'; ?></label>
                            <div class="col-sm-9">
                                <label style="width: 100%;">
                                    <input type="checkbox" id="wsw_is_over_sentences" <?php echo ($wsw_is_over_sentences =='1')?'checked':''?> >
                                    Allows you to automatically override SEOWizard keywords autodetection in sentences.
                                </label>
                                <label style="width: 100%;">
                                    <input type="checkbox" id="wsw_first_over_sentences" <?php echo ($wsw_first_over_sentences =='1')?'checked':''?> >
                                    Keyword present in first sentence
                                </label>
                                <label style="width: 100%;">
                                    <input type="checkbox" id="wsw_last_over_sentences" <?php echo ($wsw_last_over_sentences =='1')?'checked':''?>>
                                    Keyword present in last sentence
                                </label>
                            </div>
                        </div>

                    </div>
                    <!--  Zozo Tabs nested (Overview) End-->
                </div>

                <!-- Rich Snippets Settings -->
                <div>
                    <div class="form-group">

                        <div class="col-sm-12">
                            <label style="width: 100%;">
                                <input type="checkbox" id="wsw_is_rich_snippets" <?php echo ($wsw_is_rich_snippets =='1')?'checked':''?>>
                                Enable Rich Snippets
                            </label>
                            <label style="width: 100%;">
                                <input type="checkbox" id="wsw_show_rich_snippets" <?php echo ($wsw_show_rich_snippets =='1')?'checked':''?>>
                                Show Rich Snippets
                            </label>
                        </div>
                    </div>
                    <!-- Zozo Tabs nested (Subscribe) Start-->
                    <div class="nested-tabs">
                        <ul>
                            <li><a>Review</a></li>
                            <li><a>Event</a></li>
                            <li><a>People</a></li>
                            <li><a>Product</a></li>
                        </ul>

                        <div>
                            <div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Rating'; ?></label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="wsw_review_rating" style="height: 28px;width: 50%;">
                                            <?php
                                            $rating_values = WSW_Main::get_rating_values();
                                            foreach($rating_values as $rating_value)
                                            {?>
                                                <option value="<?php echo $rating_value; ?>" <?php if ($wsw_rating_value == $rating_value) echo ' selected="selected"'; ?>><?php echo $rating_value; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                </div>

                                <div class="form-group" style="margin-top: 34px;">
                                <label class="col-sm-3 control-label"><?php echo 'Author'; ?></label>
                                <div class="col-sm-9">
                                    <label style="width: 100%;">
                                        <input type="text" id="wsw_review_author" value="<?php echo $wsw_review_author; ?>" style="width: 50%;">
                                    </label>
                                </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Summary'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_review_summary" value="<?php echo $wsw_review_summary; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Description'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <textarea style="width: 50%;"  id="wsw_review_description"><?php echo $wsw_review_description; ?></textarea>
                                        </label>
                                    </div>
                                </div>

                            </div>

                            <div>
                                <div class="form-group">
                                    <label class="col-sm-12 control-label"><?php echo 'Event'; ?></label>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Name'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_event_name" value="<?php echo $wsw_event_name; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Date And Time'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_event_date" value="<?php echo $wsw_event_date; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'URL'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_event_url" value="<?php echo $wsw_event_url; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-12 control-label"><?php echo 'Location'; ?></label>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Name'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_event_location_name" value="<?php echo $wsw_event_location_name; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Street'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_event_location_street" value="<?php echo $wsw_event_location_street; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Locality'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_event_location_locality" value="<?php echo $wsw_event_location_locality; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Region'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_event_location_region" value="<?php echo $wsw_event_location_region; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>

                            </div>
                            <div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'First Name'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_people_fname" value="<?php echo $wsw_people_fname; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Last Name'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_people_lname" value="<?php echo $wsw_people_lname; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Locality'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_people_locality" value="<?php echo $wsw_people_locality; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Region'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_people_region" value="<?php echo $wsw_people_region; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Title'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_people_title" value="<?php echo $wsw_people_title; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Home URL'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_people_homeurl" value="<?php echo $wsw_people_homeurl; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Photo URL'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_people_photourl" value="<?php echo $wsw_people_photourl; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>


                            </div>
                            <div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Name'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_product_name" value="<?php echo $wsw_product_name; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Image URL'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_product_imageurl" value="<?php echo $wsw_product_imageurl; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Description'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_product_description" value="<?php echo $wsw_product_description; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Price'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <textarea  style="width: 50%;" id="wsw_product_offers"><?php echo $wsw_product_offers; ?></textarea>
                                        </label>
                                    </div>
                                </div>


                            </div>

                        </div>
                    </div>
                    <!-- Zozo Tabs nested (Subscribe) End-->
                </div>

                <!-- Social Settings -->
                <div>
                    <!-- Zozo Tabs nested (Social) Start-->
                    <div class="nested-tabs">
                        <ul>
                            <li><a>Facebook</a></li>
                            <li><a>Twitter</a></li>
                        </ul>
                        <div>
                            <div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label style="width: 100%;">
                                            <input type="checkbox" id="wsw_is_social_facebook" <?php echo ($wsw_is_social_facebook =='1')?'checked':''?>>
                                            Enable Social SEO for Facebook
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Publisher'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_social_facebook_publisher" value="<?php echo $wsw_social_facebook_publisher; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Author'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_social_facebook_author" value="<?php echo $wsw_social_facebook_author; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Title'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_social_facebook_title" value="<?php echo $wsw_social_facebook_title; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Description'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_social_facebook_description" value="<?php echo $wsw_social_facebook_description; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>


                            </div>

                            <div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label style="width: 100%;">
                                            <input type="checkbox" id="wsw_is_social_twitter" <?php echo ($wsw_is_social_twitter =='1')?'checked':''?>>
                                            Enable Social SEO for Twitter
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Title'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_social_twitter_title" value="<?php echo $wsw_social_twitter_title; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?php echo 'Description'; ?></label>
                                    <div class="col-sm-9">
                                        <label style="width: 100%;">
                                            <input type="text" id="wsw_social_twitter_description" value="<?php echo $wsw_social_twitter_description; ?>" style="width: 50%;">
                                        </label>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                    <!-- Zozo Tabs nested (Social) End-->
                </div>

                <!-- Page Analysis View -->
                <div>


                    <div class="form-group" id="wsw_page_analysis_loading" style="text-align: center;">
                    <i class="fa fa-spinner fa-spin"></i>
                    </div>
                    <div id="wsw_page_analysis_view" style="display: none;">
                    <div class="form-group">
                        <div class="col-sm-2">

                        </div>
                        <div class="col-sm-4">
                            <div class="wsw-score-box-block" style="text-align: center;">

                                    <div style="width: 100%;">
                                        <h1>Score</h1>
                                        <hr>
                                        <address id="wsw-score-value-box">
                                        </address>
                                    </div>

                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="wsw-score-box-block" style="text-align: center;">

                                    <div  style="width: 100%;">
                                        <h1>Keyword Density</h1>
                                        <hr>
                                        <address id="wsw-density-value-box">

                                        </address>
                                    </div>

                            </div>
                        </div>
                        <div class="col-sm-12" style="height: 15px;">

                        </div>

                    </div>
                    <!-- Zozo Tabs nested (Score) Start-->
                    <div class="nested-tabs">
                        <ul>
                            <li><a>Suggestions</a></li>
                            <li><a onclick='show_page_content();'>Videos<span></span></a></li>
                            <li><a onclick='show_page_lsi();'>LSI<span></span></a></li>
                        </ul>
                        <div>
                            <div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Keyword Decoration</label>
                                    <div class="col-sm-9">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"></label>
                                    <div class="col-sm-9">
                                        <div id="wsw-suggestions-keyword-view">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" style="display: none;">
                                    <label class="col-sm-3 control-label">URL</label>
                                    <div class="col-sm-9">
                                    </div>
                                </div>

                                <div class="form-group" style="display: none;">
                                    <label class="col-sm-3 control-label"></label>
                                    <div class="col-sm-9">
                                        <div id="wsw-suggestions-url-view">

                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" style="display: none;">
                                    <label class="col-sm-3 control-label">Content</label>
                                    <div class="col-sm-9">
                                    </div>
                                </div>

                                <div class="form-group" style="display: none;">
                                    <label class="col-sm-3 control-label"></label>
                                    <div class="col-sm-9">
                                        <div id="wsw-suggestions-content-view">

                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div>
                                <div id="wsw_dialog" style="display: none; text-align: left;
                                background-color: rgb(230, 230, 230);;border-color: #ebccd1;padding: 10px;
                                margin-bottom: 20px;border: 1px solid transparent;border-radius: 4px;">
                                    <label id="wsw_dialog_message">Copied URL into clipboard</label>
                                </div>
                                <div class="form-group" id="wsw_youtube_view_loading" style="text-align: center;">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </div>
                                <div  id="wsw-youtube-view">
                                    <div class="wsw-content-video-view">

                                        <?php
                                            for ($item = 0; $item < 8; $item++) {?>
                                                <div id="wsw-videos-item-thumbnail-container" style= "float:left; width: 170px;height: 300px">
                                                    <img id="wsw_clipbord_<?php echo($item);?>" data-clipboard-text="" width="150" height="100" src="" />
                                                    <div class="wsw-videos-item-duration"><label id="wsw_videos_item_duration_<?php echo($item);?>"></label></div>
                                                    <div class="wsw-videos-item-title" style="margin-right: 10px"><a id="wsw_videos_item_title_<?php echo($item);?>"href="" target="_blank" title=""></a></div>
                                                    <div class="wsw-videos-item-author"><label id="wsw_videos_item_author_<?php echo($item);?>"></label></div>
                                                    <div class="wsw-videos-item-views"><label id="wsw_videos_item_views_<?php echo($item);?>"></label></div>
                                                </div>
                                            <?php
                                            }
                                        ?>
                                    </div>
                                </div>

                            </div>
                            <div>

                                <div class="form-group" id="wsw_lsi_view_loading" style="text-align: center;">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </div>
                                <div  id="wsw-lsi-view" style="display: none;">

                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- Zozo Tabs nested (Score) End-->
                    </div>
                </div>

            </div>

        </div>
        <!-- Zozo Tabs End-->
    <div class="wsw-global-save-view">
        <button type="button" class="btn btn-primary" onclick="save_post_settings();" >Save Post Settings</button>
									<br /><p><center>Seo Wizard created by <a href="http://seo.uk.net/?wizard" target="_blank">Seo.uk.net</a></center></p><center><a href="http://seo.uk.net/?wizard" target="_blank"><img src="http://seo.uk.net/wp-content/uploads/2014/10/seo-banner.gif" /></a></center></p>
    </div>

</div>

