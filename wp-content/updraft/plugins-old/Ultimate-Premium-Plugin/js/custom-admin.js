var SFSI = jQuery;

var SfsiHelper = (function ($, context) {

    var validateUrl = function (url) {

            var pattern = /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;

            return pattern.test(url);
        },

        defineConstant = function (name, value) {

            if ("string" === typeof name && name.length > 0) {

                var value = value || false;

                var data = {};

                if (false != value) {
                    data.value = value;
                }

                data.writable = false;

                Object.defineProperty(context, name, data);

            }
        },

        openWpMedia = function (btnUploadID, inputImageId, previewDivId, funcNameSuccessHandler) {

            var btnElem, inputImgElem, previewDivElem, iconName;

            var clickHandler = function (event) {

                var send_attachment_bkp = wp.media.editor.send.attachment;

                var frame = wp.media({
                    title: 'Select or Upload image',
                    button: {
                        text: 'Use this image'
                    },
                    multiple: false // Set to true to allow multiple files to be selected
                });

                frame.on('select', function () {

                    // Get media attachment details from the frame state
                    var attachment = frame.state().get('selection').first().toJSON();
                    var url = attachment.url.split("/");
                    var fileName = url[url.length - 1];
                    var fileArr = fileName.split(".");
                    var fileType = fileArr[fileArr.length - 1];

                    if (fileType != undefined && (fileType == 'gif' || fileType == 'jpeg' || fileType == 'jpg' || fileType == 'png')) {

                        //inputImgElem.val(attachment.url);
                        //previewDivElem.attr('src',attachment.url);

                        btnElem.val("Change Picture");

                        wp.media.editor.send.attachment = send_attachment_bkp;

                        if (null !== funcNameSuccessHandler && funcNameSuccessHandler.length > 0) {

                            var iconData = {
                                "imgUrl": attachment.url
                            };

                            context[funcNameSuccessHandler](iconData, btnElem);
                        }
                    } else {
                        alert("Only Images are allowed to upload");
                        frame.open();
                    }
                });

                // Finally, open the modal on click
                frame.open();
                return false;
            };

            if ("object" === typeof btnUploadID && null !== btnUploadID) {

                btnElem = SFSI(btnUploadID);
                inputImgElem = btnElem.parent().find('input[type="hidden"]');
                previewDivElem = btnElem.parent().find('img');
                iconName = btnElem.attr('data-iconname');
                clickHandler();
            } else {

                btnElem = $('#' + btnUploadID);
                inputImgElem = $('#' + inputImageId);
                previewDivElem = $('#' + previewDivId);

                btnElem.on("click", clickHandler);
            }

        };

    return {
        validateUrl: validateUrl,
        openWpMedia: openWpMedia,
        defineConstant: defineConstant
    };

})(SFSI, this);

Object.defineProperty(this, 'SfsiHelper', {
    value: SfsiHelper,
    writable: false
});

SfsiHelper.defineConstant('arrIcons', arrIcons);

var SfsiLoader = (function ($, context) {

    var mousoverLoader = {

        show: function () {
            $(".sfsiMouseOverloader").show();
            $(".other_icons_effects_options_container").css({
                opacity: 0.05,
                background: '#f5f5f5',
                'pointer-events': 'none'
            });
        },

        hide: function () {
            $(".sfsiMouseOverloader").hide();
            $(".other_icons_effects_options_container").css({
                opacity: 1,
                background: '#fff',
                'pointer-events': 'all'
            });
        }

    };

    return {
        mousoverLoader: mousoverLoader
    }

})(SFSI, this);

SfsiHelper.defineConstant('SfsiLoader', SfsiLoader);

var _sfsi_plus_order_icons = (function (SFSI, context) {

    priv_updateIndex = function (arrOrders, iconName, iconData) {
        var arlength = arrOrders.length;

        for (var key = 0; key < arlength; key++) {

            var element = arrOrders[key];

            if (iconName == element.iconName) {
                arrOrders[key] = iconData;
                break;
            }
        }

        return arrOrders;
    },

        pub_sfsi_premium_get_icon_order = function (orderIconContainerClass) {

            var selector = "." + orderIconContainerClass + " li";
            var arrElements = SFSI(selector);

            var arrDefaultIconOrders = [

                {
                    iconName: "rss",
                    index: 1
                },
                {
                    iconName: "email",
                    index: 2
                },
                {
                    iconName: "fb",
                    index: 3
                },
                {
                    iconName: "twitter",
                    index: 5
                },
                {
                    iconName: "share",
                    index: 6
                },
                {
                    iconName: "youtube",
                    index: 7
                },
                {
                    iconName: "pinterest",
                    index: 8
                },

                {
                    iconName: "linkedin",
                    index: 9
                },
                {
                    iconName: "instagram",
                    index: 10
                },
                {
                    iconName: "houzz",
                    index: 11
                },
                {
                    iconName: "snapchat",
                    index: 12
                },

                {
                    iconName: "whatsapp",
                    index: 13
                },
                {
                    iconName: "skype",
                    index: 14
                },
                {
                    iconName: "vimeo",
                    index: 15
                },
                {
                    iconName: "soundcloud",
                    index: 16
                },

                {
                    iconName: "yummly",
                    index: 17
                },
                {
                    iconName: "flickr",
                    index: 18
                },
                {
                    iconName: "reddit",
                    index: 19
                },
                {
                    iconName: "tumblr",
                    index: 20
                },
                {
                    iconName: "fbmessenger",
                    index: 21
                },
                {
                    iconName: "mix",
                    index: 22
                },
                {
                    iconName: "ok",
                    index: 23
                },
                {
                    iconName: "telegram",
                    index: 24
                },
                {
                    iconName: "vk",
                    index: 25
                },
                {
                    iconName: "weibo",
                    index: 26
                },
                {
                    iconName: "xing",
                    index: 27
                },
                {
                    iconName: "wechat",
                    index: 28
                },
                {
                    iconName: "phone",
                    index: 29
                },
                {
                    iconName: "gab",
                    index: 30
                },
                {
                    iconName: "copylink",
                    index: 31
                },
                {
                    iconName: "mastodon",
                    index: 32
                },
                {
                    iconName: "ria",
                    index: 33
                },
                {
                    iconName: "inha",
                    index: 34
                },
                {
                    iconName: "threads",
                    index: 35
                },
                {
                    iconName: "bluesky",
                    index: 36
                }
            ];

            arrElements.each(function () {

                var iconName = SFSI(this).attr('data-icon');
                console.log(iconName);
                if ('undefined' !== typeof iconName && null != iconName) {

                    var iconData = {};
                    iconData.index = SFSI(this).attr('data-index');
                    iconData.iconName = iconName;

                    if ('custom' != iconName) {
                        arrDefaultIconOrders = priv_updateIndex(arrDefaultIconOrders, iconName, iconData);
                    } else {
                        iconData.customElementIndex = SFSI(this).attr('element-id');
                        arrDefaultIconOrders.push(iconData);
                    }
                }

            });

            return arrDefaultIconOrders;
        }

    return {
        sfsi_premium_get_icon_order: pub_sfsi_premium_get_icon_order
    }

})(SFSI, this);

SfsiHelper.defineConstant('_sfsi_plus_order_icons', _sfsi_plus_order_icons);

function sfsi_plus_section_Display(s, i) {
    "hide" == i ? (SFSI("." + s + " :input").prop("disabled", !0), SFSI("." + s + " :button").prop("disabled", !0),
        SFSI("." + s).hide()) : (SFSI("." + s + " :input").removeAttr("disabled", !0), SFSI("." + s + " :button").removeAttr("disabled", !0),
        SFSI("." + s).show());
    var diff = SFSI(".sfsi_premium_mobile_section").find('input[name=sfsi_plus_icons_onmobile]:checked').val();
}

function sfsi_plus_update_index() {
    var s = 1;
    SFSI("ul.plus_icn_listing li.plus_custom").each(function () {
        SFSI(this).children("span.sfsiplus_custom-txt").html("Custom " + s), s++;
    }),
        m = 1, SFSI("ul.sfsi_premium_mobile_icon_listing li.plus_custom").each(function () {
        SFSI(this).children("span.sfsiplus_custom-txt").html("Custom " + m), m++;
    }),
        cntt = 1, SFSI("div.cm_lnk").each(function () {
        SFSI(this).find("h2.custom").find("span.sfsiCtxt").html("Custom " + cntt + ":"),
            cntt++;
    }),
        cntt = 1, SFSI("div.plus_custom_m").find("div.sfsiplus_custom_section").each(function () {
        SFSI(this).find("label").html("Custom " + cntt + ":"), cntt++;
    });
}

function sfsipluscollapse(s) {
    var i = !0,
        e = SFSI(s).closest("div.ui-accordion-content").prev("h3.ui-accordion-header"),
        t = SFSI(s).closest("div.ui-accordion-content").first();
    e.toggleClass("ui-corner-all", i).toggleClass("accordion-header-active ui-state-active ui-corner-top", !i).attr("aria-selected", (!i).toString()),
        e.children(".ui-icon").toggleClass("ui-icon-triangle-1-e", i).toggleClass("ui-icon-triangle-1-s", !i),
        t.toggleClass("accordion-content-active", !i), i ? t.slideUp() : t.slideDown();
}

function plus_update_Sec5Iconorder() {
    SFSI("ul.plus_share_icon_order").children("li").each(function () {
        SFSI(this).attr("data-index", SFSI(this).index() + 1);
    });
}

function plus_update_Sec5IconMobileorder() {
    SFSI("ul.plus_share_icon_mobile_order").children("li").each(function () {
        SFSI(this).attr("data-index", SFSI(this).index() + 1);
    });
}

var mobileIconOldHtml = jQuery(".plus_share_icon_mobile_order").html();

function sfsi_plus_depened_sections(should_reload) {

    if ("sfsi" == SFSI("input[name='sfsi_plus_rss_icons']:checked").val()) {
        for (i = 0; 23 > i; i++) {
            var s = i + 1;
            var cls = "sfsiplus_row_" + s + "_2_sficon";
            SFSI(".sfsiplus_row_" + s + "_2").addClass(cls);
        }

        // for (var i = 16; 22 > i; i++) {
        // 	var s = i + 1;
        // 	var cls = "sfsiplus_row_" + s + "_2_sficon";
        // 	SFSI(".sfsiplus_row_" + s + "_2").addClass(cls);
        // }

        var t = SFSI(".icon_img").attr("src");
        if (t) {
            if (t.indexOf("subscribe") != -1) {
                var n = t.replace("subscribe.png", "sf_arow_icn.png");
            } else {
                var n = t.replace("email.png", "sf_arow_icn.png");
            }
            SFSI(".icon_img").attr("src", n);
        }

        /* Update Img based on icon selection */
        var tImg = SFSI(".sfsiplus_row_2_2 .sfsiplus_icon_img_wrapper img").attr("src");
        if (tImg) {
            if (tImg.indexOf("subscribe") != -1) {
                var n = tImg.replace("subscribe.png", "sf.png");
            } else {
                var n = tImg.replace("email.png", "sf.png");
            }
            SFSI(".sfsiplus_row_2_2 .sfsiplus_icon_img_wrapper img").attr("src", n);
        }

        if (!SFSI("input[name=sfsi_plus_email_bgColor]").val() || SFSI("input[name=sfsi_plus_email_bgColor]").val() == '#343D44' || SFSI("input[name=sfsi_plus_email_bgColor]").val() == '#16CB30') {
            SFSI(".sfsiplus_row_2_2 .sfsiplus_icon_img_wrapper").css("background", "#05B04E");
            //SFSI("input[name=sfsi_plus_email_bgColor]").val( '#05B04E' );
        }

    } else {
        if ("email" == SFSI("input[name='sfsi_plus_rss_icons']:checked").val()) {
            var t = SFSI(".icon_img").attr("src");
            if (t) {
                if (t.indexOf("sf_arow_icn") != -1) {
                    var n = t.replace("sf_arow_icn.png", "email.png");
                } else {
                    var n = t.replace("subscribe.png", "email.png");
                }
                SFSI(".icon_img").attr("src", n);
            }

            /* Update Img based on icon selection */
            var tImg = SFSI(".sfsiplus_row_2_2 .sfsiplus_icon_img_wrapper img").attr("src");
            if (tImg) {
                if (tImg.indexOf("sf_arow_icn") != -1) {
                    var n = tImg.replace("sf_arow_icn.png", "email.png");
                } else {
                    var n = tImg.replace("subscribe.png", "email.png");
                }
                SFSI(".sfsiplus_row_2_2 .sfsiplus_icon_img_wrapper img").attr("src", n);
            }
            if (!SFSI("input[name=sfsi_plus_email_bgColor]").val() || SFSI("input[name=sfsi_plus_email_bgColor]").val() == '#05B04E' || SFSI("input[name=sfsi_plus_email_bgColor]").val() == '#16CB30') {
                SFSI(".sfsiplus_row_2_2 .sfsiplus_icon_img_wrapper").css("background", "#343D44");
                //SFSI("input[name=sfsi_plus_email_bgColor]").val( '#343D44' );
            }
        } else {
            for (i = 0; 16 > i; i++) {
                var s = i + 1;
                var cls = "sfsiplus_row_" + s + "_2_follow";
                SFSI(".sfsiplus_row_" + s + "_2").addClass(cls);
            }

            for (var i = 17; 22 > i; i++) {
                var s = i + 1;
                var cls = "sfsiplus_row_" + s + "_2_follow";
                SFSI(".sfsiplus_row_" + s + "_2").addClass(cls);
            }

            var t = SFSI(".icon_img").attr("src");
            if (t) {
                if (t.indexOf("email") != -1) {
                    var n = t.replace("email.png", "subscribe.png");
                } else {
                    var n = t.replace("sf_arow_icn.png", "subscribe.png");
                }
                SFSI(".icon_img").attr("src", n);
            }

            /* Update Img based on icon selection */
            var tImg = SFSI(".sfsiplus_row_2_2 .sfsiplus_icon_img_wrapper img").attr("src");
            if (tImg) {
                if (tImg.indexOf("email") != -1) {
                    var n = tImg.replace("email.png", "subscribe.png");
                } else {
                    var n = tImg.replace("sf_arow_icn.png", "subscribe.png");
                }
                SFSI(".sfsiplus_row_2_2 .sfsiplus_icon_img_wrapper img").attr("src", n);
            }
            if (!SFSI("input[name=sfsi_plus_email_bgColor]").val() || SFSI("input[name=sfsi_plus_email_bgColor]").val() == '#343D44' || SFSI("input[name=sfsi_plus_email_bgColor]").val() == '#05B04E') {
                SFSI(".sfsiplus_row_2_2 .sfsiplus_icon_img_wrapper").css("background", "#16CB30");
                //SFSI("input[name=sfsi_plus_email_bgColor]").val( '#16CB30' );
            }
        }
    }

    var diff = SFSI(".sfsi_premium_mobile_section").find('input[name=sfsi_plus_icons_onmobile]:checked').val();

    arrIcons.forEach(function (iconName, index) {
        sfsi_plus_toogle_desktop_icon_for_order_icons(iconName);
        sfsi_plus_toogle_mobile_icon_for_order_icons(iconName);
        sfsi_plus_toogle_desktop_icon_for_mouserover_icons(iconName);
    });

    // Custom icons display section
    SFSI.each(SFSI("ul.plus_icn_listing li.plus_custom input[element-ctype='sfsiplus-cusotm-icon']"), function (index, element) {
        var thisElem = SFSI(this);
        var customEId = parseInt(thisElem.attr('element-id'));
        var customDElem = SFSI('.sfsiplus_custom_iconOrder[element-id="' + customEId + '"]');
        var customSection2Elem = SFSI('.tab2 .plus_custom-links .sfsiICON_' + customEId);

        if (thisElem.prop("checked")) {
            customDElem.show();
            customSection2Elem.removeClass("hide").addClass("show");
        } else {
            customDElem.hide();
            customSection2Elem.removeClass("show").addClass("hide");
        }

    });

    if ("yes" == diff) {

        SFSI.each(SFSI("ul.sfsi_premium_mobile_icon_listing li.plus_custom input[element-type='sfsiplus-cusotm-mobile-icon']"), function (index, element) {

            var thisElem = SFSI(this);
            var customEId = parseInt(thisElem.attr('data-key'));

            var customDElem = SFSI('.sfsiplus_custom_mobilesection[element-id="' + customEId + '"]');

            if (thisElem.prop("checked")) {

                SFSI.each(customDElem, function (index, Celement) {
                    SFSI(Celement).show();
                });
            } else {

                SFSI.each(customDElem, function (index, Celement) {
                    SFSI(Celement).hide();
                });
            }
        });
    } else {

        SFSI.each(SFSI("ul.plus_icn_listing li.plus_custom input[element-ctype='sfsiplus-cusotm-icon']"), function (index, element) {

            var thisElem = SFSI(this);
            var customEId = parseInt(thisElem.attr('element-id'));
            var customDElem = SFSI('.sfsiplus_custom_mobilesection[element-id="' + customEId + '"]');

            // Question 4
            var customMouseOverElem = SFSI(".other_icons_effects_options_container").find('div[data-customiconindex=' + customEId + ']');

            if (thisElem.prop("checked")) {
                customDElem.show();
                customMouseOverElem.removeClass("hide").addClass("show");
            } else {
                customDElem.hide();
                customMouseOverElem.removeClass("show").addClass("hide");

            }

        });

    }

}

function PlusCustomIConSectionsUpdate() {
    sfsi_plus_section_Display("counter".ele, show);
}

function sfsi_plus_toogle_desktop_icon_for_order_icons(iconName) {

    var desktop_display = SFSI("input[name='sfsi_plus_" + iconName + "_display']").prop("checked") ? "show" : "hide";
    var mobile_display = SFSI("input[name='sfsi_plus_" + iconName + "_mobiledisplay']").prop("checked") ? "show" : "hide";
    var diff = SFSI(".sfsi_premium_mobile_section").find('input[name=sfsi_plus_icons_onmobile]:checked').val();
    var display = desktop_display;
    if ('yes' === diff && display === 'hide') {
        display = mobile_display;
    }
    if ("facebook" == iconName) {
        sfsi_plus_section_Display("sfsiplus_fb_section", display);
    }
    sfsi_plus_section_Display("sfsiplus_" + iconName + "_section", display);
    var diffrent_mobile_order = SFSI('input[name=sfsi_plus_mobile_icons_order_setting]:checked').val();

    if (diff === 'yes' && diffrent_mobile_order == 'yes') {
        if ('show' == desktop_display) {
            SFSI('#sfsi_plus_' + ('facebook' === iconName ? 'fb' : iconName) + 'Icon_order.sfsiplus_' + ('facebook' === iconName ? 'fb' : iconName) + '_section').show();
        } else {
            SFSI('#sfsi_plus_' + ('facebook' === iconName ? 'fb' : iconName) + 'Icon_order.sfsiplus_' + ('facebook' === iconName ? 'fb' : iconName) + '_section').hide();
        }
        SFSI('.sfsiplus_' + ('facebook' === iconName ? 'fb' : iconName) + '_mobilesection.ui-sortable-handle').css('display', 'yes' == mobile_display ? 'block' : 'none');
    }
}

function sfsi_plus_toogle_icon_for_order_icons_update(iconName) {

    var desktop_display = SFSI("input[name='sfsi_plus_" + iconName + "_display']").prop("checked") ? "show" : "hide";
    var mobile_display = SFSI("input[name='sfsi_plus_" + iconName + "_mobiledisplay']").prop("checked") ? "show" : "hide";
    var desktop_displayed = SFSI(".desktop_icons_order .sfsiplus_" + iconName + "_section").css('display') !== "none";
    var mobile_displayed = SFSI(".mobile_icons_order .sfsiplus_" + iconName + "_mobilesection").css('display') !== "none";
    var diff = SFSI(".sfsi_premium_mobile_section").find('input[name=sfsi_plus_icons_onmobile]:checked').val();
    var diffrent_mobile_order = SFSI('input[name=sfsi_plus_mobile_icons_order_setting]:checked').val();

    var display = desktop_display;
    // if('yes'!==diff && display==='hide'){
    // 	display = mobile_display;
    // }
    if ("facebook" == iconName) {
        // sfsi_plus_section_Display("sfsiplus_fb_section", display);
        var desktop_displayed = SFSI(".desktop_icons_order .sfsiplus_fb_section");
        var mobile_displayed = SFSI(".mobile_icons_order .sfsiplus_fb_section");
        iconName = "fb";
    }

    /* if('no'==diffrent_mobile_order){
    	sfsi_plus_section_Display("desktop_icons_order .sfsiplus_"+iconName+"_section", desktop_display);
     	sfsi_plus_section_Display("mobile_icons_order .sfsiplus_"+iconName+"_section", desktop_display);

     }
     console.log(iconName,desktop_display,desktop_displayed,mobile_display,mobile_displayed,diff,diffrent_mobile_order); */
    if (diff == "no" && diffrent_mobile_order == 'no') {
        if ((desktop_display == "show" && !desktop_displayed)) {
            sfsi_plus_section_Display("desktop_icons_order .sfsiplus_" + iconName + "_section", desktop_display);
        } else if (desktop_display == "hide" && desktop_displayed) {
            sfsi_plus_section_Display("desktop_icons_order .sfsiplus_" + iconName + "_section", desktop_display);
        }
        if ((mobile_display == "show" && !mobile_displayed)) {
            sfsi_plus_section_Display("mobile_icons_order .sfsiplus_" + iconName + "_mobilesection", mobile_display);
        } else if (mobile_display == "hide" && mobile_displayed) {
            sfsi_plus_section_Display("mobile_icons_order .sfsiplus_" + iconName + "_mobilesection", mobile_display);
        }
    } else if (diff == "no" && diffrent_mobile_order == "yes") {
        if ((desktop_display == "show" && !desktop_displayed)) {
            sfsi_plus_section_Display("desktop_icons_order .sfsiplus_" + iconName + "_section", desktop_display);
        } else if (desktop_display == "hide" && desktop_displayed) {
            sfsi_plus_section_Display("desktop_icons_order .sfsiplus_" + iconName + "_section", desktop_display);
        }
        if ((desktop_display == "show" && !mobile_displayed)) {
            sfsi_plus_section_Display("mobile_icons_order .sfsiplus_" + iconName + "_mobilesection", mobile_display);
        } else if (desktop_display == "hide" && mobile_displayed) {
            sfsi_plus_section_Display("mobile_icons_order .sfsiplus_" + iconName + "_mobilesection", mobile_display);
        }
    } else if (diff == "yes" && diffrent_mobile_order == "no") {
        if ((desktop_display == "show" && !desktop_displayed)) {
            sfsi_plus_section_Display("desktop_icons_order .sfsiplus_" + iconName + "_section", desktop_display);
        }
        if ((mobile_display == "show" && !desktop_displayed)) {
            sfsi_plus_section_Display("desktop_icons_order .sfsiplus_" + iconName + "_section", mobile_display);
        } else if (mobile_display == "hide" && true == mobile_displayed) {
            sfsi_plus_section_Display("mobile_icons_order .sfsiplus_" + iconName + "_mobilesection", mobile_display);
        }
        if (desktop_display == "hide" && desktop_displayed && mobile_display == "hide") {
            sfsi_plus_section_Display("desktop_icons_order .sfsiplus_" + iconName + "_section", desktop_display);
        }
    } else if (diff == "yes" && diffrent_mobile_order == 'yes') {
        if ((desktop_display == "show" && !desktop_displayed)) {
            sfsi_plus_section_Display("desktop_icons_order .sfsiplus_" + iconName + "_section", desktop_display);
        } else if (desktop_display == "hide" && desktop_displayed) {
            sfsi_plus_section_Display("desktop_icons_order .sfsiplus_" + iconName + "_section", desktop_display);
        }
        if ((mobile_display == "show" && false == mobile_displayed)) {
            sfsi_plus_section_Display("mobile_icons_order .sfsiplus_" + iconName + "_mobilesection", mobile_display);
        } else if (mobile_display == "hide" && true == mobile_displayed) {
            sfsi_plus_section_Display("mobile_icons_order .sfsiplus_" + iconName + "_mobilesection", mobile_display);
        }
    }

    // sfsi_plus_section_Display("sfsiplus_"+iconName+"_section", display);

    // if(diff==='yes' && diffrent_mobile_order=='yes' ){
    // 	if('show'==desktop_display){
    // 		SFSI('#sfsi_plus_'+('facebook'===iconName?'fb':iconName)+'Icon_order.sfsiplus_'+('facebook'===iconName?'fb':iconName)+'_section').show();
    // 	}else{
    // 		SFSI('#sfsi_plus_'+('facebook'===iconName?'fb':iconName)+'Icon_order.sfsiplus_'+('facebook'===iconName?'fb':iconName)+'_section').hide();
    // 	}
    // 	SFSI('.sfsiplus_'+('facebook'===iconName?'fb':iconName)+'_mobilesection.ui-sortable-handle').css('display','yes'==mobile_display?'block':'none');
    // }
}

function sfsi_premium_toggle_mobile_icons_for_order(show) {
    var diff = SFSI(".sfsi_premium_mobile_section").find('input[name=sfsi_plus_icons_onmobile]:checked').val();

    var icons = jQuery('.plus_share_icon_mobile_order').find('li')
        .filter(function (index, child) {
            return jQuery(child).css('display') == 'block'
        })
        .map(function (index, child) {
            return child.id
        })
        .each(function (index, id) {
            /* console.log(index,id); */
            if (diff == "yes") {
                var name_to_check = id.replace('Icon_order', '_display');
                if (name_to_check == "sfsi_plus_fb_display") {
                    name_to_check = "sfsi_plus_facebook_display";
                }
                var inDesktopSelection = SFSI('input[name="' + name_to_check + '"]:checked').val() == "yes";
                /* console.log('name',name_to_check,inDesktopSelection); */
                if (show) {
                    if (!inDesktopSelection) {
                        jQuery('.plus_share_icon_order #' + id).show();
                    }
                } else {
                    if (!inDesktopSelection) {
                        jQuery('.plus_share_icon_order #' + id).hide();
                    }
                }
            }
        });
    /* console.log(icons,show); */
}

function sfsi_plus_toogle_mobile_icon_for_order_icons(iconName) {
    var display = SFSI("input[name='sfsi_plus_" + iconName + "_mobiledisplay']").prop("checked") ? "show" : "hide";
    var diff = SFSI(".sfsi_premium_mobile_section").find('input[name=sfsi_plus_icons_onmobile]:checked').val();

    if ("no" == diff) {
        display = SFSI("input[name='sfsi_plus_" + iconName + "_display']").prop("checked") ? "show" : "hide";
    }

    if ("facebook" == iconName) {
        sfsi_plus_section_Display("sfsiplus_fb_mobilesection", display);
    }

    sfsi_plus_section_Display("sfsiplus_" + iconName + "_mobilesection", display);
}

function sfsi_plus_toogle_desktop_icon_for_mouserover_icons(iconName) {

    var display = SFSI("input[name='sfsi_plus_" + iconName + "_display']").prop("checked") ? true : false;

    var elem = SFSI(".other_icons_effects_options_container").find('div[data-iconname=' + iconName + ']');

    if (display) {
        elem.removeClass('hide').addClass('show');
    } else {
        elem.removeClass('show').addClass('hide');
    }
}

function sfsi_shallDisplayIcon(iconName, isDesktop = 1) {

    var isDesktop = "undefined" !== isDesktop && null !== isDesktop ? isDesktop : 1;

    if (1 == isDesktop) {
        return SFSI("input[name='sfsi_plus_" + iconName + "_display']").prop("checked");
    } else {
        return SFSI("input[name='sfsi_plus_" + iconName + "_mobiledisplay']").prop("checked");
    }
}

// Upload Custom Skin
function plus_sfsi_customskin_upload(s, ref, upld) {
    var ttl = jQuery(ref).attr("title");
    var nonce = upld;

    var i = s,
        e = {
            action: "plus_UploadSkins",
            custom_imgurl: i,
            nonce: nonce,
        };
    SFSI.ajax({
        url: sfsi_premium_ajax_object.ajax_url,
        type: "post",
        data: e,
        success: function (msg) {
            if (msg.res = "success") {
                var arr = s.split('=');
                jQuery(ref).prev('.imgskin').attr('src', arr[1]);
                jQuery(ref).prev('.imgskin').css("display", "block");
                jQuery(ref).text("Update");
                jQuery(ref).next('.dlt_btn').css("display", "block");
            }
        }
    });
}

// Delete Custom Skin
function sfsiplus_deleteskin_icon(s, d) {
    var iconname = jQuery(s).attr("title");
    var nonce = d;
    var i = iconname,
        e = {
            action: "plus_DeleteSkin",
            iconname: i,
            nonce: nonce,
        };

    SFSI.ajax({
        url: sfsi_premium_ajax_object.ajax_url,
        type: "post",
        data: e,
        dataType: "json",
        success: function (msg) {
            if (msg.res === "success") {
                SFSI(s).prev("a").text("Upload");
                SFSI(s).prev("a").prev("img").attr("src", '');
                SFSI(s).prev("a").prev("img").css("display", "none");
                SFSI(s).css("display", "none");
            } else {
                alert("Whoops! something went wrong.")
            }
        }
    });
}

// Save Custom Skin
function SFSI_plus_done() {
    var nonce = SFSI('input[name="sfsi_plus_Iamdone_nonce"]').val();

    e = {
        action: "plus_Iamdone",
        nonce: nonce,
    };

    SFSI.ajax({
        url: sfsi_premium_ajax_object.ajax_url,
        type: "post",
        data: e,
        success: function (msg) {
            /* alert(msg); */
            SFSI("li.cstomskins_upload").children(".sfsiplus_icns_tab_3").html(msg);

            arrIcons.forEach(function (iconName, index) {
                sfsi_plus_toogle_desktop_icon_for_order_icons(iconName);
                sfsi_plus_toogle_mobile_icon_for_order_icons(iconName);
            });

            SFSI("input[element-type='sfsiplus-cusotm-icon']").prop("checked") ?
                sfsi_plus_section_Display("sfsiplus_custom_section", "show") : sfsi_plus_section_Display("sfsiplus_custom_section", "hide");
            SFSI(".cstmskins-overlay").hide("slow");
            sfsi_plus_update_step3() && sfsipluscollapse(this);
        }
    });
}

// Upload Custom Icons
function plus_sfsi_newcustomicon_upload(s) {
    var i = s;
    var nonce = SFSI('input[name="sfsi_plus_UploadIcons_nonce"]').val();
    e = {
        action: "plus_UploadIcons",
        custom_imgurl: i,
        nonce: nonce,
    };
    SFSI.ajax({
        url: sfsi_premium_ajax_object.ajax_url,
        type: "post",
        data: e,
        dataType: "json",
        async: !0,
        success: function (s) {
            if (s.res == 'success') {
                sfsiplus_afterIconSuccess(s);
            } else {
                SFSI(".upload-overlay").hide("slow");
                SFSI(".uperror").html("");
                console.log(s.res, s.res == "jpgError");
                if (s.res == "jpgError") {
                    SFSI("#plus_c0 .checkbox").css("background-position", "0px 0px")
                    console.log(SFSI('#plus_c0 .checkbox'), SFSI('#plus_c0 .checkbox').prop('checked', false));
                    sfsiplus_showErrorSuc("error", "Please upload an icon in .png format. You can convert images <a style='display: inline;text-decoration: underline;color:inherit !important' href='https://image.online-convert.com/convert-to-png' target='_blank'>here</a>.", 1)
                } else {
                    sfsiplus_showErrorSuc("error", "Some Error Occured During Upload Custom Icon", 1)

                }
            }
        }
    });
}

// Delete Custom Icons
function sfsi_plus_delete_CusIcon(inptElem, i) {
    sfsiplus_beForeLoad();
    var nonce = SFSI(".deleteCustomIcon").attr("data-nonce");
    var e = {
        action: "plus_deleteIcons",
        icon_name: i.attr("name"),
        nonce: nonce,
    };
    /* console.log(e); */

    SFSI.ajax({
        url: sfsi_premium_ajax_object.ajax_url,
        type: "post",
        data: e,
        dataType: "json",
        success: function (e) {

            if ("success" == e.res) {

                sfsiplus_showErrorSuc("success", "Saved !", 1);

                var t = parseInt(e.last_index) + 1;
                var attr = i.attr("name");

                var arrStr = attr.split("_");
                var deleteIndex = parseInt(arrStr[1]);


                attr = attr.replace('plus', '');
                SFSI("#plus_total_cusotm_icons").val(e.total_up);

                // Remove icon from Question 1 -> Desktop icons
                SFSI("ul.plus_icn_listing").find("input[name=" + i.attr("name") + "]").closest(".plus_custom").remove();
                SFSI("ul.plus_icn_listing li.plus_custom:last-child").addClass("bdr_btm_non"),

                    // Remove icon from Question 1 -> Mobile icons
                    SFSI(".sfsi_premium_mobile_icon_listing").find("input[name=" + i.attr("name") + "]").closest(".plus_custom").remove();

                // Remove icon from Question 2
                SFSI(".plus_custom-links").find("div." + attr).remove();

                // Remove icon from Question 4
                SFSI(".other_icons_effects_options_container").find("div.col-md-3[data-customIconIndex=" + deleteIndex + "]").remove();

                // Remove icon from Question 6
                SFSI(".plus_custom_m").find("div." + attr).remove(),
                    SFSI("ul.dbMobileOrder").find("li.sfsiplus_custom_mobilesection[element-id='" + deleteIndex + "']").remove();

                SFSI(".plus_share_icon_order").children("li." + attr).remove(),
                    SFSI(".plus_share_icon_mobile_order").children("li." + attr).remove(),

                    // Remove icon from Question 7
                    SFSI("ul.plus_sfsi_sample_icons").children("li." + attr).remove();

                var n = e.total_up + 1;
                9 == e.total_up && SFSI(".plus_icn_listing").append('<li id="plus_c' + t + '" class="plus_custom bdr_btm_non"><div class="radio_section tb_4_ck"><span class="checkbox" dynamic_ele="yes" style="background-position: 0px 0px;"></span><input name="plussfsiICON_' + t + '_display"  type="checkbox" value="yes" class="styled" style="display:none;" element-type="sfsiplus-cusotm-icon" isNew="yes" /></div> <span class="plus_custom-img"><img src="' + SFSI("#plugin_url").val() + 'images/custom.png" id="plus_CImg_' + t + '"  /> </span> <span class="custom sfsiplus_custom-txt">Custom' + n + ' </span> <div class="sfsiplus_right_info"> <p><span>' + object_name1.It_depends + ':</span> ' + object_name1.Upload_a + '</p><div class="inputWrapper"></div></li>');
            } else {
                sfsiplus_showErrorSuc("error", "Unkown error , please try again", 1);
            }

            return sfsi_plus_update_index(),
                plus_update_Sec5Iconorder(),
                plus_update_Sec5IconMobileorder(),
                //sfsi_plus_update_step1(),
                sfsi_plus_update_step5(),
                sfsiplus_afterLoad(), "suc";
        }
    });
}

function plus_sfsi_upload_mouseovericon(objIconData, btnElem) {

    'use strict';

    var parentElem = btnElem.parent(),
        iconImgElem = parentElem.find(".mouseover_other_icon_img"),
        revertLinkElem = parentElem.find(".mouseover_other_icon_revert_link"),
        inptElem = parentElem.find("input[name^=mouseover_other_icon_][type=hidden]"),
        iconName = btnElem.attr('data-iconname'),
        customIconIndex = btnElem.attr('data-customIconIndex');
    var nonce = SFSI(".js_mouseover_upload").attr("data-nonce");
    var dataToSend = {
        action: "plus_MouseOverIcons",
        iconName: iconName,
        custom_imgurl: objIconData.imgUrl,
        customIconIndex: customIconIndex,
        nonce: nonce,
    };

    SFSI.ajax({
        url: sfsi_premium_ajax_object.ajax_url,
        type: "post",
        data: dataToSend,
        dataType: "json",
        async: !0,
        beforeSend: function () {
            SfsiLoader.mousoverLoader.show();
        },
        success: function (resp) {

            if (true === Boolean(resp.status)) {
                revertLinkElem.removeClass("hide").addClass("show"),
                    iconImgElem.attr('src', resp.url),
                    inptElem.val(resp.url),
                    SfsiLoader.mousoverLoader.hide();
            } else {
                SfsiLoader.mousoverLoader.hide();
            }
        },
        error: function (resp) {
            SfsiLoader.mousoverLoader.hide();
        }
    });
}

function sfsi_plus_delete_mouserOverIcon(btnElem, iconName) {

    var parentElem = btnElem.parent(),
        iconImgElem = parentElem.find(".mouseover_other_icon_img"),
        revertLinkElem = parentElem.find(".mouseover_other_icon_revert_link"),
        inptElem = parentElem.find("input[name^=mouseover_other_icon_][type=hidden]"),
        customIconIndex = btnElem.attr('data-customIconIndex'),
        defaultImage = iconImgElem.attr('data-defaultImg');
    var nonce = SFSI(".js_mouseover_revert").attr("data-nonce");

    var e = {
        action: "sfsi_premium_deleteIcons",
        icon_name: iconName,
        customIconIndex: customIconIndex,
        serviceType: "mouseover",
        nonce: nonce,
    };

    SFSI.ajax({
        url: sfsi_premium_ajax_object.ajax_url,
        type: "post",
        data: e,
        dataType: "json",
        async: !0,
        beforeSend: function () {
            SfsiLoader.mousoverLoader.show();
        },
        complete: function () {
            SfsiLoader.mousoverLoader.hide();
        },
        success: function (e) {

            if (true === Boolean(e.status)) {

                btnElem.removeClass("show").addClass("hide");
                iconImgElem.attr("src", defaultImage);
                inptElem.val("");
            }
        }

    });
}

function sfsi_plus_show_counts() {
    "yes" == SFSI("input[name='sfsi_plus_display_counts']:checked").val() ? (SFSI(".sfsiplus_count_sections").slideDown(),
        sfsi_plus_showPreviewCounts()) : (SFSI(".sfsiplus_count_sections").slideUp(), sfsi_plus_showPreviewCounts());
}

function sfsi_plus_showPreviewCounts() {
    var s = 0;
    1 == SFSI("input[name='sfsi_plus_rss_countsDisplay']").prop("checked") ? (SFSI("#sfsi_plus_rss_countsDisplay").css("opacity", 1),
        s = 1) : SFSI("#sfsi_plus_rss_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_plus_email_countsDisplay']").prop("checked") ? (SFSI("#sfsi_plus_email_countsDisplay").css("opacity", 1),
        s = 1) : SFSI("#sfsi_plus_email_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_plus_facebook_countsDisplay']").prop("checked") ? (SFSI("#sfsi_plus_facebook_countsDisplay").css("opacity", 1),
        s = 1) : SFSI("#sfsi_plus_facebook_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_plus_twitter_countsDisplay']").prop("checked") ? (SFSI("#sfsi_plus_twitter_countsDisplay").css("opacity", 1),
        s = 1) : SFSI("#sfsi_plus_google_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_plus_linkedIn_countsDisplay']").prop("checked") ? (SFSI("#sfsi_plus_linkedIn_countsDisplay").css("opacity", 1),
        s = 1) : SFSI("#sfsi_plus_linkedIn_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_plus_youtube_countsDisplay']").prop("checked") ? (SFSI("#sfsi_plus_youtube_countsDisplay").css("opacity", 1),
        s = 1) : SFSI("#sfsi_plus_youtube_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_plus_pinterest_countsDisplay']").prop("checked") ? (SFSI("#sfsi_plus_pinterest_countsDisplay").css("opacity", 1),
        s = 1) : SFSI("#sfsi_plus_pinterest_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_plus_shares_countsDisplay']").prop("checked") ? (SFSI("#sfsi_plus_shares_countsDisplay").css("opacity", 1),
        s = 1) : SFSI("#sfsi_plus_shares_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_plus_instagram_countsDisplay']").prop("checked") ? (SFSI("#sfsi_plus_instagram_countsDisplay").css("opacity", 1),
        s = 1) : SFSI("#sfsi_plus_instagram_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_plus_houzz_countsDisplay']").prop("checked") ? (SFSI("#sfsi_plus_houzz_countsDisplay").css("opacity", 1),
        s = 1) : SFSI("#sfsi_plus_houzz_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_plus_snapchat_countsDisplay']").prop("checked") ? (SFSI("#sfsi_plus_snapchat_countsDisplay").css("opacity", 1),
        s = 1) : SFSI("#sfsi_plus_snapchat_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_plus_whatsapp_countsDisplay']").prop("checked") ? (SFSI("#sfsi_plus_whatsapp_countsDisplay").css("opacity", 1),
        s = 1) : SFSI("#sfsi_plus_whatsapp_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_plus_skype_countsDisplay']").prop("checked") ? (SFSI("#sfsi_plus_skype_countsDisplay").css("opacity", 1),
        s = 1) : SFSI("#sfsi_plus_skype_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_plus_vimeo_countsDisplay']").prop("checked") ? (SFSI("#sfsi_plus_vimeo_countsDisplay").css("opacity", 1),
        s = 1) : SFSI("#sfsi_plus_vimeo_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_plus_soundcloud_countsDisplay']").prop("checked") ? (SFSI("#sfsi_plus_soundcloud_countsDisplay").css("opacity", 1),
        s = 1) : SFSI("#sfsi_plus_soundcloud_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_plus_yummly_countsDisplay']").prop("checked") ? (SFSI("#sfsi_plus_yummly_countsDisplay").css("opacity", 1),
        s = 1) : SFSI("#sfsi_plus_yummly_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_plus_flickr_countsDisplay']").prop("checked") ? (SFSI("#sfsi_plus_flickr_countsDisplay").css("opacity", 1),
        s = 1) : SFSI("#sfsi_plus_flickr_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_plus_reddit_countsDisplay']").prop("checked") ? (SFSI("#sfsi_plus_reddit_countsDisplay").css("opacity", 1),
        s = 1) : SFSI("#sfsi_plus_reddit_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_plus_tumblr_countsDisplay']").prop("checked") ? (SFSI("#sfsi_plus_tumblr_countsDisplay").css("opacity", 1),
        s = 1) : SFSI("#sfsi_plus_tumblr_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_plus_ria_countsDisplay']").prop("checked") ? (SFSI("#sfsi_plus_ria_countsDisplay").css("opacity", 1),
        s = 1) : SFSI("#sfsi_plus_ria_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_plus_inha_countsDisplay']").prop("checked") ? (SFSI("#sfsi_plus_inha_countsDisplay").css("opacity", 1),
        s = 1) : SFSI("#sfsi_plus_inha_countsDisplay").css("opacity", 0), 0 == s || "no" == SFSI("input[name='sfsi_plus_display_counts']:checked").val() ? SFSI(".sfsi_Cdisplay").hide() : SFSI(".sfsi_Cdisplay").show();
}

function sfsi_plus_show_OnpostsDisplay() {
    //"yes" == SFSI("input[name='sfsi_plus_show_Onposts']:checked").val() ? SFSI(".sfsiplus_PostsSettings_section").slideDown() :SFSI(".sfsiplus_PostsSettings_section").slideUp();
}


function sfsi_plus_update_step1() {
    var nonce = SFSI("#sfsi_plus_save1").attr("data-nonce");
    global_error = 0, sfsiplus_beForeLoad(), sfsi_plus_depened_sections();

    var snapchat = SFSI("input[name='sfsi_plus_snapchat_display']:checked").val(),
        whatsapp = SFSI("input[name='sfsi_plus_whatsapp_display']:checked").val(),
        phone = SFSI("input[name='sfsi_plus_phone_display']:checked").val(),
        skype = SFSI("input[name='sfsi_plus_skype_display']:checked").val(),
        vimeo = SFSI("input[name='sfsi_plus_vimeo_display']:checked").val(),
        soundcloud = SFSI("input[name='sfsi_plus_soundcloud_display']:checked").val(),
        yummly = SFSI("input[name='sfsi_plus_yummly_display']:checked").val(),
        flickr = SFSI("input[name='sfsi_plus_flickr_display']:checked").val(),
        reddit = SFSI("input[name='sfsi_plus_reddit_display']:checked").val(),
        tumblr = SFSI("input[name='sfsi_plus_tumblr_display']:checked").val();


    var s = !1,
        i = SFSI("input[name='sfsi_plus_rss_display']:checked").val(),
        e = SFSI("input[name='sfsi_plus_email_display']:checked").val(),
        t = SFSI("input[name='sfsi_plus_facebook_display']:checked").val(),
        n = SFSI("input[name='sfsi_plus_twitter_display']:checked").val(),
        a = SFSI("input[name='sfsi_plus_share_display']:checked").val(),
        r = SFSI("input[name='sfsi_plus_youtube_display']:checked").val(),
        c = SFSI("input[name='sfsi_plus_pinterest_display']:checked").val(),
        p = SFSI("input[name='sfsi_plus_linkedin_display']:checked").val(),
        _ = SFSI("input[name='sfsi_plus_instagram_display']:checked").val(),
        threads = SFSI("input[name='sfsi_plus_threads_display']:checked").val(),
        bluesky = SFSI("input[name='sfsi_plus_bluesky_display']:checked").val(),
        houzz = SFSI("input[name='sfsi_plus_houzz_display']:checked").val(),

        _fbmsg = SFSI("input[name='sfsi_plus_fbmessenger_display']:checked").val(),
        _mix = SFSI("input[name='sfsi_plus_mix_display']:checked").val(),
        _ok = SFSI("input[name='sfsi_plus_ok_display']:checked").val(),
        _telegram = SFSI("input[name='sfsi_plus_telegram_display']:checked").val(),
        _vk = SFSI("input[name='sfsi_plus_vk_display']:checked").val(),
        _weibo = SFSI("input[name='sfsi_plus_weibo_display']:checked").val(),
        _wechat = SFSI("input[name='sfsi_plus_wechat_display']:checked").val(),
        _xing = SFSI("input[name='sfsi_plus_xing_display']:checked").val(),
        _gab = SFSI("input[name='sfsi_plus_gab_display']:checked").val(),
        _copylink = SFSI("input[name='sfsi_plus_copylink_display']:checked").val(),
        _mastodon = SFSI("input[name='sfsi_plus_mastodon_display']:checked").val(),
        _ria = SFSI("input[name='sfsi_plus_ria_display']:checked").val(),
        _inha = SFSI("input[name='sfsi_plus_inha_display']:checked").val(),

        iconsm = SFSI("input[name='sfsi_plus_icons_onmobile']:checked").val(),

        mobilei = SFSI("input[name='sfsi_plus_rss_mobiledisplay']:checked").val(),
        mobilee = SFSI("input[name='sfsi_plus_email_mobiledisplay']:checked").val(),
        mobilet = SFSI("input[name='sfsi_plus_facebook_mobiledisplay']:checked").val(),
        mobilen = SFSI("input[name='sfsi_plus_twitter_mobiledisplay']:checked").val(),
        mobilea = SFSI("input[name='sfsi_plus_share_mobiledisplay']:checked").val(),
        mobiler = SFSI("input[name='sfsi_plus_youtube_mobiledisplay']:checked").val(),
        mobilec = SFSI("input[name='sfsi_plus_pinterest_mobiledisplay']:checked").val(),
        mobilep = SFSI("input[name='sfsi_plus_linkedin_mobiledisplay']:checked").val(),
        mobile_ = SFSI("input[name='sfsi_plus_instagram_mobiledisplay']:checked").val(),
        mobilethreads = SFSI("input[name='sfsi_plus_threads_mobiledisplay']:checked").val(),
        mobilebluesky = SFSI("input[name='sfsi_plus_bluesky_mobiledisplay']:checked").val(),
        mobilehouzz = SFSI("input[name='sfsi_plus_houzz_mobiledisplay']:checked").val(),
        mobilesnapchat = SFSI("input[name='sfsi_plus_snapchat_mobiledisplay']:checked").val(),
        mobilewhatsapp = SFSI("input[name='sfsi_plus_whatsapp_mobiledisplay']:checked").val(),
        mobilephone = SFSI("input[name='sfsi_plus_phone_mobiledisplay']:checked").val(),
        mobileskype = SFSI("input[name='sfsi_plus_skype_mobiledisplay']:checked").val(),
        mobilevimeo = SFSI("input[name='sfsi_plus_vimeo_mobiledisplay']:checked").val(),
        mobilesoundcloud = SFSI("input[name='sfsi_plus_soundcloud_mobiledisplay']:checked").val(),
        mobileyummly = SFSI("input[name='sfsi_plus_yummly_mobiledisplay']:checked").val(),
        mobileflickr = SFSI("input[name='sfsi_plus_flickr_mobiledisplay']:checked").val(),
        mobilereddit = SFSI("input[name='sfsi_plus_reddit_mobiledisplay']:checked").val(),
        mobiletumblr = SFSI("input[name='sfsi_plus_tumblr_mobiledisplay']:checked").val(),

        _mobile_fbmsg = SFSI("input[name='sfsi_plus_fbmessenger_mobiledisplay']:checked").val(),
        _mobile_mix = SFSI("input[name='sfsi_plus_mix_mobiledisplay']:checked").val(),
        _mobile_ok = SFSI("input[name='sfsi_plus_ok_mobiledisplay']:checked").val(),
        _mobile_telegram = SFSI("input[name='sfsi_plus_telegram_mobiledisplay']:checked").val(),
        _mobile_vk = SFSI("input[name='sfsi_plus_vk_mobiledisplay']:checked").val(),
        _mobile_weibo = SFSI("input[name='sfsi_plus_weibo_mobiledisplay']:checked").val(),
        _mobile_wechat = SFSI("input[name='sfsi_plus_wechat_mobiledisplay']:checked").val(),
        _mobile_xing = SFSI("input[name='sfsi_plus_xing_mobiledisplay']:checked").val();
    _mobile_gab = SFSI("input[name='sfsi_plus_gab_mobiledisplay']:checked").val();
    _mobile_copylink = SFSI("input[name='sfsi_plus_copylink_mobiledisplay']:checked").val();
    _mobile_mastodon = SFSI("input[name='sfsi_plus_mastodon_mobiledisplay']:checked").val();
    _mobile_ria = SFSI("input[name='sfsi_plus_ria_mobiledisplay']:checked").val();
    _mobile_inha = SFSI("input[name='sfsi_plus_inha_mobiledisplay']:checked").val();

    var desktopIconsObjects = SFSI(".plus_icn_listing li.plus_custom input:checked").parent().next().children();
    var sfsi_custom_desktop_icons = [];

    desktopIconsObjects.each(function () {
        // IconImage
        var imgPath = SFSI(this).attr("src");

        // Index
        var index = SFSI(this).attr("data-key");

        sfsi_custom_desktop_icons[index] = imgPath;

    });

    var mobileIconsObjects = SFSI(".sfsi_premium_mobile_icon_listing li.plus_custom input:checked").parent().next().children();
    var sfsi_custom_mobile_icons = [];

    mobileIconsObjects.each(function () {
        // IconImage
        var imgPath = SFSI(this).attr("src");

        // Index
        var index = SFSI(this).attr("data-key");
        sfsi_custom_mobile_icons[index] = imgPath;

    });

    I = {
        action: "plus_updateSrcn1",
        sfsi_plus_rss_display: i,
        sfsi_plus_email_display: e,
        sfsi_plus_facebook_display: t,
        sfsi_plus_twitter_display: n,
        sfsi_plus_share_display: a,
        sfsi_plus_youtube_display: r,
        sfsi_plus_pinterest_display: c,
        sfsi_plus_linkedin_display: p,
        sfsi_plus_instagram_display: _,
        sfsi_plus_threads_display: threads,
        sfsi_plus_bluesky_display: bluesky,
        sfsi_plus_houzz_display: houzz,
        sfsi_plus_snapchat_display: snapchat,
        sfsi_plus_whatsapp_display: whatsapp,
        sfsi_plus_phone_display: phone,
        sfsi_plus_skype_display: skype,
        sfsi_plus_vimeo_display: vimeo,
        sfsi_plus_soundcloud_display: soundcloud,
        sfsi_plus_yummly_display: yummly,
        sfsi_plus_flickr_display: flickr,
        sfsi_plus_reddit_display: reddit,
        sfsi_plus_tumblr_display: tumblr,

        sfsi_plus_fbmessenger_display: _fbmsg,
        sfsi_plus_mix_display: _mix,
        sfsi_plus_ok_display: _ok,
        sfsi_plus_telegram_display: _telegram,
        sfsi_plus_vk_display: _vk,
        sfsi_plus_weibo_display: _weibo,
        sfsi_plus_wechat_display: _wechat,
        sfsi_plus_xing_display: _xing,
        sfsi_plus_gab_display: _gab,
        sfsi_plus_copylink_display: _copylink,
        sfsi_plus_mastodon_display: _mastodon,
        sfsi_plus_ria_display: _ria,
        sfsi_plus_inha_display: _inha,

        sfsi_plus_icons_onmobile: iconsm,
        sfsi_plus_rss_mobiledisplay: mobilei,
        sfsi_plus_email_mobiledisplay: mobilee,
        sfsi_plus_facebook_mobiledisplay: mobilet,
        sfsi_plus_twitter_mobiledisplay: mobilen,
        sfsi_plus_share_mobiledisplay: mobilea,
        sfsi_plus_youtube_mobiledisplay: mobiler,
        sfsi_plus_pinterest_mobiledisplay: mobilec,
        sfsi_plus_linkedin_mobiledisplay: mobilep,
        sfsi_plus_instagram_mobiledisplay: mobile_,
        sfsi_plus_threads_mobiledisplay: mobilethreads,
        sfsi_plus_bluesky_mobiledisplay: mobilebluesky,
        sfsi_plus_houzz_mobiledisplay: mobilehouzz,
        sfsi_plus_snapchat_mobiledisplay: mobilesnapchat,
        sfsi_plus_whatsapp_mobiledisplay: mobilewhatsapp,
        sfsi_plus_phone_mobiledisplay: mobilephone,
        sfsi_plus_skype_mobiledisplay: mobileskype,
        sfsi_plus_vimeo_mobiledisplay: mobilevimeo,
        sfsi_plus_soundcloud_mobiledisplay: mobilesoundcloud,
        sfsi_plus_yummly_mobiledisplay: mobileyummly,
        sfsi_plus_flickr_mobiledisplay: mobileflickr,
        sfsi_plus_reddit_mobiledisplay: mobilereddit,
        sfsi_plus_tumblr_mobiledisplay: mobiletumblr,

        sfsi_plus_fbmessenger_mobiledisplay: _mobile_fbmsg,
        sfsi_plus_mix_mobiledisplay: _mobile_mix,
        sfsi_plus_ok_mobiledisplay: _mobile_ok,
        sfsi_plus_telegram_mobiledisplay: _mobile_telegram,
        sfsi_plus_vk_mobiledisplay: _mobile_vk,
        sfsi_plus_weibo_mobiledisplay: _mobile_weibo,
        sfsi_plus_wechat_mobiledisplay: _mobile_wechat,
        sfsi_plus_xing_mobiledisplay: _mobile_xing,
        sfsi_plus_gab_mobiledisplay: _mobile_gab,
        sfsi_plus_copylink_mobiledisplay: _mobile_copylink,
        sfsi_plus_mastodon_mobiledisplay: _mobile_mastodon,
        sfsi_plus_ria_mobiledisplay: _mobile_ria,
        sfsi_plus_inha_mobiledisplay: _mobile_inha,

        sfsi_custom_desktop_icons: sfsi_custom_desktop_icons,
        sfsi_custom_mobile_icons: sfsi_custom_mobile_icons,
        nonce: nonce
    };
    SFSI.ajax({
        url: sfsi_premium_ajax_object.ajax_url,
        type: "post",
        data: I,
        async: !0,
        dataType: "json",
        timeout: 6000,
        success: function (i) {
            if (i == "wrong_nonce") {
                sfsiplus_showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 1);
                s = !1;
                sfsiplus_afterLoad();
            } else {
                "success" == i ?
                    (
                        sfsiplus_showErrorSuc("success", "Saved !", 1),
                            sfsipluscollapse("#sfsi_plus_save1"),
                            sfsi_plus_make_popBox()
                    ) :
                    (
                        global_error = 1,
                            sfsiplus_showErrorSuc("error", "Your settings could not be saved. Please see <a href='https://www.ultimatelysocial.com/saving-settings-is-not-working/' style='color:green;font-weight: bold;'>here</a>  what could be the possible issue", 1),
                            s = !1
                    ),
                    sfsiplus_afterLoad();
                sfsi_plus_depened_sections();
            }
        },
        error: function (jqXHR, textStatus) {
            if (textStatus === 'timeout') {
                global_error = 1,
                    sfsiplus_showErrorSuc("error", "Your settings could not be saved. Please see <a href='https://www.ultimatelysocial.com/saving-settings-is-not-working/' style='color:green;font-weight: bold;'>here</a>  what could be the possible issue", 1)
            }
            sfsiplus_afterLoad();
            //console.log(textStatus,jqXHR);
        }
    });
}

function sfsi_plus_update_step2() {
    var nonce = SFSI("#sfsi_plus_save2").attr("data-nonce");
    var s = sfsi_plus_validationStep2();
    if (!s) return global_error = 1, !1;
    sfsiplus_beForeLoad();

    var i = 1 == SFSI("input[name='sfsi_plus_rss_url']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_rss_url']").val(),
        e = 1 == SFSI("input[name='sfsi_plus_rss_icons']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_rss_icons']:checked").val(),
        t = 1 == SFSI("input[name='sfsi_plus_facebookPage_option']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_facebookPage_option']:checked").val(),
        n = 1 == SFSI("input[name='sfsi_plus_facebookLike_option']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_facebookLike_option']:checked").val(),
        o = 1 == SFSI("input[name='sfsi_plus_facebookShare_option']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_facebookShare_option']:checked").val(),
        a = SFSI("input[name='sfsi_plus_facebookPage_url']").val(),
        r = 1 == SFSI("input[name='sfsi_plus_twitter_followme']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_twitter_followme']:checked").val(),
        c = 1 == SFSI("input[name='sfsi_plus_twitter_followUserName']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_twitter_followUserName']").val(),
        p = 1 == SFSI("input[name='sfsi_plus_twitter_aboutPage']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_twitter_aboutPage']:checked").val(),
        _ = 1 == SFSI("input[name='sfsi_plus_twitter_page']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_twitter_page']:checked").val(),
        l = SFSI("input[name='sfsi_plus_twitter_pageURL']").val(),
        S = SFSI("textarea[name='sfsi_plus_twitter_aboutPageText']").val(),

        trdsShare = 1 == SFSI("input[name='sfsi_plus_threadsShare_option']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_threadsShare_option']:checked").val(),
        bskyShare = 1 == SFSI("input[name='sfsi_plus_blueskyShare_option']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_blueskyShare_option']:checked").val(),


        m = 1 == SFSI("input[name='sfsi_plus_youtube_page']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_youtube_page']:checked").val(),
        F = 1 == SFSI("input[name='sfsi_plus_youtube_pageUrl']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_youtube_pageUrl']").val(),
        h = 1 == SFSI("input[name='sfsi_plus_youtube_follow']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_youtube_follow']:checked").val(),
        cls = SFSI("input[name='sfsi_plus_ytube_chnlid']").val() != '' ? "id" : "",
        vchid = SFSI("input[name='sfsi_plus_ytube_chnlid']").val(),
        g = 1 == SFSI("input[name='sfsi_plus_pinterest_page']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_pinterest_page']:checked").val(),
        k = 1 == SFSI("input[name='sfsi_plus_pinterest_pageUrl']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_pinterest_pageUrl']").val(),
        y = 1 == SFSI("input[name='sfsi_plus_pinterest_pingBlog']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_pinterest_pingBlog']:checked").val(),
        b = 1 == SFSI("input[name='sfsi_plus_instagram_pageUrl']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_instagram_pageUrl']").val(),
        ria = 1 == SFSI("input[name='sfsi_plus_ria_pageUrl']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_ria_pageUrl']").val(),
        inha = 1 == SFSI("input[name='sfsi_plus_inha_pageUrl']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_inha_pageUrl']").val(),

        houzz = SFSI("input[name='sfsi_plus_houzz_pageUrl']").val(),

        w = 1 == SFSI("input[name='sfsi_plus_linkedin_page']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_linkedin_page']:checked").val(),
        x = 1 == SFSI("input[name='sfsi_plus_linkedin_pageURL']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_linkedin_pageURL']").val(),
        C = 1 == SFSI("input[name='sfsi_plus_linkedin_follow']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_linkedin_follow']:checked").val(),
        D = SFSI("input[name='sfsi_plus_linkedin_followCompany']").val(),
        U = 1 == SFSI("input[name='sfsi_plus_linkedin_SharePage']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_linkedin_SharePage']:checked").val(),
        O = SFSI("input[name='sfsi_plus_linkedin_recommendBusines']:checked").val(),
        T = SFSI("input[name='sfsi_plus_linkedin_recommendProductId']").val(),
        j = SFSI("input[name='sfsi_plus_linkedin_recommendCompany']").val();

    var P = {};

    SFSI("input[name='sfsi_plus_CustomIcon_links[]']").each(function () {
        P[SFSI(this).attr("file-id")] = this.value;
    });

    var snapchat = SFSI("input[name='sfsi_plus_snapchat_pageUrl']").val(),
        whatsappTyp = SFSI("input[name='sfsi_plus_whatsapp_url_type']:checked").val(),
        whatsappMsg = SFSI("input[name='sfsi_plus_whatsapp_message']").val(),
        whatsappMynum = SFSI("input[name='sfsi_plus_my_whatsapp_number']").val(),
        whatsappNum = SFSI("input[name='sfsi_plus_whatsapp_number']").val(),
        whatsappshrpg = SFSI("textarea[name='sfsi_plus_whatsapp_share_page']").val(),
        skype = SFSI("input[name='sfsi_plus_skype_pageUrl']").val(),
        vimeo = SFSI("input[name='sfsi_plus_vimeo_pageUrl']").val(),
        soundcloud = SFSI("input[name='sfsi_plus_soundcloud_pageUrl']").val(),

        //houzzVisitOption   = SFSI("input[name='sfsi_plus_houzzVisit_option']:checked").val(),
        // houzzShare_option  = SFSI("input[name='sfsi_plus_houzzShare_option']:checked").val(),
        // houzz_websiteId    = SFSI("input[name='sfsi_plus_houzz_websiteId']").val(),

        yummlyVisitOption = SFSI("input[name='sfsi_plus_yummlyVisit_option']:checked").val(),
        yummlyShare_option = SFSI("input[name='sfsi_plus_yummlyShare_option']:checked").val(),
        yummly = SFSI("input[name='sfsi_plus_yummly_pageUrl']").val(),

        flickr = SFSI("input[name='sfsi_plus_flickr_pageUrl']").val(),
        redditTyp = SFSI("input[name='sfsi_plus_reddit_url_type']:checked").val(),
        reddit = SFSI("input[name='sfsi_plus_reddit_pageUrl']").val(),
        tumblr = SFSI("input[name='sfsi_plus_tumblr_pageUrl']").val(),

        fbMContactChk = SFSI("input[name='sfsi_plus_fbmessengerContact_option']:checked").val(),
        fbMContactChk = "undefined" !== typeof fbMContactChk ? fbMContactChk : "no",

        fbMContactUrl = "no" == fbMContactChk ? "" : SFSI("input[name='sfsi_plus_fbmessengerContact_url']").val(),
        fbMShareChk = SFSI("input[name='sfsi_plus_fbmessengerShare_option']:checked").val(),
        fbMShareChk = "undefined" !== typeof fbMShareChk ? fbMShareChk : "no";
    fbMShareAppId = SFSI("input[name='sfsi_plus_fbmessenger_app_id']").val();

    var mixVisitChk = SFSI("input[name='sfsi_plus_mixVisit_option']:checked").val(),
        mixVisitChk = "undefined" !== typeof mixVisitChk ? mixVisitChk : "no",
        mixVisitUrl = "no" == mixVisitChk ? "" : SFSI("input[name='sfsi_plus_mixVisit_url']").val(),
        mixShareChk = SFSI("input[name='sfsi_plus_mixShare_option']:checked").val(),
        mixShareChk = "undefined" !== typeof mixShareChk ? mixShareChk : "no",

        mastodonVisitChk = SFSI("input[name='sfsi_plus_mastodonVisit_option']:checked").val() || '',
        mastodonVisitUrl = "no" == mastodonVisitChk ? "" : SFSI("input[name='sfsi_plus_mastodonVisit_url']").val() || '',
        mastodonShareChk = SFSI("input[name='sfsi_plus_mastodonShare_option']:checked").val() || '',

        okVisitChk = SFSI("input[name='sfsi_plus_okVisit_option']:checked").val(),
        okVisitChk = "undefined" !== typeof okVisitChk ? okVisitChk : "no",
        okVisitUrl = "no" == okVisitChk ? "" : SFSI("input[name='sfsi_plus_okVisit_url']").val(),
        okSubscribeChk = SFSI("input[name='sfsi_plus_okSubscribe_option']:checked").val(),
        okSubscribeChk = "undefined" !== typeof okSubscribeChk ? okSubscribeChk : "no",
        okSubscribeUrl = "no" == okSubscribeChk ? "" : SFSI("input[name='sfsi_plus_okSubscribe_userid']").val(),
        okShareChk = SFSI("input[name='sfsi_plus_okLike_option']:checked").val(),
        okShareChk = "undefined" !== typeof okShareChk ? okShareChk : "no",

        tgShareChk = SFSI("input[name='sfsi_plus_telegramShare_option']:checked").val(),
        tgShareChk = "undefined" !== typeof tgShareChk ? tgShareChk : "no",
        tgMsgChk = SFSI("input[name='sfsi_plus_telegramMessage_option']:checked").val(),
        tgMsgChk = "undefined" !== typeof tgMsgChk ? tgMsgChk : "no",
        tgMessageTxt = SFSI("input[name='sfsi_plus_telegram_message']").val(),
        tgMsgUserName = SFSI("input[name='sfsi_plus_telegram_username']").val(),

        vkVisitChk = SFSI("input[name='sfsi_plus_vkVisit_option']:checked").val(),
        vkVisitChk = "undefined" !== typeof vkVisitChk ? vkVisitChk : "no",
        vkShareChk = SFSI("input[name='sfsi_plus_vkShare_option']:checked").val(),
        vkShareChk = "undefined" !== typeof vkShareChk ? vkShareChk : "no",
        vkLikeChk = SFSI("input[name='sfsi_plus_vkLike_option']:checked").val(),
        vkLikeChk = "undefined" !== typeof vkLikeChk ? vkLikeChk : "no",

        //vkFollowChk 	= SFSI("input[name='sfsi_plus_vkFollow_option']:checked").val(),
        //vkFollowChk 	= "undefined" !== typeof vkFollowChk ? vkFollowChk: "no",

        vkVisitUrl = "no" == vkVisitChk ? "" : SFSI("input[name='sfsi_plus_vkVisit_url']").val(),
        //vkFollowUrl 	= "no" == vkFollowChk ? "" : SFSI("input[name='sfsi_plus_vkFollow_url']").val(),

        weiboVisitChk = SFSI("input[name='sfsi_plus_weiboVisit_option']:checked").val(),
        weiboVisitChk = "undefined" !== typeof weiboVisitChk ? weiboVisitChk : "no",
        weiboShareChk = SFSI("input[name='sfsi_plus_weiboShare_option']:checked").val(),
        weiboShareChk = "undefined" !== typeof weiboShareChk ? weiboShareChk : "no",
        weiboLikeChk = SFSI("input[name='sfsi_plus_weiboLike_option']:checked").val(),
        weiboLikeChk = "undefined" !== typeof weiboLikeChk ? weiboLikeChk : "no",
        weiboVisitUrl = "no" == weiboVisitChk ? "" : SFSI("input[name='sfsi_plus_weiboVisit_url']").val(),

        wechatFollowChk = SFSI("input[name='sfsi_plus_wechatFollow_option']:checked").val(),
        wechatFollowChk = "undefined" !== typeof wechatFollowChk ? wechatFollowChk : "no",
        wechatShareChk = SFSI("input[name='sfsi_plus_wechatShare_option']:checked").val(),
        wechatShareChk = "undefined" !== typeof wechatShareChk ? wechatShareChk : "no",
        wechatScanImage = SFSI("input[name='sfsi_premium_wechat_scan_image']").val(),

        xingVisitChk = SFSI("input[name='sfsi_plus_xingVisit_option']:checked").val(),
        xingVisitChk = "undefined" !== typeof xingVisitChk ? xingVisitChk : "no",
        xingShareChk = SFSI("input[name='sfsi_plus_xingShare_option']:checked").val(),
        xingShareChk = "undefined" !== typeof xingShareChk ? xingShareChk : "no",
        xingFollowChk = SFSI("input[name='sfsi_plus_xingFollow_option']:checked").val(),
        xingFollowChk = "undefined" !== typeof xingFollowChk ? xingFollowChk : "no",
        xingVisitUrl = "no" == xingVisitChk ? "" : SFSI("input[name='sfsi_plus_xingVisit_url']").val(),
        xingFollowUrl = "no" == xingFollowChk ? "" : SFSI("input[name='sfsi_plus_xingFollow_url']").val();


    var M = {
        action: "plus_updateSrcn2",
        sfsi_plus_rss_url: i,
        sfsi_plus_rss_icons: e,

        sfsi_plus_email_icons_functions: SFSI("input[name='sfsi_plus_email_icons_functions']:checked").val(),
        sfsi_plus_email_icons_contact: SFSI("input[name='sfsi_plus_email_icons_contact']").val(),
        sfsi_plus_email_icons_pageurl: SFSI("input[name='sfsi_plus_email_icons_pageurl']").val(),
        sfsi_plus_email_icons_mailchimp_apikey: SFSI("input[name='sfsi_plus_email_icons_mailchimp_apikey']").val(),
        sfsi_plus_email_icons_mailchimp_listid: SFSI("input[name='sfsi_plus_email_icons_mailchimp_listid']").val(),

        sfsi_plus_email_icons_subject_line: SFSI("input[name='sfsi_plus_email_icons_subject_line']").val(),
        sfsi_plus_email_icons_email_content: SFSI("textarea[name='sfsi_plus_email_icons_email_content']").val(),

        sfsi_plus_facebookPage_option: t,
        sfsi_plus_facebookLike_option: n,
        sfsi_plus_facebookShare_option: o,

        sfsi_plus_threadsShare_option: trdsShare,
        sfsi_plus_blueskyShare_option: bskyShare,

        sfsi_plus_facebookPage_url: a,

        sfsi_plus_twitter_followme: r,
        sfsi_plus_twitter_followUserName: c,
        sfsi_plus_twitter_aboutPage: p,
        sfsi_plus_twitter_page: _,
        sfsi_plus_twitter_pageURL: l,


        sfsi_plus_youtube_page: m,
        sfsi_plus_youtube_pageUrl: F,
        sfsi_plus_youtube_follow: h,
        sfsi_plus_youtubeusernameorid: cls,
        sfsi_plus_ytube_chnlid: vchid,

        sfsi_plus_pinterest_page: g,
        sfsi_plus_pinterest_pageUrl: k,

        sfsi_plus_instagram_pageUrl: b,
        sfsi_plus_ria_pageUrl: ria,
        sfsi_plus_inha_pageUrl: inha,

        //sfsi_plus_houzzVisit_option : houzzVisitOption,
        sfsi_plus_houzz_pageUrl: houzz,
        // sfsi_plus_houzzShare_option : houzzShare_option,
        // sfsi_plus_houzz_websiteId   : houzz_websiteId,

        sfsi_plus_snapchat_pageUrl: snapchat,
        sfsi_plus_whatsapp_url_type: whatsappTyp,
        sfsi_plus_whatsapp_message: whatsappMsg,

        sfsi_plus_my_whatsapp_number: whatsappMynum,
        sfsi_plus_whatsapp_number: whatsappNum,
        sfsi_plus_whatsapp_share_page: whatsappshrpg,

        sfsi_plus_skype_options: SFSI("input[name='sfsi_plus_skype_options']:checked").val(),
        sfsi_plus_skype_pageUrl: skype,

        sfsi_plus_vimeo_pageUrl: vimeo,
        sfsi_plus_soundcloud_pageUrl: soundcloud,

        sfsi_plus_yummlyVisit_option: yummlyVisitOption,
        sfsi_plus_yummly_pageUrl: yummly,
        sfsi_plus_yummlyShare_option: yummlyShare_option,

        sfsi_plus_flickr_pageUrl: flickr,
        sfsi_plus_reddit_url_type: redditTyp,
        sfsi_plus_reddit_pageUrl: reddit,
        sfsi_plus_tumblr_pageUrl: tumblr,

        sfsi_plus_pinterest_pingBlog: y,
        sfsi_plus_linkedin_page: w,
        sfsi_plus_linkedin_pageURL: x,
        sfsi_plus_linkedin_follow: C,
        sfsi_plus_linkedin_followCompany: D,
        sfsi_plus_linkedin_SharePage: U,
        sfsi_plus_linkedin_recommendBusines: O,
        sfsi_plus_linkedin_recommendCompany: j,
        sfsi_plus_linkedin_recommendProductId: T,

        sfsi_plus_fbmessengerContact_option: fbMContactChk,
        sfsi_plus_fbmessengerShare_option: fbMShareChk,
        sfsi_plus_fbmessengerShare_app_id: fbMShareAppId,
        sfsi_plus_fbmessengerContact_url: fbMContactUrl,

        sfsi_plus_mixVisit_option: mixVisitChk,
        sfsi_plus_mixVisit_url: mixVisitUrl,
        sfsi_plus_mixShare_option: mixShareChk,

        sfsi_plus_okVisit_option: okVisitChk,
        sfsi_plus_okVisit_url: okVisitUrl,
        sfsi_plus_okSubscribe_option: okSubscribeChk,
        sfsi_plus_okSubscribe_userid: okSubscribeUrl,
        sfsi_plus_okLike_option: okShareChk,

        sfsi_plus_telegramShare_option: tgShareChk,
        sfsi_plus_telegramMessage_option: tgMsgChk,
        sfsi_plus_telegram_message: tgMessageTxt,
        sfsi_plus_telegram_username: tgMsgUserName,

        sfsi_plus_vkVisit_option: vkVisitChk,
        sfsi_plus_vkShare_option: vkShareChk,
        sfsi_plus_vkLike_option: vkLikeChk,
        //sfsi_plus_vkFollow_option : vkFollowChk,
        //sfsi_plus_vkFollow_url 	  : vkFollowUrl,
        sfsi_plus_vkVisit_url: vkVisitUrl,

        sfsi_plus_weiboVisit_option: weiboVisitChk,
        sfsi_plus_weiboShare_option: weiboShareChk,
        sfsi_plus_weiboLike_option: weiboLikeChk,
        sfsi_plus_weiboVisit_url: weiboVisitUrl,

        sfsi_plus_mastodonVisit_url: mastodonVisitUrl,
        sfsi_plus_mastodonShare_option: mastodonShareChk,
        sfsi_plus_mastodonVisit_option: mastodonVisitChk,

        sfsi_plus_wechatFollow_option: wechatFollowChk,
        sfsi_premium_wechat_scan_image: wechatScanImage,
        sfsi_plus_wechatShare_option: wechatShareChk,

        sfsi_plus_xingVisit_option: xingVisitChk,
        sfsi_plus_xingShare_option: xingShareChk,
        sfsi_plus_xingFollow_option: xingFollowChk,
        sfsi_plus_xingVisit_url: xingVisitUrl,
        sfsi_plus_xingFollow_url: xingFollowUrl,

        sfsi_plus_custom_links: P,
        nonce: nonce
    };
    SFSI.ajax({
        url: sfsi_premium_ajax_object.ajax_url,
        type: "post",
        data: M,
        async: !0,
        timeout: 6000,
        dataType: "json",
        success: function (s) {
            if (s == "wrong_nonce") {
                sfsiplus_showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 2);
                return_value = !1;
                sfsiplus_afterLoad();
            } else {
                "success" == s ? (sfsiplus_showErrorSuc("success", "Saved !", 2), sfsipluscollapse("#sfsi_plus_save2"),
                    sfsi_plus_depened_sections(true)) : (global_error = 1, sfsiplus_showErrorSuc("error", "Your settings could not be saved. Please see <a href='https://www.ultimatelysocial.com/saving-settings-is-not-working/' style='color:green;font-weight: bold;'>here</a>  what could be the possible issue", 2),
                    return_value = !1), sfsiplus_afterLoad();
            }
        },
        error: function (jqXHR, textStatus) {
            if (textStatus === 'timeout') {
                global_error = 1,
                    sfsiplus_showErrorSuc("error", "Your settings could not be saved. Please see <a href='https://www.ultimatelysocial.com/saving-settings-is-not-working/' style='color:green;font-weight: bold;'>here</a>  what could be the possible issue", 2)
            }
            sfsiplus_afterLoad();
            //console.log(textStatus,jqXHR);
        }
    });
}

function sfsi_plus_update_step3() {
    var nonce = SFSI("#sfsi_plus_save3").attr("data-nonce");
    var s = sfsi_plus_validationStep3();
    if (!s) return global_error = 1, !1;
    sfsiplus_beForeLoad();

    var i = SFSI("input[name='sfsi_plus_actvite_theme']:checked").val(),
        e = SFSI("input[name='sfsi_plus_mouseOver']:checked").val(),
        t = SFSI("input[name='sfsi_plus_shuffle_icons']:checked").val(),
        n = SFSI("input[name='sfsi_plus_shuffle_Firstload']:checked").val(),
        o = SFSI("input[name=sfsi_plus_same_icons_mouseOver_effect]:checked").val(),
        a = SFSI("input[name='sfsi_plus_shuffle_interval']:checked").val(),
        r = SFSI("input[name='sfsi_plus_shuffle_intervalTime']").val(),
        c = SFSI("input[name='sfsi_plus_specialIcon_animation']:checked").val(),
        p = SFSI("input[name='sfsi_plus_specialIcon_MouseOver']:checked").val(),
        _ = SFSI("input[name='sfsi_plus_specialIcon_Firstload']:checked").val(),
        l = SFSI("#sfsi_plus_specialIcon_Firstload_Icons option:selected").val(),
        S = SFSI("input[name='sfsi_plus_specialIcon_interval']:checked").val(),
        u = SFSI("input[name='sfsi_plus_specialIcon_intervalTime']").val(),
        f = SFSI("#sfsi_plus_specialIcon_intervalIcons option:selected").val(),

        sfsi_plus_mouseOver_effect_type = SFSI("input[name='sfsi_plus_mouseOver_effect_type']:checked").val(),
        /* Flat color settings */
        sfsi_plus_rss_bgColor = SFSI("input[name='sfsi_plus_rss_bgColor']").val(),
        sfsi_plus_email_bgColor = SFSI("input[name='sfsi_plus_email_bgColor']").val(),
        sfsi_plus_facebook_bgColor = SFSI("input[name='sfsi_plus_facebook_bgColor']").val(),
        sfsi_plus_twitter_bgColor = SFSI("input[name='sfsi_plus_twitter_bgColor']").val(),
        sfsi_plus_threads_bgColor = SFSI("input[name='sfsi_plus_threads_bgColor']").val(),
        sfsi_plus_bluesky_bgColor = SFSI("input[name='sfsi_plus_bluesky_bgColor']").val(),
        sfsi_plus_share_bgColor = SFSI("input[name='sfsi_plus_share_bgColor']").val(),
        sfsi_plus_youtube_bgColor = SFSI("input[name='sfsi_plus_youtube_bgColor']").val(),
        sfsi_plus_pinterest_bgColor = SFSI("input[name='sfsi_plus_pinterest_bgColor']").val(),
        sfsi_plus_linkedin_bgColor = SFSI("input[name='sfsi_plus_linkedin_bgColor']").val(),
        sfsi_plus_instagram_bgColor = SFSI("input[name='sfsi_plus_instagram_bgColor']").val(),
        sfsi_plus_ria_bgColor = SFSI("input[name='sfsi_plus_ria_bgColor']").val(),
        sfsi_plus_inha_bgColor = SFSI("input[name='sfsi_plus_inha_bgColor']").val(),
        sfsi_plus_houzz_bgColor = SFSI("input[name='sfsi_plus_houzz_bgColor']").val(),
        sfsi_plus_snapchat_bgColor = SFSI("input[name='sfsi_plus_snapchat_bgColor']").val(),
        sfsi_plus_whatsapp_bgColor = SFSI("input[name='sfsi_plus_whatsapp_bgColor']").val(),
        sfsi_plus_skype_bgColor = SFSI("input[name='sfsi_plus_skype_bgColor']").val(),
        sfsi_plus_phone_bgColor = SFSI("input[name='sfsi_plus_phone_bgColor']").val(),
        sfsi_plus_vimeo_bgColor = SFSI("input[name='sfsi_plus_vimeo_bgColor']").val(),
        sfsi_plus_soundcloud_bgColor = SFSI("input[name='sfsi_plus_soundcloud_bgColor']").val(),
        sfsi_plus_yummly_bgColor = SFSI("input[name='sfsi_plus_yummly_bgColor']").val(),
        sfsi_plus_flickr_bgColor = SFSI("input[name='sfsi_plus_flickr_bgColor']").val(),
        sfsi_plus_reddit_bgColor = SFSI("input[name='sfsi_plus_reddit_bgColor']").val(),
        sfsi_plus_tumblr_bgColor = SFSI("input[name='sfsi_plus_tumblr_bgColor']").val(),
        sfsi_plus_fbmessenger_bgColor = SFSI("input[name='sfsi_plus_fbmessenger_bgColor']").val(),
        sfsi_plus_gab_bgColor = SFSI("input[name='sfsi_plus_gab_bgColor']").val(),
        sfsi_plus_mix_bgColor = SFSI("input[name='sfsi_plus_mix_bgColor']").val(),
        sfsi_plus_ok_bgColor = SFSI("input[name='sfsi_plus_ok_bgColor']").val(),
        sfsi_plus_telegram_bgColor = SFSI("input[name='sfsi_plus_telegram_bgColor']").val(),
        sfsi_plus_vk_bgColor = SFSI("input[name='sfsi_plus_vk_bgColor']").val(),
        sfsi_plus_wechat_bgColor = SFSI("input[name='sfsi_plus_wechat_bgColor']").val(),
        sfsi_plus_weibo_bgColor = SFSI("input[name='sfsi_plus_weibo_bgColor']").val(),
        sfsi_plus_xing_bgColor = SFSI("input[name='sfsi_plus_xing_bgColor']").val(),
        sfsi_plus_copylink_bgColor = SFSI("input[name='sfsi_plus_copylink_bgColor']").val(),
        sfsi_plus_mastodon_bgColor = SFSI("input[name='sfsi_plus_mastodon_bgColor']").val();

    mouseover_other_icons_transition_effect = SFSI("select[name='mouseover_other_icons_transition_effect']").val();

    var arrAllOtherIconsElem = SFSI("input[name^=mouseover_other_icon_][value!='']");

    var objMouseOverOtherIcons = {};

    if (arrAllOtherIconsElem.length > 0) {

        var objMouseOverOtherIconsForCustomIcons = {};

        SFSI.each(arrAllOtherIconsElem, function (i, element) {

            var iconImg = SFSI(element).val();
            var iconName = SFSI(element).attr('data-iconname');
            var customIconIndex = SFSI(element).attr('data-customiconindex');

            if (iconImg.length > 0 && SfsiHelper.validateUrl(iconImg)) {

                iconImg = iconImg.replace(wp_upload_dir_object.baseurl + "/", "");

                if ("custom" == iconName && customIconIndex.length > 0) {
                    objMouseOverOtherIconsForCustomIcons[customIconIndex] = iconImg;
                } else {
                    objMouseOverOtherIcons[iconName] = iconImg;
                }
            }

        });

        if (false === SFSI.isEmptyObject(objMouseOverOtherIconsForCustomIcons)) {
            objMouseOverOtherIcons['custom'] = objMouseOverOtherIconsForCustomIcons;
        }

    }

    var data = {
        action: "plus_updateSrcn3",
        sfsi_plus_actvite_theme: i,

        sfsi_plus_mouseOver: e,
        sfsi_plus_mouseOver_effect: o,
        sfsi_plus_mouseOver_effect_type: sfsi_plus_mouseOver_effect_type,
        sfsi_plus_mouseOver_other_icon_images: objMouseOverOtherIcons,
        mouseover_other_icons_transition_effect: mouseover_other_icons_transition_effect,
        sfsi_plus_shuffle_icons: t,
        sfsi_plus_shuffle_Firstload: n,
        sfsi_plus_shuffle_interval: a,
        sfsi_plus_shuffle_intervalTime: r,

        sfsi_plus_specialIcon_animation: c,
        sfsi_plus_specialIcon_MouseOver: p,
        sfsi_plus_specialIcon_Firstload: _,
        sfsi_plus_specialIcon_Firstload_Icons: l,
        sfsi_plus_specialIcon_interval: S,
        sfsi_plus_specialIcon_intervalTime: u,
        sfsi_plus_specialIcon_intervalIcons: f,

        sfsi_plus_rss_bgColor: sfsi_plus_rss_bgColor,
        sfsi_plus_email_bgColor: sfsi_plus_email_bgColor,
        sfsi_plus_facebook_bgColor: sfsi_plus_facebook_bgColor,
        sfsi_plus_twitter_bgColor: sfsi_plus_twitter_bgColor,
        sfsi_plus_threads_bgColor: sfsi_plus_threads_bgColor,
        sfsi_plus_bluesky_bgColor: sfsi_plus_bluesky_bgColor,
        sfsi_plus_share_bgColor: sfsi_plus_share_bgColor,
        sfsi_plus_youtube_bgColor: sfsi_plus_youtube_bgColor,
        sfsi_plus_pinterest_bgColor: sfsi_plus_pinterest_bgColor,
        sfsi_plus_linkedin_bgColor: sfsi_plus_linkedin_bgColor,
        sfsi_plus_instagram_bgColor: sfsi_plus_instagram_bgColor,
        sfsi_plus_ria_bgColor: sfsi_plus_ria_bgColor,
        sfsi_plus_inha_bgColor: sfsi_plus_inha_bgColor,
        sfsi_plus_houzz_bgColor: sfsi_plus_houzz_bgColor,
        sfsi_plus_snapchat_bgColor: sfsi_plus_snapchat_bgColor,
        sfsi_plus_whatsapp_bgColor: sfsi_plus_whatsapp_bgColor,
        sfsi_plus_skype_bgColor: sfsi_plus_skype_bgColor,
        sfsi_plus_phone_bgColor: sfsi_plus_phone_bgColor,
        sfsi_plus_vimeo_bgColor: sfsi_plus_vimeo_bgColor,
        sfsi_plus_soundcloud_bgColor: sfsi_plus_soundcloud_bgColor,
        sfsi_plus_yummly_bgColor: sfsi_plus_yummly_bgColor,
        sfsi_plus_flickr_bgColor: sfsi_plus_flickr_bgColor,
        sfsi_plus_reddit_bgColor: sfsi_plus_reddit_bgColor,
        sfsi_plus_tumblr_bgColor: sfsi_plus_tumblr_bgColor,
        sfsi_plus_fbmessenger_bgColor: sfsi_plus_fbmessenger_bgColor,
        sfsi_plus_gab_bgColor: sfsi_plus_gab_bgColor,
        sfsi_plus_mix_bgColor: sfsi_plus_mix_bgColor,
        sfsi_plus_ok_bgColor: sfsi_plus_ok_bgColor,
        sfsi_plus_telegram_bgColor: sfsi_plus_telegram_bgColor,
        sfsi_plus_vk_bgColor: sfsi_plus_vk_bgColor,
        sfsi_plus_wechat_bgColor: sfsi_plus_wechat_bgColor,
        sfsi_plus_weibo_bgColor: sfsi_plus_weibo_bgColor,
        sfsi_plus_xing_bgColor: sfsi_plus_xing_bgColor,
        sfsi_plus_copylink_bgColor: sfsi_plus_copylink_bgColor,
        sfsi_plus_mastodon_bgColor: sfsi_plus_mastodon_bgColor,

        nonce: nonce
    };
    SFSI.ajax({
        url: sfsi_premium_ajax_object.ajax_url,
        type: "post",
        data: data,
        async: !0,
        timeout: 6000,
        dataType: "json",
        success: function (s) {
            if (s == "wrong_nonce") {
                sfsiplus_showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 3);
                return_value = !1;
                sfsiplus_afterLoad();
            } else {
                "success" == s ? (sfsiplus_showErrorSuc("success", "Saved !", 3), sfsipluscollapse("#sfsi_plus_save3")) : (sfsiplus_showErrorSuc("error", "Your settings could not be saved. Please see <a href='https://www.ultimatelysocial.com/saving-settings-is-not-working/' style='color:green;font-weight: bold;'>here</a>  what could be the possible issue", 3),
                    return_value = !1), sfsiplus_afterLoad();
            }
        },
        error: function (jqXHR, textStatus) {
            if (textStatus === 'timeout') {
                global_error = 1,
                    sfsiplus_showErrorSuc("error", "Your settings could not be saved. Please see <a href='https://www.ultimatelysocial.com/saving-settings-is-not-working/' style='color:green;font-weight: bold;'>here</a>  what could be the possible issue", 3)
            }
            sfsiplus_afterLoad();
            //console.log(textStatus,jqXHR);
        },
    });
}

function sfsi_plus_update_step4() {
    var nonce = SFSI("#sfsi_plus_save4").attr("data-nonce");
    var s = !1,
        i = sfsi_plus_validationStep4();
    if (!i) return global_error = 1, !1;
    sfsiplus_beForeLoad();

    var e = SFSI("input[name='sfsi_plus_display_counts']:checked").val(),

        t = 1 == SFSI("input[name='sfsi_plus_email_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_email_countsDisplay']:checked").val(),
        n = 1 == SFSI("input[name='sfsi_plus_email_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_email_countsFrom']:checked").val(),

        o = SFSI("input[name='sfsi_plus_email_manualCounts']").val(),


        r = 1 == SFSI("input[name='sfsi_plus_rss_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_rss_countsDisplay']:checked").val(),
        c = SFSI("input[name='sfsi_plus_rss_manualCounts']").val(),

        p = 1 == SFSI("input[name='sfsi_plus_facebook_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_facebook_countsDisplay']:checked").val(),
        _ = 1 == SFSI("input[name='sfsi_plus_facebook_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_facebook_countsFrom']:checked").val(),
        mp = SFSI("input[name='sfsi_plus_facebook_mypageCounts']").val(),
        la = SFSI("input[name='sfsi_plus_facebook_countsFrom_blog']").val(),
        l = SFSI("input[name='sfsi_plus_facebook_manualCounts']").val(),

        S = 1 == SFSI("input[name='sfsi_plus_twitter_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_twitter_countsDisplay']:checked").val(),
        bskyd = 1 == SFSI("input[name='sfsi_plus_bluesky_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_bluesky_countsDisplay']:checked").val(),
        trdsd = 1 == SFSI("input[name='sfsi_plus_threads_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_threads_countsDisplay']:checked").val(),
        u = 1 == SFSI("input[name='sfsi_plus_twitter_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_twitter_countsFrom']:checked").val(),
        bskyf = 1 == SFSI("input[name='sfsi_plus_bluesky_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_bluesky_countsFrom']:checked").val(),
        trdsf = 1 == SFSI("input[name='sfsi_plus_threads_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_threads_countsFrom']:checked").val(),
        f = SFSI("input[name='sfsi_plus_twitter_manualCounts']").val(),
        bsky = SFSI("input[name='sfsi_plus_bluesky_manualCounts']").val(),
        trds = SFSI("input[name='sfsi_plus_threads_manualCounts']").val(),
        d = SFSI("input[name='sfsiplus_tw_consumer_key']").val(),
        I = SFSI("input[name='sfsiplus_tw_consumer_secret']").val(),
        m = SFSI("input[name='sfsiplus_tw_oauth_access_token']").val(),
        F = SFSI("input[name='sfsiplus_tw_oauth_access_token_secret']").val(),

        k = 1 == SFSI("input[name='sfsi_plus_linkedIn_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_linkedIn_countsFrom']:checked").val(),
        y = SFSI("input[name='sfsi_plus_linkedIn_manualCounts']").val(),
        b = SFSI("input[name='sfsi_plus_ln_company']").val(),
        w = SFSI("input[name='sfsi_plus_ln_api_key']").val(),
        x = SFSI("input[name='sfsi_plus_ln_secret_key']").val(),
        C = SFSI("input[name='sfsi_plus_ln_oAuth_user_token']").val(),
        D = 1 == SFSI("input[name='sfsi_plus_linkedIn_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_linkedIn_countsDisplay']:checked").val(),
        k = 1 == SFSI("input[name='sfsi_plus_linkedIn_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_linkedIn_countsFrom']:checked").val(),
        y = SFSI("input[name='sfsi_plus_linkedIn_manualCounts']").val(),

        U = 1 == SFSI("input[name='sfsi_plus_youtube_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_youtube_countsDisplay']:checked").val(),
        O = 1 == SFSI("input[name='sfsi_plus_youtube_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_youtube_countsFrom']:checked").val(),
        T = SFSI("input[name='sfsi_plus_youtube_manualCounts']").val(),

        P = 1 == SFSI("input[name='sfsi_plus_pinterest_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_pinterest_countsDisplay']:checked").val(),
        M = 1 == SFSI("input[name='sfsi_plus_pinterest_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_pinterest_countsFrom']:checked").val(),
        L = SFSI("input[name='sfsi_plus_pinterest_manualCounts']").val(),
        B = SFSI("input[name='sfsi_plus_pinterest_user']").val(),
        E = SFSI("input[name='sfsi_plus_pinterest_board_name']").val(),
        pIntAccessToken = SFSI("input[name='sfsi_plus_pinterest_access_token']").val(),

        z = 1 == SFSI("input[name='sfsi_plus_instagram_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_instagram_countsDisplay']:checked").val(),
        A = 1 == SFSI("input[name='sfsi_plus_instagram_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_instagram_countsFrom']:checked").val(),
        N = SFSI("input[name='sfsi_plus_instagram_manualCounts']").val(),
        H = SFSI("input[name='sfsi_plus_instagram_User']").val(),

        R = 1 == SFSI("input[name='sfsi_plus_shares_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_shares_countsDisplay']:checked").val(),
        W = 1 == SFSI("input[name='sfsi_plus_shares_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_shares_countsFrom']:checked").val(),
        q = SFSI("input[name='sfsi_plus_shares_manualCounts']").val(),

        houzzDisplay = 1 == SFSI("input[name='sfsi_plus_houzz_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_houzz_countsDisplay']:checked").val(),
        houzzFrom = 1 == SFSI("input[name='sfsi_plus_houzz_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_houzz_countsFrom']:checked").val(),
        houzzCount = SFSI("input[name='sfsi_plus_houzz_manualCounts']").val(),

        snapchatDisplay = 1 == SFSI("input[name='sfsi_plus_snapchat_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_snapchat_countsDisplay']:checked").val(),
        snapchatFrom = 1 == SFSI("input[name='sfsi_plus_snapchat_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_snapchat_countsFrom']:checked").val(),
        snapchatCount = SFSI("input[name='sfsi_plus_snapchat_manualCounts']").val(),

        riaDisplay = 1 == SFSI("input[name='sfsi_plus_ria_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_ria_countsDisplay']:checked").val(),
        riaFrom = 1 == SFSI("input[name='sfsi_plus_ria_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_ria_countsFrom']:checked").val(),
        riaCount = SFSI("input[name='sfsi_plus_ria_manualCounts']").val(),

        inhaDisplay = 1 == SFSI("input[name='sfsi_plus_inha_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_inha_countsDisplay']:checked").val(),
        inhaFrom = 1 == SFSI("input[name='sfsi_plus_inha_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_inha_countsFrom']:checked").val(),
        inhaCount = SFSI("input[name='sfsi_plus_inha_manualCounts']").val(),

        whatsappDisplay = 1 == SFSI("input[name='sfsi_plus_whatsapp_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_whatsapp_countsDisplay']:checked").val(),
        whatsappFrom = 1 == SFSI("input[name='sfsi_plus_whatsapp_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_whatsapp_countsFrom']:checked").val(),
        whatsappCount = SFSI("input[name='sfsi_plus_whatsapp_manualCounts']").val(),
        skypeDisplay = 1 == SFSI("input[name='sfsi_plus_skype_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_skype_countsDisplay']:checked").val(),

        skypeFrom = 1 == SFSI("input[name='sfsi_plus_skype_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_skype_countsFrom']:checked").val(),
        skypeCount = SFSI("input[name='sfsi_plus_skype_manualCounts']").val(),

        phoneDisplay = 1 == SFSI("input[name='sfsi_plus_phone_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_phone_countsDisplay']:checked").val(),
        phoneCount = SFSI("input[name='sfsi_plus_phone_manualCounts']").val(),

        vimeoDisplay = 1 == SFSI("input[name='sfsi_plus_vimeo_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_vimeo_countsDisplay']:checked").val(),
        vimeoFrom = 1 == SFSI("input[name='sfsi_plus_vimeo_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_vimeo_countsFrom']:checked").val(),
        vimeoCount = SFSI("input[name='sfsi_plus_vimeo_manualCounts']").val(),

        soundcloudDisplay = 1 == SFSI("input[name='sfsi_plus_soundcloud_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_soundcloud_countsDisplay']:checked").val(),
        soundcloudFrom = 1 == SFSI("input[name='sfsi_plus_soundcloud_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_soundcloud_countsFrom']:checked").val(),
        soundcloudCount = SFSI("input[name='sfsi_plus_soundcloud_manualCounts']").val(),

        yummlyDisplay = 1 == SFSI("input[name='sfsi_plus_yummly_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_yummly_countsDisplay']:checked").val(),
        yummlyFrom = 1 == SFSI("input[name='sfsi_plus_yummly_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_yummly_countsFrom']:checked").val(),
        yummlyCount = SFSI("input[name='sfsi_plus_yummly_manualCounts']").val(),

        flickrDisplay = 1 == SFSI("input[name='sfsi_plus_flickr_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_flickr_countsDisplay']:checked").val(),
        flickrFrom = 1 == SFSI("input[name='sfsi_plus_flickr_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_flickr_countsFrom']:checked").val(),
        flickrCount = SFSI("input[name='sfsi_plus_flickr_manualCounts']").val(),

        redditDisplay = 1 == SFSI("input[name='sfsi_plus_reddit_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_reddit_countsDisplay']:checked").val(),
        redditFrom = 1 == SFSI("input[name='sfsi_plus_reddit_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_reddit_countsFrom']:checked").val(),
        redditCount = SFSI("input[name='sfsi_plus_reddit_manualCounts']").val(),

        tumblrDisplay = 1 == SFSI("input[name='sfsi_plus_tumblr_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_tumblr_countsDisplay']:checked").val(),
        tumblrFrom = 1 == SFSI("input[name='sfsi_plus_tumblr_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_tumblr_countsFrom']:checked").val(),
        tumblrCount = SFSI("input[name='sfsi_plus_tumblr_manualCounts']").val(),

        fbmessenger_countDisplay = 1 == SFSI("input[name='sfsi_plus_fbmessenger_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_fbmessenger_countsDisplay']:checked").val(),
        fbmessenger_From = 1 == SFSI("input[name='sfsi_plus_tumblr_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_tumblr_countsFrom']:checked").val(),
        fbmessenger_count = SFSI("input[name='sfsi_plus_fbmessenger_manualCounts']").val(),

        gab_countDisplay = 1 == SFSI("input[name='sfsi_plus_gab_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_gab_countsDisplay']:checked").val(),
        gab_From = 1 == SFSI("input[name='sfsi_plus_gab_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_gab_countsFrom']:checked").val(),
        gab_count = SFSI("input[name='sfsi_plus_gab_manualCounts']").val(),

        mix_countsDisplay = 1 == SFSI("input[name='sfsi_plus_mix_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_mix_countsDisplay']:checked").val(),
        mix_From = 1 == SFSI("input[name='sfsi_plus_tumblr_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_tumblr_countsFrom']:checked").val(),

        mix_count = SFSI("input[name='sfsi_plus_mix_manualCounts']").val(),

        ok_countsDisplay = 1 == SFSI("input[name='sfsi_plus_ok_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_ok_countsDisplay']:checked").val(),
        ok_From = 1 == SFSI("input[name='sfsi_plus_tumblr_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_tumblr_countsFrom']:checked").val(),

        ok_count = SFSI("input[name='sfsi_plus_ok_manualCounts']").val(),

        vk_countsDisplay = 1 == SFSI("input[name='sfsi_plus_vk_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_vk_countsDisplay']:checked").val(),
        vk_From = 1 == SFSI("input[name='sfsi_plus_tumblr_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_tumblr_countsFrom']:checked").val(),

        vk_count = SFSI("input[name='sfsi_plus_vk_manualCounts']").val(),

        telegram_countsDisplay = 1 == SFSI("input[name='sfsi_plus_telegram_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_telegram_countsDisplay']:checked").val(),
        telegram_From = 1 == SFSI("input[name='sfsi_plus_tumblr_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_tumblr_countsFrom']:checked").val(),

        telegram_count = SFSI("input[name='sfsi_plus_telegram_manualCounts']").val(),

        weibo_countsDisplay = 1 == SFSI("input[name='sfsi_plus_weibo_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_weibo_countsDisplay']:checked").val(),
        weibo_From = 1 == SFSI("input[name='sfsi_plus_mastodon_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_mastodon_countsFrom']:checked").val(),
        weibo_count = SFSI("input[name='sfsi_plus_weibo_manualCounts']").val(),


        xing_countsDisplay = 1 == SFSI("input[name='sfsi_plus_xing_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_xing_countsDisplay']:checked").val(),
        xing_From = 1 == SFSI("input[name='sfsi_plus_tumblr_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_tumblr_countsFrom']:checked").val(),
        xing_count = SFSI("input[name='sfsi_plus_xing_manualCounts']").val(),

        mastodon_countsDisplay = 1 == SFSI("input[name='sfsi_plus_mastodon_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_mastodon_countsDisplay']:checked").val(),
        mastodon_From = 1 == SFSI("input[name='sfsi_plus_tumblr_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_tumblr_countsFrom']:checked").val(),
        mastodon_count = SFSI("input[name='sfsi_plus_mastodon_manualCounts']").val(),

        wechat_countsDisplay = 1 == SFSI("input[name='sfsi_plus_wechat_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_wechat_countsDisplay']:checked").val(),
        wechat_From = 1 == SFSI("input[name='sfsi_plus_wechat_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_wechat_countsFrom']:checked").val(),
        wechat_count = SFSI("input[name='sfsi_plus_wechat_manualCounts']").val(),

        copylink_countsDisplay = 1 == SFSI("input[name='sfsi_plus_copylink_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_copylink_countsDisplay']:checked").val(),
        copylink_From = 1 == SFSI("input[name='sfsi_plus_copylink_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_copylink_countsFrom']:checked").val(),
        copylink_count = SFSI("input[name='sfsi_plus_copylink_manualCounts']").val(),

        appid = SFSI("input[name='sfsi_plus_facebook_appid']").val(),
        appsecret = SFSI("input[name='sfsi_plus_facebook_appsecret']").val(),

        pappid = SFSI("input[name='sfsi_plus_pinterest_appid']").val(),
        pappsecret = SFSI("input[name='sfsi_plus_pinterest_appsecret']").val(),
        pappurl = SFSI("input[name='sfsi_plus_pinterest_appurl']").val(),
        paccessToken = SFSI("input[name='sfsi_plus_pinterest_access_token']").val(),

        fbcachingActive = SFSI("input[name='sfsi_plus_fb_count_caching_active']:checked").val(),
        fbcachingInterval = SFSI("select[name='sfsi_plus_fb_caching_interval']").val(),

        twcachingActive = SFSI("input[name='sfsi_plus_tw_count_caching_active']:checked").val(),

        min_display_counts = SFSI("input[name='sfsi_plus_min_display_counts']").val();

    fbcachingInterval = typeof fbcachingInterval != undefined && fbcachingInterval != null ? fbcachingInterval : 1;

    var _countData = {
        action: "plus_updateSrcn4",
        sfsi_plus_display_counts: e,

        sfsi_plus_email_countsDisplay: t,
        sfsi_plus_email_countsFrom: n,
        sfsi_plus_email_manualCounts: o,

        sfsi_plus_rss_countsDisplay: r,
        sfsi_plus_rss_manualCounts: c,

        sfsi_plus_facebook_countsDisplay: p,
        sfsi_plus_facebook_countsFrom: _,
        sfsi_plus_fb_count_caching_active: fbcachingActive,
        sfsi_plus_fb_caching_interval: fbcachingInterval,
        sfsi_plus_facebook_mypageCounts: mp,
        sfsi_plus_facebook_appid: appid,
        sfsi_plus_facebook_appsecret: appsecret,
        sfsi_plus_facebook_countsFrom_blog: la,
        sfsi_plus_facebook_manualCounts: l,

        sfsi_plus_twitter_countsDisplay: S,
        sfsi_plus_bluesky_countsDisplay: bskyd,
        sfsi_plus_threads_countsDisplay: trdsd,
        sfsi_plus_twitter_countsFrom: u,
        sfsi_plus_bluesky_countsFrom: bskyf,
        sfsi_plus_threads_countsFrom: trdsf,
        sfsi_plus_twitter_manualCounts: f,
        sfsi_plus_bluesky_manualCounts: bsky,
        sfsi_plus_threads_manualCounts: trds,
        sfsiplus_tw_consumer_key: d,
        sfsiplus_tw_consumer_secret: I,
        sfsiplus_tw_oauth_access_token: m,
        sfsiplus_tw_oauth_access_token_secret: F,
        sfsi_plus_tw_count_caching_active: twcachingActive,

        sfsi_plus_linkedIn_countsDisplay: D,
        sfsi_plus_linkedIn_countsFrom: k,
        sfsi_plus_linkedIn_manualCounts: y,
        sfsi_plus_ln_company: b,
        sfsi_plus_ln_api_key: w,
        sfsi_plus_ln_secret_key: x,
        sfsi_plus_ln_oAuth_user_token: C,

        sfsi_plus_youtube_countsDisplay: U,
        sfsi_plus_youtube_countsFrom: O,
        sfsi_plus_youtube_manualCounts: T,
        sfsi_plus_youtube_channelId: SFSI("input[name='sfsi_plus_youtube_channelId']").val(),

        sfsi_plus_pinterest_countsDisplay: P,
        sfsi_plus_pinterest_countsFrom: M,

        sfsi_plus_pinterest_appid: pappid,
        sfsi_plus_pinterest_appsecret: pappsecret,
        sfsi_plus_pinterest_appurl: pappurl,
        sfsi_plus_pinterest_access_token: pIntAccessToken,
        sfsi_plus_pinterest_manualCounts: L,
        sfsi_plus_pinterest_user: B,
        sfsi_plus_pinterest_board_name: E,

        sfsi_plus_instagram_countsDisplay: z,
        sfsi_plus_instagram_countsFrom: A,
        sfsi_plus_instagram_manualCounts: N,
        sfsi_plus_instagram_User: H,
        sfsi_plus_instagram_clientid: SFSI("input[name='sfsi_plus_instagram_clientid']").val(),
        sfsi_plus_instagram_appurl: SFSI("input[name='sfsi_plus_instagram_appurl']").val(),
        sfsi_plus_instagram_token: SFSI("input[name='sfsi_plus_instagram_token']").val(),

        sfsi_plus_shares_countsDisplay: R,
        sfsi_plus_shares_countsFrom: W,
        sfsi_plus_shares_manualCounts: q,

        sfsi_plus_houzz_countsDisplay: houzzDisplay,
        sfsi_plus_houzz_countsFrom: houzzFrom,
        sfsi_plus_houzz_manualCounts: houzzCount,

        sfsi_plus_snapchat_countsDisplay: snapchatDisplay,
        sfsi_plus_snapchat_countsFrom: snapchatFrom,
        sfsi_plus_snapchat_manualCounts: snapchatCount,

        sfsi_plus_ria_countsDisplay: riaDisplay,
        sfsi_plus_ria_countsFrom: riaFrom,
        sfsi_plus_ria_manualCounts: riaCount,

        sfsi_plus_inha_countsDisplay: inhaDisplay,
        sfsi_plus_inha_countsFrom: inhaFrom,
        sfsi_plus_inha_manualCounts: inhaCount,

        sfsi_plus_whatsapp_countsDisplay: whatsappDisplay,
        sfsi_plus_whatsapp_countsFrom: whatsappFrom,
        sfsi_plus_whatsapp_manualCounts: whatsappCount,

        sfsi_plus_phone_countsDisplay: phoneDisplay,
        sfsi_plus_phone_manualCounts: phoneCount,

        sfsi_plus_skype_countsDisplay: skypeDisplay,
        sfsi_plus_skype_countsFrom: skypeFrom,
        sfsi_plus_skype_manualCounts: skypeCount,

        sfsi_plus_vimeo_countsDisplay: vimeoDisplay,
        sfsi_plus_vimeo_countsFrom: vimeoFrom,
        sfsi_plus_vimeo_manualCounts: vimeoCount,

        sfsi_plus_soundcloud_countsDisplay: soundcloudDisplay,
        sfsi_plus_soundcloud_countsFrom: soundcloudFrom,
        sfsi_plus_soundcloud_manualCounts: soundcloudCount,

        sfsi_plus_yummly_countsDisplay: yummlyDisplay,
        sfsi_plus_yummly_countsFrom: yummlyFrom,
        sfsi_plus_yummly_manualCounts: yummlyCount,

        sfsi_plus_flickr_countsDisplay: flickrDisplay,
        sfsi_plus_flickr_countsFrom: flickrFrom,
        sfsi_plus_flickr_manualCounts: flickrCount,

        sfsi_plus_reddit_countsDisplay: redditDisplay,
        sfsi_plus_reddit_countsFrom: redditFrom,
        sfsi_plus_reddit_manualCounts: redditCount,

        sfsi_plus_tumblr_countsDisplay: tumblrDisplay,
        sfsi_plus_tumblr_countsFrom: tumblrFrom,
        sfsi_plus_tumblr_manualCounts: tumblrCount,

        sfsi_plus_mastodon_countsDisplay: mastodon_countsDisplay,
        sfsi_plus_mastodon_countsFrom: mastodon_From,
        sfsi_plus_mastodon_manualCounts: mastodon_count,

        sfsi_plus_wechat_countsDisplay: wechat_countsDisplay,
        sfsi_plus_wechat_countsFrom: wechat_From,
        sfsi_plus_wechat_manualCounts: wechat_count,

        sfsi_plus_copylink_countsDisplay: copylink_countsDisplay,
        sfsi_plus_copylink_countsFrom: copylink_From,
        sfsi_plus_copylink_manualCounts: copylink_count,

        sfsi_plus_fbmessenger_countsDisplay: fbmessenger_countDisplay,
        sfsi_plus_gab_countsDisplay: gab_countDisplay,
        sfsi_plus_mix_countsDisplay: mix_countsDisplay,
        sfsi_plus_ok_countsDisplay: ok_countsDisplay,
        sfsi_plus_vk_countsDisplay: vk_countsDisplay,
        sfsi_plus_telegram_countsDisplay: telegram_countsDisplay,
        sfsi_plus_weibo_countsDisplay: weibo_countsDisplay,
        sfsi_plus_xing_countsDisplay: xing_countsDisplay,

        sfsi_plus_fbmessenger_countsFrom: fbmessenger_From,
        sfsi_plus_gab_countsFrom: gab_From,
        sfsi_plus_mix_countsFrom: mix_From,
        sfsi_plus_ok_countsFrom: ok_From,
        sfsi_plus_vk_countsFrom: vk_From,
        sfsi_plus_telegram_countsFrom: telegram_From,
        sfsi_plus_weibo_countsFrom: weibo_From,
        sfsi_plus_xing_countsFrom: xing_From,

        sfsi_plus_fbmessenger_manualCounts: fbmessenger_count,
        sfsi_plus_gab_manualCounts: gab_count,
        sfsi_plus_mix_manualCounts: mix_count,
        sfsi_plus_ok_manualCounts: ok_count,
        sfsi_plus_vk_manualCounts: vk_count,
        sfsi_plus_telegram_manualCounts: telegram_count,
        sfsi_plus_weibo_manualCounts: weibo_count,
        sfsi_plus_xing_manualCounts: xing_count,

        sfsi_plus_min_display_counts: min_display_counts,
        nonce: nonce
    };

    return SFSI.ajax({
        url: sfsi_premium_ajax_object.ajax_url,
        type: "post",
        data: _countData,
        dataType: "json",
        timeout: 6000,
        async: !0,
        success: function (s) {
            if (s == "wrong_nonce") {
                sfsiplus_showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 4);
                global_error = 1;
                sfsiplus_afterLoad();
            } else {
                "success" == s.res ? (sfsiplus_showErrorSuc("success", "Saved !", 4), sfsipluscollapse("#sfsi_plus_save4"),
                    sfsi_plus_showPreviewCounts()) : (sfsiplus_showErrorSuc("error", "Your settings could not be saved. Please see <a href='https://www.ultimatelysocial.com/saving-settings-is-not-working/' style='color:green;font-weight: bold;'>here</a>  what could be the possible issue", 4),
                    global_error = 1), sfsiplus_afterLoad();
                /* console.log(_countData); */
                if ('yes' == _countData.sfsi_plus_display_counts) {
                    SFSI('.tab8 .sfsi_premium_responsive_icon_option_li .sfsiplus_tab_3_icns.sfsiplus_shwthmbfraftr').show();
                    sfsi_premium_responsive_toggle_count();
                    // SFSI('.tab8 input[name="sfsi_plus_responsive_icon_show_count"]:checked').parent().find('.radio').click();
                } else {
                    SFSI('.tab8 .sfsi_premium_responsive_icon_option_li .sfsiplus_tab_3_icns.sfsiplus_shwthmbfraftr').hide();
                    // if(SFSI('.tab8 input[name="sfsi_plus_responsive_icon_show_count"]:checked').val()=="yes"){
                    // SFSI('.tab8 input[name="sfsi_plus_responsive_icon_show_count"]:checked').parent().find('.radio').click();
                    // }
                    /* console.log('hiding'); */
                    sfsi_premium_responsive_toggle_count();

                }
            }
        },
        error: function (jqXHR, textStatus) {
            if (textStatus === 'timeout') {
                global_error = 1,
                    sfsiplus_showErrorSuc("error", "Your settings could not be saved. Please see <a href='https://www.ultimatelysocial.com/saving-settings-is-not-working/' style='color:green;font-weight: bold;'>here</a>  what could be the possible issue", 4)
            }
            sfsiplus_afterLoad();
            //console.log(textStatus,jqXHR);
        },
    }), s;
}

//Murt Code Start
// if(document.querySelector('input[name="sfsi_plus_loadjscript"]:checked').value == "no"){
// 	document.getElementById("afterdisable").style.display = "block";
// 	}else if(document.querySelector('input[name="sfsi_plus_loadjscript"]:checked').value == "yes"){
// 		document.getElementById("afterdisable").style.display = "none";
// 	}
// function sfsi_show_div(a){
// 	if(a=="no"){
// 		document.getElementById("afterdisable").style.display = "block";
// 	}else{
// 		document.getElementById("afterdisable").style.display = "none";
// 	}
// }
function sfsi_premium_upload_update_javascript_libraries(e) {

    e.preventDefault();
    var send_attachment_bkp = wp.media.editor.send.attachment;

    var frame = wp.media({
        title: 'Upload JScript libraries',
        button: {
            text: 'Upload'
        },
        multiple: false // Set to true to allow multiple files to be selected
    });
    /* console.log(frame); */
    frame.on('select', function () {

        // Get media attachment details from the frame state
        var attachment = frame.state().get('selection').first().toJSON();
        var url = attachment.url.split("/");
        var fileName = url[url.length - 1];
        var fileArr = fileName.split(".");
        var fileType = fileArr[fileArr.length - 1];
        if (fileType != undefined && fileType == 'js') {
            /* console.log(attachment.url); */
            SFSI(e.target).parents('.sfsi_plus_other_btn').find('input[name="sfsi_plus_jscript_fileName[]"]').val(attachment.url);
            SFSI(e.target).parents('.sfsi_plus_other_btn').find('.sfsi_premium_file_name_display').text(fileName);

            wp.media.editor.send.attachment = send_attachment_bkp;
        } else {
            alert("Only Js files are allowed to upload");
            frame.open();
        }
    }.bind(e));

    // Finally, open the modal on click
    frame.open();
}

function sfsi_premium_upload_javascript_libraries(e) {

    e.preventDefault();
    var send_attachment_bkp = wp.media.editor.send.attachment;

    var frame = wp.media({
        title: 'Upload JScript libraries',
        button: {
            text: 'Upload'
        },
        multiple: false // Set to true to allow multiple files to be selected
    });

    frame.on('select', function () {

        // Get media attachment details from the frame state
        var attachment = frame.state().get('selection').first().toJSON();
        var url = attachment.url.split("/");
        var fileName = url[url.length - 1];
        var fileArr = fileName.split(".");
        var fileType = fileArr[fileArr.length - 1];

        if (fileType != undefined && fileType == 'js') {
            jQuery('input[name="sfsi_plus_jscript_fileName[]"]').val(attachment.url);
            jQuery('.sfsi_premium_file_name_display').text(fileName).removeClass("hide").addClass('show');
            jQuery('.sfsi_plus_other_btn').removeClass("hide").addClass('show');
            jQuery('.sfsi_premium_upload_btn').removeClass("show").addClass('hide');
            wp.media.editor.send.attachment = send_attachment_bkp;
        } else {
            alert("Only Js files are allowed to upload");
            frame.open();
        }
    }.bind(e));

    // Finally, open the modal on click
    frame.open();
}


function sfsi_premium_upload_more_javascript_libraries(e) {
    e.preventDefault();
    var send_attachment_bkp = wp.media.editor.send.attachment;
    var frame = wp.media({
        title: 'Upload more JScript libraries',
        button: {
            text: 'Upload'
        },
        multiple: false // Set to true to allow multiple files to be selected
    });

    frame.on('select', function () {

        // Get media attachment details from the frame state
        var attachment = frame.state().get('selection').first().toJSON();

        var url = attachment.url.split("/");
        var fileName = url[url.length - 1];
        var fileArr = fileName.split(".");
        var fileType = fileArr[fileArr.length - 1];

        if (fileType != undefined && fileType == 'js') {
            var x = SFSI(".sfsi_plus_other_btn:first").clone();
            x.find('.sfsi_premium_file_name_display').text(fileName);
            x.find('input[name="sfsi_plus_jscript_fileName[]"]').val(attachment.url);
            SFSI(e.target).parents('.sfsi_plus_other_btn').parent().append(x);
            wp.media.editor.send.attachment = send_attachment_bkp;
        } else {
            alert("Only Js files are allowed to upload");
            frame.open();
        }
    }.bind(e));

    // Finally, open the modal on click
    frame.open();
}


function sfsi_premium_delete_javascript_libraries(e) {
    e.preventDefault();
    var x = SFSI(".sfsi_plus_other_btn");
    /* 	console.log(x.length ,SFSI(e.target)); */
    if (x.length > 1) {
        SFSI(e.target).parents('.sfsi_plus_other_btn').remove();
    } else {
        x.removeClass('show').addClass('hide');
        SFSI(".sfsi_premium_upload_btn").removeClass('hide').addClass('show');
    }
}


//Murt Code End
function sfsi_plus_update_step5() {

    var nonce = SFSI("#sfsi_plus_save5").attr("data-nonce");
    // sfsi_plus_update_step3();
    var s = sfsi_plus_validationStep5();
    if (!s) return global_error = 1, !1;
    sfsiplus_beForeLoad();

    var i = SFSI("input[name='sfsi_plus_icons_size']").val(),
        e = SFSI("input[name='sfsi_plus_icons_perRow']").val(),
        t = SFSI("input[name='sfsi_plus_icons_spacing']").val(),
        verticalSpace = SFSI("input[name='sfsi_plus_icons_verical_spacing']").val(),
        n = SFSI("#sfsi_plus_icons_Alignment").val(),
        na = SFSI("#sfsi_plus_horizontal_verical_Alignment").val(),
        ca = SFSI("#sfsi_plus_icons_Alignment_via_shortcode").val(),
        ba = SFSI("#sfsi_plus_icons_Alignment_via_widget").val(),
        am = SFSI("#sfsi_plus_mobile_horizontal_verical_Alignment").val(),
        mc = SFSI("#sfsi_plus_mobile_icons_Alignment_via_widget").val(),
        ma = SFSI("#sfsi_plus_mobile_icons_Alignment_via_shortcode").val(),
        mb = SFSI("#sfsi_plus_mobile_icons_Alignment").val(),
        eq = SFSI("input[name='sfsi_plus_mobile_icons_perRow']").val(),

        followicon = SFSI("#sfsi_plus_follow_icons_language").val(),
        facebookicon = SFSI("#sfsi_plus_facebook_icons_language").val(),
        youtubeicon = SFSI("#sfsi_plus_youtube_icons_language").val(),
        twittericon = SFSI("#sfsi_plus_twitter_icons_language").val(),
        linkedinicon = SFSI("#sfsi_plus_linkedin_icons_language").val(),


        iaa = SFSI("input[name='sfsi_plus_tooltip_border_Color']").val(),
        ia = SFSI("input[name='sfsi_plus_tooltip_Color']").val(),
        mcc = SFSI("#sfsi_plus_tooltip_alighn").val(),
        lang = SFSI("#sfsi_plus_icons_language").val(),
        o = SFSI("input[name='sfsi_plus_icons_ClickPageOpen']:checked").val(),
        noop = SFSI("input[name='sfsi_plus_icons_AddNoopener']:checked").val(),
        a = SFSI("input[name='sfsi_plus_icons_float']:checked").val(),
        dsb = SFSI("input[name='sfsi_plus_disable_floaticons']:checked").val(),
        dsbv = SFSI("input[name='sfsi_plus_disable_viewport']:checked").val(),
        r = SFSI("#sfsi_plus_icons_floatPosition").val(),
        c = SFSI("input[name='sfsi_plus_icons_stick']:checked").val();

    var arrDesktopIconOrders = _sfsi_plus_order_icons.sfsi_premium_get_icon_order('plus_share_icon_order');
    var arrMobileIconOrders = _sfsi_plus_order_icons.sfsi_premium_get_icon_order('plus_share_icon_mobile_order');

    var orderIconsData = {};
    orderIconsData.data = arrDesktopIconOrders;

    var orderMobileIconsData = {};
    orderMobileIconsData.data = arrMobileIconOrders;

    h = new Array();

    SFSI(".sfsiplus_custom_iconOrder").each(function () {
        h.push({
            order: SFSI(this).attr("data-index"),
            ele: SFSI(this).attr("element-id")
        });
    });

    var sfsi_plus_mobile_icons_order_setting = SFSI("input[name='sfsi_plus_mobile_icons_order_setting']:checked").val();

    var v = 1 == SFSI("input[name='sfsi_plus_rss_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_rss_MouseOverText']").val(),
        g = 1 == SFSI("input[name='sfsi_plus_email_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_email_MouseOverText']").val(),
        k = 1 == SFSI("input[name='sfsi_plus_twitter_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_twitter_MouseOverText']").val(),
        y = 1 == SFSI("input[name='sfsi_plus_facebook_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_facebook_MouseOverText']").val(),
        w = 1 == SFSI("input[name='sfsi_plus_linkedIn_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_linkedIn_MouseOverText']").val(),
        x = 1 == SFSI("input[name='sfsi_plus_youtube_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_youtube_MouseOverText']").val(),
        C = 1 == SFSI("input[name='sfsi_plus_pinterest_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_pinterest_MouseOverText']").val(),
        insD = 1 == SFSI("input[name='sfsi_plus_instagram_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_instagram_MouseOverText']").val(),
        bsky = 1 == SFSI("input[name='sfsi_plus_bluesky_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_bluesky_MouseOverText']").val(),
        trds = 1 == SFSI("input[name='sfsi_plus_threads_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_threads_MouseOverText']").val(),
        D = 1 == SFSI("input[name='sfsi_plus_houzz_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_houzz_MouseOverText']").val(),
        snapchat = 1 == SFSI("input[name='sfsi_plus_snapchat_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_snapchat_MouseOverText']").val(),
        whatsapp = 1 == SFSI("input[name='sfsi_plus_whatsapp_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_whatsapp_MouseOverText']").val(),
        skype = 1 == SFSI("input[name='sfsi_plus_skype_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_skype_MouseOverText']").val(),
        vimeo = 1 == SFSI("input[name='sfsi_plus_vimeo_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_vimeo_MouseOverText']").val(),
        soundcloud = 1 == SFSI("input[name='sfsi_plus_soundcloud_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_soundcloud_MouseOverText']").val(),
        yummly = 1 == SFSI("input[name='sfsi_plus_yummly_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_yummly_MouseOverText']").val(),
        flickr = 1 == SFSI("input[name='sfsi_plus_flickr_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_flickr_MouseOverText']").val(),
        reddit = 1 == SFSI("input[name='sfsi_plus_reddit_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_reddit_MouseOverText']").val(),
        tumblr = 1 == SFSI("input[name='sfsi_plus_tumblr_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_tumblr_MouseOverText']").val(),
        mastodon = 1 == SFSI("input[name='sfsi_plus_mastodon_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_mastodon_MouseOverText']").val(),
        ria = 1 == SFSI("input[name='sfsi_plus_ria_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_ria_MouseOverText']").val(),
        inha = 1 == SFSI("input[name='sfsi_plus_inha_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_inha_MouseOverText']").val(),

        fbmessenger = 1 == SFSI("input[name='sfsi_plus_fbmessenger_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_fbmessenger_MouseOverText']").val(),
        gab = 1 == SFSI("input[name='sfsi_plus_gab_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_gab_MouseOverText']").val(),
        mix = 1 == SFSI("input[name='sfsi_plus_mix_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_mix_MouseOverText']").val(),
        ok = 1 == SFSI("input[name='sfsi_plus_ok_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_ok_MouseOverText']").val(),
        vk = 1 == SFSI("input[name='sfsi_plus_vk_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_vk_MouseOverText']").val(),
        telegram = 1 == SFSI("input[name='sfsi_plus_telegram_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_telegram_MouseOverText']").val(),
        wechat = 1 == SFSI("input[name='sfsi_plus_wechat_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_wechat_MouseOverText']").val(),
        xing = 1 == SFSI("input[name='sfsi_plus_xing_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_xing_MouseOverText']").val(),
        copylink = 1 == SFSI("input[name='sfsi_plus_copylink_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_copylink_MouseOverText']").val(),

        U = 1 == SFSI("input[name='sfsi_plus_share_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_share_MouseOverText']").val(),
        O = {};
    SFSI("input[name='sfsi_plus_custom_MouseOverTexts[]']").each(function () {
        O[SFSI(this).attr("file-id")] = this.value;
    });

    var chkEle = SFSI('[data-cl="sfsi_premium_custom_social_data_post_types"]');
    var sfsi_custom_social_data_post_types_data = chkEle.serialize();

    var twt_aboutPageTxt = SFSI("textarea[name='sfsi_plus_twitter_aboutPageText']").val();
    var twt_aboutPage = 1 == SFSI("input[name='sfsi_plus_twitter_aboutPage']").prop("disabled") ? "" : SFSI("input[name='sfsi_plus_twitter_aboutPage']:checked").val();
    var twtAddCard = SFSI("input[name='sfsi_plus_twitter_twtAddCard']:checked").val();
    var twtCardType = SFSI("#twitterCardType").val();
    var twtCardThandle = SFSI("input[name='sfsi_plus_twitter_card_twitter_handle']").val();


    /****************************** Set global text & picture STARTS ****************************************/

    var sfsiSocialMediaImage = SFSI("input[name=sfsi-social-media-image]").val();

    var sfsiSocialtTitleTxt = SFSI("textarea[name='social_fbGLTw_title_textarea']").val();
    var sfsiSocialDescription = SFSI("textarea[name='social_fbGLTw_description_textarea']").val();

    var sfsiSocialPinterestImage = SFSI("input[name='sfsi-social-pinterest-image']").val();
    var sfsiSocialPinterestDesc = SFSI("textarea[name='social_pinterest_description_textarea']").val();

    var sfsiSocialTwitterDesc = SFSI("textarea[name='social_twitter_description_textarea']").val();

    /****************************** Set global text & picture CLOSES ****************************************/


    /****************************** URL Shortner handling STARTS ****************************************/

        //var sfsi_urlShortnerSetting 	= SFSI("input[name='sfsi_plus_url_shortner_setting']:checked").val();
    var sfsi_urlShortnerTypeSetting = SFSI("input[name='sfsi_plus_url_shorting_api_type_setting']:checked").val();

    //sfsi_urlShortnerTypeSetting = (sfsi_urlShortnerSetting=='yes')? sfsi_urlShortnerTypeSetting:'no';

    // List of social icons for which url shortning to be implemented
    var chkEleList = SFSI('[data-usl="sfsi_premium_url_shortner_icons_names_list"]');
    var sfsi_premium_url_shortner_icons_names_list = chkEleList.serialize();


    var sfsi_plus_url_shortner_bitly_key = SFSI.trim(SFSI("input[name='sfsi_plus_url_shortner_bitly_key']").val());

    /****************************** URL Shortner handling CLOSES ****************************************/

        // Get setting if user wants to allow our plugin to add meta tags, if disabled = no, our plugin will add meta tags
    var sfsi_plus_disable_usm_og_meta_tags = SFSI("input[name='sfsi_plus_disable_usm_og_meta_tags']:checked").val();

    var sfsi_plus_custom_css = SFSI("textarea[name='sfsi_plus_custom_css']").val();
    var sfsi_plus_custom_admin_css = SFSI("textarea[name='sfsi_plus_custom_admin_css']").val();

    var sfsi_plus_cumulative_count_active = SFSI("input[name='sfsi_plus_cumulative_count_active']:checked").val();

    var sfsi_plus_http_cumulative_count_active = SFSI("input[name='sfsi_plus_http_cumulative_count_active']:checked").val();
    var sfsi_plus_http_cumulative_count_previous_domain = SFSI("input[name='sfsi_plus_http_cumulative_count_previous_domain']").val();
    var sfsi_plus_http_cumulative_count_new_domain = SFSI("input[name='sfsi_plus_http_cumulative_count_new_domain']").val();

    var sfsi_plus_facebook_cumulative_count_active = SFSI("input[name='sfsi_plus_facebook_cumulative_count_active']:checked").val() || 'no';
    var sfsi_plus_pinterest_cumulative_count_active = SFSI("input[name='sfsi_plus_pinterest_cumulative_count_active']:checked").val() || 'no';

    var sfsi_plus_loadjquery = SFSI("input[name='sfsi_plus_loadjquery']:checked").val();
    //Murt Code Start

    var sfsi_plus_loadjscript = SFSI("input[name='sfsi_plus_loadjscript']:checked").val();
    var sfsi_plus_counts_without_slash = SFSI("input[name='sfsi_plus_counts_without_slash']:checked").val() || 'no';

    var sfsi_premium_jscriptFileName = SFSI("input[name='sfsi_plus_jscript_fileName[]']").map(function () {
        return SFSI(this).val();
    }).get();
    // var morejscriptFileName = SFSI("input[name='sfsi_plus_more_jscript_fileName']").val();

    //Murt Code End
    var se = SFSI("input[name='sfsi_plus_icons_suppress_errors']:checked").val();
    var nf = SFSI("input[name='sfsi_plus_nofollow_links']:checked").val();
    var sfsi_plus_icon_hover_show_pinterest = 'no' !== SFSI("select[name='sfsi_plus_icon_hover_type']").val() ? 'yes' : 'no';
    var sfsi_plus_icon_hover_type = SFSI("select[name='sfsi_plus_icon_hover_type']").val();
    var sfsi_plus_icon_hover_language = SFSI("select[name='sfsi_plus_icon_hover_language']").val();
    var sfsi_plus_icon_hover_placement = SFSI("select[name='sfsi_plus_icon_hover_placement']").val();
    var sfsi_plus_icon_hover_device = SFSI("select[name='sfsi_plus_icon_hover_device']").val();
    var sfsi_plus_icon_hover_on_all_pages = SFSI("select[name='sfsi_plus_icon_hover_on_all_pages']").val();
    var sfsi_plus_icon_hover_width = SFSI('input[name="sfsi_plus_icon_hover_width"]').val();
    var sfsi_plus_icon_hover_height = SFSI('input[name="sfsi_plus_icon_hover_height"]').val();
    var sfsi_plus_icon_hover_desktop = 'no';
    var sfsi_plus_icon_hover_mobile = 'no';
    if (sfsi_plus_icon_hover_device === "desktop") {
        sfsi_plus_icon_hover_desktop = 'yes';
    } else if (sfsi_plus_icon_hover_device === "mobile") {
        sfsi_plus_icon_hover_mobile = 'yes';
    } else if (sfsi_plus_icon_hover_device === "mobile-desktop") {
        sfsi_plus_icon_hover_desktop = 'yes';
        sfsi_plus_icon_hover_mobile = 'yes';
    }
    var includekeywords = [];

    includekeywords = SFSI("input[name='sfsi_plus_icon_hover_include_urlKeywords[]']").map(function () {
        return SFSI(this).val();
    }).get();
    var excludekeywords = [];
    excludekeywords = SFSI("input[name='sfsi_plus_icon_hover_exclude_urlKeywords[]']").map(function () {
        return SFSI(this).val();
    }).get();

    var sfsi_premium_static_path = SFSI("input[name='sfsi_premium_static_path']").val();
    var sfsi_premium_featured_image_as_og_image = SFSI("input[name='sfsi_premium_featured_image_as_og_image']:checked").val() || "no";
    // var sfsi_urlShortnerTypeSetting = SFSI("input[name='sfsi_plus_url_shorting_api_type_setting']:checked").val();

    var sfsi_premium_pinterest_sharing = SFSI("input[name='sfsi_premium_pinterest_sharing_texts_and_pics']:checked").val();
    var sfsi_premium_pinterest_placement = SFSI("input[name='sfsi_premium_pinterest_placements']:checked").val();
    var sfsi_plus_mobile_open_type_setting = SFSI("input[name='sfsi_plus_mobile_open_type_setting']:checked").val();
    var sfsi_plus_icons_mobile_ClickPageOpen = SFSI("input[name='sfsi_plus_icons_mobile_ClickPageOpen']:checked").val();

    var sfsi_plus_hook_priority_value = SFSI("input[name='sfsi_plus_hook_priority_value']").val();
    var sfsi_plus_change_number_format = SFSI("input[name='sfsi_plus_change_number_format']:checked").val();
    var sfsi_plus_disable_promotions = SFSI("input[name='sfsi_plus_disable_promotions']:checked").val();

    var T = {
        action: "plus_updateSrcn5",
        sfsi_plus_icons_size: i,
        sfsi_plus_icons_Alignment: n,
        sfsi_plus_horizontal_verical_Alignment: na,
        sfsi_plus_icons_Alignment_via_shortcode: ca,
        sfsi_plus_icons_Alignment_via_widget: ba,
        sfsi_plus_icons_perRow: e,

        sfsi_plus_follow_icons_language: followicon,
        sfsi_plus_facebook_icons_language: facebookicon,
        sfsi_plus_youtube_icons_language: youtubeicon,
        sfsi_plus_twitter_icons_language: twittericon,
        sfsi_plus_linkedin_icons_language: linkedinicon,
        sfsi_plus_icons_language: lang,
        sfsi_plus_icons_spacing: t,
        sfsi_plus_icons_verical_spacing: verticalSpace,
        sfsi_plus_icons_ClickPageOpen: o,
        sfsi_plus_icons_AddNoopener: noop,
        sfsi_plus_icons_float: a,
        sfsi_plus_disable_floaticons: dsb,
        sfsi_plus_disable_viewport: dsbv,
        sfsi_plus_icons_floatPosition: r,
        sfsi_plus_icons_stick: c,
        sfsi_plus_rss_MouseOverText: v,
        sfsi_plus_email_MouseOverText: g,
        sfsi_plus_twitter_MouseOverText: k,
        sfsi_plus_facebook_MouseOverText: y,
        sfsi_plus_youtube_MouseOverText: x,
        sfsi_plus_linkedIn_MouseOverText: w,
        sfsi_plus_pinterest_MouseOverText: C,
        sfsi_plus_share_MouseOverText: U,
        sfsi_plus_instagram_MouseOverText: insD,
        sfsi_plus_bluesky_MouseOverText: bsky,
        sfsi_plus_threads_MouseOverText: trds,
        sfsi_plus_houzz_MouseOverText: D,
        sfsi_plus_custom_MouseOverTexts: O,

        // Order of icons data STARTS //
        sfsi_plus_mobile_icons_order_setting: sfsi_plus_mobile_icons_order_setting,
        sfsi_order_icons_desktop: orderIconsData,
        sfsi_order_icons_mobile: orderMobileIconsData,


        // Order of icons data CLOSES //

        sfsi_plus_mobile_icon_alignment_setting: SFSI("input[name='sfsi_plus_mobile_icon_alignment_setting']:checked").val() || 'no',
        sfsi_plus_mobile_horizontal_verical_Alignment: am,
        sfsi_plus_mobile_icons_Alignment_via_widget: mc,
        sfsi_plus_mobile_icons_Alignment_via_shortcode: ma,
        sfsi_plus_mobile_icons_Alignment: mb,
        sfsi_plus_mobile_icons_perRow: eq,
        sfsi_plus_mobile_icon_setting: SFSI("input[name='sfsi_plus_mobile_icon_setting']:checked").val(),
        sfsi_plus_icons_mobilesize: SFSI("input[name='sfsi_plus_icons_mobilesize']").val(),
        sfsi_plus_icons_mobilespacing: SFSI("input[name='sfsi_plus_icons_mobilespacing']").val(),
        sfsi_plus_icons_verical_mobilespacing: SFSI("input[name='sfsi_plus_icons_verical_mobilespacing']").val(),


        sfsi_plus_snapchat_MouseOverText: snapchat,

        sfsi_plus_whatsapp_MouseOverText: whatsapp,

        sfsi_plus_skype_MouseOverText: skype,

        sfsi_plus_vimeo_MouseOverText: vimeo,

        sfsi_plus_soundcloud_MouseOverText: soundcloud,

        sfsi_plus_yummly_MouseOverText: yummly,

        sfsi_plus_flickr_MouseOverText: flickr,

        sfsi_plus_reddit_MouseOverText: reddit,

        sfsi_plus_tumblr_MouseOverText: tumblr,

        sfsi_plus_fbmessenger_MouseOverText: fbmessenger,
        sfsi_plus_gab_MouseOverText: gab,
        sfsi_plus_mix_MouseOverText: mix,
        sfsi_plus_ok_MouseOverText: ok,
        sfsi_plus_telegram_MouseOverText: telegram,
        sfsi_plus_vk_MouseOverText: vk,
        sfsi_plus_weibo_MouseOverText: wechat,
        sfsi_plus_xing_MouseOverText: xing,
        sfsi_plus_copylink_MouseOverText: copylink,
        sfsi_plus_mastodon_MouseOverText: mastodon,
        sfsi_plus_ria_MouseOverText: ria,
        sfsi_plus_inha_MouseOverText: inha,

        sfsi_plus_Facebook_linking: SFSI("input[name='sfsi_plus_Facebook_linking']:checked").val(),
        sfsi_plus_facebook_linkingcustom_url: SFSI("input[name='sfsi_plus_facebook_linkingcustom_url']").val(),
        sfsi_plus_tooltip_border_Color: iaa,
        sfsi_plus_tooltip_alighn: mcc,
        sfsi_plus_tooltip_Color: ia,

        sfsi_custom_social_data_post_types_data: sfsi_custom_social_data_post_types_data,

        sfsi_plus_social_sharing_options: SFSI("input[name='sfsi_plus_social_sharing_options']:checked").val(),
        sfsiSocialMediaImage: sfsiSocialMediaImage,
        sfsiSocialtTitleTxt: sfsiSocialtTitleTxt,
        sfsiSocialDescription: sfsiSocialDescription,

        sfsiSocialPinterestImage: sfsiSocialPinterestImage,
        sfsiSocialPinterestDesc: sfsiSocialPinterestDesc,
        sfsiSocialTwitterDesc: sfsiSocialTwitterDesc,

        sfsi_plus_disable_usm_og_meta_tags: sfsi_plus_disable_usm_og_meta_tags,

        sfsi_plus_twitter_aboutPageText: twt_aboutPageTxt,
        sfsi_plus_twitter_twtAddCard: twtAddCard,
        sfsi_plus_twitter_twtCardType: twtCardType,
        sfsi_plus_twitter_card_twitter_handle: twtCardThandle,

        sfsi_premium_url_shortner_icons_names_list: sfsi_premium_url_shortner_icons_names_list,
        sfsi_plus_url_shorting_api_type_setting: sfsi_urlShortnerTypeSetting,
        sfsi_plus_url_shortner_bitly_key: sfsi_plus_url_shortner_bitly_key,

        sfsi_plus_custom_css: sfsi_plus_custom_css,
        sfsi_plus_custom_admin_css: sfsi_plus_custom_admin_css,

        sfsi_plus_cumulative_count_active: sfsi_plus_cumulative_count_active,
        sfsi_plus_http_cumulative_count_active: sfsi_plus_http_cumulative_count_active,
        sfsi_plus_http_cumulative_count_previous_domain: sfsi_plus_http_cumulative_count_previous_domain,
        sfsi_plus_http_cumulative_count_new_domain: sfsi_plus_http_cumulative_count_new_domain,
        sfsi_plus_facebook_cumulative_count_active: sfsi_plus_facebook_cumulative_count_active,
        sfsi_plus_pinterest_cumulative_count_active: sfsi_plus_pinterest_cumulative_count_active,

        sfsi_plus_loadjquery: sfsi_plus_loadjquery,

        sfsi_plus_icons_suppress_errors: se,
        sfsi_plus_nofollow_links: nf,
        sfsi_premium_static_path: sfsi_premium_static_path,

        sfsi_plus_loadjscript: sfsi_plus_loadjscript,
        sfsi_premium_jscriptFileName: sfsi_premium_jscriptFileName,
        sfsi_premium_featured_image_as_og_image: sfsi_premium_featured_image_as_og_image,
        sfsi_premium_pinterest_sharing_texts_and_pics: sfsi_premium_pinterest_sharing,
        sfsi_premium_pinterest_placements: sfsi_premium_pinterest_placement,
        sfsi_plus_mobile_open_type_setting: sfsi_plus_mobile_open_type_setting,
        sfsi_plus_icons_mobile_ClickPageOpen: sfsi_plus_icons_mobile_ClickPageOpen,
        sfsi_plus_counts_without_slash: sfsi_plus_counts_without_slash,
        sfsi_plus_hook_priority_value: sfsi_plus_hook_priority_value,
        sfsi_plus_change_number_format: sfsi_plus_change_number_format,
        sfsi_plus_disable_promotions: sfsi_plus_disable_promotions,
        nonce: nonce
    };
    SFSI.ajax({
        url: sfsi_premium_ajax_object.ajax_url,
        type: "post",
        data: T,
        dataType: "json",
        timeout: 6000,
        async: !0,
        success: function (s) {
            if (s == "wrong_nonce") {
                sfsiplus_showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 5);
                global_error = 1;
                sfsiplus_afterLoad();
            } else {
                "success" == s ? (sfsiplus_showErrorSuc("success", "Saved !", 5), sfsipluscollapse("#sfsi_plus_save5")) : (global_error = 1,
                    sfsiplus_showErrorSuc("error", "Your settings could not be saved. Please see <a href='https://www.ultimatelysocial.com/saving-settings-is-not-working/' style='color:green;font-weight: bold;'>here</a>  what could be the possible issue", 5)), sfsiplus_afterLoad();
            }
        },
        error: function (jqXHR, textStatus) {
            if (textStatus === 'timeout') {
                global_error = 1,
                    sfsiplus_showErrorSuc("error", "Your settings could not be saved. Please see <a href='https://www.ultimatelysocial.com/saving-settings-is-not-working/' style='color:green;font-weight: bold;'>here</a>  what could be the possible issue", 5)
            }
            sfsiplus_afterLoad();
            //console.log(textStatus,jqXHR);
        },
    });
}

function sfsi_plus_update_step6() {
    var nonce = SFSI("#sfsi_plus_save6").attr("data-nonce");
    sfsiplus_beForeLoad();
    var s = SFSI("input[name='sfsi_plus_show_Onposts']:checked").val(),
        i = SFSI("input[name='sfsi_plus_textBefor_icons']").val(),
        e = SFSI("#sfsi_plus_icons_alignment").val(),
        t = SFSI("#sfsi_plus_icons_DisplayCounts").val(),
        n = {
            action: "plus_updateSrcn6",
            sfsi_plus_show_Onposts: s,
            sfsi_plus_icons_DisplayCounts: t,
            sfsi_plus_icons_alignment: e,
            sfsi_plus_textBefor_icons: i,
            nonce: nonce
        };
    SFSI.ajax({
        url: sfsi_premium_ajax_object.ajax_url,
        type: "post",
        data: n,
        dataType: "json",
        async: !0,
        success: function (s) {
            if (s == "wrong_nonce") {
                sfsiplus_showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 6);
                global_error = 1;
                sfsiplus_afterLoad();
            } else {
                "success" == s ? (sfsiplus_showErrorSuc("success", "Saved !", 6), sfsipluscollapse("#sfsi_plus_save6")) : (global_error = 1,
                    sfsiplus_showErrorSuc("error", "Your settings could not be saved. Please see <a href='https://www.ultimatelysocial.com/saving-settings-is-not-working/' style='color:green;font-weight: bold;'>here</a>  what could be the possible issue", 6)), sfsiplus_afterLoad();
            }
        },

    });
}

function sfsi_plus_update_step7() {
    var nonce = SFSI("#sfsi_plus_save7").attr("data-nonce");
    var s = sfsi_plus_validationStep7();
    if (!s) return global_error = 1, !1;
    sfsiplus_beForeLoad();

    var i = SFSI("textarea[name='sfsi_plus_popup_text']").val(),
        e = SFSI("#sfsi_plus_popup_font option:selected").val(),
        fs = SFSI("#sfsi_plus_popup_fontStyle").val(),
        t = SFSI("input[name='sfsi_plus_popup_fontColor']").val(),
        n = SFSI("input[name='sfsi_plus_popup_fontSize']").val(),
        o = SFSI("input[name='sfsi_plus_popup_background_color']").val(),
        a = SFSI("input[name='sfsi_plus_popup_border_color']").val(),
        r = SFSI("input[name='sfsi_plus_popup_border_thickness']").val(),
        c = SFSI("input[name='sfsi_plus_popup_border_shadow']:checked").val(),
        p = SFSI("input[name='sfsi_plus_Show_popupOn']:checked").val(),
        pbp = SFSI("input[name='sfsi_plus_Show_popupOn_somepages_blogpage']:checked").val(),
        psp = SFSI("input[name='sfsi_plus_Show_popupOn_somepages_selectedpage']:checked").val(),
        _ = [];

    SFSI("#sfsi_plus_Show_popupOn_PageIDs :selected").each(function (s, i) {
        _[s] = SFSI(i).val();
    });

    var l = SFSI('input[name="sfsi_plus_Shown_pop[]"]:checked').map(function () {
        return this.value;
    }).get();


    var contOfPopup = SFSI("input[name='sfsi_plus_popup_type_iconsOrForm']:checked").val();

    var sfsi_plus_popup_show_on_desktop = SFSI("input[name='sfsi_plus_popup_show_on_desktop']:checked").val();
    var sfsi_plus_popup_show_on_mobile = SFSI("input[name='sfsi_plus_popup_show_on_mobile']:checked").val();

    var sfsi_plus_Hide_popupOnScroll = SFSI("input[name='sfsi_plus_Hide_popupOnScroll']").val();
    var sfsi_plus_Hide_popupOn_OutsideClick = SFSI("input[name='sfsi_plus_Hide_popupOn_OutsideClick']").val();

    var S = SFSI("input[name='sfsi_plus_Shown_popupOnceTime']").val(),
        u = SFSI("#sfsi_plus_Shown_popuplimitPerUserTime").val(),
        f = {
            action: "plus_updateSrcn7",
            sfsi_plus_popup_text: i,
            sfsi_plus_popup_font: e,
            sfsi_plus_popup_fontColor: t,
            sfsi_plus_popup_fontSize: n,
            sfsi_plus_popup_fontStyle: fs,
            sfsi_plus_popup_background_color: o,
            sfsi_plus_popup_border_color: a,
            sfsi_plus_popup_border_thickness: r,
            sfsi_plus_popup_border_shadow: c,
            sfsi_plus_Show_popupOn: p,
            sfsi_plus_Show_popupOn_somepages_blogpage: pbp,
            sfsi_plus_Show_popupOn_somepages_selectedpage: psp,
            sfsi_plus_Show_popupOn_PageIDs: _,
            sfsi_plus_Shown_pop: l,
            sfsi_plus_Shown_popupOnceTime: S,
            sfsi_plus_Shown_popuplimitPerUserTime: u,

            sfsi_plus_Hide_popupOnScroll: sfsi_plus_Hide_popupOnScroll,
            sfsi_plus_Hide_popupOn_OutsideClick: sfsi_plus_Hide_popupOn_OutsideClick,

            sfsi_plus_popup_limit: SFSI('input[name="sfsi_plus_popup_limit"]:checked').val(),
            sfsi_plus_popup_limit_count: SFSI('input[name="sfsi_plus_popup_limit_count"]').val(),
            sfsi_plus_popup_limit_type: SFSI('select[name="sfsi_plus_popup_limit_type"]').val(),
            sfsi_plus_popup_type_iconsOrForm: contOfPopup,
            sfsi_plus_popup_show_on_desktop: sfsi_plus_popup_show_on_desktop,
            sfsi_plus_popup_show_on_mobile: sfsi_plus_popup_show_on_mobile,
            nonce: nonce
        };
    SFSI.ajax({
        url: sfsi_premium_ajax_object.ajax_url,
        type: "post",
        data: f,
        dataType: "json",
        timeout: 6000,
        async: !0,
        success: function (s) {
            if (s == "wrong_nonce") {
                sfsiplus_showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 7);
                sfsiplus_afterLoad();
            } else {
                "success" == s ? (sfsiplus_showErrorSuc("success", "Saved !", 7), sfsipluscollapse("#sfsi_plus_save7")) : sfsiplus_showErrorSuc("error", "Your settings could not be saved. Please see <a href='https://www.ultimatelysocial.com/saving-settings-is-not-working/' style='color:green;font-weight: bold;'>here</a>  what could be the possible issue", 7),
                    sfsiplus_afterLoad();
            }
        },
        error: function (jqXHR, textStatus) {
            if (textStatus === 'timeout') {
                global_error = 1,
                    sfsiplus_showErrorSuc("error", "Your settings could not be saved. Please see <a href='https://www.ultimatelysocial.com/saving-settings-is-not-working/' style='color:green;font-weight: bold;'>here</a>  what could be the possible issue", 7)
            }
            sfsiplus_afterLoad();
            //console.log(textStatus,jqXHR);
        }
    });
}


function sfsi_plus_update_step8() {
    var nonce = SFSI("#sfsi_plus_save8").attr("data-nonce");
    var s = sfsi_plus_validationStep7();
    s = true;
    if (!s) return global_error = 1, !1;
    sfsiplus_beForeLoad();
    if (SFSI("input[name='sfsi_plus_icon_hover_show_pinterest']:checked").val() == "yes") {
        SFSI('.sfsi_premium_pinterest_positions_section').show();
    } else {
        SFSI('.sfsi_premium_pinterest_positions_section').hide();

    }

    var i = SFSI("input[name='sfsi_plus_show_via_widget']:checked").val(),
        e = SFSI("input[name='sfsi_plus_float_on_page']:checked").val(),
        t = SFSI("input[name='sfsi_plus_float_page_position']:checked").val(),
        n = SFSI("input[name='sfsi_plus_place_item_manually']:checked").val(),
        rectP = SFSI("input[name='sfsi_plus_place_rectangle_icons_item_manually']:checked").val(),

        sfsi_plus_widget_horizontal_verical_Alignment = SFSI("#sfsi_plus_widget_horizontal_verical_Alignment").val(),
        sfsi_plus_widget_mobile_horizontal_verical_Alignment = SFSI("#sfsi_plus_widget_mobile_horizontal_verical_Alignment").val(),

        sfsi_plus_float_horizontal_verical_Alignment = SFSI("#sfsi_plus_float_horizontal_verical_Alignment").val(),
        sfsi_plus_float_mobile_horizontal_verical_Alignment = SFSI("input[name='sfsi_plus_mobile_float']:checked").val() == 'yes' ?
            SFSI("#sfsi_plus_float_mobile_horizontal_verical_Alignment").val() :
            sfsi_plus_float_horizontal_verical_Alignment,

        sfsi_plus_shortcode_horizontal_verical_Alignment = SFSI("#sfsi_plus_shortcode_horizontal_verical_Alignment").val(),
        sfsi_plus_shortcode_mobile_horizontal_verical_Alignment = SFSI("#sfsi_plus_shortcode_mobile_horizontal_verical_Alignment").val(),

        sfsi_plus_beforeafterposts_horizontal_verical_Alignment = SFSI("#sfsi_plus_beforeafterposts_horizontal_verical_Alignment").val(),
        sfsi_plus_beforeafterposts_mobile_horizontal_verical_Alignment = SFSI("#sfsi_plus_beforeafterposts_mobile_horizontal_verical_Alignment").val(),

        o = SFSI("input[name='sfsi_plus_show_item_onposts']:checked").val(),
        a = SFSI("input[name='sfsi_plus_display_button_type']:checked").val(),
        r = SFSI("input[name='sfsi_plus_post_icons_size']").val(),
        c = SFSI("input[name='sfsi_plus_post_icons_spacing']").val(),
        vSpace = SFSI("input[name='sfsi_plus_post_icons_vertical_spacing']").val(),
        p = SFSI("input[name='sfsi_plus_show_Onposts']:checked").val(),
        v = SFSI("input[name='sfsi_plus_textBefor_icons']").val(),
        x = SFSI("#sfsi_plus_icons_alignment").val(),
        z = SFSI("#sfsi_plus_icons_DisplayCounts").val(),
        b = SFSI("input[name='sfsi_plus_display_before_posts']:checked").val(),
        d = SFSI("input[name='sfsi_plus_display_after_posts']:checked").val(),
        f = SFSI("input[name='sfsi_plus_display_before_blogposts']:checked").val(),
        g = SFSI("input[name='sfsi_plus_display_after_blogposts']:checked").val(),
        rsub = SFSI("input[name='sfsi_plus_rectsub']:checked").val(),
        rfb = SFSI("input[name='sfsi_plus_rectfb']:checked").val(),
        rtwr = SFSI("input[name='sfsi_plus_recttwtr']:checked").val(),
        rpin = SFSI("input[name='sfsi_plus_rectpinit']:checked").val(),
        rlinkedin = SFSI("input[name='sfsi_plus_rectlinkedin']:checked").val(),
        rreddit = SFSI("input[name='sfsi_plus_rectreddit']:checked").val(),
        rfbshare = SFSI("input[name='sfsi_plus_rectfbshare']:checked").val(),
        rpostTypes = SFSI('select[name="sfsi_plus_choose_post_types"]').val(),
        rpostTypesResonsive = SFSI('select[name="sfsi_plus_choose_post_types_responsive"]').val(),
        rTaxonomies = SFSI('select[name="sfsi_plus_taxonomies_for_icons"]').val(),

        mr = SFSI("input[name='sfsi_plus_post_mobile_icons_size']").val(),
        mc = SFSI("input[name='sfsi_plus_post_mobile_icons_spacing']").val(),
        mvSpace = SFSI("input[name='sfsi_plus_post_mobile_icons_vertical_spacing']").val(),
        _ = [];
    console.log(rpostTypes, 'rpostTypes', rpostTypesResonsive);

    var
        sfsi_plus_display_on_all_icons = SFSI('select[name="sfsi_plus_display_on_all_icons"]').val(),
        sfsi_plus_display_rule_round_icon_widget = SFSI('input[name="sfsi_plus_display_rule_round_icon_widget"]:checked').val() || 'no',
        sfsi_plus_display_rule_round_icon_define_location = SFSI('input[name="sfsi_plus_display_rule_round_icon_define_location"]:checked').val() || 'no',
        sfsi_plus_display_rule_round_icon_shortcode = SFSI('input[name="sfsi_plus_display_rule_round_icon_shortcode"]:checked').val() || 'no',
        sfsi_plus_display_rule_round_icon_before_after_post = SFSI('input[name="sfsi_plus_display_rule_round_icon_before_after_post"]:checked').val() || 'no',
        sfsi_plus_display_rule_rect_icon_before_after_post = SFSI('input[name="sfsi_plus_display_rule_rect_icon_before_after_post"]:checked').val() || 'no',
        sfsi_plus_display_rule_responsive_icon_before_after_post = SFSI('input[name="sfsi_plus_display_rule_responsive_icon_before_after_post"]:checked').val() || 'no',
        sfsi_plus_display_rule_responsive_icon_shortcode = SFSI('input[name="sfsi_plus_display_rule_responsive_icon_shortcode"]:checked').val() || 'no',
        sfsi_plus_display_rule_icon_sticky_bar = SFSI('input[name="sfsi_plus_display_rule_icon_sticky_bar"]:checked').val() || 'no',
        sfsi_plus_display_rule_rect_icon_shortcode = SFSI('input[name="sfsi_plus_display_rule_rect_icon_shortcode"]:checked').val() || 'no';

    var mst = SFSI("input[name='sfsi_plus_icons_floatMargin_top']").val(),
        msb = SFSI("input[name='sfsi_plus_icons_floatMargin_bottom']").val(),
        msl = SFSI("input[name='sfsi_plus_icons_floatMargin_left']").val(),
        msr = SFSI("input[name='sfsi_plus_icons_floatMargin_right']").val(),
        makeIcon = SFSI("input[name='sfsi_plus_make_icon']:checked").val();

    var sfsi_plus_icon_hover_show_pinterest = SFSI("input[name='sfsi_plus_icon_hover_show_pinterest']:checked").length == 1 ? 'yes' : 'no';
    var sfsi_plus_icon_hover_type = SFSI("select[name='sfsi_plus_icon_hover_type']").val();
    var sfsi_plus_icon_hover_custom_icon_url = SFSI("input[name='sfsi_plus_icon_hover_custom_icon_url']").val();
    var sfsi_plus_icon_hover_language = SFSI("select[name='sfsi_plus_icon_hover_language']").val();
    var sfsi_plus_icon_hover_placement = SFSI("select[name='sfsi_plus_icon_hover_placement']").val();
    var sfsi_plus_icon_hover_device = SFSI("select[name='sfsi_plus_icon_hover_device']").val();
    var sfsi_plus_icon_hover_on_all_pages = SFSI("select[name='sfsi_plus_icon_hover_on_all_pages']").val();
    var sfsi_plus_icon_hover_width = SFSI('input[name="sfsi_plus_icon_hover_width"]').val();
    var sfsi_plus_icon_hover_height = SFSI('input[name="sfsi_plus_icon_hover_height"]').val();
    var sfsi_plus_icon_hover_desktop = 'no';
    var sfsi_plus_icon_hover_mobile = 'no';
    if (sfsi_plus_icon_hover_device === "desktop") {
        sfsi_plus_icon_hover_desktop = 'yes';
    } else if (sfsi_plus_icon_hover_device === "mobile") {
        sfsi_plus_icon_hover_mobile = 'yes';
    } else if (sfsi_plus_icon_hover_device === "mobile-desktop") {
        sfsi_plus_icon_hover_desktop = 'yes';
        sfsi_plus_icon_hover_mobile = 'yes';
    }
    var includekeywords = [];
    includekeywords = SFSI("input[name='sfsi_plus_icon_hover_include_urlKeywords[]']").map(function () {
        return SFSI(this).val();
    }).get();
    var excludekeywords = [];
    excludekeywords = SFSI("input[name='sfsi_plus_icon_hover_exclude_urlKeywords[]']").map(function () {
        return SFSI(this).val();
    }).get();

    var sfsi_plus_icon_hover_list_include_custom_post_types = [];
    sfsi_plus_icon_hover_list_include_custom_post_types = SFSI("input[name='sfsi_plus_icon_hover_list_include_custom_post_types[]']:checked").map(function () {
        return SFSI(this).val();
    }).get();
    var sfsi_plus_icon_hover_list_include_taxonomies = [];
    sfsi_plus_icon_hover_list_include_taxonomies = SFSI("input[name='sfsi_plus_icon_hover_list_include_taxonomies[]']:checked").map(function () {
        return SFSI(this).val();
    }).get();
    var sfsi_plus_icon_hover_list_exclude_custom_post_types = [];
    sfsi_plus_icon_hover_list_exclude_custom_post_types = SFSI("input[name='sfsi_plus_icon_hover_list_exclude_custom_post_types[]']:checked").map(function () {
        return SFSI(this).val();
    }).get();
    var sfsi_plus_icon_hover_list_exclude_taxonomies = [];
    sfsi_plus_icon_hover_list_exclude_taxonomies = SFSI("input[name='sfsi_plus_icon_hover_list_exclude_taxonomies[]']:checked").map(function () {
        return SFSI(this).val();
    }).get();

    var sfsi_plus_icon_hover_exclude_home = SFSI('input[name="sfsi_plus_icon_hover_exclude_home"]:checked').val() || 'no';
    var sfsi_plus_icon_hover_exclude_page = SFSI('input[name="sfsi_plus_icon_hover_exclude_page"]:checked').val() || 'no';
    var sfsi_plus_icon_hover_exclude_post = SFSI('input[name="sfsi_plus_icon_hover_exclude_post"]:checked').val() || 'no';
    var sfsi_plus_icon_hover_exclude_tag = SFSI('input[name="sfsi_plus_icon_hover_exclude_tag"]:checked').val() || 'no';
    var sfsi_plus_icon_hover_exclude_category = SFSI('input[name="sfsi_plus_icon_hover_exclude_category"]:checked').val() || 'no';
    var sfsi_plus_icon_hover_exclude_date_archive = SFSI('input[name="sfsi_plus_icon_hover_exclude_date_archive"]:checked').val() || 'no';
    var sfsi_plus_icon_hover_exclude_author_archive = SFSI('input[name="sfsi_plus_icon_hover_exclude_author_archive"]:checked').val() || 'no';
    var sfsi_plus_icon_hover_exclude_search = SFSI('input[name="sfsi_plus_icon_hover_exclude_search"]:checked').val() || 'no';
    var sfsi_plus_icon_hover_exclude_url = SFSI('input[name="sfsi_plus_icon_hover_exclude_url"]:checked').val() || 'no';
    var sfsi_plus_icon_hover_urlKeywords = SFSI('input[name="sfsi_plus_icon_hover_urlKeywords"]:checked').val() || 'no';
    var sfsi_plus_icon_hover_switch_exclude_custom_post_types = SFSI('input[name="sfsi_plus_icon_hover_switch_exclude_custom_post_types"]:checked').val() || 'no';
    var sfsi_plus_icon_hover_switch_exclude_taxonomies = SFSI('input[name="sfsi_plus_icon_hover_switch_exclude_taxonomies"]:checked').val() || 'no';

    var sfsi_plus_icon_hover_include_home = SFSI('input[name="sfsi_plus_icon_hover_include_home"]:checked').val() || 'no';
    var sfsi_plus_icon_hover_include_page = SFSI('input[name="sfsi_plus_icon_hover_include_page"]:checked').val() || 'no';
    var sfsi_plus_icon_hover_include_post = SFSI('input[name="sfsi_plus_icon_hover_include_post"]:checked').val() || 'no';
    var sfsi_plus_icon_hover_include_tag = SFSI('input[name="sfsi_plus_icon_hover_include_tag"]:checked').val() || 'no';
    var sfsi_plus_icon_hover_include_category = SFSI('input[name="sfsi_plus_icon_hover_include_category"]:checked').val() || 'no';
    var sfsi_plus_icon_hover_include_date_archive = SFSI('input[name="sfsi_plus_icon_hover_include_date_archive"]:checked').val() || 'no';
    var sfsi_plus_icon_hover_include_author_archive = SFSI('input[name="sfsi_plus_icon_hover_include_author_archive"]:checked').val() || 'no';
    var sfsi_plus_icon_hover_include_search = SFSI('input[name="sfsi_plus_icon_hover_include_search"]:checked').val() || 'no';
    var sfsi_plus_icon_hover_include_url = SFSI('input[name="sfsi_plus_icon_hover_include_url"]:checked').val() || 'no';
    var sfsi_plus_icon_hover_urlKeywords = SFSI('input[name="sfsi_plus_icon_hover_urlKeywords"]:checked').val() || 'no';
    var sfsi_plus_icon_hover_switch_include_custom_post_types = SFSI('input[name="sfsi_plus_icon_hover_switch_include_custom_post_types"]:checked').val() || 'no';
    var sfsi_plus_icon_hover_switch_include_taxonomies = SFSI('input[name="sfsi_plus_icon_hover_switch_include_taxonomies"]:checked').val() || 'no';
    var sfsi_plus_display_before_woocomerce_desc;
    var sfsi_plus_display_after_woocomerce_desc;
    if (SFSI('.sfsi_premium_responsive_icon_option_li').is(":visible")) {
        if (SFSI('input[name="sfsi_plus_display_before_woocomerce_desc"]:last').is(':checked')) {
            sfsi_plus_display_before_woocomerce_desc = 'yes';
        } else {
            sfsi_plus_display_before_woocomerce_desc = 'no';
        }

        if (SFSI('input[name="sfsi_plus_display_after_woocomerce_desc"]:last').is(':checked')) {
            sfsi_plus_display_after_woocomerce_desc = 'yes';
        } else {
            sfsi_plus_display_after_woocomerce_desc = 'no';
        }
    } else {

        if (SFSI('input[name="sfsi_plus_display_before_woocomerce_desc"]:first').is(':checked')) {
            sfsi_plus_display_before_woocomerce_desc = 'yes';
        } else {
            sfsi_plus_display_before_woocomerce_desc = 'no';
        }

        if (SFSI('input[name="sfsi_plus_display_after_woocomerce_desc"]:first').is(':checked')) {
            sfsi_plus_display_after_woocomerce_desc = 'yes';
        } else {
            sfsi_plus_display_after_woocomerce_desc = 'no';
        }
    }
    // var sfsi_plus_display_before_woocomerce_desc = (! SFSI('.sfsi_premium_responsive_icon_option_li').is(":visible") ? SFSI('input[name="sfsi_plus_display_before_woocomerce_desc"]:checked:first').val() : SFSI('input[name="sfsi_plus_display_before_woocomerce_desc"]:checked:last').val()) || 'no';
    // var sfsi_plus_display_after_woocomerce_desc = (! SFSI('.sfsi_premium_responsive_icon_option_li').is(":visible") ? SFSI('input[name="sfsi_plus_display_after_woocomerce_desc"]:checked:first').val() : SFSI('input[name="sfsi_plus_display_after_woocomerce_desc"]:checked:last').val()) || 'no';
    var sfsi_plus_responsive_icons_show_on_desktop = SFSI('input[name="sfsi_plus_responsive_icons_show_on_desktop"]:checked').val() || 'no'
    var sfsi_plus_responsive_icons_show_on_mobile = SFSI('input[name="sfsi_plus_responsive_icons_show_on_mobile"]:checked').val() || 'no'
    var sfsi_plus_responsive_icons_before_post = SFSI('input[name="sfsi_plus_responsive_icons_before_post"]:checked').val() || 'no'
    var sfsi_plus_responsive_icons_after_post = SFSI('input[name="sfsi_plus_responsive_icons_after_post"]:checked').val() || 'no'
    var sfsi_plus_responsive_icons_before_post_on_taxonomy = SFSI('input[name="sfsi_plus_responsive_icons_before_post_on_taxonomy"]:checked').val() || 'no'
    var sfsi_plus_responsive_icons_after_post_on_taxonomy = SFSI('input[name="sfsi_plus_responsive_icons_after_post_on_taxonomy"]:checked').val() || 'no'
    var sfsi_plus_responsive_icons_before_pages = SFSI('input[name="sfsi_plus_responsive_icons_before_pages"]:checked').val() || 'no'
    var sfsi_plus_responsive_icons_after_pages = SFSI('input[name="sfsi_plus_responsive_icons_after_pages"]:checked').val() || 'no'
    var responsive_icons = {
        "default_icons": {},
        "custom_icons": {},
        "settings": {}
    };
    SFSI('.sfsi_premium_responsive_default_icon_container input[type="checkbox"]').each(function (index, obj) {
        // console.log(index, obj);

        var data_obj = {};
        data_obj.active = (true == SFSI(obj).prop('checked')) ? 'yes' : 'no';
        var iconname = SFSI(obj).attr('data-icon');
        var next_section = SFSI(obj).parent().parent();
        data_obj.text = next_section.find('input[name="sfsi_premium_responsive_' + iconname + '_input"]').val();
        data_obj.url = next_section.find('input[name="sfsi_premium_responsive_' + iconname + '_url_input"]').val();
        responsive_icons.default_icons[iconname] = data_obj;
    });
    SFSI('.sfsi_premium_responsive_custom_icon_container input[type="checkbox"]').each(function (index, obj) {
        if (SFSI(obj).attr('id') != "sfsi_premium_responsive_custom_new_display") {
            var data_obj = {};
            data_obj.active = true == SFSI(obj).prop('checked') ? 'yes' : 'no';
            var icon_index = SFSI(obj).attr('data-custom-index');
            var next_section = SFSI(obj).parent().parent();
            data_obj['added'] = SFSI('input[name="sfsi_premium_responsive_custom_' + index + '_added"]').val();
            data_obj.icon = next_section.find('img').attr('src');

            data_obj["bg-color"] = next_section.find('.sfsi_premium_bg-color-picker').val();

            data_obj.text = next_section.find('input[name="sfsi_premium_responsive_custom_' + icon_index + '_input"]').val();
            data_obj.url = next_section.find('input[name="sfsi_premium_responsive_custom_' + icon_index + '_url_input"]').val();
            responsive_icons.custom_icons[index] = data_obj;
        }
    });
    responsive_icons.settings.icon_size = SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_size"]').val();
    responsive_icons.settings.icon_width_type = SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val();
    responsive_icons.settings.icon_width_size = SFSI('input[name="sfsi_premium_responsive_icons_sttings_icon_width_size"]').val();
    responsive_icons.settings.edge_type = SFSI('select[name="sfsi_premium_responsive_icons_settings_edge_type"]').val();
    responsive_icons.settings.edge_radius = SFSI('select[name="sfsi_premium_responsive_icons_settings_edge_radius"]').val();
    responsive_icons.settings.style = SFSI('select[name="sfsi_premium_responsive_icons_settings_style"]').val();
    responsive_icons.settings.margin = SFSI('input[name="sfsi_premium_responsive_icons_settings_margin"]').val();
    responsive_icons.settings.text_align = SFSI('select[name="sfsi_premium_responsive_icons_settings_text_align"]').val();
    responsive_icons.settings.show_count = SFSI('input[name="sfsi_plus_responsive_icon_show_count"]:checked').val();
    responsive_icons.settings.responsive_mobile_icons = SFSI('select[name="sfsi_premium_responsive_mobile_icons"]').val();
    responsive_icons.settings.counter_color = SFSI('input[name="sfsi_plus_responsive_counter_color"]').val();
    responsive_icons.settings.counter_bg_color = SFSI('input[name="sfsi_plus_responsive_counter_bg_color"]').val();
    responsive_icons.settings.share_count_text = SFSI('input[name="sfsi_plus_responsive_counter_share_count_text"]').val();
    responsive_icons.settings.margin_above = SFSI('input[name="sfsi_premium_responsive_icons_settings_margin_above"]').val();
    responsive_icons.settings.margin_below = SFSI('input[name="sfsi_premium_responsive_icons_settings_margin_below"]').val();

    var sticky_bar_icons = {
        "default_icons": {},
        "custom_icons": {},
        "settings": {}
    };

    SFSI('.sfsi_premium_sticky_default_icon_container input[type="checkbox"]').each(function (index, obj) {
        var data_obj = {};
        data_obj.active = (true == SFSI(obj).prop('checked')) ? 'yes' : 'no';
        var iconname = SFSI(obj).attr('data-icon');
        var next_section_sticky = SFSI(obj).parent().parent();
        console.log('sticky bar', data_obj.active, SFSI(obj).attr('checked'), iconname, next_section_sticky);

        data_obj.url = next_section_sticky.find('input[name="sfsi_premium_sticky_' + iconname + '_url_input"]').val();
        sticky_bar_icons.default_icons[iconname] = data_obj;
    });

    SFSI('.sfsi_premium_sticky_custom_icon_container input[type="checkbox"]').each(function (index, obj) {
        if (SFSI(obj).attr('id') != "sfsi_premium_sticky_custom_new_display") {
            var data_obj = {};
            data_obj.active = true == SFSI(obj).prop('checked') ? 'yes' : 'no';
            var icon_index = SFSI(obj).attr('data-custom-index');
            console.log('sticky', data_obj.active, icon_index, SFSI(obj).prop('checked'));

            var next_section_sticky = SFSI(obj).parent().parent().parent();
            data_obj['added'] = SFSI('input[name="sfsi_premium_sticky_custom_' + index + '_added"]').val();
            data_obj.icon = next_section_sticky.find('img').attr('src');
            data_obj['bg-color'] = next_section_sticky.find('.sfsi_premium_sticky_bg-color-picker').val();

            // data_obj.text = next_section_sticky.find('input[name="sfsi_premium_sticky_custom_' + icon_index + '_input"]').val();
            data_obj.url = next_section_sticky.find('input[name="sfsi_premium_sticky_custom_' + icon_index + '_url_input"]').val();
            sticky_bar_icons.custom_icons[index] = data_obj;
        }
    });

    sticky_bar_icons.settings.desktop = SFSI("input[name='sfsi_plus_sticky_bar_desktop']:checked").val() || 'no';
    sticky_bar_icons.settings.desktop_width = SFSI("input[name='sfsi_plus_sticky_bar_desktop_width']").val();
    sticky_bar_icons.settings.desktop_placement = SFSI("select[name='sfsi_plus_sticky_bar_desktop_placement']").val();
    sticky_bar_icons.settings.display_position = SFSI("input[name='sfsi_plus_sticky_bar_display_position']").val();
    sticky_bar_icons.settings.desktop_placement_direction = SFSI("select[name='sfsi_plus_sticky_bar_desktop_placement_direction']").val();
    sticky_bar_icons.settings.mobile = SFSI("input[name='sfsi_plus_sticky_bar_mobile']:checked").val() || 'no';
    sticky_bar_icons.settings.mobile_width = SFSI("input[name='sfsi_plus_sticky_bar_mobile_width']").val();
    sticky_bar_icons.settings.mobile_placement = SFSI("select[name='sfsi_plus_sticky_bar_mobile_placement']").val();
    sticky_bar_icons.settings.counts = SFSI("input[name='sfsi_plus_sticky_bar_counts']:checked").val();
    sticky_bar_icons.settings.bg_color = SFSI("input[name='sfsi_plus_sticky_bar_bg_color']").val();
    sticky_bar_icons.settings.color = SFSI("input[name='sfsi_plus_sticky_bar_color']").val();
    sticky_bar_icons.settings.share_count_text = SFSI("input[name='sfsi_plus_sticky_bar_share_count_text']").val();
    var sfsi_plus_sticky_bar = SFSI("input[name='sfsi_plus_sticky_bar']:checked").val();
    var f = {
        action: "plus_updateSrcn8",

        sfsi_plus_show_via_widget: i,
        sfsi_plus_float_on_page: e,
        sfsi_plus_float_page_position: t,
        sfsi_plus_place_item_manually: n,
        // sfsi_plus_place_rect_shortcode:rectP,

        sfsi_plus_icons_floatMargin_top: mst,
        sfsi_plus_icons_floatMargin_bottom: msb,
        sfsi_plus_icons_floatMargin_left: msl,
        sfsi_plus_icons_floatMargin_right: msr,
        sfsi_plus_make_icon: makeIcon,


        sfsi_plus_mobile_widget: SFSI("input[name='sfsi_plus_mobile_widget']:checked").val(),
        sfsi_plus_mobile_float: SFSI("input[name='sfsi_plus_mobile_float']:checked").val(),
        sfsi_plus_mobile_shortcode: SFSI("input[name='sfsi_plus_mobile_shortcode']:checked").val(),
        sfsi_plus_mobile_beforeafterposts: SFSI("input[name='sfsi_plus_mobile_beforeafterposts']:checked").val(),

        sfsi_plus_widget_horizontal_verical_Alignment: sfsi_plus_widget_horizontal_verical_Alignment,
        sfsi_plus_widget_mobile_horizontal_verical_Alignment: sfsi_plus_widget_mobile_horizontal_verical_Alignment,

        sfsi_plus_float_horizontal_verical_Alignment: sfsi_plus_float_horizontal_verical_Alignment,
        sfsi_plus_float_mobile_horizontal_verical_Alignment: sfsi_plus_float_mobile_horizontal_verical_Alignment,

        sfsi_plus_shortcode_horizontal_verical_Alignment: sfsi_plus_shortcode_horizontal_verical_Alignment,
        sfsi_plus_shortcode_mobile_horizontal_verical_Alignment: sfsi_plus_shortcode_mobile_horizontal_verical_Alignment,

        sfsi_plus_beforeafterposts_horizontal_verical_Alignment: sfsi_plus_beforeafterposts_horizontal_verical_Alignment,
        sfsi_plus_beforeafterposts_mobile_horizontal_verical_Alignment: sfsi_plus_beforeafterposts_mobile_horizontal_verical_Alignment,

        sfsi_plus_show_item_onposts: o,
        sfsi_plus_display_button_type: a,
        sfsi_plus_post_icons_size: r,
        sfsi_plus_post_icons_spacing: c,
        sfsi_plus_post_icons_vertical_spacing: vSpace,
        sfsi_plus_show_Onposts: p,
        sfsi_plus_textBefor_icons: v,
        sfsi_plus_icons_alignment: x,
        sfsi_plus_icons_DisplayCounts: z,
        sfsi_plus_display_before_posts: b,
        sfsi_plus_display_after_posts: d,
        sfsi_plus_display_before_blogposts: f,
        sfsi_plus_display_after_blogposts: g,
        sfsi_plus_rectsub: rsub,
        sfsi_plus_rectfb: rfb,
        sfsi_plus_recttwtr: rtwr,
        sfsi_plus_rectpinit: rpin,
        sfsi_plus_rectlinkedin: rlinkedin,
        sfsi_plus_rectreddit: rreddit,
        sfsi_plus_rectfbshare: rfbshare,
        sfsi_plus_choose_post_types: rpostTypes,
        sfsi_plus_choose_post_types_responsive: rpostTypesResonsive,
        sfsi_plus_taxonomies_for_icons: rTaxonomies,

        sfsi_plus_textBefor_icons_font_size: SFSI("input[name='sfsi_plus_textBefor_icons_font_size']").val(),
        sfsi_plus_textBefor_icons_fontcolor: SFSI("input[name='sfsi_plus_textBefor_icons_fontcolor']").val(),
        sfsi_plus_textBefor_icons_font: SFSI("select[name='sfsi_plus_textBefor_icons_font']").val(),
        sfsi_plus_textBefor_icons_font_type: SFSI("select[name='sfsi_plus_textBefor_icons_font_type']").val(),

        sfsi_plus_float_page_mobileposition: SFSI("input[name='sfsi_plus_float_page_mobileposition']:checked").val(),
        sfsi_plus_icons_floatMargin_mobiletop: SFSI("input[name='sfsi_plus_icons_floatMargin_mobiletop']").val(),
        sfsi_plus_icons_floatMargin_mobilebottom: SFSI("input[name='sfsi_plus_icons_floatMargin_mobilebottom']").val(),
        sfsi_plus_icons_floatMargin_mobileleft: SFSI("input[name='sfsi_plus_icons_floatMargin_mobileleft']").val(),
        sfsi_plus_icons_floatMargin_mobileright: SFSI("input[name='sfsi_plus_icons_floatMargin_mobileright']").val(),
        sfsi_plus_make_mobileicon: SFSI("input[name='sfsi_plus_make_mobileicon']:checked").val(),

        sfsi_plus_marginAbove_postIcon: SFSI("input[name='sfsi_plus_marginAbove_postIcon']").val(),
        sfsi_plus_marginBelow_postIcon: SFSI("input[name='sfsi_plus_marginBelow_postIcon']").val(),
        sfsi_plus_display_after_pageposts: SFSI("input[name='sfsi_plus_display_after_pageposts']:checked").val(),
        sfsi_plus_display_before_pageposts: SFSI("input[name='sfsi_plus_display_before_pageposts']:checked").val(),

        sfsi_plus_widget_show_on_desktop: SFSI("input[name='sfsi_plus_widget_show_on_desktop']:checked").val(),
        sfsi_plus_widget_show_on_mobile: SFSI("input[name='sfsi_plus_widget_show_on_mobile']:checked").val(),

        sfsi_plus_float_show_on_desktop: SFSI("input[name='sfsi_plus_float_show_on_desktop']:checked").val(),
        sfsi_plus_float_show_on_mobile: SFSI("input[name='sfsi_plus_float_show_on_mobile']:checked").val(),

        sfsi_plus_shortcode_show_on_desktop: SFSI("input[name='sfsi_plus_shortcode_show_on_desktop']:checked").val(),
        sfsi_plus_shortcode_show_on_mobile: SFSI("input[name='sfsi_plus_shortcode_show_on_mobile']:checked").val(),

        sfsi_plus_rectangle_icons_shortcode_show_on_desktop: SFSI("input[name='sfsi_plus_rectangle_icons_shortcode_show_on_desktop']:checked").val(),
        sfsi_plus_rectangle_icons_shortcode_show_on_mobile: SFSI("input[name='sfsi_plus_rectangle_icons_shortcode_show_on_mobile']:checked").val(),

        sfsi_plus_beforeafterposts_show_on_desktop: SFSI("input[name='sfsi_plus_beforeafterposts_show_on_desktop']:checked").val(),
        sfsi_plus_beforeafterposts_show_on_mobile: SFSI("input[name='sfsi_plus_beforeafterposts_show_on_mobile']:checked").val(),

        sfsi_plus_display_on_all_icons: sfsi_plus_display_on_all_icons,
        sfsi_plus_display_rule_round_icon_widget: sfsi_plus_display_rule_round_icon_widget,
        sfsi_plus_display_rule_round_icon_define_location: sfsi_plus_display_rule_round_icon_define_location,
        sfsi_plus_display_rule_round_icon_shortcode: sfsi_plus_display_rule_round_icon_shortcode,
        sfsi_plus_display_rule_round_icon_before_after_post: sfsi_plus_display_rule_round_icon_before_after_post,
        sfsi_plus_display_rule_rect_icon_before_after_post: sfsi_plus_display_rule_rect_icon_before_after_post,
        sfsi_plus_display_rule_responsive_icon_before_after_post: sfsi_plus_display_rule_responsive_icon_before_after_post,
        sfsi_plus_display_rule_responsive_icon_shortcode: sfsi_plus_display_rule_responsive_icon_shortcode,
        sfsi_plus_display_rule_icon_sticky_bar: sfsi_plus_display_rule_icon_sticky_bar,
        sfsi_plus_display_rule_rect_icon_shortcode: sfsi_plus_display_rule_rect_icon_shortcode,

        sfsi_plus_icon_hover_show_pinterest: sfsi_plus_icon_hover_show_pinterest,
        sfsi_plus_icon_hover_type: sfsi_plus_icon_hover_type,
        sfsi_plus_icon_hover_custom_icon_url: sfsi_plus_icon_hover_custom_icon_url,
        sfsi_plus_icon_hover_language: sfsi_plus_icon_hover_language,
        sfsi_plus_icon_hover_placement: sfsi_plus_icon_hover_placement,
        sfsi_plus_icon_hover_desktop: sfsi_plus_icon_hover_desktop,
        sfsi_plus_icon_hover_mobile: sfsi_plus_icon_hover_mobile,
        sfsi_plus_icon_hover_on_all_pages: sfsi_plus_icon_hover_on_all_pages,
        sfsi_plus_icon_hover_width: sfsi_plus_icon_hover_width,
        sfsi_plus_icon_hover_height: sfsi_plus_icon_hover_height,

        sfsi_plus_icon_hover_exclude_home: sfsi_plus_icon_hover_exclude_home,
        sfsi_plus_icon_hover_exclude_page: sfsi_plus_icon_hover_exclude_page,
        sfsi_plus_icon_hover_exclude_post: sfsi_plus_icon_hover_exclude_post,
        sfsi_plus_icon_hover_exclude_tag: sfsi_plus_icon_hover_exclude_tag,
        sfsi_plus_icon_hover_exclude_category: sfsi_plus_icon_hover_exclude_category,
        sfsi_plus_icon_hover_exclude_date_archive: sfsi_plus_icon_hover_exclude_date_archive,
        sfsi_plus_icon_hover_exclude_author_archive: sfsi_plus_icon_hover_exclude_author_archive,
        sfsi_plus_icon_hover_exclude_search: sfsi_plus_icon_hover_exclude_search,
        sfsi_plus_icon_hover_exclude_url: sfsi_plus_icon_hover_exclude_url,
        sfsi_plus_icon_hover_exclude_urlKeywords: excludekeywords,
        sfsi_plus_icon_hover_switch_exclude_custom_post_types: sfsi_plus_icon_hover_switch_exclude_custom_post_types,
        sfsi_plus_icon_hover_list_exclude_custom_post_types: sfsi_plus_icon_hover_list_exclude_custom_post_types,
        sfsi_plus_icon_hover_switch_exclude_taxonomies: sfsi_plus_icon_hover_switch_exclude_taxonomies,
        sfsi_plus_icon_hover_list_exclude_taxonomies: sfsi_plus_icon_hover_list_exclude_taxonomies,

        sfsi_plus_icon_hover_include_home: sfsi_plus_icon_hover_include_home,
        sfsi_plus_icon_hover_include_page: sfsi_plus_icon_hover_include_page,
        sfsi_plus_icon_hover_include_post: sfsi_plus_icon_hover_include_post,
        sfsi_plus_icon_hover_include_tag: sfsi_plus_icon_hover_include_tag,
        sfsi_plus_icon_hover_include_category: sfsi_plus_icon_hover_include_category,
        sfsi_plus_icon_hover_include_date_archive: sfsi_plus_icon_hover_include_date_archive,
        sfsi_plus_icon_hover_include_author_archive: sfsi_plus_icon_hover_include_author_archive,
        sfsi_plus_icon_hover_include_search: sfsi_plus_icon_hover_include_search,
        sfsi_plus_icon_hover_include_url: sfsi_plus_icon_hover_include_url,
        sfsi_plus_icon_hover_include_urlKeywords: includekeywords,
        sfsi_plus_icon_hover_switch_include_custom_post_types: sfsi_plus_icon_hover_switch_include_custom_post_types,
        sfsi_plus_icon_hover_list_include_custom_post_types: sfsi_plus_icon_hover_list_include_custom_post_types,
        sfsi_plus_icon_hover_switch_include_taxonomies: sfsi_plus_icon_hover_switch_include_taxonomies,
        sfsi_plus_icon_hover_list_include_taxonomies: sfsi_plus_icon_hover_list_include_taxonomies,


        sfsi_plus_display_before_woocomerce_desc: sfsi_plus_display_before_woocomerce_desc,
        sfsi_plus_display_after_woocomerce_desc: sfsi_plus_display_after_woocomerce_desc,
        sfsi_plus_responsive_icons: responsive_icons,
        sfsi_plus_responsive_icons_show_on_desktop: sfsi_plus_responsive_icons_show_on_desktop,
        sfsi_plus_responsive_icons_show_on_mobile: sfsi_plus_responsive_icons_show_on_mobile,
        sfsi_plus_responsive_icons_before_post: sfsi_plus_responsive_icons_before_post,
        sfsi_plus_responsive_icons_after_post: sfsi_plus_responsive_icons_after_post,
        sfsi_plus_responsive_icons_before_post_on_taxonomy: sfsi_plus_responsive_icons_before_post_on_taxonomy,
        sfsi_plus_responsive_icons_after_post_on_taxonomy: sfsi_plus_responsive_icons_after_post_on_taxonomy,
        sfsi_plus_responsive_icons_before_pages: sfsi_plus_responsive_icons_before_pages,
        sfsi_plus_responsive_icons_after_pages: sfsi_plus_responsive_icons_after_pages,
        sfsi_plus_post_mobile_icons_size: mr,
        sfsi_plus_post_mobile_icons_spacing: mc,
        sfsi_plus_post_mobile_icons_vertical_spacing: mvSpace,
        sfsi_plus_mobile_size_space_beforeafterposts: SFSI("input[name='sfsi_plus_mobile_size_space_beforeafterposts']:checked").val(),
        sfsi_plus_sticky_bar: sfsi_plus_sticky_bar,
        sfsi_plus_sticky_icons: sticky_bar_icons,
        sfsi_plus_place_item_gutenberg: SFSI("input[name='sfsi_plus_place_item_gutenberg']:checked").val(),

        nonce: nonce
    };
    /////////////////////// Get selected Include and Exclusion Rules ////////////////////////

    var sfsi_plus_icons_rules = SFSI("input[name='sfsi_plus_icons_rules']:checked").val(),

        keys = ['home', 'page', 'post', 'tag', 'category', 'date_archive', 'author_archive', 'search', 'url'],
        inexcludeRules = {};

    inexcludeRules.sfsi_plus_icons_rules = sfsi_plus_icons_rules;

    keys.forEach(function (element, index) {

        var exkeyName = "sfsi_plus_exclude_" + element;
        inexcludeRules[exkeyName] = SFSI("input[name='" + exkeyName + "']:checked").val();

        var inkeyName = "sfsi_plus_include_" + element;
        inexcludeRules[inkeyName] = SFSI("input[name='" + inkeyName + "']:checked").val();
    });

    //rules for urls
    var keywords = [];
    keywords = SFSI("input[name='sfsi_plus_urlKeywords[]']").map(function () {
        return SFSI(this).val();
    }).get();

    inexcludeRules.sfsi_plus_urlKeywords = keywords;

    var includekeywords = [];
    includekeywords = SFSI("input[name='sfsi_plus_include_urlKeywords[]']").map(function () {
        return SFSI(this).val();
    }).get();

    inexcludeRules.sfsi_plus_include_urlKeywords = includekeywords;

    // rules for exclude include custom post types //

    // Checkbox
    var sfsi_plus_switch_exclude_custom_post_types = SFSI("input[name='sfsi_plus_switch_exclude_custom_post_types']:checked").val();
    sfsi_plus_switch_exclude_custom_post_types = (sfsi_plus_switch_exclude_custom_post_types == undefined) ? "no" : "yes";

    inexcludeRules.sfsi_plus_switch_exclude_custom_post_types = sfsi_plus_switch_exclude_custom_post_types;

    var sfsi_plus_switch_include_custom_post_types = SFSI("input[name='sfsi_plus_switch_include_custom_post_types']:checked").val();
    sfsi_plus_switch_include_custom_post_types = (sfsi_plus_switch_include_custom_post_types == undefined) ? "no" : "yes";

    inexcludeRules.sfsi_plus_switch_include_custom_post_types = sfsi_plus_switch_include_custom_post_types;


    // List post types
    var chkEleExList = SFSI('[data-cl="sfsi_plus_list_exclude_custom_post_types"]');
    var sfsi_plus_list_exclude_custom_post_types = chkEleExList.serialize();

    inexcludeRules.sfsi_plus_list_exclude_custom_post_types = sfsi_plus_list_exclude_custom_post_types;

    var chkEleInList = SFSI('[data-cl="sfsi_plus_list_include_custom_post_types"]');
    var sfsi_plus_list_include_custom_post_types = chkEleInList.serialize();

    inexcludeRules.sfsi_plus_list_include_custom_post_types = sfsi_plus_list_include_custom_post_types;

    // rules for exclude rules custom taxonomies

    // Checkbox
    var sfsi_plus_switch_exclude_taxonomies = SFSI("input[name='sfsi_plus_switch_exclude_taxonomies']:checked").val();
    sfsi_plus_switch_exclude_taxonomies = (sfsi_plus_switch_exclude_taxonomies == undefined) ? "no" : "yes";

    inexcludeRules.sfsi_plus_switch_exclude_taxonomies = sfsi_plus_switch_exclude_taxonomies;

    var sfsi_plus_switch_include_taxonomies = SFSI("input[name='sfsi_plus_switch_include_taxonomies']:checked").val();
    sfsi_plus_switch_include_taxonomies = (sfsi_plus_switch_include_taxonomies == undefined) ? "no" : "yes";

    inexcludeRules.sfsi_plus_switch_include_taxonomies = sfsi_plus_switch_include_taxonomies;

    // List taxonomies
    var chkEleExListTaxonomies = SFSI('[data-cl="sfsi_plus_list_exclude_taxonomies"]');
    var sfsi_plus_list_exclude_taxonomies = chkEleExListTaxonomies.serialize();

    inexcludeRules.sfsi_plus_list_exclude_taxonomies = sfsi_plus_list_exclude_taxonomies;

    var chkEleInListTaxonomies = SFSI('[data-cl="sfsi_plus_list_include_taxonomies"]');
    var sfsi_plus_list_include_taxonomies = chkEleInListTaxonomies.serialize();

    inexcludeRules.sfsi_plus_list_include_taxonomies = sfsi_plus_list_include_taxonomies;


    // Add in data object to send in ajax
    SFSI.extend(true, f, inexcludeRules);

    /////////////////////// Get selected Include and Exclusion Rules ////////////////////////

    SFSI.ajax({
        url: sfsi_premium_ajax_object.ajax_url,
        type: "post",
        data: f,
        dataType: "json",
        timeout: 6000,
        async: !0,
        success: function (s) {
            if (s == "wrong_nonce") {
                sfsiplus_showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 8);
                sfsiplus_afterLoad();
            } else {
                "success" == s ? (sfsiplus_showErrorSuc("success", "Saved !", 8), sfsipluscollapse("#sfsi_plus_save8")) : sfsiplus_showErrorSuc("error", "Your settings could not be saved. Please see <a href='https://www.ultimatelysocial.com/saving-settings-is-not-working/' style='color:green;font-weight: bold;'>here</a>  what could be the possible issue", 8), sfsiplus_afterLoad()
            }
        },
        error: function (jqXHR, textStatus) {
            if (textStatus === 'timeout') {
                global_error = 1,
                    sfsiplus_showErrorSuc("error", "Your settings could not be saved. Please see <a href='https://www.ultimatelysocial.com/saving-settings-is-not-working/' style='color:green;font-weight: bold;'>here</a>  what could be the possible issue", 8)
            }
            sfsiplus_afterLoad();
            //console.log(textStatus,jqXHR);
        }
    });
}

function sfsi_plus_update_step9() {
    var nonce = SFSI("#sfsi_plus_save9").attr("data-nonce");
    sfsiplus_beForeLoad();
    var ie = SFSI("input[name='sfsi_plus_form_adjustment']:checked").val(),
        je = SFSI("input[name='sfsi_plus_form_height']").val(),
        ke = SFSI("input[name='sfsi_plus_form_width']").val(),
        le = SFSI("input[name='sfsi_plus_form_border']:checked").val(),
        me = SFSI("input[name='sfsi_plus_form_border_thickness']").val(),
        ne = SFSI("input[name='sfsi_plus_form_border_color']").val(),
        oe = SFSI("input[name='sfsi_plus_form_background']").val(),

        ae = SFSI("input[name='sfsi_plus_form_heading_text']").val(),
        be = SFSI("#sfsi_plus_form_heading_font option:selected").val(),
        ce = SFSI("#sfsi_plus_form_heading_fontstyle option:selected").val(),
        de = SFSI("input[name='sfsi_plus_form_heading_fontcolor']").val(),
        ee = SFSI("input[name='sfsi_plus_form_heading_fontsize']").val(),
        fe = SFSI("#sfsi_plus_form_heading_fontalign option:selected").val(),

        ue = SFSI("input[name='sfsi_plus_form_field_text']").val(),
        ve = SFSI("#sfsi_plus_form_field_font option:selected").val(),
        we = SFSI("#sfsi_plus_form_field_fontstyle option:selected").val(),
        xe = SFSI("input[name='sfsi_plus_form_field_fontcolor']").val(),
        ye = SFSI("input[name='sfsi_plus_form_field_fontsize']").val(),
        ze = SFSI("#sfsi_plus_form_field_fontalign option:selected").val(),

        i = SFSI("input[name='sfsi_plus_form_button_text']").val(),
        j = SFSI("#sfsi_plus_form_button_font option:selected").val(),
        k = SFSI("#sfsi_plus_form_button_fontstyle option:selected").val(),
        l = SFSI("input[name='sfsi_plus_form_button_fontcolor']").val(),
        m = SFSI("input[name='sfsi_plus_form_button_fontsize']").val(),
        n = SFSI("#sfsi_plus_form_button_fontalign option:selected").val(),
        o = SFSI("input[name='sfsi_plus_form_button_background']").val();

    pd = SFSI("input[name='sfsi_plus_shall_display_privacy_notice']:checked").val(),
        pp = SFSI("input[name='sfsi_plus_form_privacynotice_text']").val(),
        qp = SFSI("#sfsi_plus_form_privacynotice_font option:selected").val(),
        rp = SFSI("#sfsi_plus_form_privacynotice_fontstyle option:selected").val(),
        sp = SFSI("input[name='sfsi_plus_form_privacynotice_fontcolor']").val(),
        tp = SFSI("input[name='sfsi_plus_form_privacynotice_fontsize']").val(),
        up = SFSI("#sfsi_plus_form_privacynotice_fontalign option:selected").val();

    var f = {
        action: "plus_updateSrcn9",
        sfsi_plus_form_adjustment: ie,
        sfsi_plus_form_height: je,
        sfsi_plus_form_width: ke,
        sfsi_plus_form_border: le,
        sfsi_plus_form_border_thickness: me,
        sfsi_plus_form_border_color: ne,
        sfsi_plus_form_background: oe,

        sfsi_plus_form_heading_text: ae,
        sfsi_plus_form_heading_font: be,
        sfsi_plus_form_heading_fontstyle: ce,
        sfsi_plus_form_heading_fontcolor: de,
        sfsi_plus_form_heading_fontsize: ee,
        sfsi_plus_form_heading_fontalign: fe,

        sfsi_plus_form_field_text: ue,
        sfsi_plus_form_field_font: ve,
        sfsi_plus_form_field_fontstyle: we,
        sfsi_plus_form_field_fontcolor: xe,
        sfsi_plus_form_field_fontsize: ye,
        sfsi_plus_form_field_fontalign: ze,

        sfsi_plus_form_button_text: i,
        sfsi_plus_form_button_font: j,
        sfsi_plus_form_button_fontstyle: k,
        sfsi_plus_form_button_fontcolor: l,
        sfsi_plus_form_button_fontsize: m,
        sfsi_plus_form_button_fontalign: n,
        sfsi_plus_form_button_background: o,

        sfsi_plus_shall_display_privacy_notice: pd,
        sfsi_plus_form_privacynotice_text: pp,
        sfsi_plus_form_privacynotice_font: qp,
        sfsi_plus_form_privacynotice_fontstyle: rp,
        sfsi_plus_form_privacynotice_fontcolor: sp,
        sfsi_plus_form_privacynotice_fontsize: tp,
        sfsi_plus_form_privacynotice_fontalign: up,
        nonce: nonce
    };
    SFSI.ajax({
        url: sfsi_premium_ajax_object.ajax_url,
        type: "post",
        data: f,
        dataType: "json",
        timeout: 6000,
        async: !0,
        success: function (s) {
            if (s == "wrong_nonce") {
                sfsiplus_showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 9);
                sfsiplus_afterLoad();
            } else {
                "success" == s ? (sfsiplus_showErrorSuc("success", "Saved !", 9), sfsipluscollapse("#sfsi_plus_save9"), sfsi_plus_create_suscriber_form()) : sfsiplus_showErrorSuc("error", "Your settings could not be saved. Please see <a href='https://www.ultimatelysocial.com/saving-settings-is-not-working/' style='color:green;font-weight: bold;'>here</a>  what could be the possible issue", 9),
                    sfsiplus_afterLoad();
            }
        },
        error: function (jqXHR, textStatus) {
            if (textStatus === 'timeout') {
                global_error = 1,
                    sfsiplus_showErrorSuc("error", "Your settings could not be saved. Please see <a href='https://www.ultimatelysocial.com/saving-settings-is-not-working/' style='color:green;font-weight: bold;'>here</a>  what could be the possible issue", 9)
            }
            sfsiplus_afterLoad();
            //console.log(textStatus,jqXHR);
        }
    });
}

function sfsiplus_afterIconSuccess(s) {
    if (s.res = "success") {

        'use strict';

        var i = s.key + 1,
            e = s.element,
            t = e + 1;

        SFSI("#plus_total_cusotm_icons").val(s.element);
        SFSI(".upload-overlay").hide("slow");
        SFSI(".uperror").html("");
        sfsiplus_showErrorSuc("success", "Custom Icon updated successfully", 1);

        d = new Date();

        var iconUrl = s.img_path + '"?" ' + d.getTime();

        var iconUrlVal = s.img_path;
        console.log(iconUrl, iconUrlVal, s.img_path);
        var sms_call_template = SFSI("#sms-call-note-template").html();

        // Update icons in Question 1-> Desktop icons
        var lastChild = SFSI("ul.plus_icn_listing li.plus_custom:last-child");

        lastChild.removeClass("bdr_btm_non");
        lastChild.addClass("vertical-align");
        lastChildList = lastChild.find("span.plus_custom-img");
        if (lastChildList.length > 0) {
            lastChildChild = SFSI(lastChildList[0]);
        } else {
            lastChildChild = lastChild.children("span.plus_custom-img");
        }

        lastChildChild.children("img").attr("src", s.img_path + "?" + d.getTime()).attr('data-key', s.key);
        //var changeBtn = '<a name="plussfsiICON_'+ s.key+'" class="changeCustomIcon" title="Change custom icon" alt="Change custom icon">Change</a>';
        var deleteBtn = '<a name="plussfsiICON_' + s.key + '" class="deleteCustomIcon" title="Delete custom icon" alt="Delete custom icon">Delete</a>';
        console.log(lastChild.children("span.sfsiplus_custom-txt"));
        if (lastChild.children("span.sfsiplus_custom-txt").length > 0) {
            lastChild.children("span.sfsiplus_custom-txt").after(deleteBtn);
        } else {
            console.log(lastChildChild.parent());
            SFSI(lastChildChild.parent()).after(deleteBtn);
        }

        var newElem = SFSI("ul.plus_icn_listing input[name=plussfsiICON_" + s.key + "]");

        newElem.removeAttr("ele-type");
        newElem.removeAttr("element-type");
        newElem.removeAttr("isnew");
        newElem.attr("element-ctype", "sfsiplus-cusotm-icon");
        newElem.attr("element-id", s.key);
        newElem.attr("checked", "checked");

        icons_name = SFSI("li.plus_custom:last-child").find("input.styled").attr("name");

        var n = icons_name.split("_");

        s.img_path += "?" + d.getTime();

        // If uploaded custom icon count is greater than 10
        10 > e &&

        //<a name="plussfsiICON_'+i+'" class="deleteCustomIcon" title="Delete custom icon" alt="Delete custom icon">Delete</a>

        // --------------------- Update icons in Question 1-> Create item to upload new icon -----------------//
        SFSI(".plus_icn_listing").append('<li id="plus_c' + i + '" class="plus_custom bdr_btm_non"><div><div class="radio_section tb_4_ck"><span class="checkbox" dynamic_ele="yes" style="background-position: 0px 0px;"></span><input name="plussfsiICON_' + i + '"  type="checkbox" value="yes" class="styled" style="display:none;" element-type="sfsiplus-cusotm-icon" isNew="yes" /></div> <span class="plus_custom-img"><img src="' + SFSI("#plugin_url").val() + 'images/custom.png" id="plus_CImg_' + i + '"  /> </span> <span class="custom sfsiplus_custom-txt">Custom' + t + ' </span> </div><div class="sfsiplus_right_info"> <p><span>' + object_name1.It_depends + ':</span> ' + object_name1.Upload_a + '</p><div class="inputWrapper"></div></li>'),
            SFSI(".sfsi_premium_mobile_icon_listing").append('<li id="plus_c' + s.key + '" class="plus_custom"> <div class="radio_section tb_4_ck"><span class="checkbox" style="background-position: 0px -36px;"></span><input checked="true" data-key="' + s.key + '" name="plussfsiICON_' + s.key + '"  type="checkbox" value="yes" class="styled" /></div> <span class="plus_custom-img"><img data-key="' + s.key + '" src="' + s.img_path + "?" + d.getTime() + '" id="plus_CImg_' + s.key + '"  /> </span> <span class="custom sfsiplus_custom-txt">Custom ' + s.key + ' </span></li>'),

            // --------------------- Update icons in Question 2 ----------------------------//
            SFSI(".sfsiplus_custom_section").show(),
            SFSI(".plus_custom-links").append(' <div class="row  sfsiICON_' + s.key + ' cm_lnk"> <h2 class="custom"> <span class="customstep2-img"> <img   src="' + s.img_path + "?" + d.getTime() + '" /> </span> <span class="sfsiCtxt">Custom ' + e + '</span> </h2> <div class="inr_cont "><p>Where do you want this icon to link to?</p> <p class="radio_section fb_url sfsiplus_custom_section  sfsiICON_' + s.key + '" ><label>Link :</label><input file-id="' + s.key + '" name="sfsi_plus_CustomIcon_links[]" type="text" value="" placeholder="http://" class="add" /></p>' + sms_call_template + '</div></div>');

        // --------------------- Update in Question 4 ----------------------------//

        var arrCustomElem = SFSI(".other_icons_effects_options_container div.col-md-3[data-customiconindex!=-1]"),
            lastCustomElem = arrCustomElem.slice(-1)[0],

            lastShownSrNo = parseInt(SFSI(lastCustomElem).find('.mouseover_other_icon_label').attr('data-customiconsrno')),

            newCustomIconName = "Custom" + (s.key + 1);

        if ("number" === typeof lastShownSrNo && false == isNaN(lastShownSrNo)) {
            var newCustomIconName = "Custom" + (lastShownSrNo + 1);
        }

        mouseoverOnclickChange = "onclick=SfsiHelper.openWpMedia(this,'','','plus_sfsi_upload_mouseovericon');",
            mouseoverOnclickRevert = "onclick=sfsi_plus_delete_mouserOverIcon(SFSI(this),SFSI(this).attr('data-iconname'));",

            mouseoverCustomIconTmpl = '<div class="col-md-3 bottommargin10" data-customIconIndex="' + s.key + '"><label class="mouseover_other_icon_label">' + newCustomIconName + '</label><img data-defaultImg="' + s.img_path + '" class="mouseover_other_icon_img" src="' + s.img_path + '"><input data-customIconIndex="' + s.key + '" data-iconname="custom" type="hidden" value="" name="mouseover_other_icon_custom"><a ' + mouseoverOnclickChange + ' data-customIconIndex="' + s.key + '" data-iconname="custom" class="mouseover_other_icon_change_link" href="javascript:void(0)" class="mouseover_other_icon">Change</a><a ' + mouseoverOnclickRevert + ' data-customIconIndex="' + s.key + '" data-iconname="custom" class="mouseover_other_icon_revert_link hide" href="javascript:void(0)" class="mouseover_other_icon">Revert</a></div>';

        SFSI(".other_icons_effects_options_container").append(mouseoverCustomIconTmpl);

        // --------------------- Update icons in Question 6-> Mouseover text ----------------------------//
        var o = SFSI("div.plus_custom_m").find("div.mouseover_field").length;

        SFSI("div.plus_custom_m").append(0 == o % 2 ? '<div class="clear"> </div> <div class="mouseover_field sfsiplus_custom_section sfsiICON_' + s.key + '"><label>Custom ' + e + ':</label><input name="sfsi_plus_custom_MouseOverTexts[]" value="" type="text" file-id="' + s.key + '" /></div>' : '<div class="cHover " ><div class="mouseover_field sfsiplus_custom_section sfsiICON_' + s.key + '"><label>Custom ' + e + ':</label><input name="sfsi_plus_custom_MouseOverTexts[]" value="" type="text" file-id="' + s.key + '" /></div>');

        // --------------------- Update icons in Question 6 -> Order of icons ----------------------------//
        SFSI("ul.plus_share_icon_order").append('<li class="sfsiplus_custom_iconOrder sfsiICON_' + s.key + '" data-icon="custom" data-index="" element-id="' + s.key + '" id=""><a href="#" title="Custom Icon" ><img src="' + s.img_path + '" alt="" class="sfcm"/></a></li>'),

            // --------------------- Update icons in Question 6 -> Order of icons ----------------------------//
            SFSI("ul.plus_share_icon_mobile_order").append('<li class="sfsiplus_custom_mobilesection sfsiplus_custom_icon_mobileOrder sfsiICON_' + s.key + '" data-icon="custom" data-index="" element-id="' + s.key + '" id=""><a href="#" title="Custom Icon" ><img src="' + s.img_path + '" alt="" class="sfcm"/></a></li>'),
            SFSI("ul.dbMobileOrder").append('<li class="sfsiplus_custom_mobilesection sfsiplus_custom_icon_mobileOrder sfsiICON_' + s.key + '" data-icon="custom" data-index="" element-id="' + s.key + '" id=""><a href="#" title="Custom Icon" ><img src="' + s.img_path + '" alt="" class="sfcm"/></a></li>'),

            // --------------------- Update icons in Question 7 ----------------------------//
            SFSI("ul.plus_sfsi_sample_icons").append('<li class="sfsiICON_' + s.key + '" element-id="' + s.key + '" ><div><img src="' + s.img_path + '" alt="Linked In" class="sfcm"/><span class="sfsi_Cdisplay">12k</span></div></li>'),

            sfsi_plus_update_index(),
            plus_update_Sec5Iconorder(),
            plus_update_Sec5IconMobileorder(),
            //sfsi_plus_update_step1(),
            sfsi_plus_update_step2(),
            sfsi_plus_update_step5(),
            SFSI(".upload-overlay").css("pointer-events", "auto"), sfsi_plus_showPreviewCounts(),
            sfsiplus_afterLoad();
    }
}

function sfsiplus_beforeIconSubmit(s) {
    if (SFSI(".uperror").html("Uploading....."), window.File && window.FileReader && window.FileList && window.Blob) {
        SFSI(s).val() || SFSI(".uperror").html("File is empty");
        var i = s.files[0].size,
            e = s.files[0].type;
        switch (e) {
            case "image/png":
            case "image/gif":
            case "image/jpeg":
            case "image/pjpeg":
                break;

            default:
                return SFSI(".uperror").html("Unsupported file"), !1;
        }
        return i > 1048576 ? (SFSI(".uperror").html("Image should be less than 1 MB"), !1) : !0;
    }
    return !0;
}

function sfsiplus_bytesToSize(s) {
    var i = ["Bytes", "KB", "MB", "GB", "TB"];
    if (0 == s) return "0 Bytes";
    var e = parseInt(Math.floor(Math.log(s) / Math.log(1024)));
    return Math.round(s / Math.pow(1024, e), 2) + " " + i[e];
}

function sfsiplus_showErrorSuc(s, i, e) {
    if ("error" == s) {
        var t = "errorMsg";
    } else {
        var t = "sucMsg";
    }
    return SFSI(".tab" + e + ">." + t).html(i), SFSI(".tab" + e + ">." + t).show(),
        SFSI(".tab" + e + ">." + t), setTimeout(function () {
        SFSI("." + t).slideUp("slow");
    }, 5e3), !1;
}

function sfsiplus_beForeLoad() {
    SFSI(".loader-img").show(), SFSI(".save_button >a").html("Saving..."), SFSI(".save_button >a").css("pointer-events", "none");
}

function sfsiplus_afterLoad() {
    SFSI("input").removeClass("inputError"), SFSI(".save_button >a").html(object_name.Sa_ve),
        SFSI("#save_plus_all_settings").html(object_name1.Save_All_Settings),
        SFSI(".save_button >a").css("pointer-events", "auto"), SFSI(".save_button >a").removeAttr("onclick"),
        SFSI(".loader-img").hide();
}

function sfsi_plus_make_popBox() {
    var s = 0;

    var headingTxt = escapeHtml(SFSI('textarea[name="sfsi_plus_popup_text"]').val());
    headingTxt = headingTxt.replace(new RegExp("\n", "g"), "<br>");
    var elementH2 = SFSI(".sfsi_plus_Popinner h2");
    var element = SFSI(".sfsi_plus_Popinner");


    SFSI(".plus_sfsi_sample_icons >li").each(function () {
        "none" != SFSI(this).css("display") && (s = 1);
    }), 0 == s ? element.hide() : element.show(), "" != headingTxt ? (elementH2.text(headingTxt),
        elementH2.show()) : elementH2.hide(),
        element.css({
            "border-color": SFSI('input[name="sfsi_plus_popup_border_color"]').val(),
            "border-width": SFSI('input[name="sfsi_plus_popup_border_thickness"]').val(),
            "border-style": "solid"
        });


    var grpKey = 'sfsi_plus_popup_';

    element.sfsipluscss("background-color", SFSI('input[name="' + grpKey + 'background_color"]').val());

    elementH2.sfsipluscss("font-family", SFSI("#" + grpKey + "font").val());
    elementH2.sfsipluscss("font-style", SFSI("#" + grpKey + "fontStyle").val());

    elementH2.css("font-size", parseInt(SFSI('input[name="' + grpKey + 'fontSize"]').val()));

    elementH2.sfsipluscss("color", SFSI('input[name="' + grpKey + 'fontColor"]').val());

    "yes" == SFSI('input[name="' + grpKey + 'border_shadow"]:checked').val() ? element.css("box-shadow", "12px 30px 18px #CCCCCC") : element.css("box-shadow", "none");
}

jQuery.fn.extend({
    sfsipluscss: function (styleName, value) {
        if (typeof this[0] != 'undefined') {
            this[0].style.setProperty(styleName, value, 'important');
        }
    },
});

function sfsi_plus_set_style(elem, styleName, value) {
    if (typeof elem != 'undefined') {
        elem[0].style.setProperty(styleName, value, 'important');
    }
}

function sfsi_plus_stick_widget(s) {
    0 == sfsiplus_initTop.length && (SFSI(".sfsi_plus_widget").each(function (s) {
        sfsiplus_initTop[s] = SFSI(this).position().top;
    }));
    var i = SFSI(window).scrollTop(),
        e = [],
        t = [];
    SFSI(".sfsi_plus_widget").each(function (s) {
        e[s] = SFSI(this).position().top, t[s] = SFSI(this);
    });
    var n = !1;
    for (var o in e) {
        var a = parseInt(o) + 1;
        e[o] < i && e[a] > i && a < e.length ? (SFSI(t[o]).css({
            position: "fixed",
            top: s
        }), SFSI(t[a]).css({
            position: "",
            top: sfsiplus_initTop[a]
        }), n = !0) : SFSI(t[o]).css({
            position: "",
            top: sfsiplus_initTop[o]
        });
    }
    if (!n) {
        var r = e.length - 1,
            c = -1;
        e.length > 1 && (c = e.length - 2), sfsiplus_initTop[r] < i ? (SFSI(t[r]).css({
            position: "fixed",
            top: s
        }), c >= 0 && SFSI(t[c]).css({
            position: "",
            top: sfsiplus_initTop[c]
        })) : (SFSI(t[r]).css({
            position: "",
            top: sfsiplus_initTop[r]
        }), c >= 0 && e[c] < i);
    }
}

function sfsi_plus_hideFooter() {
}

window.onerror = function () {
};

SFSI(window).on('load', function () {
    SFSI("#sfpluspageLoad").fadeOut(2e3);
});


//changes done {Monad}
function sfsi_plus_selectText(containerid) {
    if (document.selection) {
        var range = document.body.createTextRange();
        range.moveToElementText(document.getElementById(containerid));
        range.select();
    } else if (window.getSelection()) {
        var range = document.createRange();
        range.selectNode(document.getElementById(containerid));
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
    }
}

function sfsi_plug_text_before_icons() {
    //SFSI('input[name="sfsi_plus_textBefor_icons_fontcolor"]').val() != "" ? (SFSI(".sfsi_plus_subscribe_Popinner").find('input[name="data[Widget][email]"]').css("color", SFSI('input[name="sfsi_plus_form_field_fontcolor"]').val())) : '';
}

function sfsi_get_string_between_curly_brackets(string) {
    var found = [], // an array to collect the strings that are found
        rxp = /{([^}]+)}/g,
        curMatch;

    while (curMatch = rxp.exec(string)) {
        found.push(curMatch[1]);
    }

    return found;
}

function sfsi_isValidUrl(url) {

    var regForUrl = /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;

    if (!regForUrl.test(url)) {
        return false;
    }

    return true;
}

function sfsi_array_chunk(input, size, preserveKeys) { // eslint-disable-line camelcase
    //  discuss at: http://locutus.io/php/array_chunk/
    // original by: Carlos R. L. Rodrigues (http://www.jsfromhell.com)
    // improved by: Brett Zamir (http://brett-zamir.me)
    //      note 1: Important note: Per the ECMAScript specification,
    //      note 1: objects may not always iterate in a predictable order
    //   example 1: array_chunk(['Kevin', 'van', 'Zonneveld'], 2)
    //   returns 1: [['Kevin', 'van'], ['Zonneveld']]
    //   example 2: array_chunk(['Kevin', 'van', 'Zonneveld'], 2, true)
    //   returns 2: [{0:'Kevin', 1:'van'}, {2: 'Zonneveld'}]
    //   example 3: array_chunk({1:'Kevin', 2:'van', 3:'Zonneveld'}, 2)
    //   returns 3: [['Kevin', 'van'], ['Zonneveld']]
    //   example 4: array_chunk({1:'Kevin', 2:'van', 3:'Zonneveld'}, 2, true)
    //   returns 4: [{1: 'Kevin', 2: 'van'}, {3: 'Zonneveld'}]

    var x
    var p = ''
    var i = 0
    var c = -1
    var l = input.length || 0
    var n = []

    if (size < 1) {
        return null
    }

    if (Object.prototype.toString.call(input) === '[object Array]') {
        if (preserveKeys) {
            while (i < l) {
                (x = i % size) ?
                    n[c][i] = input[i] : n[++c] = {};
                n[c][i] = input[i]
                i++
            }
        } else {
            while (i < l) {
                (x = i % size) ?
                    n[c][x] = input[i] : n[++c] = [input[i]]
                i++
            }
        }
    } else {
        if (preserveKeys) {
            for (p in input) {
                if (input.hasOwnProperty(p)) {
                    (x = i % size) ?
                        n[c][p] = input[p] : n[++c] = {};
                    n[c][p] = input[p]
                    i++
                }
            }
        } else {
            for (p in input) {
                if (input.hasOwnProperty(p)) {
                    (x = i % size) ?
                        n[c][x] = input[p] : n[++c] = [input[p]]
                    i++
                }
            }
        }
    }

    return n;
}

function sfsi_replaceBetween(str, start, end, what) {
    return str.substring(0, start) + what + str.substring(end);
}

function sfsi_filteredText(txt, searchStr, replaceStr) {

    var startIndex = 0,
        index;

    while ((index = txt.indexOf(searchStr, startIndex)) > -1) {
        txt = sfsi_replaceBetween(txt, index, index + searchStr.length, replaceStr);
    }

    return txt;
}

function sfsi_strip_special_characters(string = '') {

    if ((string === null) || (string === ''))
        return false;
    else
        string = string.replace(/[`~!@#$%^&*()_|+\-=÷¿?;'",.<>]/gi, '');
    return string;
}

var sfsi_strip_scripts = function (a, b, c) {
    b = new Option;
    b.innerHTML = a;
    for (a = b.getElementsByTagName('script'); c = a[0];) c.parentNode.removeChild(c);
    return b.innerHTML
}

function sfsi_strip_only_html_tags(str) {
    if ((str === null) || (str === ''))
        return false;
    else
        str = str.toString();
    return str.replace(/<[^>]*>/g, '');
}

function sfsi_strip_html_tags_with_content(str) {

    if ((str === null) || (str === '')) {
        return false;
    } else {

        var html = SFSI.parseHTML(str);

        SFSI.each(html, function (i, el) {

            var nodeName = el.nodeName.toLowerCase();

            if ("#text" != nodeName) {
                var strtoreplace = "<" + nodeName + ">" + el.textContent + "</" + nodeName + ">";
                str = str.replace(strtoreplace, '');
            }

        });

        return str;
    }
}

function escapeHtml(unsafe = '') {
    if (typeof unsafe != 'string') unsafe = '';
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function sfsi_plus_create_suscriber_form() {
    //Popbox customization
    "no" == SFSI('input[name="sfsi_plus_form_adjustment"]:checked').val() ? SFSI(".sfsi_plus_subscribe_Popinner").css({
        "width": parseInt(SFSI('input[name="sfsi_plus_form_width"]').val()),
        "height": parseInt(SFSI('input[name="sfsi_plus_form_height"]').val())
    }) : SFSI(".sfsi_plus_subscribe_Popinner").css({
        "width": '',
        "height": ''
    });

    "yes" == SFSI('input[name="sfsi_plus_form_adjustment"]:checked').val() ? SFSI(".sfsi_plus_html > .sfsi_plus_subscribe_Popinner").css({
        "width": "100%"
    }) : '';

    "yes" == SFSI('input[name="sfsi_plus_form_border"]:checked').val() ? SFSI(".sfsi_plus_subscribe_Popinner").css({
        "border": SFSI('input[name="sfsi_plus_form_border_thickness"]').val() + "px solid " + SFSI('input[name="sfsi_plus_form_border_color"]').val()
    }) : SFSI(".sfsi_plus_subscribe_Popinner").css("border", "none");

    SFSI('input[name="sfsi_plus_form_background"]').val() != "" ? (SFSI(".sfsi_plus_subscribe_Popinner").css("background-color", SFSI('input[name="sfsi_plus_form_background"]').val())) : '';

    //Heading customization
    var _htext = escapeHtml(SFSI('input[name="sfsi_plus_form_heading_text"]').val());
    var _hfont = SFSI('#sfsi_plus_form_heading_font').val();
    var _hfontstyle = SFSI('#sfsi_plus_form_heading_fontstyle').val();
    var _hfontcolor = SFSI('input[name="sfsi_plus_form_heading_fontcolor"]').val();
    var _hfontsize = parseInt(SFSI('input[name="sfsi_plus_form_heading_fontsize"]').val());
    var _hfontalign = SFSI('#sfsi_plus_form_heading_fontalign').val();
    var _helem = SFSI(".sfsi_plus_subscribe_Popinner").find('h5');

    _htext != "" ? _helem.text(_htext) : _helem.text('');

    _hfont != "" ? _helem.attr("style", "font-family: " + _hfont + " !important") : '';

    if (_hfontstyle != 'bold') {
        _hfontstyle != "" ? _helem.css("font-style", _hfontstyle) : '';
        _helem.css("font-weight", '');
    } else {
        _hfontstyle != "" ? _helem.css("font-weight", "bold") : '';
        _helem.css("font-style", '');
    }

    _hfontcolor != "" ? _helem.css("color", _hfontcolor) : '';
    _hfontsize != "" ? _helem.css("font-size", _hfontsize) : '';
    _hfontalign != "" ? _helem.css("text-align", _hfontalign) : '';

    //Field customization
    var _text = SFSI('input[name="sfsi_plus_form_field_text"]').val();
    var _font = SFSI('#sfsi_plus_form_field_font').val();
    var _fontstyle = SFSI('#sfsi_plus_form_field_fontstyle').val();
    var _fontcolor = SFSI('input[name="sfsi_plus_form_field_fontcolor"]').val();
    var _fontsize = parseInt(SFSI('input[name="sfsi_plus_form_field_fontsize"]').val());
    var _fontalign = SFSI('#sfsi_plus_form_field_fontalign').val();

    var _elem = SFSI(".sfsi_plus_subscribe_Popinner").find('input[name="data[Widget][email]"]');
    var _elem1 = SFSI(".sfsi_plus_left_container > .sfsi_plus_subscribe_Popinner").find('input[name="data[Widget][email]"]');
    var _elem2 = SFSI(".like_pop_box > .sfsi_plus_subscribe_Popinner").find('input[name="data[Widget][email]"]');

    _text != "" ? (_elem.attr("placeholder", _text)) : _elem.attr("placeholder", '');
    _text != "" ? (_elem1.val(_text)) : _elem1.val('');
    _text != "" ? (_elem2.val(_text)) : _elem2.val('');

    _font != "" ? (_elem.css("font-family", _font)) : '';

    if (_fontstyle != "bold") {
        _fontstyle != "" ? _elem.css("font-style", _fontstyle) : '';
        _elem.css("font-weight", '');
    } else {
        _fontstyle != "" ? _elem.css("font-weight", 'bold') : '';
        _elem.css("font-style", '');
    }

    _fontcolor != "" ? _elem.css("color", _fontcolor) : '';
    _fontsize != "" ? _elem.css("font-size", _fontsize) : '';
    _fontalign != "" ? _elem.css("text-align", _fontalign) : '';

    //Button customization
    var btnText = SFSI('input[name="sfsi_plus_form_button_text"]').val();
    var btnFontFamily = SFSI('#sfsi_plus_form_button_font').val();
    var btnFontStyle = SFSI('#sfsi_plus_form_button_fontstyle').val();
    var btnFontColor = SFSI('input[name="sfsi_plus_form_button_fontcolor"]').val();
    var btnFontSize = parseInt(SFSI('input[name="sfsi_plus_form_button_fontsize"]').val());
    var btnFontAlign = SFSI('#sfsi_plus_form_button_fontalign').val();
    var btnBgrnd = SFSI('#sfsi_plus_form_button_background').val();
    var btnElem = SFSI(".sfsi_plus_subscribe_Popinner").find('input[name="subscribe"]');

    btnText != "" ? btnElem.attr("value", btnText) : '';
    btnFontFamily != "" ? btnElem.css("font-family", btnFontFamily) : '';

    if (btnFontStyle != "bold") {
        btnFontStyle != "" ? btnElem.css("font-style", btnFontStyle) : '';
        btnElem.css("font-weight", '');
    } else {
        btnFontStyle != "" ? btnElem.css("font-weight", 'bold') : '';
        btnElem.css("font-style", '');
    }

    btnFontColor != "" ? btnElem.css("color", btnFontColor) : '';
    btnFontSize != "" ? btnElem.css("font-size", btnFontSize) : '';
    btnFontAlign != "" ? btnElem.css("text-align", btnFontAlign) : '';
    btnBgrnd != "" ? btnElem.css("background-color", btnBgrnd) : '';

    // Privacy Notice
    var privacyText = SFSI('input[name="sfsi_plus_form_privacynotice_text"]').val();
    privacyText = sfsi_strip_html_tags_with_content(sfsi_strip_scripts(sfsi_strip_special_characters(privacyText)));

    var privacyTextColor = SFSI('input[name="sfsi_plus_form_privacynotice_fontcolor"]').val();
    var privacyFontSize = parseInt(SFSI('input[name="sfsi_plus_form_privacynotice_fontsize"]').val());
    var privacyAlign = SFSI('#sfsi_plus_form_privacynotice_fontalign').val();
    var privacyFontFamily = SFSI('#sfsi_plus_form_privacynotice_font').val();
    var privacyFontStyle = SFSI('#sfsi_plus_form_privacynotice_fontstyle').val();

    var privacyDisplay = SFSI('input[name="sfsi_plus_shall_display_privacy_notice"]:checked').val();

    var privacyTextElem = SFSI(".sfsi_plus_subscribe_Popinner").find('.sfsi_plus_privacy_notice');

    var arrTxtLink = sfsi_get_string_between_curly_brackets(privacyText);

    if (arrTxtLink.length > 0) {

        arrTxtLink = sfsi_array_chunk(arrTxtLink, 2, false);

        SFSI.each(arrTxtLink, function (key, val) {

            var linkTxt = val[0];
            var linkHref = val[1];

            if (typeof linkHref != 'undefined' && linkHref.length > 0 && sfsi_isValidUrl(linkHref)) {

                var flinkTxt = typeof linkTxt != 'undefined' && linkTxt.length > 0 ? SFSI.trim(linkTxt) : 'Privacy Policy';

                var linkStr = '<a target="_blank" style="box-shadow: 0 1px 0 0 currentColor;" href="' + linkHref + '">' + flinkTxt + '</a>';

                linkTxt = '{' + linkTxt + '}';

                privacyText = sfsi_filteredText(privacyText, linkTxt, linkStr);
                privacyText = privacyText.replace("{" + linkHref + "}", "");
            }

        });
    }

    typeof privacyText != 'undefined' ? privacyTextElem.html(privacyText) : privacyTextElem.html('');

    if (typeof privacyAlign != 'undefined') {
        privacyTextElem.css("text-align", privacyAlign);
    }

    if (typeof privacyTextColor != 'undefined') {
        privacyTextElem.css("color", privacyTextColor);
    }

    if (typeof privacyFontFamily != 'undefined') {
        privacyTextElem.css("font-family", privacyFontFamily);
    }

    if (typeof privacyFontStyle != 'undefined') {

        if (privacyFontStyle != "bold") {
            privacyFontStyle != "" ? privacyTextElem.css("font-style", privacyFontStyle) : '';
            privacyTextElem.css("font-weight", '');
        } else {
            privacyFontStyle != "" ? privacyTextElem.css("font-weight", 'bold') : '';
            privacyTextElem.css("font-style", '');
        }
    }

    if (typeof privacyFontSize != 'undefined') {
        privacyTextElem.css({
            "font-size": privacyFontSize
        });
    }

    if (typeof privacyDisplay != 'undefined') {
        var display = "no" == privacyDisplay ? "none" : "block";
        privacyTextElem.parent().css({
            "display": display
        });
    }

    var innerHTML = SFSI(".sfsi_plus_html > .sfsi_plus_subscribe_Popinner").html();
    var styleCss = SFSI(".sfsi_plus_html > .sfsi_plus_subscribe_Popinner").attr("style");

    innerHTML = '<div style="' + styleCss + '">' + innerHTML + '</div>';
    SFSI(".sfsi_plus_subscription_html > xmp").html(innerHTML);

    /*var data = {
		action:"getForm",
		heading: SFSI('input[name="sfsi_plus_form_heading_text"]').val(),
		placeholder:SFSI('input[name="sfsi_plus_form_field_text"]').val(),
		button:SFSI('input[name="sfsi_plus_form_button_text"]').val()
	};
	SFSI.ajax({
        url:sfsi_premium_ajax_object.ajax_url,
        type:"post",
        data:data,
        success:function(s) {
			SFSI(".sfsi_plus_subscription_html").html(s);
		}
    });*/
}

var global_error = 0;

function sfsi_premium_pinterest_hide_image() {
    if (SFSI("input[name='sfsi_premium_pinterest_sharing_texts_and_pics']:checked").val() == "no") {
        SFSI('.pinterest_global_image').show();
        SFSI('.pinterest_gap').show();

    } else {
        SFSI('.pinterest_global_image').hide();
        SFSI('.pinterest_gap').show();

    }
}

window.sfsi_load = function () {
    sfsi_premium_pinterest_hide_image();


    sfsi_premium_togle_pinterest_positions();

    function sfsi_premium_togle_pinterest_positions() {
        if (SFSI("input[name='sfsi_plus_icon_hover_show_pinterest']:checked").val() !== "yes") {
            SFSI('.sfsi_premium_pinterest_positions_section').hide();
        }
    }

    function showAccessToken() {

        var accsssToken = SFSI("input[name='sfsi_plus_facebook_countsFrom_blog']").val();

        if (accsssToken.length > 0) {
            SFSI(".sfsi_plus_facebookfbtocen").slideDown();
            SFSI(".sfsi_premium_fbInstructions_fbtoken").slideDown();
        }
    }

    // ******************** Image Picker handling for remove image STARTS here ***********************//

    var noImgUrl = SFSI(".usm-remove-media").attr('data-noimageurl');

    SFSI('.imgpicker').hover(function () {

        var imgSrc = SFSI(this).children('img').attr('src');

        if (noImgUrl != imgSrc) {
            SFSI(this).children(".usm-remove-media").show();
            SFSI(this).children(".usm-overlay").show();
        }

    }, function () {

        var imgSrc = SFSI(this).children('img').attr('src');

        if (noImgUrl != imgSrc) {
            SFSI(this).children(".usm-remove-media").hide();
            SFSI(this).children(".usm-overlay").hide();
        }
    });

    SFSI('.usm-remove-media').click(function () {

        // Hide overlay & remove link
        SFSI(this).prev().hide();
        SFSI(this).hide();

        // Set image value empty
        SFSI(this).parent().next().next().next().val("");

        // Set no image in image preview
        var noImgUrl = SFSI(this).attr("data-noimageurl");
        SFSI(this).prev().prev().attr("src", noImgUrl);

        SFSI(this).parent().next().children('.button').val("Add Picture");
        SFSI(this).parent().parent().find("input[type='hidden']").val("");

    });

    // ******************** Image Picker handling CLOSES here ***********************//


    var emailOptionQue2 = SFSI("input[name='sfsi_plus_rss_icons']:checked").val();

    if ("string" === typeof emailOptionQue2) {

        var arrSel = ['plus_share_icon_order', 'plus_share_icon_mobile_order', 'dbMobileOrder'];

        arrSel.forEach(function (element, index) {

            var _imgElem = SFSI("." + element + " .sfsiplus_email_section").find('img');
            _imgElem = _imgElem.length > 0 ? _imgElem : SFSI("." + element + " .sfsiplus_email_mobilesection").find('img');

            if (_imgElem.length > 0) {
                var _emailSrc = _imgElem.attr('src');
                _emailSrc = _emailSrc.replace(/email/g, emailOptionQue2);
                _imgElem.attr('src', _emailSrc);
            }

        });
    }

    SFSI("body").on("click", ".sfsi_plus_exclude_url_checkSec span.checkbox", function () {
        var value = SFSI(this).css("background-position");
        if (value === '0px -36px') {
            SFSI(".sfsi_plus_keywords_container, .sfsi_plus_addAnotherFiled").show();
        } else {
            SFSI(".sfsi_plus_keywords_container, .sfsi_plus_addAnotherFiled").hide();
        }
    });

    /*SFSI("body").on("click", ".radio", function () {
    	console.log(SFSI(this));
    });*/

    SFSI("body").on("click", ".sfsi_plus_popup_timelimit_radioCheck span.radio", function () {
        var value = SFSI(this).next("input[name='sfsi_plus_popup_limit']").val();
        if (value === 'yes') {
            SFSI(".sfsi_plus_popup_timelimit").show();
        } else {
            SFSI(".sfsi_plus_popup_timelimit").hide();
        }
    });


    SFSI("body").on("click", ".sfsi_premium_mobile_section span.radio", function () {

        var value = SFSI(this).next("input[name='sfsi_plus_icons_onmobile']").val();

        if ('yes' == value) {
            SFSI(".sfsi_premium_mobile_icon_listing").show();

            var mobileIconDBHtml = SFSI("ul.dbMobileOrder").html();

            SFSI(".plus_share_icon_mobile_order").html(mobileIconDBHtml);

            arrIcons.forEach(function (iconName, index) {
                sfsi_plus_toogle_mobile_icon_for_order_icons(iconName);
            });
        } else {

            SFSI(".sfsi_premium_mobile_icon_listing").hide();

            var desktopIconHtml = SFSI(".plus_share_icon_order").html();

            SFSI(".plus_share_icon_mobile_order").html(desktopIconHtml);

            SFSI(".plus_share_icon_mobile_order li").each(function () {

                var ref = SFSI(this);

                var oldClasses = ref.attr("class");
                oldClasses = oldClasses.replace(/section/g, "mobilesection");
                ref.attr("class", oldClasses);

                var oldId = ref.attr("id");

                if ('undefined' != typeof oldId) {
                    oldId = oldId.replace(/order/g, "mobile_order");
                    ref.attr("id", oldId);
                }
            });
        }

    });

    SFSI("body").on("click", ".sfsi_plus_mobile_float span.radio", function () {
        var value = SFSI(this).next("input[name='sfsi_plus_mobile_float']").val();
        if (value === 'yes') {
            SFSI(".sfsi_plus_mobile_float_section").show();
        } else {
            SFSI(".sfsi_plus_mobile_float_section").hide();
        }
    });

    SFSI("body").on("click", ".sfsi_plus_mobile_widget span.radio", function () {
        var value = SFSI(this).next("input[name='sfsi_plus_mobile_widget']").val();
        if (value === 'yes') {
            SFSI(".sfsi_plus_widget_mobile_icons_alignment").removeClass("hide").addClass("show");
        } else {
            SFSI(".sfsi_plus_widget_mobile_icons_alignment").removeClass("show").addClass("hide");
        }
    });

    SFSI("body").on("click", ".sfsi_plus_mobile_shortcode span.radio", function () {
        var value = SFSI(this).next("input[name='sfsi_plus_mobile_shortcode']").val();
        if (value === 'yes') {
            SFSI(".sfsi_plus_shortcode_mobile_icons_alignment").removeClass("hide").addClass("show");
        } else {
            SFSI(".sfsi_plus_shortcode_mobile_icons_alignment").removeClass("show").addClass("hide");
        }
    });

    SFSI("body").on("click", ".sfsi_plus_mobile_beforeafterposts span.radio", function () {
        var value = SFSI(this).next("input[name='sfsi_plus_mobile_beforeafterposts']").val();
        if (value === 'yes') {
            SFSI(".sfsi_plus_beforeafterposts_mobile_icons_alignment").removeClass("hide").addClass("show");
        } else {
            SFSI(".sfsi_plus_beforeafterposts_mobile_icons_alignment").removeClass("show").addClass("hide");
        }
    });

    SFSI("body").on("click", ".sfsi_plus_mobile_size_space_beforeafterposts span.radio", function () {
        var value = SFSI(this).next("input[name='sfsi_plus_mobile_size_space_beforeafterposts']").val();
        if (value === 'yes') {
            SFSI(".sfsi_plus_beforeafterposts_mobile_icons_size_space").removeClass("hide").addClass("show");
        } else {
            SFSI(".sfsi_plus_beforeafterposts_mobile_icons_size_space").removeClass("show").addClass("hide");
        }
    });

    SFSI("body").on("click", ".sfsi_plus_mobile_selection span.radio", function () {
        var value = SFSI(this).next("input[name='sfsi_plus_mobile_icon_setting']").val();
        if (value === 'yes') {
            SFSI(".sfsi_plus_mobile_icons_setting_wrpr").show();
        } else {
            SFSI(".sfsi_plus_mobile_icons_setting_wrpr").hide();
        }

    });

    SFSI("body").on("click", ".sfsi_plus_mobile_selection_alignment span.radio", function () {
        var value = SFSI(this).next("input[name='sfsi_plus_mobile_icon_alignment_setting']").val();
        if (value === 'yes') {
            SFSI(".sfsi_plus_mobile_icons_alignment_setting_wrpr").show();
        } else {
            SFSI(".sfsi_plus_mobile_icons_alignment_setting_wrpr").hide();
        }
    });

    SFSI("body").on("click", ".sfsi_plus_tokenGenerateButton a", function () {

        var clienId = SFSI("input[name='sfsi_plus_instagram_clientid']").val();
        var redirectUrl = SFSI("input[name='sfsi_plus_instagram_appurl']").val();

        // var scope = "likes+comments+basic+public_content+follower_list+relationships";
        var scope = "basic";
        var instaUrl = "https://www.instagram.com/oauth/authorize/?client_id=<id>&redirect_uri=<url>&response_type=token&scope=" + scope;

        if (clienId !== '' && redirectUrl !== '') {
            instaUrl = instaUrl.replace('<id>', clienId);
            instaUrl = instaUrl.replace('<url>', redirectUrl);

            window.open(instaUrl, '_blank');
        } else {
            alert("Please enter client id and redirect url first");
        }

    });

    SFSI("body").on("click", ".sfsi_plus_codeGenerateButton a", function () {

        var appId = SFSI("input[name='sfsi_plus_pinterest_appid']").val();
        var appSecret = SFSI("input[name='sfsi_plus_pinterest_appsecret']").val();
        var redirectUrl = SFSI("input[name='sfsi_plus_pinterest_appurl']").val();

        var scope = "read_public";
        var pinUrl = "https://api.pinterest.com/oauth/?response_type=code&redirect_uri=<url>&client_id=<id>&scope=" + scope;

        if (appId !== '' && redirectUrl !== '') {
            pinUrl = pinUrl.replace('<id>', appId);
            pinUrl = pinUrl.replace('<url>', redirectUrl);

            window.open(pinUrl, '_blank');
        } else {
            alert("Please enter App ID and redirect url first");
        }

    });


    SFSI("body").on("click", ".sfsi_plus_paccessTokenGenerateButton a", function () {

        var appId = SFSI("input[name='sfsi_plus_pinterest_appid']").val();
        var appSecret = SFSI("input[name='sfsi_plus_pinterest_appsecret']").val();
        var code = SFSI("input[name='sfsi_plus_pinterest_code']").val();

        var pinUrl = "https://api.pinterest.com/v1/oauth/token?grant_type=authorization_code&client_id=<id>&client_secret=<appSecret>&code=<code>";

        if (appId !== '' && appSecret !== '' && code !== '') {
            pinUrl = pinUrl.replace('<id>', appId);
            pinUrl = pinUrl.replace('<appSecret>', appSecret);
            pinUrl = pinUrl.replace('<code>', code);

            SFSI.ajax({
                url: pinUrl,
                dataType: 'json',
                type: 'POST',
                data: {
                    grant_type: "authorization_code",
                    client_id: appId,
                    client_secret: appSecret,
                    code: code
                },
                success: function (data) {
                    if (typeof data != undefined && typeof data.access_token != undefined) {
                        SFSI("input[name='sfsi_plus_pinterest_access_token']").val(data.access_token);
                        alert("Access token successfully generated");
                    }
                },
                error: function (data) {
                    alert("Failed to generate access token");
                }
            });
        } else {
            alert("Please enter App ID,App secret and Code first");
        }

    });

    SFSI(".lanOnchange").change(function () {

        var currentDrpdown = SFSI(this).parents(".icons_size");
        var nonce = SFSI('input[name="sfsi_premium_lanOnchange_nonce"]').val();
        var data = {
            action: "getIconPreview",
            nonce: nonce,
            iconValue: SFSI(this).val(),
            iconname: SFSI(this).attr("data-iconUrl")
        };
        var id = SFSI(this).attr('id');
        if (id.indexOf('linkedin') != -1) {

            currentDrpdown.children(".social-img-link").html('<img src="' + data.iconname + "/icon_" + data.iconValue + '.svg" >');
            return;
        }
        if (id.indexOf('youtube') != -1) {
            currentDrpdown.children(".social-img-link").html('<img src="' + data.iconname + "/icon_" + data.iconValue + '.svg">');
            return;
        }
        SFSI.ajax({
            url: sfsi_premium_ajax_object.ajax_url,
            type: "post",
            data: data,
            success: function (s) {
                currentDrpdown.children(".social-img-link").html(s);
            }
        });
    });

    SFSI(".sfsiplus_tab_3_icns").on("click", ".cstomskins_upload", function () {
        SFSI(".cstmskins-overlay").show("slow", function () {
            e = 0;
        });
    });

    SFSI(document).on("click", '#custmskin_clspop', function () {
        SFSI_plus_done();
        SFSI(".cstmskins-overlay").hide("slow");
    });

    sfsi_plus_create_suscriber_form();

    SFSI('input[name="sfsi_plus_form_privacynotice_fontsize"],input[name="sfsi_plus_form_privacynotice_text"],input[name="sfsi_plus_form_heading_text"], input[name="sfsi_plus_form_border_thickness"], input[name="sfsi_plus_form_height"], input[name="sfsi_plus_form_width"], input[name="sfsi_plus_form_heading_fontsize"], input[name="sfsi_plus_form_field_text"], input[name="sfsi_plus_form_field_fontsize"], input[name="sfsi_plus_form_button_text"], input[name="sfsi_plus_form_button_fontsize"]').on("keyup click", sfsi_plus_create_suscriber_form);

    SFSI('input[name="sfsi_plus_form_privacynotice_fontcolor"],input[name="sfsi_plus_form_border_color"], input[name="sfsi_plus_form_background"] ,input[name="sfsi_plus_form_heading_fontcolor"], input[name="sfsi_plus_form_field_fontcolor"] ,input[name="sfsi_plus_form_button_fontcolor"],input[name="sfsi_plus_form_button_background"]').on("focus", sfsi_plus_create_suscriber_form);

    //SFSI("#sfsi_plus_form_privacynotice_font,#sfsi_plus_form_privacynotice_fontalign,#sfsi_plus_form_heading_font, #sfsi_plus_form_heading_fontstyle, #sfsi_plus_form_heading_fontalign, #sfsi_plus_form_field_font, #sfsi_plus_form_field_fontstyle, #sfsi_plus_form_field_fontalign, #sfsi_plus_form_button_font, #sfsi_plus_form_button_fontstyle, #sfsi_plus_form_button_fontalign").on("change", sfsi_plus_create_suscriber_form );
    SFSI("#sfsi_plus_form_privacynotice_font,#sfsi_plus_form_privacynotice_fontstyle,#sfsi_plus_form_privacynotice_fontalign,#sfsi_plus_form_heading_font, #sfsi_plus_form_heading_fontstyle, #sfsi_plus_form_heading_fontalign, #sfsi_plus_form_field_font, #sfsi_plus_form_field_fontstyle, #sfsi_plus_form_field_fontalign, #sfsi_plus_form_button_font, #sfsi_plus_form_button_fontstyle, #sfsi_plus_form_button_fontalign").on("change", sfsi_plus_create_suscriber_form);

    SFSI(document).on("click", '.radio', function () {

        var s = SFSI(this).parent().find("input:radio:first");

        var name = s.attr("name");
        var val = s.val();
        /*console.log('name',name,"val",val);*/
        switch (name) {

            case 'sfsi_plus_form_adjustment':
                if (val == 'no')
                    s.parents(".row_tab").next(".row_tab").show("fast");
                else
                    s.parents(".row_tab").next(".row_tab").hide("fast");
                sfsi_plus_create_suscriber_form();
                break;

            case 'sfsi_plus_form_border':
                if (val == 'yes')
                    s.parents(".row_tab").next(".row_tab").show("fast");
                else
                    s.parents(".row_tab").next(".row_tab").hide("fast");
                sfsi_plus_create_suscriber_form();
                break;

            case 'sfsi_plus_shall_display_privacy_notice':

                SFSI('input[name="' + name + '"]').removeAttr('checked');

                if (val == 'yes') {
                    SFSI('input[name="' + name + '"][value="yes"]').prop('checked', 'true');
                    s.parents(".row_tab").nextAll().addClass("show").removeClass("hide");
                } else {
                    SFSI('input[name="' + name + '"][value="no"]').prop('checked', 'true');
                    s.parents(".row_tab").nextAll().addClass("hide").removeClass("show");
                }

                sfsi_plus_create_suscriber_form();

                break;

            case 'sfsi_plus_icons_suppress_errors':
            case 'sfsi_plus_nofollow_links':

                SFSI('input[name="' + name + '"]').removeAttr('checked');

                if (s.val() == 'yes')
                    SFSI('input[name="' + name + '"][value="yes"]').prop('checked', 'true');
                else
                    SFSI('input[name="' + name + '"][value="no"]').prop('checked', 'true');
                break;

            case 'sfsi_plus_icons_rules':

                SFSI('input[name="' + name + '"]').removeAttr('checked');

                var value = parseInt(val);

                SFSI('.sfsi_exclude_ul').removeClass('show').addClass('hide');
                SFSI('.iconRuleApply').hide();

                switch (value) {

                    case 1:
                    case 2:
                        s.parent().next('.sfsi_exclude_ul').removeClass('hide').addClass('show');
                        SFSI('.iconRuleApply').show();
                        break;

                }
                break;

            case 'sfsi_plus_mobile_icons_order_setting':

                SFSI('input[name="' + name + '"]').removeAttr('checked');

                var orderMelem = SFSI('.plus_share_icon_mobile_order').parent();

                if ('yes' == val) {
                    SFSI('input[name="' + name + '"][value="yes"]').attr('checked', 'true');
                    /*  console.log(orderMelem); */
                    orderMelem.removeClass('hide').addClass('show');
                    sfsi_premium_toggle_mobile_icons_for_order(false);
                } else {
                    SFSI('input[name="' + name + '"][value="no"]').attr('checked', 'true');
                    orderMelem.removeClass('show').addClass('hide');
                    sfsi_premium_toggle_mobile_icons_for_order(true);
                }
                break;

            case 'sfsi_plus_mouseOver_effect_type':

                SFSI('input[name="' + name + '"]').removeAttr('checked');

                if ('same_icons' == val) {
                    SFSI('.same_icons_effects').removeClass('hide').addClass('show');
                    SFSI('.other_icons_effects_options').removeClass('show').addClass('hide');
                } else if ('other_icons' == val) {
                    SFSI('.same_icons_effects').removeClass('show').addClass('hide');
                    SFSI('.other_icons_effects_options').removeClass('hide').addClass('show');

                }

                break;

            case 'sfsi_plus_loadjscript':

                SFSI('input[name="' + name + '"]').click(function () {

                    if (val == "yes") {
                        SFSI('.afterdisable').removeClass('show').addClass('hide');
                    } else {
                        SFSI('.afterdisable').removeClass('hide').addClass('show');
                    }
                });
                break;

            case 'sfsi_plus_icons_ClickPageOpen':
            case 'sfsi_plus_icons_mobile_ClickPageOpen':
            case 'sfsi_plus_mobile_open_type_setting':

                let s_1 = SFSI('input[name="sfsi_plus_icons_ClickPageOpen"]:checked');
                let s_2 = SFSI('input[name="sfsi_plus_icons_mobile_ClickPageOpen"]:checked');
                let s_3 = SFSI('input[name="sfsi_plus_mobile_open_type_setting"]:checked');

                if (
                    (s_1.val() === 'no' && s_2.val() === 'no') ||
                    (s_2.val() === 'no' && s_3.val() === 'yes') ||
                    (s_1.val() === 'no' && s_3.val() === 'no')
                ) {
                    jQuery(".usmi-premium-noopener").slideUp(300);
                } else {
                    jQuery(".usmi-premium-noopener").slideDown(300);
                }


                /*SFSI('input[name="' + name + '"]').click(function () {
                console.log("sfsiplus_row_onl sfsi_premium_mobile_version "+val);*/
                if (s_3.val() == "yes") {
                    SFSI('.sfsiplus_row_onl.sfsi_premium_mobile_version').show();
                } else {
                    SFSI('.sfsiplus_row_onl.sfsi_premium_mobile_version').hide();
                }

                break;

            default:

        }
    });

    SFSI(document).on("click", '.radio', function () {
        SFSI(this).next('input[type="radio"]').trigger('click');
    });

    // Adding subscriber form option in Question 7 STARTS //
    SFSI(document).on("click", '.radio', function () {

        var s = SFSI(this).parent().find("input:radio:first");

        if (s.attr("name") == "sfsi_plus_popup_type_iconsOrForm") {

            if (s.val() == 'icons') {
                SFSI(".plus_sfsi_sample_icons").show();
                SFSI(".plus_sfsi_subscribe_form").hide();
            } else {
                SFSI(".plus_sfsi_sample_icons").hide();
                SFSI(".plus_sfsi_subscribe_form").show();
            }
        }
    });
    // Adding subscriber form option in Question 7 CLOSES //


    // ********************************************* Show/Hide desktop-mobile setting div in  Question 7 STARTS **************************//
    SFSI(document).on("click", '.radio', function () {

        var s = SFSI(this).parent().find("input:radio:first");

        if (s.attr("name") == "sfsi_plus_Show_popupOn") {

            if (s.val() == 'none') {
                SFSI(".popupDesktopMobileLi").hide();
            } else {
                SFSI(".popupDesktopMobileLi").show();
            }
        }
    });

    // ********************************************* Show/Hide desktop-mobile setting div in  Question 7 CLOSES **************************//

    SFSI(document).on('click', '.radio', function () {

        var s = SFSI(this).parent().find("input:radio:first");

        if (s.attr("name") == "sfsi_plus_Show_popupOn") {

            if (s.val() == 'somepages') {
                SFSI(".popup_containter").slideDown();
            } else {
                SFSI(".popup_containter").slideUp();
            }
        }
    });

    /* Flat icon BG color */
    SFSI('#sfsi_plus_rss_bgColor, #sfsi_plus_email_bgColor, #sfsi_plus_facebook_bgColor, #sfsi_plus_twitter_bgColor, #sfsi_plus_share_bgColor, #sfsi_plus_pinterest_bgColor, #sfsi_plus_linkedin_bgColor, #sfsi_plus_instagram_bgColor, #sfsi_plus_ria_bgColor, #sfsi_plus_inha_bgColor, #sfsi_plus_houzz_bgColor, #sfsi_plus_snapchat_bgColor, #sfsi_plus_whatsapp_bgColor, #sfsi_plus_skype_bgColor, #sfsi_plus_phone_bgColor, #sfsi_plus_vimeo_bgColor, #sfsi_plus_soundcloud_bgColor, #sfsi_plus_yummly_bgColor, #sfsi_plus_flickr_bgColor, #sfsi_plus_reddit_bgColor, #sfsi_plus_tumblr_bgColor, #sfsi_plus_fbmessenger_bgColor, #sfsi_plus_gab_bgColor, #sfsi_plus_ok_bgColor, #sfsi_plus_telegram_bgColor, #sfsi_plus_vk_bgColor, #sfsi_plus_wechat_bgColor, #sfsi_plus_weibo_bgColor, #sfsi_plus_xing_bgColor, #sfsi_plus_mastodon_bgColor, #sfsi_plus_threads_bgColor, #sfsi_plus_bluesky_bgColor').wpColorPicker({
        defaultColor: true,
        hide: true,
        palettes: true,
        change: function (event, ui) {
            var currentClick = SFSI(this),
                currentId = currentClick.attr('id'),
                selectedColor = ui.color.toString(); // Get the color from the color picker UI

            currentClick.parents('.sfsiplus_icon_bgcolor').find('.sfsiplus_icon_img_wrapper').css({'background': selectedColor});
            SFSI('.mouseover_' + currentId).css({'background': selectedColor});
        },
        clear: function () {
            var currentClick = SFSI(this),
                currentParent = currentClick.parents('.sfsiplus_icon_bgcolor'),
                defaultBGColor = currentParent.find('.sfsi_plus_input_bgColor').attr('data-default-color'),
                currentId = currentClick.attr('id');

            currentParent.find('.sfsiplus_icon_img_wrapper').css({'background': defaultBGColor});
            SFSI('.mouseover_' + currentId).css({'background': defaultBGColor});
        },
    });

    /* For gradient BG Colors */
    SFSI('#sfsi_plus_youtube_bgColor, #sfsi_plus_mix_bgColor, #sfsi_plus_copylink_bgColor').wpColorPicker({
        defaultColor: false,
        hide: true,
        palettes: true,
        change: function (event, ui) {
            var currentClick = SFSI(this),
                currentId = currentClick.attr('id'),
                selectedColor = currentClick.val();

            currentClick.parents('.sfsiplus_icon_bgcolor').find('.sfsiplus_icon_img_wrapper').css({'background': selectedColor});
            SFSI('.mouseover_' + currentId).css({'background': selectedColor});
        },
        clear: function () {
            var currentClick = SFSI(this),
                currentParent = currentClick.parents('.sfsiplus_icon_bgcolor'),
                currentParentFindInput = currentParent.find('.sfsi_plus_input_bgColor');
            defaultBGColor = currentParentFindInput.attr('data-default-color-custom'),
                currentId = currentParentFindInput.attr('id');

            currentParent.find('.sfsiplus_icon_img_wrapper').css({'background': defaultBGColor});
            SFSI('.mouseover_' + currentId).css({'background': defaultBGColor});
        },
    });

    var arrIds = [
        'sfsi_plus_form_border_color',
        'sfsi_plus_form_background',
        'sfsi_plus_form_heading_fontcolor',
        'sfsi_plus_form_field_fontcolor',
        'sfsi_plus_form_button_fontcolor',
        'sfsi_plus_form_button_background',
        'sfsi_plus_form_privacynotice_fontcolor'
    ];

    SFSI('#sfsi_plus_textBefor_icons_fontcolor').wpColorPicker({
        defaultColor: false,
        // change: function(event, ui){sfsi_plus_create_suscriber_form()},
        // clear: function() {sfsi_plus_create_suscriber_form()},
        hide: true,
        palettes: true
    });

    SFSI.each(arrIds, function (index, value) {

        SFSI('#' + value).wpColorPicker({
            defaultColor: false,
            change: function (event, ui) {
                sfsi_plus_create_suscriber_form()
            },
            clear: function () {
                sfsi_plus_create_suscriber_form()
            },
            hide: true,
            palettes: true
        });
    });

    function i() {
        SFSI(".uperror").html(""), sfsiplus_afterLoad();
        var s = SFSI('.bdr_btm_non input[name="' + SFSI("#upload_id").val() + '"]');
        s.removeAttr("checked");
        var i = SFSI(s).parent().find("span:first");
        return SFSI(i).css("background-position", "0px 0px"), SFSI(".upload-overlay").hide("slow"), !1;
    }

    SFSI("#accordion").accordion({
        collapsible: !0,
        active: !1,
        heightStyle: "content",
        event: "click",
        beforeActivate: function (s, i) {
            if (i.newHeader[0]) var e = i.newHeader,
                t = e.next(".ui-accordion-content");
            else var e = i.oldHeader,
                t = e.next(".ui-accordion-content");
            var n = "true" == e.attr("aria-selected");
            return e.toggleClass("ui-corner-all", n).toggleClass("accordion-header-active ui-state-active ui-corner-top", !n).attr("aria-selected", (!n).toString()),
                e.children(".ui-icon").toggleClass("ui-icon-triangle-1-e", n).toggleClass("ui-icon-triangle-1-s", !n),
                t.toggleClass("accordion-content-active", !n), n ? t.slideUp() : t.slideDown(), !1;
        }
    }),
        SFSI("#accordion1").accordion({
            collapsible: !0,
            active: !1,
            heightStyle: "content",
            event: "click",
            beforeActivate: function (s, i) {
                if (i.newHeader[0]) var e = i.newHeader,
                    t = e.next(".ui-accordion-content");
                else var e = i.oldHeader,
                    t = e.next(".ui-accordion-content");
                var n = "true" == e.attr("aria-selected");
                return e.toggleClass("ui-corner-all", n).toggleClass("accordion-header-active ui-state-active ui-corner-top", !n).attr("aria-selected", (!n).toString()),
                    e.children(".ui-icon").toggleClass("ui-icon-triangle-1-e", n).toggleClass("ui-icon-triangle-1-s", !n),
                    t.toggleClass("accordion-content-active", !n), n ? t.slideUp() : t.slideDown(), !1;
            }
        }),
        SFSI("#accordion2").accordion({
            collapsible: !0,
            active: !1,
            heightStyle: "content",
            event: "click",
            beforeActivate: function (s, i) {
                if (i.newHeader[0]) var e = i.newHeader,
                    t = e.next(".ui-accordion-content");
                else var e = i.oldHeader,
                    t = e.next(".ui-accordion-content");
                var n = "true" == e.attr("aria-selected");
                return e.toggleClass("ui-corner-all", n).toggleClass("accordion-header-active ui-state-active ui-corner-top", !n).attr("aria-selected", (!n).toString()),
                    e.children(".ui-icon").toggleClass("ui-icon-triangle-1-e", n).toggleClass("ui-icon-triangle-1-s", !n),
                    t.toggleClass("accordion-content-active", !n), n ? t.slideUp() : t.slideDown(), !1;
            }
        }),
        SFSI(".closeSec").on("click", function () {
            var s = !0,
                i = SFSI(this).closest("div.ui-accordion-content").prev("h3.ui-accordion-header").first(),
                e = SFSI(this).closest("div.ui-accordion-content").first();
            i.toggleClass("ui-corner-all", s).toggleClass("accordion-header-active ui-state-active ui-corner-top", !s).attr("aria-selected", (!s).toString()),
                i.children(".ui-icon").toggleClass("ui-icon-triangle-1-e", s).toggleClass("ui-icon-triangle-1-s", !s),
                e.toggleClass("accordion-content-active", !s), s ? e.slideUp() : e.slideDown();
        }),
        SFSI(document).click(function (s) {
            var i = SFSI(".sfsi_plus_FrntInner"),
                e = SFSI(".sfsi_plus_wDiv"),
                t = SFSI("#at15s");
            i.is(s.target) || 0 !== i.has(s.target).length || e.is(s.target) || 0 !== e.has(s.target).length || t.is(s.target) || 0 !== t.has(s.target).length || i.fadeOut();
        }),
        SFSI(".sfsi_plus_outr_div").find(".addthis_button").mousemove(function () {
            var s = SFSI(".sfsi_plus_outr_div").find(".addthis_button").offset().top + 10;
            SFSI("#at15s").css({
                top: s + "px",
                left: SFSI(".sfsi_plus_outr_div").find(".addthis_button").offset().left + "px"
            });
        }),

        SFSI('#sfsi_plus_popup_background_color').wpColorPicker({
            defaultColor: false,
            change: function (event, ui) {
                sfsi_plus_make_popBox()
            },
            clear: function () {
                sfsi_plus_make_popBox()
            },
            hide: true,
            palettes: true
        }),
        SFSI('#sfsi_plus_popup_border_color').wpColorPicker({
            defaultColor: false,
            change: function (event, ui) {
                sfsi_plus_make_popBox()
            },
            clear: function () {
                sfsi_plus_make_popBox()
            },
            hide: true,
            palettes: true
        }),
        SFSI('#sfsi_plus_popup_fontColor').wpColorPicker({
            defaultColor: false,
            change: function (event, ui) {
                sfsi_plus_make_popBox()
            },
            clear: function () {
                sfsi_plus_make_popBox()
            },
            hide: true,
            palettes: true
        }),
        SFSI('#sfsi_plus_tooltip_Color').wpColorPicker({
            defaultColor: false,
            hide: true,
            palettes: true
        }),
        SFSI('#sfsi_plus_tooltip_border_Color').wpColorPicker({
            defaultColor: false,
            hide: true,
            palettes: true
        }),

        SFSI("div#sfsiplusid_linkedin").find(".icon4").find("a").find("img").mouseover(function () {
            SFSI(this).attr("src", sfsi_premium_ajax_object.plugin_url + "images/visit_icons/linkedIn_hover.svg");
        }),
        SFSI("div#sfsiplusid_linkedin").find(".icon4").find("a").find("img").mouseleave(function () {
            SFSI(this).attr("src", sfsi_premium_ajax_object.plugin_url + "images/visit_icons/linkedIn.svg");
        }),
        // SFSI("div#sfsiplusid_youtube").find(".icon1").find("a").find("img").mouseover(function() {
        //     SFSI(this).attr("src", sfsi_premium_ajax_object.plugin_url + "images/visit_icons/youtube_hover.svg");
        // }),
        // SFSI("div#sfsiplusid_youtube").find(".icon1").find("a").find("img").mouseleave(function() {
        //     SFSI(this).attr("src", sfsi_premium_ajax_object.plugin_url + "images/visit_icons/youtube.svg");
        // }),
        SFSI("div#sfsiplusid_facebook").find(".icon1").find("a").find("img").mouseover(function () {
            SFSI(this).css("opacity", "0.9");
        }),
        SFSI("div#sfsiplusid_facebook").find(".icon1").find("a").find("img").mouseleave(function () {
            SFSI(this).css({
                "opacity": "1"
            });
            /*{Monad}*/
        }),
        SFSI("div#sfsiplusid_twitter").find(".cstmicon1").find("a").find("img").mouseover(function () {
            SFSI(this).css("opacity", "0.9");
        }),
        SFSI("div#sfsiplusid_twitter").find(".cstmicon1").find("a").find("img").mouseleave(function () {
            SFSI(this).css("opacity", "1");
        }),
        SFSI("#sfsi_plus_save1").on("click", function () {
            sfsi_plus_update_step1() && sfsipluscollapse(this);
        }),
        SFSI("#sfsi_plus_save2").on("click", function () {
            sfsi_plus_update_step2() && sfsipluscollapse(this);
        }),
        SFSI("#sfsi_plus_save3").on("click", function () {
            sfsi_plus_update_step3() && sfsipluscollapse(this);
        }),
        SFSI("#sfsi_plus_save4").on("click", function () {
            sfsi_plus_update_step4() && sfsipluscollapse(this);
        }),
        SFSI("#sfsi_plus_save5").on("click", function () {
            sfsi_plus_update_step5() && sfsipluscollapse(this);
        }),
        SFSI("#sfsi_plus_save6").on("click", function () {
            sfsi_plus_update_step6() && sfsipluscollapse(this);
        }),
        SFSI("#sfsi_plus_save7").on("click", function () {
            sfsi_plus_update_step7() && sfsipluscollapse(this);
        }),
        SFSI("#sfsi_plus_save8").on("click", function () {
            sfsi_plus_update_step8() && sfsipluscollapse(this);
        }),
        SFSI("#sfsi_plus_save9").on("click", function () {
            sfsi_plus_update_step9() && sfsipluscollapse(this);
        }),
        SFSI("#sfsi_premium_save_export").on("click", function () {
            sfsi_premium_save_export();
        }),
        SFSI("#sfsi_premium_recheck_license").on("click", function () {
            sfsi_premium_recheck_license();
        }),
        SFSI("#save_plus_all_settings").on("click", function () {
            return SFSI("#save_plus_all_settings").text("Saving.."), SFSI(".save_button >a").css("pointer-events", "none"),

                sfsi_plus_update_step1(), sfsi_plus_update_step9(), 1 == global_error ? (sfsiplus_showErrorSuc("error", 'Some Selection error in "Which icons do you want to show on your site?" tab.', 8),
                global_error = 0, !1) : (sfsi_plus_update_step2(), 1 == global_error ? (sfsiplus_showErrorSuc("error", 'Some Selection error in "What do you want the icon to do?" tab.', 8),
                global_error = 0, !1) : (sfsi_plus_update_step3(), 1 == global_error ? (sfsiplus_showErrorSuc("error", 'Some Selection error in "What design & animation do you want to give your icons?" tab.', 8),
                global_error = 0, !1) : (sfsi_plus_update_step4(), 1 == global_error ? (sfsiplus_showErrorSuc("error", 'Some Selection error in "Do you want to display "counts" next to your icons?" tab.', 8),
                global_error = 0, !1) : (sfsi_plus_update_step5(), 1 == global_error ? (sfsiplus_showErrorSuc("error", 'Some Selection error in "Any other wishes for your main icons?" tab.', 8),
                global_error = 0, !1) : (sfsi_plus_update_step6(), 1 == global_error ? (sfsiplus_showErrorSuc("error", 'Some Selection error in "Do you want to display icons at the end of every post?" tab.', 8),
                global_error = 0, !1) : (sfsi_plus_update_step7(), 1 == global_error ? (sfsiplus_showErrorSuc("error", 'Some Selection error in "Do you want to display a pop-up, asking people to subscribe?" tab.', 8),
                global_error = 0, !1) : sfsi_plus_update_step8(), 1 == global_error ? (sfsiplus_showErrorSuc("error", 'Some Selection error in "Where shall they be displayed?" tab.', 8),
                /*global_error = 0, !1) :void (0 == global_error && sfsiplus_showErrorSuc("success", 'Saved! Now go to the <a href="widgets.php">widget</a> area and place the widget into your sidebar (if not done already)', 8))))))));*/
                global_error = 0, !1) : void (0 == global_error && sfsiplus_showErrorSuc("success", '', 8))))))));
        }),

        SFSI(document).on("change", '.fileUPInput', function () {
            sfsiplus_beForeLoad(), sfsiplus_beforeIconSubmit(this) && (SFSI(".upload-overlay").css("pointer-events", "none"),
                SFSI("#customIconFrm").ajaxForm({
                    dataType: "json",
                    success: sfsiplus_afterIconSuccess,
                    resetForm: !0
                }).submit());
        }),
        SFSI(".pop-up").on("click", function () {
            ("fbex-s2" == SFSI(this).attr("data-id") || "linkex-s2" == SFSI(this).attr("data-id")) && (SFSI("." + SFSI(this).attr("data-id")).hide(),
                SFSI("." + SFSI(this).attr("data-id")).css("opacity", "1"), SFSI("." + SFSI(this).attr("data-id")).css("z-index", "1000")),
                SFSI("." + SFSI(this).attr("data-id")).show("slow");
        }),

        SFSI(document).on("click", '#close_popup', function () {
            SFSI(".read-overlay").hide("slow");
        });
    var e = 0;

    SFSI(document).on("click", ".deleteCustomIcon", function () {
        if (confirm("Are you sure want to delete this Icon..?? ")) {
            var inptElem = SFSI(this).parent().find('input');
            sfsi_plus_delete_CusIcon(this, inptElem);
        }
    });

    SFSI(".plus_icn_listing").on("click", ".checkbox", function () {
        if (1 == e) return !1;
        "yes" == SFSI(this).attr("dynamic_ele") && (s = SFSI(this).parent().find("input:checkbox:first"),
            s.is(":checked") ? SFSI(s).attr("checked", !1) : SFSI(s).attr("checked", !0)), s = SFSI(this).parent().find("input:checkbox:first"),
        "yes" == SFSI(s).attr("isNew") && ("0px 0px" == SFSI(this).css("background-position") ? (SFSI(s).attr("checked", !0),
            SFSI(this).css("background-position", "0px -36px")) : (SFSI(s).removeAttr("checked", !0),
            SFSI(this).css("background-position", "0px 0px")));

        var s = SFSI(this).parent().find("input:checkbox:first");

        if (s.is(":checked") && "sfsiplus-cusotm-icon" == s.attr("element-type")) SFSI(".fileUPInput").attr("name", "custom_icons[]"),
            SFSI(".upload-overlay").show("slow", function () {
                e = 0;
            }),

            SFSI("#upload_id").val(s.attr("name"));
        else if (!s.is(":checked") && "sfsiplus-cusotm-icon" == s.attr("element-type")) return s.attr("ele-type") ? (SFSI(this).attr("checked", !0),
            SFSI(this).css("background-position", "0px -36px"), e = 0, !1) : "";
    }),
        SFSI(".plus_icn_listing").on("click", ".checkbox", function () {
            checked = SFSI(this).parent().find("input:checkbox:first"), "sfsi_plus_email_display" != checked.attr("name") || checked.is(":checked") || SFSI(".demail-1").show("slow");
        }),
        SFSI("#deac_email2").on("click", function () {
            SFSI(".demail-1").hide("slow");
        }),
        SFSI("#deac_email3").on("click", function () {
            SFSI(".demail-2").hide("slow"), SFSI(".demail-3").show("slow");
        }),
        SFSI(".hideemailpop").on("click", function () {
            SFSI('input[name="sfsi_plus_email_display"]').attr("checked", !0),
                SFSI('input[name="sfsi_plus_email_display"]').parent().find("span:first").css("background-position", "0px -36px"),
                SFSI(".demail-1").hide("slow"), SFSI(".demail-2").hide("slow"), SFSI(".demail-3").hide("slow");
        }),
        SFSI(".hidePop").on("click", function () {
            SFSI(".demail-1").hide("slow"), SFSI(".demail-2").hide("slow"), SFSI(".demail-3").hide("slow");
        }),
        SFSI(".sfsiplus_activate_footer").on("click", function () {
            var nonce = SFSI(this).attr("data-nonce");
            SFSI(this).text("activating....");
            var s = {
                action: "plus_activateFooter",
                nonce: nonce
            };
            SFSI.ajax({
                url: sfsi_premium_ajax_object.ajax_url,
                type: "post",
                data: s,
                dataType: "json",
                success: function (s) {
                    if (s.res == "wrong_nonce") {
                        SFSI(".sfsiplus_activate_footer").css("font-size", "18px");
                        SFSI(".sfsiplus_activate_footer").text("Unauthorised Request, Try again after refreshing page");
                    } else {
                        "success" == s.res && (SFSI(".demail-1").hide("slow"), SFSI(".demail-2").hide("slow"),
                            SFSI(".demail-3").hide("slow"), SFSI(".sfsiplus_activate_footer").text("Ok, activate link"));
                    }
                }
            });
        }),
        SFSI(".sfsiplus_removeFooter").on("click", function () {
            var nonce = SFSI(this).attr("data-nonce");
            SFSI(this).text("working....");
            var s = {
                action: "plus_removeFooter",
                nonce: nonce
            };
            SFSI.ajax({
                url: sfsi_premium_ajax_object.ajax_url,
                type: "post",
                data: s,
                dataType: "json",
                success: function (s) {
                    if (s.res == "wrong_nonce") {
                        SFSI(".sfsiplus_removeFooter").text("Unauthorised Request, Try again after refreshing page");
                    } else {
                        "success" == s.res && (SFSI(".sfsiplus_removeFooter").fadeOut("slow"), SFSI(".sfsiplus_footerLnk").fadeOut("slow"));
                    }
                }
            });
        }),
        SFSI(document).on("click", '.radio', function () {
            var s = SFSI(this).parent().find("input:radio:first");
            "sfsi_plus_display_counts" == s.attr("name") && sfsi_plus_show_counts();
        }),
        SFSI("#close_Uploadpopup").on("click", i),
        SFSI(document).on("click", '.radio', function () {
            var s = SFSI(this).parent().find("input:radio:first");
            "sfsi_plus_show_Onposts" == s.attr("name") && sfsi_plus_show_OnpostsDisplay();
        }),
        sfsi_plus_show_OnpostsDisplay(),
        sfsi_plus_depened_sections(),
        sfsi_plus_show_counts(),
        sfsi_plus_showPreviewCounts(),

        SFSI(".plus_share_icon_order").sortable({
            update: function () {
                SFSI(".plus_share_icon_order li").each(function () {
                    SFSI(this).attr("data-index", SFSI(this).index() + 1);
                });
            },
            revert: !0
        }),

        SFSI(".plus_share_icon_mobile_order").sortable({
            update: function () {
                SFSI(".plus_share_icon_mobile_order li").each(function () {
                    SFSI(this).attr("data-index", SFSI(this).index() + 1);
                });
            },
            revert: !0
        }),

        SFSI(document).on("change", '#sfsi_plus_horizontal_verical_Alignment', function () {
            "Horizontal" == SFSI('#sfsi_plus_horizontal_verical_Alignment').val() ? SFSI(".sfsi_plus_horiZontal_slideup").slideDown() : SFSI(".sfsi_plus_horiZontal_slideup").slideUp(), (SFSI('input[name="sfsi_plus_icons_perRow"]').val(''))
        }),

        SFSI(document).on("change", '#sfsi_plus_mobile_horizontal_verical_Alignment', function () {
            "Horizontal" == SFSI('#sfsi_plus_mobile_horizontal_verical_Alignment').val() ? SFSI(".sfsi_plus_horiZontal_mobile_slideup").slideDown() : SFSI(".sfsi_plus_horiZontal_mobile_slideup").slideUp(), (SFSI('input[name="sfsi_plus_mobile_icons_perRow"]').val(''))
        }),


        SFSI(document).on("click", '.radio', function () {
            var s = SFSI(this).parent().find("input:radio:first");

            "sfsi_plus_email_countsFrom" == s.attr("name") && (SFSI('input[name="sfsi_plus_email_countsDisplay"]').prop("checked", !0),
                SFSI('input[name="sfsi_plus_email_countsDisplay"]').parent().find("span.checkbox").attr("style", "background-position:0px -36px;"),

                "manual" == SFSI("input[name='sfsi_plus_email_countsFrom']:checked").val() ? SFSI("input[name='sfsi_plus_email_manualCounts']").slideDown() : SFSI("input[name='sfsi_plus_email_manualCounts']").slideUp()),
            "sfsi_plus_facebook_countsFrom" == s.attr("name") && (SFSI('input[name="sfsi_plus_facebook_countsDisplay"]').prop("checked", !0),
                SFSI('input[name="sfsi_plus_facebook_countsDisplay"]').parent().find("span.checkbox").attr("style", "background-position:0px -36px;"),
                "mypage" == SFSI("input[name='sfsi_plus_facebook_countsFrom']:checked").val() ? (SFSI(".sfsiplus_fbpgidwpr,input[name='sfsi_plus_facebook_mypageCounts']").slideDown(), SFSI("#fbappinstructionContainer").slideDown()) : (SFSI(".sfsiplus_fbpgidwpr,input[name='sfsi_plus_facebook_mypageCounts']").slideUp()),
                "likes" == SFSI("input[name='sfsi_plus_facebook_countsFrom']:checked").val() ? (SFSI("#fbappinstructionContainer").slideDown(), showAccessToken()) : SFSI(".sfsi_plus_facebookfbtocen").slideUp(),
                //"likes" == SFSI("input[name='sfsi_plus_facebook_countsFrom']:checked").val() ? SFSI(".sfsi_premium_fbInstructions_fbtoken").slideDown() :SFSI(".sfsi_premium_fbInstructions_fbtoken").slideUp(),

                "manual" == SFSI("input[name='sfsi_plus_facebook_countsFrom']:checked").val() ? (SFSI("input[name='sfsi_plus_facebook_manualCounts']").slideDown(), SFSI("#fbappinstructionContainer").slideUp()) : SFSI("input[name='sfsi_plus_facebook_manualCounts']").slideUp()),
            "sfsi_plus_twitter_countsFrom" == s.attr("name") && (SFSI('input[name="sfsi_plus_twitter_countsDisplay"]').prop("checked", !0),
                SFSI('input[name="sfsi_plus_twitter_countsDisplay"]').parent().find("span.checkbox").attr("style", "background-position:0px -36px;"),

                "manual" == SFSI("input[name='sfsi_plus_twitter_countsFrom']:checked").val() ? (SFSI("input[name='sfsi_plus_twitter_manualCounts']").slideDown(), SFSI('.sfsi_plus_tw_caching_section').parent().hide(),
                    SFSI(".tw_follow_options").slideUp()) : (SFSI("input[name='sfsi_plus_twitter_manualCounts']").slideUp(), SFSI('.sfsi_plus_tw_caching_section').parent().show(),
                    SFSI(".tw_follow_options").slideDown())), "sfsi_plus_linkedIn_countsFrom" == s.attr("name") && (SFSI('input[name="sfsi_plus_linkedIn_countsDisplay"]').prop("checked", !0),
                SFSI('input[name="sfsi_plus_linkedIn_countsDisplay"]').parent().find("span.checkbox").attr("style", "background-position:0px -36px;"),

                "manual" == SFSI("input[name='sfsi_plus_linkedIn_countsFrom']:checked").val() ? (SFSI("input[name='sfsi_plus_linkedIn_manualCounts']").slideDown(),
                    SFSI(".linkedIn_options").slideUp()) : (SFSI("input[name='sfsi_plus_linkedIn_manualCounts']").slideUp(),
                    SFSI(".linkedIn_options").slideDown())), "sfsi_plus_youtube_countsFrom" == s.attr("name") && (SFSI('input[name="sfsi_plus_youtube_countsDisplay"]').prop("checked", !0),
                SFSI('input[name="sfsi_plus_youtube_countsDisplay"]').parent().find("span.checkbox").attr("style", "background-position:0px -36px;"),

                "manual" == SFSI("input[name='sfsi_plus_youtube_countsFrom']:checked").val() ? (SFSI("input[name='sfsi_plus_youtube_manualCounts']").slideDown(),
                    SFSI(".youtube_options").slideUp()) : (SFSI("input[name='sfsi_plus_youtube_manualCounts']").slideUp(),
                    SFSI(".youtube_options").slideDown())), "sfsi_plus_pinterest_countsFrom" == s.attr("name") && (SFSI('input[name="sfsi_plus_pinterest_countsDisplay"]').prop("checked", !0),
                SFSI('input[name="sfsi_plus_pinterest_countsDisplay"]').parent().find("span.checkbox").attr("style", "background-position:0px -36px;"),

                "manual" == SFSI("input[name='sfsi_plus_pinterest_countsFrom']:checked").val() ? (SFSI("input[name='sfsi_plus_pinterest_manualCounts']").slideDown(),
                    SFSI(".pin_options").slideUp()) : SFSI("input[name='sfsi_plus_pinterest_manualCounts']").slideUp()),
            "sfsi_plus_instagram_countsFrom" == s.attr("name") && (SFSI('input[name="sfsi_plus_instagram_countsDisplay"]').prop("checked", !0),
                SFSI('input[name="sfsi_plus_instagram_countsDisplay"]').parent().find("span.checkbox").attr("style", "background-position:0px -36px;"),

                "manual" == SFSI("input[name='sfsi_plus_instagram_countsFrom']:checked").val() ? (SFSI("input[name='sfsi_plus_instagram_manualCounts']").slideDown(),
                    SFSI(".instagram_userLi").slideUp()) : (SFSI("input[name='sfsi_plus_instagram_manualCounts']").slideUp(),
                    SFSI(".instagram_userLi").slideDown())), "sfsi_plus_shares_countsFrom" == s.attr("name") && (SFSI('input[name="sfsi_plus_shares_countsDisplay"]').prop("checked", !0),
                SFSI('input[name="sfsi_plus_shares_countsDisplay"]').parent().find("span.checkbox").attr("style", "background-position:0px -36px;"),
                "manual" == SFSI("input[name='sfsi_plus_shares_countsFrom']:checked").val() ? SFSI("input[name='sfsi_plus_shares_manualCounts']").slideDown() : SFSI("input[name='sfsi_plus_shares_manualCounts']").slideUp());
        }),
        sfsi_plus_make_popBox(),
        SFSI('textarea[name="sfsi_plus_popup_text"] ,input[name="sfsi_plus_popup_background_color"],input[name="sfsi_plus_popup_border_color"],input[name="sfsi_plus_popup_border_thickness"],input[name="sfsi_plus_popup_fontSize"],input[name="sfsi_plus_popup_fontColor"]').on("keyup click", sfsi_plus_make_popBox),
        SFSI("#sfsi_plus_popup_font ,#sfsi_plus_popup_fontStyle").on("change", sfsi_plus_make_popBox),
        /*SFSI(".radio").live("click", function() {*/

        SFSI(document).on("click", '.radio', function () {
            var s = SFSI(this).parent().find("input:radio:first");
            "sfsi_plus_popup_border_shadow" == s.attr("name") && sfsi_plus_make_popBox();
        }), /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ? SFSI("img.sfsi_premium_wicon").on("click", function (s) {
        s.stopPropagation();
        var i = SFSI("#sfsi_plus_floater_sec").val();
        SFSI("div.sfsi_premium_wicons").css("z-index", "0"), SFSI(this).parent().parent().parent().siblings("div.sfsi_premium_wicons").find(".sfsiplus_inerCnt").find("div.sfsi_plus_tool_tip_2").hide(),
        SFSI(this).parent().parent().parent().parent().siblings("li").length > 0 && (SFSI(this).parent().parent().parent().parent().siblings("li").find("div.sfsi_plus_tool_tip_2").css("z-index", "0"),
            SFSI(this).parent().parent().parent().parent().siblings("li").find("div.sfsi_premium_wicons").find(".sfsiplus_inerCnt").find("div.sfsi_plus_tool_tip_2").hide()),
            SFSI(this).parent().parent().parent().css("z-index", "1000000"), SFSI(this).parent().parent().css({
            "z-index": "999"
        }), SFSI(this).attr("effect") && "fade_in" == SFSI(this).attr("effect") && (SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
            opacity: 1,
            "z-index": 10
        }), SFSI(this).parent().css("opacity", "1")), SFSI(this).attr("effect") && "scale" == SFSI(this).attr("effect") && (SFSI(this).parent().addClass("scale"),
            SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
                opacity: 1,
                "z-index": 10
            }), SFSI(this).parent().css("opacity", "1")), SFSI(this).attr("effect") && "combo" == SFSI(this).attr("effect") && (SFSI(this).parent().addClass("scale"),
            SFSI(this).parent().css("opacity", "1"), SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
            opacity: 1,
            "z-index": 10
        })), ("top-left" == i || "top-right" == i) && SFSI(this).parent().parent().parent().parent("#sfsi_plus_floater").length > 0 && "sfsi_plus_floater" == SFSI(this).parent().parent().parent().parent().attr("id") ? (SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").addClass("sfsi_plc_btm"),
            SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").find("span.bot_arow").addClass("top_big_arow"),
            SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
                opacity: 1,
                "z-index": 10
            }), SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").show()) : (SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").find("span.bot_arow").removeClass("top_big_arow"),
            SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").removeClass("sfsi_plc_btm"),
            SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
                opacity: 1,
                "z-index": 1e3
            }), SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").show());
    }) : SFSI("img.sfsi_premium_wicon").on("mouseenter", function () {
        var s = SFSI("#sfsi_plus_floater_sec").val();
        SFSI("div.sfsi_premium_wicons").css("z-index", "0"), SFSI(this).parent().parent().parent().siblings("div.sfsi_premium_wicons").find(".sfsiplus_inerCnt").find("div.sfsi_plus_tool_tip_2").hide(),
        SFSI(this).parent().parent().parent().parent().siblings("li").length > 0 && (SFSI(this).parent().parent().parent().parent().siblings("li").find("div.sfsi_plus_tool_tip_2").css("z-index", "0"),
            SFSI(this).parent().parent().parent().parent().siblings("li").find("div.sfsi_premium_wicons").find(".sfsiplus_inerCnt").find("div.sfsi_plus_tool_tip_2").hide()),
            SFSI(this).parent().parent().parent().css("z-index", "1000000"), SFSI(this).parent().parent().css({
            "z-index": "999"
        }), SFSI(this).attr("effect") && "fade_in" == SFSI(this).attr("effect") && (SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
            opacity: 1,
            "z-index": 10
        }), SFSI(this).parent().css("opacity", "1")), SFSI(this).attr("effect") && "scale" == SFSI(this).attr("effect") && (SFSI(this).parent().addClass("scale"),
            SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
                opacity: 1,
                "z-index": 10
            }), SFSI(this).parent().css("opacity", "1")), SFSI(this).attr("effect") && "combo" == SFSI(this).attr("effect") && (SFSI(this).parent().addClass("scale"),
            SFSI(this).parent().css("opacity", "1"), SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
            opacity: 1,
            "z-index": 10
        })), ("top-left" == s || "top-right" == s) && SFSI(this).parent().parent().parent().parent("#sfsi_plus_floater").length > 0 && "sfsi_plus_floater" == SFSI(this).parent().parent().parent().parent().attr("id") ? (SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").addClass("sfsi_plc_btm"),
            SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").find("span.bot_arow").addClass("top_big_arow"),
            SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
                opacity: 1,
                "z-index": 10
            }), SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").show()) : (SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").find("span.bot_arow").removeClass("top_big_arow"),
            SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").removeClass("sfsi_plc_btm"),
            SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
                opacity: 1,
                "z-index": 10
            }), SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").show());
    }), SFSI("div.sfsi_premium_wicons").on("mouseout", function () {
        SFSI(this).children("div.sfsiplus_inerCnt").children("a.sficn").attr("effect") && "fade_in" == SFSI(this).children("div.sfsiplus_inerCnt").children("a.sficn").attr("effect") && SFSI(this).children("div.sfsiplus_inerCnt").find("a.sficn").css("opacity", "0.6"),
        SFSI(this).children("div.sfsiplus_inerCnt").children("a.sficn").attr("effect") && "scale" == SFSI(this).children("div.sfsiplus_inerCnt").children("a.sficn").attr("effect") && SFSI(this).children("div.sfsiplus_inerCnt").find("a.sficn").removeClass("scale"),
        SFSI(this).children("div.sfsiplus_inerCnt").children("a.sficn").attr("effect") && "combo" == SFSI(this).children("div.sfsiplus_inerCnt").children("a.sficn").attr("effect") && (SFSI(this).children("div.sfsiplus_inerCnt").find("a.sficn").css("opacity", "0.6"),
            SFSI(this).children("div.sfsiplus_inerCnt").find("a.sficn").removeClass("scale")), SFSI(this).children(".sfsiplus_inerCnt").find("div.sfsi_plus_tool_tip_2").hide();
    }),
        SFSI("body").on("click", function () {
            SFSI(".sfsiplus_inerCnt").find("div.sfsi_plus_tool_tip_2").hide();
        }),
        SFSI(".adminTooltip >a").on("mouseenter", function () {
            SFSI(this).offset().top, SFSI(this).parent("div").find("div.sfsi_plus_tool_tip_2_inr").css("opacity", "1"),
                SFSI(this).parent("div").find("div.sfsi_plus_tool_tip_2_inr").show();
        }),
        SFSI(".adminTooltip").on("mouseleave", function () {
            "none" != SFSI(".sfsi_plus_gpls_tool_bdr").css("display") && 0 != SFSI(".sfsi_plus_gpls_tool_bdr").css("opacity") ? SFSI(".pop_up_box ").on("click", function () {
                SFSI(this).parent("div").find("div.sfsi_plus_tool_tip_2_inr").css("opacity", "0"), SFSI(this).parent("div").find("div.sfsi_plus_tool_tip_2_inr").hide();
            }) : (SFSI(this).parent("div").find("div.sfsi_plus_tool_tip_2_inr").css("opacity", "0"),
                SFSI(this).parent("div").find("div.sfsi_plus_tool_tip_2_inr").hide());
        }),
        SFSI(".expand-area").on("click", function () {
            object_name.Re_ad == SFSI(this).text() ? (SFSI(this).siblings("p").children("label").fadeIn("slow"),
                SFSI(this).text(object_name1.Coll_apse)) : (SFSI(this).siblings("p").children("label").fadeOut("slow"),
                SFSI(this).text(object_name.Re_ad));
        }),


        SFSI(document).on("click", '.radio', function () {
            /*console.log("radio clicked",SFSI(this));*/

            var s = SFSI(this).parent().find("input:radio:first");

            //----------------- Customization for Twitter add card STARTS----------------------------//

            if (s.attr("name") == "sfsi_plus_twitter_twtAddCard" && s.attr("checked")) {

                if (s.val() == "no") {
                    SFSI(".cardDataContainer").hide("slow");
                } else {
                    SFSI(".cardDataContainer").show("slow");
                }
            }

            //----------------- Customization for Twitter add card CLOSES----------------------------//

            //----------------- Customization for URL Shortning STARTS----------------------------//
            /*console.log("checked1",s.attr("name"));*/

            if (s.attr("name") == "sfsi_plus_url_shorting_api_type_setting" && s.attr("checked")) {
                console.log("checked", s.attr("name"));
                if (s.val() == "bitly") {
                    var is_bitly_v4 = SFSI("input[name='sfsi_premium_bitly_v4']").val();
                    if ("no" == is_bitly_v4) {
                        SFSI(".sfsi_plus_bitly_access_token").show("slow");
                    } else {
                        SFSI(".sfsi_plus_bitly_access_token").hide("slow");
                    }
                    SFSI(".sfsi_plus_bitly_authorize_button").show("slow");
                } else {
                    SFSI(".sfsi_plus_bitly_access_token").hide("slow");
                    SFSI(".sfsi_plus_bitly_authorize_button").hide("slow");
                }
            } else {
                SFSI(".sfsi_plus_bitly_access_token").hide("slow");
                SFSI(".sfsi_plus_bitly_authorize_button").hide("slow");
            }


            //----------------- Customization for URL Shortning CLOSES----------------------------//

            "sfsi_plus_icons_float" == s.attr("name") && "yes" == s.val() && (SFSI(".float_options").slideDown("slow"),
                SFSI('input[name="sfsi_plus_icons_stick"][value="no"]').attr("checked", !0), SFSI('input[name="sfsi_plus_icons_stick"][value="yes"]').removeAttr("checked"),
                SFSI('input[name="sfsi_plus_icons_stick"][value="no"]').parent().find("span").attr("style", "background-position:0px -41px;"),
                SFSI('input[name="sfsi_plus_icons_stick"][value="yes"]').parent().find("span").attr("style", "background-position:0px -0px;")),
            ("sfsi_plus_icons_stick" == s.attr("name") && "yes" == s.val() || "sfsi_plus_icons_float" == s.attr("name") && "no" == s.val()) && (SFSI(".float_options").slideUp("slow"),
                SFSI('input[name="sfsi_plus_icons_float"][value="no"]').prop("checked", !0), SFSI('input[name="sfsi_plus_icons_float"][value="yes"]').prop("checked", !1),
                SFSI('input[name="sfsi_plus_icons_float"][value="no"]').parent().find("span.radio").attr("style", "background-position:0px -41px;"),
                SFSI('input[name="sfsi_plus_icons_float"][value="yes"]').parent().find("span.radio").attr("style", "background-position:0px -0px;"));

            if (s.attr("name") == "sfsi_plus_pinterest_countsFrom") {

                if (s.val() == 'board' || s.val() == 'accountpins' || s.val() == 'followers') {

                    SFSI(".sfsi_plus_pinterest_access_token_ul").show("slow");

                    if (s.val() == 'board') {
                        SFSI(".sfsi_input_section_pinterest_username_board_li").show("slow");
                    } else if (s.val() == 'followers') {
                        SFSI(".sfsi_input_section_pinterest_username_board_li").hide("slow");
                    }
                }

                if (s.val() == 'pins' || s.val() == 'manual') {

                    SFSI(".sfsi_plus_pinterest_access_token_ul").hide("slow");
                    SFSI(".sfsi_input_section_pinterest_username_board_li").hide("slow");

                }
            }

            if (s.attr("name") == "sfsi_plus_facebook_countsFrom" && s.attr("checked")) {

                if (s.val() == 'likes') {

                    SFSI("div.sfsi_plus_fb_caching_section").slideDown("slow");
                } else {

                    SFSI("div.sfsi_plus_fb_caching_section").slideUp("slow");
                }
            }

            if (s.attr("name") == "sfsi_plus_fb_count_caching_active") {

                if (s.val() == 'no') {
                    SFSI('input[name="' + s.attr("name") + '"][value="no"]').attr("checked", "checked");
                    SFSI('input[name="' + s.attr("name") + '"][value="yes"]').removeAttr("checked");
                    SFSI('input[name="' + s.attr("name") + '"]').parent().parent().next('.field').hide();
                } else {
                    SFSI('input[name="' + s.attr("name") + '"][value="no"]').removeAttr("checked");
                    SFSI('input[name="' + s.attr("name") + '"][value="yes"]').attr("checked", "checked");
                    SFSI('input[name="' + s.attr("name") + '"]').parent().parent().next('.field').show();
                }
            }

            if (s.attr("name") == "sfsi_plus_tw_count_caching_active") {

                if (s.val() == 'no') {
                    SFSI('input[name="' + s.attr("name") + '"][value="no"]').attr("checked", "checked");
                    SFSI('input[name="' + s.attr("name") + '"][value="yes"]').removeAttr("checked");
                } else {
                    SFSI('input[name="' + s.attr("name") + '"][value="no"]').removeAttr("checked");
                    SFSI('input[name="' + s.attr("name") + '"][value="yes"]').attr("checked", "checked");
                }
            }

        }),
    SFSI(".sfsi_plus_wDiv").length > 0 && setTimeout(function () {
        var s = parseInt(SFSI(".sfsi_plus_wDiv").height()) + 0 + "px";
        SFSI(".sfsi_plus_holders").each(function () {
            SFSI(this).css("height", s);
        });
    }, 200),


        SFSI('.sfsi_navigate_to_question6').on("click", function () {

            if (SFSI('#ui-id-3').hasClass('accordion-header-active') && SFSI('#ui-id-3').hasClass('ui-state-active') && SFSI('#ui-id-3').hasClass('ui-corner-top')) {

                SFSI('.section2sfsiColbtn').trigger('click');

                if (!SFSI('#ui-id-11').hasClass('accordion-header-active') && !SFSI('#ui-id-11').hasClass('ui-state-active') && !SFSI('#ui-id-11').hasClass('ui-corner-top')) {
                    SFSI('#ui-id-11').trigger('click');
                }
                var pos = SFSI("#custom_social_data_setting").offset();
                SFSI('html, body').animate({
                    scrollTop: pos.top / 2 - 200
                }, 2000);
            }
        });

    SFSI('input[name="sfsi_plus_cumulative_count_active"]').click(function () {
        if ("yes" == SFSI(this).val()) {
            SFSI('.sfsi_premium_aggregate_options').show();
        } else {
            SFSI('.sfsi_premium_aggregate_options').hide();
        }
    });

    SFSI('input[name="sfsi_plus_http_cumulative_count_active"]').click(function () {
        if ("no" == SFSI(this).val()) {
            SFSI('.sfsi_plus_not_http_cumulative_count_active').show();
        } else {
            SFSI('.sfsi_plus_not_http_cumulative_count_active').hide();
        }
    })

    SFSI(document).on("click", '.checkbox', function () {

        var s = SFSI(this).parent().find("input:checkbox:first");

        var inputName = s.attr("name");
        var inputChecked = s.prop("checked");
        SFSI(".uperror").html('');


        //----------------- Customization for twitter card section STARTS----------------------------//
        switch (inputName) {

            case 'sfsi_plus_twitter_aboutPage':

                var elem = SFSI('.sfsi_navigate_to_question6');
                var elemC = SFSI('#twitterSettingContainer');

                if (inputChecked) {
                    elem.addClass('addCss').removeClass('removeCss');
                    elemC.css('display', 'block');
                } else {
                    elem.removeClass('addCss').addClass('removeCss');
                    elemC.css('display', 'none');
                }

                var chkd = SFSI("input[name='sfsi_plus_twitter_twtAddCard']:checked").val();

                if (inputChecked) {
                    SFSI(".contTwitterCard").show("slow");
                    if (chkd == "no") {
                        SFSI(".cardDataContainer").hide();
                    }
                } else {
                    if (chkd == "yes") {
                        SFSI('input[value="yes"][name="sfsi_plus_twitter_twtAddCard"]').prop("checked", false);
                        SFSI('input[value="no"][name="sfsi_plus_twitter_twtAddCard"]').prop("checked", true);
                    }
                    SFSI(".contTwitterCard").hide("slow");
                }

                break;

            case 'sfsi_plus_Hide_popupOnScroll':
            case 'sfsi_plus_Hide_popupOn_OutsideClick':

                var elem = SFSI('input[name="' + inputName + '"]');

                if (inputChecked) {
                    elem.val("yes");
                } else {
                    elem.val("no");
                }

                break;

            case 'sfsi_plus_mouseOver':

                var elem = SFSI('input[name="' + inputName + '"]');

                var togglelem = SFSI('.mouse-over-effects');

                if (inputChecked) {
                    togglelem.removeClass('hide').addClass('show');
                } else {
                    togglelem.removeClass('show').addClass('hide');
                }

                break;


            case 'sfsi_plus_shuffle_icons':

                var elem = SFSI('input[name="' + inputName + '"]');

                var togglelem = SFSI('.shuffle_sub');

                if (inputChecked) {
                    togglelem.removeClass('hide').addClass('show');
                } else {
                    togglelem.removeClass('show').addClass('hide');
                }

                break;

            case 'sfsi_plus_place_rectangle_icons_item_manually':
                var elem = SFSI('input[name="' + inputName + '"]');

                var togglelem = elem.parent().next();

                if (inputChecked) {
                    togglelem.removeClass('hide').addClass('show');
                } else {
                    togglelem.removeClass('show').addClass('hide');
                }

                break;

            case 'sfsi_plus_icon_hover_show_pinterest':
                var elem = SFSI('input[name="' + inputName + '"]');

                var togglelem = elem.parent().next();

                if (inputChecked) {

                    togglelem.removeClass('hide').addClass('show');
                    SFSI('.sfsi_plus_include_exclude_wrapper .sfsi_premium_restriction_warning_pinterest').removeClass('hide');
                } else {
                    togglelem.removeClass('show').addClass('hide');
                    SFSI('.sfsi_plus_include_exclude_wrapper .sfsi_premium_restriction_warning_pinterest').addClass('hide');
                }

                break;

            case 'sfsi_plus_houzzShare_option':

                var elem = SFSI('input[name="' + inputName + '"]');

                var togglelem = SFSI('.houzzideabooks');

                if (inputChecked) {
                    togglelem.removeClass('hide').addClass('show');
                } else {
                    togglelem.removeClass('show').addClass('hide');
                }

                break;

            case 'sfsi_plus_wechatFollow_option':

                var elem = SFSI('input[name="' + inputName + '"]');

                var togglelem = SFSI('.sfsi_premium_wechat_follow_desc');

                if (inputChecked) {
                    togglelem.removeClass('hide').addClass('show');
                } else {
                    togglelem.removeClass('show').addClass('hide');
                }

                break;

            case 'sfsi_plus_fbmessengerShare_option':
                var elem = jQuery('.sfsi_premium_only_fbmessangershare');
                if (inputChecked) {
                    elem.show();
                } else {
                    elem.hide();
                }
                break;
            case 'sfsi_premium_responsive_custom_new_display':
                var elem = SFSI('input[name="' + inputName + '"]');
                var elem_li = elem.parent().parent();
                if (inputChecked) {
                    elem.prop('checked', false);
                    elem.parent().find('span').css('background-position', '0px 0px');

                    var added_count = jQuery('.sfsi_premium_responsive_custom_icon_container .radio_section.tb_4_ck input[type="hidden"][value="yes"]').length;
                    jQuery('.sfsi_premium_responsive_custom_icon_container.sfsi_premium_responsive_custom_icon_' + added_count + '_container input[type="hidden"]').attr('value', 'yes');
                    jQuery('.sfsi_premium_responsive_custom_icon_container.sfsi_premium_responsive_custom_icon_' + added_count + '_container').show();
                    // jQuery('.sfsi_premium_responsive_custom_icon_container.sfsi_premium_responsive_custom_icon_'+added_count+'_container .sfsi_premium_responsive_custom_'+added_count+'_display').attr('checked',true);
                    jQuery('.sfsi_premium_responsive_custom_icon_container.sfsi_premium_responsive_custom_icon_' + added_count + '_container .checkbox').click();
                    jQuery('.sfsi_premium_responsive_custom_icon_container.sfsi_premium_responsive_custom_icon_' + (added_count - 1) + '_container .sfsi_premium_responsive_custom_delete_btn').hide();
                    jQuery('.sfsi_premium_responsive_custom_icon_container.sfsi_premium_responsive_custom_icon_' + added_count + '_container .sfsi_premium_responsive_custom_delete_btn').show();
                    if (added_count >= 4) {
                        elem_li.hide();
                    }
                    // var added_count=window.sfsi_premium_custom_responsive_icons.filter(function(data){data.added==='yes'}).length;
                    // var next_to_add=0;
                    // jQuery('')
                    // var template = elem_li.parent().find('template');
                    // template_clone= jQuery(template.html());
                    // elem_li.parent().append(template_clone);
                    // elem_li.parent().append(template);
                    // elem_li.parent().append(elem_li);
                }
                break;
            case 'sfsi_premium_sticky_custom_new_display':
                var elem = SFSI('input[name="' + inputName + '"]');
                var elem_li = elem.parent().parent();
                console.log('checking');
                if (inputChecked) {
                    elem.prop('checked', false);
                    elem.parent().find('span').css('background-position', '0px 0px');
                    var added_count = jQuery('.sfsi_premium_sticky_custom_icon_container .radio_section.tb_4_ck input[type="hidden"][value="yes"]').length;
                    jQuery('.sfsi_premium_sticky_custom_icon_container.sfsi_premium_sticky_custom_icon_' + added_count + '_container input[type="hidden"]').attr('value', 'yes');
                    console.log(added_count);

                    jQuery('.sfsi_premium_sticky_custom_icon_container.sfsi_premium_sticky_custom_icon_' + added_count + '_container').show();
                    jQuery('.sfsi_premium_sticky_custom_icon_container.sfsi_premium_sticky_custom_icon_' + added_count + '_container').css("display", "inline-block");
                    ;

                    // jQuery('.sfsi_premium_sticky_custom_icon_container.sfsi_premium_sticky_custom_icon_'+added_count+'_container .sfsi_premium_sticky_custom_'+added_count+'_display').attr('checked',true);
                    jQuery('.sfsi_premium_sticky_custom_icon_container.sfsi_premium_sticky_custom_icon_' + added_count + '_container .checkbox').click();
                    jQuery('.sfsi_premium_sticky_custom_icon_container.sfsi_premium_sticky_custom_icon_' + (added_count - 1) + '_container .sfsi_premium_sticky_custom_delete_btn').hide();
                    jQuery('.sfsi_premium_sticky_custom_icon_container.sfsi_premium_sticky_custom_icon_' + added_count + '_container .sfsi_premium_sticky_custom_delete_btn').show();
                    if (added_count >= 4) {
                        elem_li.hide();
                    } else {
                        elem_li.show();
                    }
                    // var added_count=window.sfsi_premium_custom_responsive_icons.filter(function(data){data.added==='yes'}).length;
                    // var next_to_add=0;
                    // jQuery('')
                    // var template = elem_li.parent().find('template');
                    // template_clone= jQuery(template.html());
                    // elem_li.parent().append(template_clone);
                    // elem_li.parent().append(template);
                    // elem_li.parent().append(elem_li);
                }
                break;
            case 'sfsi_premium_responsive_facebook_display':
                if (inputChecked) {
                    SFSI('.sfsi_premium_responsive_icon_facebook_container').parents('a').show();
                    var icon = inputName.replace('sfsi_premium_responsive_', '').replace('_display', '');
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                } else {

                    SFSI('.sfsi_premium_responsive_icon_facebook_container').parents('a').hide();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                }
                break;
            case 'sfsi_premium_responsive_Twitter_display':
                if (inputChecked) {
                    SFSI('.sfsi_premium_responsive_icon_twitter_container').parents('a').show();
                    var icon = inputName.replace('sfsi_premium_responsive_', '').replace('_display', '');
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                } else {
                    SFSI('.sfsi_premium_responsive_icon_twitter_container').parents('a').hide();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                }
                break;
            case 'sfsi_premium_responsive_Follow_display':
                if (inputChecked) {
                    SFSI('.sfsi_premium_responsive_icon_follow_container').parents('a').show();
                    var icon = inputName.replace('sfsi_premium_responsive_', '').replace('_display', '');
                } else {
                    SFSI('.sfsi_premium_responsive_icon_follow_container').parents('a').hide();
                }
                window.sfsi_premium_fittext_shouldDisplay = true;
                jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                    if (jQuery(a_container).css('display') !== "none") {
                        sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                    }
                })
                break;
            case 'sfsi_premium_responsive_pinterest_display':
                if (inputChecked) {
                    SFSI('.sfsi_premium_responsive_icon_pinterest_container').parents('a').show();
                    var icon = inputName.replace('sfsi_premium_responsive_', '').replace('_display', '');
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                } else {
                    SFSI('.sfsi_premium_responsive_icon_pinterest_container').parents('a').hide();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                }
                break;
            case 'sfsi_premium_responsive_Linkedin_display':
                if (inputChecked) {
                    SFSI('.sfsi_premium_responsive_icon_linkedin_container').parents('a').show();
                    var icon = inputName.replace('sfsi_premium_responsive_', '').replace('_display', '');
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                } else {
                    SFSI('.sfsi_premium_responsive_icon_linkedin_container').parents('a').hide();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                }
                break;

            case 'sfsi_premium_responsive_Whatsapp_display':
                if (inputChecked) {
                    SFSI('.sfsi_premium_responsive_icon_whatsapp_container').parents('a').show();
                    var icon = inputName.replace('sfsi_premium_responsive_', '').replace('_display', '');
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                } else {
                    SFSI('.sfsi_premium_responsive_icon_whatsapp_container').parents('a').hide();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                }
                break;
            case 'sfsi_premium_responsive_vk_display':
                if (inputChecked) {
                    SFSI('.sfsi_premium_responsive_icon_vk_container').parents('a').show();
                    var icon = inputName.replace('sfsi_premium_responsive_', '').replace('_display', '');
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                } else {
                    SFSI('.sfsi_premium_responsive_icon_vk_container').parents('a').hide();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                }
                break;
            case 'sfsi_premium_responsive_Odnoklassniki_display':
                if (inputChecked) {
                    SFSI('.sfsi_premium_responsive_icon_odnoklassniki_container').parents('a').show();
                    var icon = inputName.replace('sfsi_premium_responsive_', '').replace('_display', '');
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                } else {
                    SFSI('.sfsi_premium_responsive_icon_odnoklassniki_container').parents('a').hide();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                }
                break;
            case 'sfsi_premium_responsive_Telegram_display':
                if (inputChecked) {
                    SFSI('.sfsi_premium_responsive_icon_telegram_container').parents('a').show();
                    var icon = inputName.replace('sfsi_premium_responsive_', '').replace('_display', '');
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                } else {
                    SFSI('.sfsi_premium_responsive_icon_telegram_container').parents('a').hide();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                }
                break;
            case 'sfsi_premium_responsive_Weibo_display':
                if (inputChecked) {
                    SFSI('.sfsi_premium_responsive_icon_weibo_container').parents('a').show();
                    var icon = inputName.replace('sfsi_premium_responsive_', '').replace('_display', '');
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                } else {
                    SFSI('.sfsi_premium_responsive_icon_weibo_container').parents('a').hide();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                }
                break;
            case 'sfsi_premium_responsive_QQ2_display':
                if (inputChecked) {
                    SFSI('.sfsi_premium_responsive_icon_qq2_container').parents('a').show();
                    var icon = inputName.replace('sfsi_premium_responsive_', '').replace('_display', '');
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                } else {
                    SFSI('.sfsi_premium_responsive_icon_qq2_container').parents('a').hide();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                }
                break;
            case 'sfsi_premium_responsive_xing_display':
                if (inputChecked) {
                    SFSI('.sfsi_premium_responsive_icon_xing_container').parents('a').show();
                    var icon = inputName.replace('sfsi_premium_responsive_', '').replace('_display', '');
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                } else {
                    SFSI('.sfsi_premium_responsive_icon_xing_container').parents('a').hide();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                }
                break;
            case 'sfsi_premium_responsive_custom_0_display':
                if (inputChecked) {
                    SFSI('.sfsi_premium_responsive_icon_0_container').parents('a').show();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                } else {
                    SFSI('.sfsi_premium_responsive_icon_0_container').parents('a').hide();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                }
                break;
            case 'sfsi_premium_responsive_custom_1_display':
                if (inputChecked) {
                    SFSI('.sfsi_premium_responsive_icon_1_container').parents('a').show();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                } else {
                    SFSI('.sfsi_premium_responsive_icon_1_container').parents('a').hide();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                }
                break;
            case 'sfsi_premium_responsive_custom_2_display':
                if (inputChecked) {
                    SFSI('.sfsi_premium_responsive_icon_2_container').parents('a').show();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                } else {
                    SFSI('.sfsi_premium_responsive_icon_2_container').parents('a').hide();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                }
                break;
            case 'sfsi_premium_responsive_custom_3_display':
                if (inputChecked) {
                    SFSI('.sfsi_premium_responsive_icon_3_container').parents('a').show();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                } else {
                    SFSI('.sfsi_premium_responsive_icon_3_container').parents('a').hide();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                }
                break;
            case 'sfsi_premium_responsive_custom_4_display':
                if (inputChecked) {
                    SFSI('.sfsi_premium_responsive_icon_4_container').parents('a').show();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                } else {
                    SFSI('.sfsi_premium_responsive_icon_4_container').parents('a').hide();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                }
                break;
            case 'sfsi_premium_responsive_custom_5_display':
                if (inputChecked) {
                    SFSI('.sfsi_premium_responsive_icon_5_container').parents('a').show();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                } else {
                    SFSI('.sfsi_premium_responsive_icon_5_container').parents('a').hide();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                }
                break;
            case 'sfsi_premium_responsive_custom_6_display':
                if (inputChecked) {
                    SFSI('.sfsi_premium_responsive_icon_6_container').parents('a').show();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                } else {
                    SFSI('.sfsi_premium_responsive_icon_6_container').parents('a').hide();
                    if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                        window.sfsi_premium_fittext_shouldDisplay = true;
                        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                            if (jQuery(a_container).css('display') !== "none") {
                                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                            }
                        })
                    }
                }
                break;
        }
        sfsi_premium_resize_icons_container();

        var checkboxElem = SFSI(this);
        sfsi_toggle_include_exclude_posttypes_taxonomies(checkboxElem, inputName, inputChecked, s);

        //----------------- Customization for Twitter add card CLOSES----------------------------//


        ("sfsi_plus_shuffle_Firstload" == s.attr("name") && "checked" == s.attr("checked") || "sfsi_plus_shuffle_interval" == s.attr("name") && "checked" == s.attr("checked")) && (SFSI('input[name="sfsi_plus_shuffle_icons"]').parent().find("span").css("background-position", "0px -36px"),
            SFSI('input[name="sfsi_plus_shuffle_icons"]').attr("checked", "checked")), "sfsi_plus_shuffle_icons" == s.attr("name") && "checked" != s.attr("checked") && (SFSI('input[name="sfsi_plus_shuffle_Firstload"]').removeAttr("checked"),
            SFSI('input[name="sfsi_plus_shuffle_Firstload"]').parent().find("span").css("background-position", "0px 0px"),
            SFSI('input[name="sfsi_plus_shuffle_interval"]').removeAttr("checked"), SFSI('input[name="sfsi_plus_shuffle_interval"]').parent().find("span").css("background-position", "0px 0px"));
        var sfsi_plus_icon_hover_switch_exclude_custom_post_types = SFSI('input[name="sfsi_plus_icon_hover_switch_exclude_custom_post_types"]:checked').val() || 'no';
        var sfsi_plus_icon_hover_switch_exclude_taxonomies = SFSI('input[name="sfsi_plus_icon_hover_switch_exclude_taxonomies"]:checked').val() || 'no';

    });

    function sfsi_toggle_include_exclude_posttypes_taxonomies(checkboxElem, inputFieldName, inputChecked, inputFieldElem) {

        switch (inputFieldName) {

            case 'sfsi_plus_switch_exclude_custom_post_types':
            case 'sfsi_plus_switch_include_custom_post_types':
            case 'sfsi_plus_icon_hover_switch_exclude_custom_post_types':
            case 'sfsi_plus_icon_hover_switch_include_custom_post_types':

                var elem = inputFieldElem.parent().parent().find("#sfsi_premium_custom_social_data_post_types_ul");

                if (inputChecked) {
                    elem.show();
                } else {
                    elem.hide();
                }

                break;

            case 'sfsi_plus_switch_exclude_taxonomies':
            case 'sfsi_plus_switch_include_taxonomies':
            case 'sfsi_plus_icon_hover_switch_exclude_taxonomies':
            case 'sfsi_plus_icon_hover_switch_include_taxonomies':

                var elem = inputFieldElem.parent().parent().find("#sfsi_premium_taxonomies_ul");

                if (inputChecked) {
                    elem.show();
                } else {
                    elem.hide();
                }

                break;

            case 'sfsi_plus_include_url':
            case 'sfsi_plus_exclude_url':

                var value = checkboxElem.css("background-position");
                var keyWCnt = checkboxElem.parent().next().next();

                if (value === '0px -36px') {
                    keyWCnt.show();
                    keyWCnt.next().show();
                } else {
                    keyWCnt.hide();
                    keyWCnt.next().hide();
                }

                break;
        }
    }

    SFSI("body").on("click", "#getMeFullAccess", function (event) {
        event.preventDefault();
        var email = SFSI(this).parents("form").find("input[type='email']").val();
        var feedid = SFSI(this).parents("form").find("input[name='feedid']").val();

        var error = false;
        var regEx = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;

        if (email === '') {
            error = true;
        }

        if (!regEx.test(email)) {
            error = true;
        }
        /*console.log(feedid, error);*/

        if (!error) {
            SFSI(this).css("pointer-events", "none");
            if (undefined == feedid || feedid == "") {

                var nonce = SFSI(this).attr('data-nonce-fetch-feed-id');
                e = {
                    action: "sfsi_premium_get_feed_id",
                    nonce: nonce,
                };
                SFSI.ajax({
                    url: sfsi_premium_ajax_object.ajax_url,
                    type: "post",
                    data: e,
                    dataType: "json",
                    async: !0,
                    success: function (s) {
                        console.log('testing mail1', feedid, error, s);

                        if (s.res == "wrong_nonce") {
                            SFSI(".sfsiplus_removeFooter").text("Unauthorised Request, Try again after refreshing page");
                            SFSI(".sfsi_premium_getMeFullAccess_class").css("pointer-events", "initial");

                        } else {
                            if ("success" == s.res) {
                                var feedid = s.feed_id;
                                if ("" !== feedid && null !== feedid) {
                                    console.log('testing mail');
                                    SFSI("#getMeFullAccess").parents("form").find("input[name='feed_id']").val(feedid);
                                    SFSI("#getMeFullAccess").parents("form").submit();
                                    SFSI(".sfsi_premium_getMeFullAccess_class").css("pointer-events", "initial");

                                } else {
                                    alert("Error: Claiming didn't work. Please try again later.");
                                    SFSI(".sfsi_premium_getMeFullAccess_class").css("pointer-events", "initial");

                                }
                            } else {
                                if ("failed" == s.res) {
                                    alert("Error: " + s.message + ".");
                                    SFSI(".sfsi_premium_getMeFullAccess_class").css("pointer-events", "initial");

                                } else {
                                    alert("Error: Please try again.");
                                    SFSI(".sfsi_premium_getMeFullAccess_class").css("pointer-events", "initial");

                                }
                            }
                        }
                    }
                });
            } else {
                SFSI("#getMeFullAccess").parents("form").submit();
            }
        } else {
            alert("Error: Please provide your email address.");
        }
    });

    SFSI('form#calimingOptimizationForm').on('keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    function sfsi_premium_add_deleteAnotherField(addBtnClassName, inputName, deleteBtnClassName, classKeywordContainter) {

        var addBtnClassName = "." + addBtnClassName;

        SFSI("body").on("click", addBtnClassName, function () {

            var newCount = parseInt(SFSI(this).attr("data-count")) + 1;
            var html = '<fieldset><label>String ' + newCount + ':</label><input type="text" name="' + inputName + '" value="" /><a href="javascript:" class="' + deleteBtnClassName + '">Delete</a></fieldset>';
            SFSI(this).attr("data-count", newCount);
            SFSI(this).prev("." + classKeywordContainter).append(html);

        });

        SFSI("body").on("click", "." + deleteBtnClassName, function () {
            SFSI(this).parent("fieldset").remove();
        });
    }

    sfsi_premium_add_deleteAnotherField('sfsi_plus_addAnotherFiled', 'sfsi_plus_urlKeywords[]', 'sfsi_plus_deleteKeywordField', 'excludecontainter');
    sfsi_premium_add_deleteAnotherField('sfsi_plus_include_addAnotherFiled', 'sfsi_plus_include_urlKeywords[]', 'sfsi_plus_include_deleteKeywordField', 'includecontainter');

    sfsi_premium_add_deleteAnotherField('sfsi_plus_icon_hover_exclude_addAnotherFiled', 'sfsi_plus_icon_hover_exclude_urlKeywords[]', 'sfsi_plus_icon_hover_deleteKeywordField', 'excludecontainter_icon_hover');
    sfsi_premium_add_deleteAnotherField('sfsi_plus_icon_hover_include_addAnotherFiled', 'sfsi_plus_icon_hover_include_urlKeywords[]', 'sfsi_plus_icon_hover_include_deleteKeywordField', 'includecontainter_icon_hover');


    SFSI("body").on("click", ".sfsi_plus_profile_check_section span.checkbox", function () {
        if (SFSI(this).next("input[name='sfsi_plus_facebookFollow_option']").is(":checked")) {
            SFSI(".sfsi_plus_profile_url_section").show();
        } else {
            SFSI(".sfsi_plus_profile_url_section").hide();
        }
    });

    /*SFSI(".checkbox").live("click", function()
	{
        var s = SFSI(this).parent().find("input:checkbox:first");
        "sfsi_plus_float_on_page" == s.attr("name") && "yes" == s.val() && (
        SFSI('input[name="sfsi_plus_icons_stick"][value="no"]').attr("checked", !0), SFSI('input[name="sfsi_plus_icons_stick"][value="yes"]').removeAttr("checked"),
        SFSI('input[name="sfsi_plus_icons_stick"][value="no"]').parent().find("span").attr("style", "background-position:0px -41px;"),
        SFSI('input[name="sfsi_plus_icons_stick"][value="yes"]').parent().find("span").attr("style", "background-position:0px -0px;"));
    });
	SFSI(".radio").live("click", function()
	{
        var s = SFSI(this).parent().find("input:radio:first");
		var a = SFSI(".cstmfltonpgstck");
		("sfsi_plus_icons_stick" == s.attr("name") && "yes" == s.val()) && (
        SFSI('input[name="sfsi_plus_float_on_page"][value="no"]').prop("checked", !0), SFSI('input[name="sfsi_plus_float_on_page"][value="yes"]').prop("checked", !1),
        SFSI('input[name="sfsi_plus_float_on_page"][value="no"]').parent().find("span.checkbox").attr("style", "background-position:0px -41px;"),
        SFSI('input[name="sfsi_plus_float_on_page"][value="yes"]').parent().find("span.checkbox").attr("style", "background-position:0px -0px;"),
		jQuery(a).children(".checkbox").css("background-position", "0px 0px" ), sfsiplus_toggleflotpage(a));
    });*/

    if (true != window.sfsi_premium_custom_form_initiated) {
        window.Custom.init();
        window.sfsi_premium_custom_form_initiated = true;
    }
};

window.sfsi_main_loaded = false;
SFSI(document).ready(function () {
    if (false === window.sfsi_main_loaded) {
        window.sfsi_main_loaded = true;
        window.sfsi_load();
    }
})
SFSI(window).on('load', function () {
    if (false === window.sfsi_main_loaded) {
        window.sfsi_main_loaded = true;
        window.sfsi_load();
    }
});

//for utube channel name and id
function showhideutube(ref) {
    var chnlslctn = SFSI(ref).children("input").val();
    var _currentParentNext = SFSI(ref).parent(".enough_waffling").next(".cstmutbtxtwpr");
    if (chnlslctn == "name") {
        _currentParentNext.children(".cstmutbchnlnmewpr").slideDown();
        _currentParentNext.children(".cstmutbchnlidwpr").slideUp();
    } else {
        _currentParentNext.children(".cstmutbchnlidwpr").slideDown();
        _currentParentNext.children(".cstmutbchnlnmewpr").slideUp();
    }
}

var sfsiplus_initTop = new Array();

function checkforinfoslction(ref) {
    var pos = jQuery(ref).children(".checkbox").css("background-position");

    if (pos == "0px 0px") {
        jQuery(ref).next(".sfsiplus_right_info").children("p").children("label").hide();
        jQuery(ref).next(".sfsiplus_right_info").children(".sfsiplus_show_desktop_mobile_setting_li").hide();

        if (jQuery(ref).parent().attr('class') == 'sfsiplus_show_via_widget_li') {
            jQuery('.sfsi_plus_alignments_mobile_widget').removeClass("show").addClass("hide");
            jQuery('.sfsi_plus_widget_icons_alignment').removeClass("show").addClass("hide");
            jQuery('.sfsi_plus_widget_mobile_icons_alignment').removeClass("show").addClass("hide");
        }

        if (jQuery(ref).parent().attr('class') == 'sfsiplusplacethemanulywpr') {
            jQuery('.sfsi_plus_shortcode_icons_alignment').removeClass("show").addClass("hide");
            jQuery('.sfsi_plus_shortcode_mobile_icons_alignment').removeClass("show").addClass("hide");
            jQuery('.sfsi_plus_alignments_mobile_shortcode').removeClass("show").addClass("hide");
        }

        if (jQuery(ref).parent().attr('class') == 'sfsiplus_sticky_bar') {
            jQuery('.sfsi_plus_sticky_bar').removeClass("show").addClass("hide");
        }

    } else {
        jQuery(ref).next(".sfsiplus_right_info").children("p").children("label").show();
        jQuery(ref).next(".sfsiplus_right_info").children(".sfsiplus_show_desktop_mobile_setting_li").show();

        if (jQuery(ref).parent().attr('class') == 'sfsiplus_show_via_widget_li') {

            jQuery('.sfsi_plus_alignments_mobile_widget').removeClass("hide").addClass("show");

            jQuery('.sfsi_plus_widget_icons_alignment').removeClass("hide").addClass("show");
            jQuery('.sfsi_plus_widget_mobile_icons_alignment').removeClass("hide").addClass("show");

            if (jQuery("input[name='sfsi_plus_mobile_widget']:checked").val() == "no") {
                jQuery('.sfsi_plus_widget_mobile_icons_alignment').removeClass("show").addClass("hide");
            }
        }

        if (jQuery(ref).parent().attr('class') == 'sfsiplusplacethemanulywpr') {
            jQuery('.sfsi_plus_shortcode_icons_alignment').removeClass("hide").addClass("show");
            jQuery('.sfsi_plus_alignments_mobile_shortcode').removeClass("hide").addClass("show");

            if (jQuery("input[name='sfsi_plus_mobile_shortcode']:checked").val() == "no") {
                jQuery('.sfsi_plus_shortcode_mobile_icons_alignment').removeClass("show").addClass("hide");
            } else {
                jQuery('.sfsi_plus_shortcode_mobile_icons_alignment').removeClass("hide").addClass("show");
            }
        }

        if (jQuery(ref).parent().attr('class') == 'sfsiplus_sticky_bar') {
            jQuery('.sfsi_plus_sticky_bar').removeClass("hide").addClass("show");
        }
    }
}

function sfsiplus_toggleflotpage(ref) {
    var pos = jQuery(ref).children(".checkbox").css("background-position");
    if (pos == "0px 0px") {
        jQuery(ref).next(".sfsiplus_right_info").children("p").children(".sfsiplus_sub-subtitle").hide();
        jQuery(ref).next(".sfsiplus_right_info").children(".sfsiplus_tab_3_icns").hide();
        jQuery(ref).next(".sfsiplus_right_info").children(".sfsiplus_show_desktop_mobile_setting_li").hide();

        if (jQuery(ref).parent().attr('class') == "sfsi_plus_place_beforeAfterPosts") {
            jQuery(".sfsi_rectangle_ul").hide();
            jQuery(".sfsi_rectangle_ul").find(".sfsiplus_show_desktop_mobile_setting_li").hide();
        }

        if (jQuery(ref).parent().attr('class') == "sfsiplusLocationli") {
            jQuery('.sfsi_plus_float_icons_alignment').removeClass("show").addClass("hide");
        }
    } else {
        jQuery(ref).next(".sfsiplus_right_info").children("p").children(".sfsiplus_sub-subtitle").show();
        jQuery(ref).next(".sfsiplus_right_info").children(".sfsiplus_tab_3_icns").show();
        jQuery(ref).next(".sfsiplus_right_info").children(".sfsiplus_show_desktop_mobile_setting_li").show();

        if (jQuery(ref).parent().attr('class') == "sfsi_plus_place_beforeAfterPosts") {
            jQuery(".sfsi_rectangle_ul").show();
            jQuery(".sfsi_rectangle_ul").find(".sfsiplus_show_desktop_mobile_setting_li").show();
        }

        if (jQuery(ref).parent().attr('class') == "sfsiplusLocationli") {
            jQuery('.sfsi_plus_float_icons_alignment').removeClass("hide").addClass("show");
        }
        window.sfsi_premium_fittext_shouldDisplay = true;
        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
            if (jQuery(a_container).css('display') !== "none") {
                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
            }
        });
        sfsi_premium_resize_icons_container();


    }
}

function sfsiplus_togglbtmsection(show, hide, ref) {
    jQuery(ref).parent("ul").children("li.clckbltglcls").each(function (index, element) {
        jQuery(this).children(".radio").css("background-position", "0px 0px");
        jQuery(this).children(".styled").attr("checked", "false");
    });
    jQuery(ref).children(".radio").css("background-position", "0px -41px");
    jQuery(ref).children(".styled").attr("checked", "true");

    jQuery("." + show).show();
    jQuery("." + show).children(".radiodisplaysection").show();
    jQuery("." + hide).hide();
    jQuery("." + hide).children(".radiodisplaysection").hide();


    if (jQuery(ref).children('input[name="sfsi_plus_display_button_type"]').val() == "normal_button") {

        var mobileSelection;

        jQuery(".sfsi_plus_mobile_beforeafterposts").children().find(".radio").each(function (index, element) {

            if (jQuery(element).css("background-position") == "0px -41px") {
                mobileSelection = jQuery(element).next().val();
            }
        });

        if (jQuery(ref).children('.radio').css("background-position") == "0px -41px") {
            jQuery('.sfsi_plus_alignments_mobile_beforeafterposts').css("display", "block");
            jQuery('.sfsi_plus_beforeafterposts_icons_alignment').css("display", "block");
        } else {
            jQuery('.sfsi_plus_beforeafterposts_icons_alignment').css("display", "none");
            jQuery('.sfsi_plus_alignments_mobile_beforeafterposts').css("display", "none");
        }

        if (mobileSelection == "yes") {
            jQuery('.sfsi_plus_beforeafterposts_mobile_icons_alignment').removeClass(".hide").addClass("show");
        } else {
            jQuery('.sfsi_plus_beforeafterposts_mobile_icons_alignment').removeClass(".show").addClass("hide");
        }

    }
    if (jQuery(ref).children('input[name="sfsi_plus_display_button_type"]').val() == "responsive_button") {
        jQuery('.sfsi_premium_responsive_icon_option_li').show();
    } else {
        jQuery('.sfsi_premium_responsive_icon_option_li').hide();
    }

}

/* Fix label click issue in "Show them before or after posts" */
SFSI('.sfsiplus_top_tabs label').on('click', function (e) {
    SFSI(this).parent().find('input[type="radio"]').trigger('click');
});

/* check facebook access token */
SFSI('#sfsi_plus_facebook_fbtoken').on("click", function (e) {
    e.preventDefault();
    if (SFSI('input[name="sfsi_plus_facebook_countsFrom_blog"]').val()) {
        check_access_token();
    }
});

function check_access_token() {
    var fb_url_27 = "https://graph.facebook.com/v2.7/?id=http://www.google.com&access_token=" + SFSI('input[name="sfsi_plus_facebook_countsFrom_blog"]').val();
    var fb_url_29 = "https://graph.facebook.com/v2.9/?id=http://www.google.com&fields=engagement&access_token=" + SFSI('input[name="sfsi_plus_facebook_countsFrom_blog"]').val();

    SFSI.ajax(fb_url_27)
        .done(function (e) {
            if (e.share == undefined) {

                // Call 2.9 version
                SFSI.ajax(fb_url_29)
                    .done(function (f) {
                        if (!f.engagement) {
                            SFSI('#sfsi_plus_facebook_notice').html('<span style="color:red;margin-top: 15px; margin-left: 60px;font-size: 14px;"><strong>Token is invalid!</strong> ');
                        } else {
                            SFSI('#sfsi_plus_facebook_token_notice').html('<strong>Token valid:</strong> Facebook share count for http://google.com: ' + f.engagement.share_count).show();
                        }

                    })
                    .fail(function (f) {
                        SFSI('#sfsi_plus_facebook_token_notice').html('<span style="color:red;"> <strong>Error:</strong> Access Token Invalid!</span>').show();
                    })
            } else {

                if (!e.share) {
                    SFSI('#sfsi_plus_facebook_notice').html('<span style="color:red;margin-top: 15px; margin-left: 60px;font-size: 14px;"><strong>Token is invalid!</strong> ');
                } else {
                    SFSI('#sfsi_plus_facebook_token_notice').html('<strong>Token valid:</strong> Facebook share count for http://google.com: ' + e.share.share_count).show();
                }
            }
        })
        .fail(function (e) {
            SFSI('#sfsi_plus_facebook_token_notice').html('<span style="color:red;"> <strong>Error:</strong> Access Token Invalid!</span>').show();
        })
}

function open_save_image(btnUploadID, inputImageId, previewDivId) {

    var btnElem, inputImgElem, previewDivElem;

    var clickHandler = function (event) {

        var send_attachment_bkp = wp.media.editor.send.attachment;

        var frame = wp.media({
            title: 'Select or Upload Media for Social Media',
            button: {
                text: 'Use this media'
            },
            multiple: false // Set to true to allow multiple files to be selected
        });

        frame.on('select', function () {

            // Get media attachment details from the frame state
            var attachment = frame.state().get('selection').first().toJSON(),

                url = attachment.url.split("/");
            fileName = url[url.length - 1];
            fileArr = fileName.split(".");
            fileType = fileArr[fileArr.length - 1];

            if (fileType != undefined && (fileType == 'gif' || fileType == 'jpeg' || fileType == 'jpg' || fileType == 'png')) {

                inputImgElem.val(attachment.url);
                previewDivElem.attr('src', attachment.url);

                btnElem.val("Change Picture");

                wp.media.editor.send.attachment = send_attachment_bkp;
            } else {
                alert("Only Images are allowed to upload");
                frame.open();
            }
        });

        // Finally, open the modal on click
        frame.open();
        return false;
    };

    if ("object" === typeof btnUploadID && null !== btnUploadID) {

        btnElem = SFSI(btnUploadID);
        inputImgElem = btnElem.parent().find('input[type="hidden"]');
        previewDivElem = btnElem.parent().find('img');

        clickHandler();
    } else {
        btnElem = SFSI('#' + btnUploadID);
        inputImgElem = SFSI('#' + inputImageId);
        previewDivElem = SFSI('#' + previewDivId);

        btnElem.on("click", clickHandler);
    }

}

function remaining_char_display(textareaId, remaincharBoxId) {
    SFSI('#' + textareaId).keyup(function () {
        var txtareaVal = SFSI.trim(SFSI('#' + textareaId).val());
        var remaining_max = SFSI(this).attr("maxlength");
        var remaining_char = remaining_max - txtareaVal.length;
        SFSI("#" + remaincharBoxId).text(remaining_char);
    });
}

open_save_image('sfsi-social-media-image-button', 'sfsi-social-media-image', 'sfsi-social-media-image-preview');
open_save_image('sfsi-social-pinterest-image-button', 'sfsi-social-pinterest-image', 'sfsi-social-pinterest-image-preview');

remaining_char_display('social_fbGLTw_title_textarea', 'sfsi_title_remaining_char');
remaining_char_display('social_fbGLTw_description_textarea', 'sfsi_desc_remaining_char');
remaining_char_display('social_twitter_description_textarea', 'sfsi_twitter_desc_remaining_char');


SFSI(function () {
    SFSI('.mkPop').on("keydown", function (e) {
        if (e.shiftKey && (e.which == 188 || e.which == 190)) {
            e.preventDefault();
        }
    });
});

SFSI(function ($) {

    var $elem = SFSI('.sfsi_mainContainer');

    SFSI('.learnmore').click(function () {
        SFSI('html, body').animate({
            scrollTop: $elem.height()
        }, 800);
        SFSI('#accordion2 h3').trigger('click');
    });
});

SFSI(document).ready(function () {

    SFSI(".plus_share_icon_order li a,.plus_share_icon_mobile_order li a").on("click", function () {
        return false;
    });

    SFSI('select[name="sfsi_plus_display_on_all_icons"]').change(function () {
        if ('yes' == SFSI(this).val()) {
            SFSI('#sfsiplusIncludeExcludeRulesAppliesTo ul').removeClass('show').addClass('hide').hide();
        } else {
            SFSI('#sfsiplusIncludeExcludeRulesAppliesTo ul').removeClass('hide').addClass('show').show();
        }
    });

    SFSI('.mouseover_other_icon_change_link').on('click', function () {

        SfsiHelper.openWpMedia(this, '', '', 'plus_sfsi_upload_mouseovericon');

    });

    SFSI('.mouseover_other_icon_revert_link').on('click', function () {

        var revertElem = SFSI(this);
        var iconName = revertElem.attr('data-iconname');

        if ("string" === typeof iconName && iconName.length > 0) {
            sfsi_plus_delete_mouserOverIcon(revertElem, iconName);
        }

    });
    window.pinterest_custom_type = SFSI("select.sfsi_plus_img_on_hover_input[name='sfsi_plus_icon_hover_type']").val();
    SFSI("select.sfsi_plus_img_on_hover_input[name='sfsi_plus_icon_hover_type']").change((data) => {
        sfsi_premium_pinterest_custom_icon(data);
    });
});

function ChangeUrl(title, url) {
    if (typeof (history.pushState) != "undefined") {
        var obj = {
            Title: title,
            Url: url
        };
        history.pushState(obj, obj.Title, obj.Url);
    } else {
        alert("Browser does not support HTML5.");
    }
}

SFSI(document).ready(function () {

    SFSI('select[name="sfsi_plus_icon_hover_type"]').change(function (val) {
        if (SFSI(this).val() !== "no") {
            SFSI('.sfsi_premium_show_no_icon').hide();
            SFSI('.sfsi_premium_show_icon').show();
            if (SFSI(this).val() === 'small-rectangle' || SFSI(this).val() === 'large-rectangle') {
                // SFSI('.sfsi_premium_square_icon').hide();
                SFSI('.sfsi_premium_icon_rectangle').show();

            } else {
                // SFSI('.sfsi_premium_square_icon').show();
                SFSI('.sfsi_premium_icon_rectangle').hide();
            }
            if (SFSI('select[name="sfsi_plus_icon_hover_on_all_pages"]').val() == 'yes') {
                SFSI('.sfsi_plus_page_exclude>ul').addClass('hide').removeClass('show').hide();
                SFSI('.sfsi_plus_page_include>ul').addClass('hide').removeClass('show').hide();
            } else if (SFSI('.sfsi_plus_icon_hover_on_all_pages').val() == 'include') {
                SFSI('.sfsi_plus_page_include').show();
                SFSI('.sfsi_plus_page_exclude>ul').addClass('hide').removeClass('show').hide();
                SFSI('.sfsi_plus_page_include>ul').addClass('show').removeClass('hide').show();
            } else {
                SFSI('.sfsi_plus_page_exclude').show();
                SFSI('.sfsi_plus_page_exclude>ul').addClass('show').removeClass('hide').show();
                SFSI('.sfsi_plus_page_include>ul').addClass('hide').removeClass('show').hide();
            }
        } else {
            SFSI('.sfsi_premium_show_icon').hide();
            SFSI('.sfsi_premium_show_no_icon').show();
            SFSI('.sfsi_plus_page_exclude').hide();
            SFSI('.sfsi_plus_page_include').hide();
        }
    })
    SFSI('select[name="sfsi_plus_icon_hover_on_all_pages"]').change(function (val) {
        if (SFSI('select[name="sfsi_plus_icon_hover_type"]').val() === 'no') {
            SFSI('.sfsi_plus_page_exclude').hide();
            SFSI('.sfsi_plus_page_include').hide();
        } else {
            if (SFSI(this).val() == 'yes') {
                SFSI('.sfsi_plus_page_exclude>ul').addClass('hide').removeClass('show').hide();
                SFSI('.sfsi_plus_page_include>ul').addClass('hide').removeClass('show').hide();
            } else if (SFSI(this).val() == 'include') {
                SFSI('.sfsi_plus_page_include').show();
                SFSI('.sfsi_plus_page_exclude>ul').addClass('hide').removeClass('show').hide();
                SFSI('.sfsi_plus_page_include>ul').addClass('show').removeClass('hide').show();
            } else {
                SFSI('.sfsi_plus_page_exclude').show();
                SFSI('.sfsi_plus_page_exclude>ul').addClass('show').removeClass('hide').show();
                SFSI('.sfsi_plus_page_include>ul').addClass('hide').removeClass('show').hide();
            }
        }
    });
    SFSI(document).on('click', '.sfsi_plus_icon_hover_exclude_url_checkSec .checkbox', function () {
        var checkbox = SFSI(this).parent().find('input:checkbox:first');
        if (checkbox.length > 0) {
            if (checkbox[0].checked) {
                SFSI('.excludecontainter_icon_hover').show();
                SFSI('.sfsi_plus_icon_hover_exclude_addAnotherFiled').css({
                    'display': 'inline-block'
                });
            } else {
                SFSI('.excludecontainter_icon_hover').hide();
                SFSI('.sfsi_plus_icon_hover_exclude_addAnotherFiled').hide();
            }
        }
    });
    SFSI(document).on('click', '.sfsi_plus_icon_hover_include_url_checkSec .checkbox', function () {
        var checkbox = SFSI(this).parent().find('input:checkbox:first');
        if (checkbox.length > 0) {
            if (checkbox[0].checked) {
                SFSI('.includecontainter_icon_hover').show();
                SFSI('.sfsi_plus_icon_hover_include_addAnotherFiled').css({
                    'display': 'inline-block'
                });
            } else {
                SFSI('.includecontainter_icon_hover').hide();
                SFSI('.sfsi_plus_icon_hover_include_addAnotherFiled').hide();
            }
        }
    });
    var sfsi_premium_responsive_icon_bg_color = {
        "Buffer": "#151515",
        "Facebook": "#336699",
        "Flicker": "#FF0084",
        "Follow": "#00B04E",
        "GooglePlus": "#DD4B39",
        "Houzz": "#7BC044",
        "Houzz": "#7BC044",
        "Instagram": "",
        "Linkedin": "#0877B5",
        "Mail": "#343D44",
        "Pinterest": "#CB3233",
        "Reddit": "#FF4500",
        "RSS": "#FF9845",
        "Skype": "#00A9F0",
        "Share": "#26AD62",
        "Snapchat": "#F5E728",
        "Soundcloud": "#FF4500",
        "Speicifcfeeds": "#E54543",
        "Tumblr": "#36465F",
        "Twitter": "#020202",
        "Vimeo": "#1AB7EA",
        "Whatsapp": "#3CD946",
        "Yelp": "#C6331E",
        "Yummly": "#E36308",
        "Youtube": "#E02F2F",
        "Amazon": "#2E2B2C",
        "Angieslist": "#3EA258",
        "Blogger": "#F38032",
        "Goodreads": "#784733",
        "Print": "#353535",
        "Steam": "#373435",
        "Stumbleupon": "#E9513D",
        "Telegram": "#33A1D1",
        "Twitch": "#6B529B",
        "Vk": "#4E77A2",
        "Xing": "#286266",
        "Flipboard": "#E02828",
        "Bandcamp": "#6199AA",
        "Spotify": "#1ED760",
        "Odnoklassniki": "#F58220",
        "Pocket": "#EF4056",
        "Meetup": "#ED1C40",
        "Discord": "#6E8ACC",
        "GitHub": "#000000",
        "Wordpress": "#464342",
        "CodePen": "#231F20",
        "Etsy": "#F27224",
        "Meneame": "#E35614",
        "Digg": "#000000",
        "Delicious": "#000000",
        "Frype.com": "#FF6600",
        "THN": "#190A8D",
        "MeWe": "#190A8D",
        "Viber": "#7C529E",
        "Line": "#3ACE01",
        "LiveJournal": "#15374C",
        "IMDB": "#E5B922",
        "Patreon": "#FF5900",
        "Dloky": "#E73F38",
        "BBB": "#0D7C99",
        "Weibo": "#E6162D",
        "Academia": "#41454A",
        "Wikipedia": "#000000",
        "TripAdvisor": "#00AF87",
        "Zillow": "#3871B8",
        "Realtor": "#000000",
        "Messenger": "#447BBF",
        "Yahoo": "#6A2E8C",
        "iTunes": "#EE3459",
        "AppStore": "#3C69B3",
        "PlayStore": "#1AB0C3",
        "SellCodes": "#149E4A",
        "Windows": "Store#118B44",
        "BookBub": "#E61E25",
        "Threema": "#50504F",
        "Stocktwits": "#283646",
        "Refind": "#1A4696",
        "Thumbtack": "#F27802",
        "Mixcloud": "#232323",
        "Slack2": "#232323",
        "Airbnb": "#FF7977",
        "Kudzu": "#1790BF",
        "movingcompanyrevies": "#405361",
        "Superpages": "#F8981B",
        "QQ2": "#D5D5D5",
        "WeChat": "#4BAD33",
        "scoop.it": "#93C240",
        "Venmo": "#4395CF",
        "Apple": "#000000",
        "PayPal": "linear-gradient(#1F87C9 , #223463)",
        "mymovingreviews": "#039146",
        "yellowpages": "#F4E72A",
        "medium": "#000000",
        "ReFind2": "#0090F2",
        "stackoverflow": "#F08026",
        "Apple": "Pay#000000",
        "Blogvlovinv": "#000000",
        "Hometalk": "#51A2D6",
        "stitcher": "#000000",
        "iheart": "#DB0A38",
        "periscope": "#41A9C7",
        "Bitcoin": "#F7931A",
        "GAB": "#25CC80",
        "Teachers Pay Teachers": "#09A56C",
        "Pocket_casts": "#F44336",
        "Next": "Door#19975D",
        "Home": "Advisor #F69020",
        "Vine": "#00B389",
        "WikiDIY": "#678C3C",
        "Talk": "#FFE812",
        "Dailymotion": "#0066DC",
        "Mix.com": "",
        "Ravelry": "#DC2357",
        "Steem": "#2463A6",
        "ebay": "#E1E1E1",
        "Steemit": "#64C3A5",
        "Trustpilot": "linear-gradient(#F05426 , #F78D2A)  ",
        "DTube": "#ED1E24",
        "Phone": "#51AD47",
        "Spreaker": "#0E0E0E",
        "HackerNews": "#F26622",
        "Booking.com": "radial-gradient(#2654A5 , #233D7B)",
        "tunein": "#3EB4A7",
        "Behance": "#2A2A2A",
        "500px": "#2A2A2A",
        "Apple podcast": "linear-gradient(#7E29C5 , #D36CFB)",
        "xiaohongshu": "#F40A0B",
        "TuneIn": "#1C203C",
        "About": "us#1496E2",
        "Informations": "#E9891D",
        "Gallery": "#27BA9A",
        "Question": "#F74741",
        "Text": -"icon#363436",
        "Home": "#5C80C8",
        "Shopping cart": "#F7AB00",
        "Calendar": "#A00341",
        "Shazam": "#0187FD",
        "Poshmark": "linear-gradient(#832432 , #B63A4D)",
        "Tidal.com": "#202020",
        "Zalo.com": "#02ABD3"
    }
    for (var key in sfsi_premium_responsive_icon_bg_color) {
        if (sfsi_premium_responsive_icon_bg_color.hasOwnProperty(key)) {
            var icon_name = key.toLowerCase();
            var class_name = 'sfsi_premium_responsive_icon_' + icon_name + '_container';
            jQuery('.' + class_name).css('background-color', sfsi_premium_responsive_icon_bg_color[key]);
        }
    }
    sfsi_premium_responsive_icon_intraction_handler();
    SFSI('input[name="sfsi_plus_responsive_counter_color"]').wpColorPicker({
        hide: true,
        palettes: true,
        change: function (event, ui) {
            var color = ui.color.toString();
            var target = jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icons_count,.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icons_count h3,.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icons_count h6');
            target.css('color', color);
        },
        clear: function (event) {
            var color = "";
            var target = jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icons_count');
            target.css('color', '#000');
        }

    });
    SFSI('input[name="sfsi_plus_responsive_counter_bg_color"]').wpColorPicker({
        hide: true,
        palettes: true,
        change: function (event, ui) {
            var color = ui.color.toString();
            var target = jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icons_count');
            target.css('background-color', color);
        },
        clear: function (event) {
            var color = "";
            var target = jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icons_count');
            target.css('background-color', '#fff');
        }

    });

    SFSI('input[name="sfsi_plus_sticky_bar_bg_color"]').wpColorPicker({
        hide: true,
        palettes: true,
        change: function (event, ui) {
            var color = ui.color.toString();
            var target = jQuery('.sfsi_premium_sticky_bar_count');
            target.css('background-color', color);
        },
        clear: function (event) {
            var color = "";
            var target = jQuery('.sfsi_premium_sticky_bar_count');
            target.css('background-color', '#fff');
        }
    });

    SFSI('input[name="sfsi_plus_sticky_bar_color"]').wpColorPicker({
        hide: true,
        palettes: true,
        change: function (event, ui) {
            var color = ui.color.toString();
            var target = jQuery('.sfsi_premium_sticky_bar_count');
            target.css('background-color', color);
        },
        clear: function (event) {
            var color = "";
            var target = jQuery('.sfsi_premium_sticky_bar_count');
            target.css('background-color', '#fff');
        }
    });


    SFSI('.sfsi_premium_bg-color-picker').wpColorPicker({
        hide: true,
        palettes: true,
        change: function (event, ui) {
            var color = ui.color.toString();
            var target = jQuery(event.target);
            target.parents('.sfsi_premium_responsive_custom_icon_container').find('.sfsi_premium_responsive_icon_item_container').css('background-color', color);
            var name = jQuery(this).attr('name');
            var custom_icon_id = name.replace('sfsi_plus_responsive_icon_', '').replace('_bg_color', '');
            jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icon_' + custom_icon_id + '_container').css('background-color', color);

        },
        clear: function (event) {
            var color = "";
            var target = jQuery(event.target);
            target.parent().find('input[type="hidden"]').val(color);
            var name = jQuery(this).attr('name');
            var custom_icon_id = name.replace('sfsi_plus_responsive_icon_', '').replace('_bg_color', '');
            jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icon_' + custom_icon_id + '_container').css('background-color', '#729fcf');
        }

    });

    SFSI('.sfsi_premium_sticky_bg-color-picker').wpColorPicker({
        hide: true,
        palettes: true,
        change: function (event, ui) {
            var color = ui.color.toString();
            var target = jQuery(event.target);
            target.parents('.sfsi_premium_sticky_custom_icon_container').find('.sfsi_premium_sticky_icon_item_container').css('background-color', color);
            var name = jQuery(this).attr('name');
            var custom_icon_id = name.replace('sfsi_plus_sticky_icon_', '').replace('_bg_color', '');
            jQuery('.sfsi_premium_sticky_icon_preview .sfsi_premium_sticky_icon_' + custom_icon_id + '_container').css('background-color', color);

        },
        clear: function (event) {
            var color = "";
            var target = jQuery(event.target);
            target.parent().find('input[type="hidden"]').val(color);
            var name = jQuery(this).attr('name');
            var custom_icon_id = name.replace('sfsi_plus_sticky_icon_', '').replace('_bg_color', '');
            jQuery('.sfsi_premium_sticky_icon_preview .sfsi_premium_sticky_icon_' + custom_icon_id + '_container').css('background-color', '#729fcf');
        }

    });
    SFSI('body').on('click', '.sfsi_premium_logo_upload', function (e) {
        e.preventDefault();
        var button = jQuery(this),
            custom_id = jQuery(this).attr('data-custom-index'),
            custom_uploader = wp.media({
                title: 'Insert Icon',
                library: {
                    type: 'image'
                },
                button: {
                    text: 'Insert Icon' // button label text
                },
                multiple: false // for multiple image selection set to true
            }).on('select', function () { // it also has "open" and "close" events
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                button.parent().find('input[type="hidden"]').val(attachment.url);
                jQuery('.sfsi_premium_responsive_icon_custom_' + custom_id + '_container img').attr('src', attachment.url).show();
                jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icon_' + custom_id + '_container img').attr('src', attachment.url).show();

                jQuery('.sfsi_premium_sticky_icon_custom_' + custom_id + '_container img').attr('src', attachment.url).show();
                jQuery('.sfsi_premium_sticky_icon_item_container .sfsi_premium_sticky_icon_' + custom_id + '_container img').attr('src', attachment.url).show();

            })
                .open();
    });
    SFSI('body').on('click', '.sfsi_premium_sticky_custom_logo', function (e) {
        e.preventDefault();
        var button = jQuery(this),
            custom_id = jQuery(this).attr('data-custom-index'),
            custom_uploader = wp.media({
                title: 'Insert Icon',
                library: {
                    type: 'image'
                },
                button: {
                    text: 'Insert Icon' // button label text
                },
                multiple: false // for multiple image selection set to true
            }).on('select', function () { // it also has "open" and "close" events
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                button.parent().find('input[type="hidden"]').val(attachment.url);
                jQuery('.sfsi_premium_sticky_icon_custom_' + custom_id + '_container img').attr('src', attachment.url).show();
                jQuery('.sfsi_premium_sticky_icon_item_container .sfsi_premium_sticky_icon_' + custom_id + '_container img').attr('src', attachment.url).show();

            })
                .open();
    });
});

function upload_image_wechat_scan(e) {
    e.preventDefault();
    var send_attachment_bkp = wp.media.editor.send.attachment;

    var frame = wp.media({
        title: 'Select or Upload image for icon',
        button: {
            text: 'Use this media'
        },
        multiple: false // Set to true to allow multiple files to be selected
    });

    frame.on('select', function () {

        // Get media attachment details from the frame state
        var attachment = frame.state().get('selection').first().toJSON();

        var url = attachment.url.split("/");
        var fileName = url[url.length - 1];
        var fileArr = fileName.split(".");
        var fileType = fileArr[fileArr.length - 1];

        if (fileType != undefined && (fileType == 'jpeg' || fileType == 'jpg' || fileType == 'png' || fileType == 'gif')) {
            jQuery('input[name="sfsi_premium_wechat_scan_image"]').val(attachment.url);
            jQuery('.sfsi_plus_wechat_display>img').attr('src', attachment.url);
            jQuery('.sfsi_plus_wechat_display').removeClass("hide").addClass('show');
            jQuery('.sfsi_plus_wechat_instruction').removeClass("show").addClass('hide');
            wp.media.editor.send.attachment = send_attachment_bkp;
        } else {
            alert("Only Images are allowed to upload");
            frame.open();
        }
    });

    // Finally, open the modal on click
    frame.open();
}

function sfsi_premium_delete_wechat_scan_upload(event, context) {
    event.preventDefault();
    var nonce = SFSI('input[name="sfsi_plus_deleteWebChatFollow_nonce"]').val();
    if (context === "from-server") {
        e = {
            action: "sfsi_plus_deleteWebChatFollow",
            nonce: nonce,
        };
        jQuery.ajax({
            url: sfsi_premium_ajax_object.ajax_url,
            type: "post",
            data: e,
            dataType: "json",
            async: !0,
            success: function (s) {
                if (s.res == 'success') {
                    jQuery('input[name="sfsi_premium_wechat_scan_image"]').val('');
                    jQuery('.sfsi_plus_wechat_display>img').attr('src', '');
                    jQuery('.sfsi_plus_wechat_display').removeClass("show").addClass('hide');
                    jQuery('.sfsi_plus_wechat_instruction').removeClass("hide").addClass('show');
                    // sfsiplus_afterIconSuccess(s);
                } else {
                    SFSI(".upload-overlay").hide("slow");
                    // SFSI(".uperror").html(s.res);
                    sfsiplus_showErrorSuc("Error", "Some Error Occured During Delete of wechat scan", 1)
                }
            }
        });
    } else {
        jQuery('input[name="sfsi_premium_wechat_scan_image"]').val('');
        jQuery('.sfsi_plus_wechat_display>img').attr('src', '');
        jQuery('.sfsi_plus_wechat_display').removeClass("show").addClass('hide');
        jQuery('.sfsi_plus_wechat_instruction').removeClass("hide").addClass('show');
    }
}

function sfsi_premium_responsive_icon_intraction_handler() {
    window.sfsi_premium_fittext_shouldDisplay = true;
    SFSI('select[name="sfsi_premium_responsive_icons_settings_edge_type"]').on('change', function () {
        $target_div = (SFSI(this).parent());
        if (SFSI(this).val() === "Round") {
            $target_div.parent().children().css('display', 'inline-block');
            var radius = jQuery('select[name="sfsi_premium_responsive_icons_settings_edge_radius"]').val() + 'px'
            jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icon_item_container,.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icons_count').css('border-radius', radius);

        } else {
            $target_div.parent().children().hide();
            $target_div.show();
            jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icon_item_container,.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icons_count').css('border-radius', 'unset');

        }
    });
    SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').on('change', function () {
        $target_div = (SFSI(this).parent());
        if (SFSI(this).val() === "Fixed icon width") {
            $target_div.parent().children().css('display', 'block');
            jQuery('.sfsi_premium_responsive_icon_option_li .sfsi_premium_responsive_mobile').css('display', 'block');
            jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container').css('width', 'auto').css('display', 'inline-block');
            jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container a').css('flex-basis', 'unset');
            jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icon_item_container').css('width', jQuery('input[name="sfsi_premium_responsive_icons_sttings_icon_width_size"]').val());

            jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container_box_fully_container').removeClass('sfsi_premium_icons_container_box_fully_container').addClass('sfsi_premium_icons_container_box_fixed_container');
            jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container_box_fixed_container').removeClass('sfsi_premium_icons_container_box_fully_container').addClass('sfsi_premium_icons_container_box_fixed_container');
            window.sfsi_premium_fittext_shouldDisplay = true;
            jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                if (jQuery(a_container).css('display') !== "none") {
                    sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                }
            })
        } else {
            $target_div.parent().children().hide();
            $target_div.show();
            jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container').css('width', '100%').css('display', 'flex');
            jQuery('.sfsi_premium_responsive_icon_option_li .sfsi_premium_responsive_mobile').css('display', 'none');
            jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container a').css('flex-basis', '100%');
            jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icon_item_container').css('width', '100%');

            jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container_box_fixed_container').removeClass('sfsi_premium_icons_container_box_fixed_container').addClass('sfsi_premium_icons_container_box_fully_container');
            jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container_box_fully_container').removeClass('sfsi_premium_icons_container_box_fixed_container').addClass('sfsi_premium_icons_container_box_fully_container');
            window.sfsi_premium_fittext_shouldDisplay = true;
            jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                if (jQuery(a_container).css('display') !== "none") {
                    sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                }
            })
        }
        jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icons_count').removeClass('sfsi_premium_fixed_count_container').removeClass('sfsi_premium_responsive_count_container').addClass('sfsi_premium_' + (jQuery(this).val() == "Fully responsive" ? 'responsive' : 'fixed') + '_count_container')
        jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container>a').removeClass('sfsi_premium_responsive_fluid').removeClass('sfsi_premium_responsive_fixed_width').addClass('sfsi_premium_responsive_' + (jQuery(this).val() == "Fully responsive" ? 'fluid' : 'fixed_width'))
        sfsi_premium_resize_icons_container();

    })
    jQuery(document).on('keyup', 'input[name="sfsi_premium_responsive_icons_sttings_icon_width_size"]', function () {
        if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() === "Fixed icon width") {
            jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icon_item_container').css('width', jQuery(this).val() + 'px');
            window.sfsi_premium_fittext_shouldDisplay = true;
            jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
                if (jQuery(a_container).css('display') !== "none") {
                    sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
                }
            })
        }
        sfsi_premium_resize_icons_container();
    });
    jQuery(document).on('change', 'input[name="sfsi_premium_responsive_icons_sttings_icon_width_size"]', function () {
        if (SFSI('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() === "Fixed icon width") {
            jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icon_item_container').css('width', jQuery(this).val() + 'px');
        }
    });
    jQuery(document).on('keyup', 'input[name="sfsi_premium_responsive_icons_settings_margin"]', function () {
        jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container a,.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icons_count').css('margin-right', jQuery(this).val() + 'px');
    });
    jQuery(document).on('change', 'input[name="sfsi_premium_responsive_icons_settings_margin"]', function () {
        jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container a,.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icons_count').css('margin-right', jQuery(this).val() + 'px');
        // jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container').css('width',(jQuery('.sfsi_premium_responsive_icons').width()-(jQuery('.sfsi_premium_responsive_icons_count').width()+jQuery(this).val()))+'px');

    });
    jQuery(document).on('change', 'select[name="sfsi_premium_responsive_icons_settings_text_align"]', function () {
        if (jQuery(this).val() === "Centered") {
            jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container a').css('text-align', 'center');
        } else {
            jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container a').css('text-align', 'left');
        }
    });
    jQuery('.sfsi_premium_responsive_default_icon_container input.sfsi_premium_responsive_input').on('keyup', function () {
        jQuery(this).parent().find('.sfsi_premium_responsive_icon_item_container').find('span').text(jQuery(this).val());
        var iconName = jQuery(this).attr('name');
        var icon = iconName.replace('sfsi_premium_responsive_', '').replace('_input', '');
        jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icon_' + (icon.toLowerCase()) + '_container span').text(jQuery(this).val());
        window.sfsi_premium_fittext_shouldDisplay = true;
        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
            if (jQuery(a_container).css('display') !== "none") {
                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
            }
        })
        sfsi_premium_resize_icons_container();
    })
    jQuery('.sfsi_premium_responsive_custom_icon_container input.sfsi_premium_responsive_input').on('keyup', function () {
        jQuery(this).parent().find('.sfsi_premium_responsive_icon_item_container').find('span').text(jQuery(this).val());
        var iconName = jQuery(this).attr('name');
        var icon = iconName.replace('sfsi_premium_responsive_custom_', '').replace('_input', '');
        jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icon_' + icon + '_container span').text(jQuery(this).val())
        window.sfsi_premium_fittext_shouldDisplay = true;
        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
            if (jQuery(a_container).css('display') !== "none") {
                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
            }
        })
        sfsi_premium_resize_icons_container();

    })
    jQuery('.sfsi_premium_responsive_custom_url_toggler, .sfsi_premium_responsive_default_url_toggler').click(function (event) {
        event.preventDefault();
        sfsi_premium_responsive_open_url(event);
    });
    jQuery('.sfsi_premium_responsive_custom_url_toggler, .sfsi_premium_responsive_default_url_toggler').click(function (event) {
        event.preventDefault();
        sfsi_premium_responsive_open_url(event);
    })
    jQuery('.sfsi_premium_responsive_custom_url_hide, .sfsi_premium_responsive_default_url_hide').click(function (event) {
        event.preventDefault();
        /* console.log(event,jQuery(event.target)); */
        var _currentParent = jQuery(event.target).parent().parent();
        _currentParent.find('.sfsi_premium_responsive_custom_url_hide').hide();
        _currentParent.find('.sfsi_premium_responsive_url_input').hide();
        _currentParent.find('.sfsi_premium_responsive_default_url_hide').hide();
        _currentParent.find('.sfsi_premium_responsive_default_url_toggler').show();
        _currentParent.find('.sfsi_premium_responsive_custom_url_toggler').show();
    });
    jQuery('select[name="sfsi_premium_responsive_icons_settings_icon_size"]').change(function (event) {
        jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icon_item_container,.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icons_count').removeClass('sfsi_premium_small_button').removeClass('sfsi_premium_medium_button').removeClass('sfsi_premium_large_button').addClass('sfsi_premium_' + (jQuery(this).val().toLowerCase()) + '_button');
        jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container').removeClass('sfsi_premium_small_button_container').removeClass('sfsi_premium_medium_button_container').removeClass('sfsi_premium_large_button_container').addClass('sfsi_premium_' + (jQuery(this).val().toLowerCase()) + '_button_container')
    })
    jQuery(document).on('change', 'select[name="sfsi_premium_responsive_icons_settings_edge_radius"]', function (event) {
        var radius = jQuery(this).val() + 'px'
        jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icon_item_container,.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icons_count').css('border-radius', radius);

    });
    jQuery(document).on('change', 'select[name="sfsi_premium_responsive_icons_settings_style"]', function (event) {
        if ('Flat' === jQuery(this).val()) {
            jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icon_item_container').removeClass('sfsi_premium_responsive_icon_gradient');
        } else {
            jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icon_item_container').addClass('sfsi_premium_responsive_icon_gradient');
        }
    });
    jQuery(document).on('mouseenter', '.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container a', function () {
        jQuery(this).css('opacity', 0.8);
    })
    jQuery(document).on('mouseleave', '.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container a', function () {
        jQuery(this).css('opacity', 1);
    })
    window.sfsi_premium_fittext_shouldDisplay = true;
    jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
        if (jQuery(a_container).css('display') !== "none") {
            sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
        }
    })
    sfsi_premium_resize_icons_container();
    jQuery('.ui-accordion-header.ui-state-default.ui-accordion-icons').click(function (data) {
        window.sfsi_premium_fittext_shouldDisplay = true;
        jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
            if (jQuery(a_container).css('display') !== "none") {
                sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
            }
        })
        sfsi_premium_resize_icons_container();
    });
    jQuery('select[name="sfsi_premium_responsive_icons_settings_text_align"]').change(function (event) {
        var data = jQuery(event.target).val();
        if (data == "Centered") {
            jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icon_item_container').removeClass('sfsi_premium_left-align_icon').addClass('sfsi_premium_centered_icon');
        } else {
            jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icon_item_container').removeClass('sfsi_premium_centered_icon').addClass('sfsi_premium_left-align_icon');
        }
    });
    jQuery('a.sfsi_premium_responsive_custom_delete_btn').click(function (event) {
        event.preventDefault();
        var icon_num = jQuery(this).attr('data-id');
        //reset the current block;
        // var last_block = jQuery('.sfsi_premium_responsive_custom_icon_4_container').clone();
        var cur_block = jQuery('.sfsi_premium_responsive_custom_icon_' + icon_num + '_container');
        cur_block.find('.sfsi_premium_responsive_custom_delete_btn').hide();
        cur_block.find('input[name="sfsi_premium_responsive_custom_' + icon_num + '_added"]').val('no');
        cur_block.find('.sfsi_premium_responsive_custom_' + icon_num + '_added').attr('value', 'no');
        cur_block.find('.radio_section.tb_4_ck .checkbox').click();
        cur_block.hide();


        if (icon_num > 0) {
            var prev_block = jQuery('.sfsi_premium_responsive_custom_icon_' + (icon_num - 1) + '_container');
            prev_block.find('.sfsi_premium_responsive_custom_delete_btn').show();
        }
        if (icon_num == 4) {
            jQuery(this).parents('ul').find('.sfsi_premium_responsive_custom_icon_button_container').show();
        }

        // jQuery('.sfsi_premium_responsive_custom_icon_container').each(function(index,custom_icon){
        // 	var target= jQuery(custom_icon);
        // 	target.find('.sfsi_premium_responsive_custom_delete_btn');
        // 	var custom_id = target.find('.sfsi_premium_responsive_custom_delete_btn').attr('data-id');
        // 	if(custom_id>icon_num){
        // 		target.removeClass('sfsi_premium_responsive_custom_icon_'+custom_id+'_container').addClass('sfsi_premium_responsive_custom_icon_'+(custom_id-1)+'_container');
        // 		target.find('input[name="sfsi_premium_responsive_custom_'+custom_id+'_added"]').attr('name',"sfsi_premium_responsive_custom_"+(custom_id-1)+"_added");
        // 		target.find('#sfsi_premium_responsive_'+custom_id+'_display').removeClass('sfsi_premium_responsive_custom_'+custom_id+'_display').addClass('sfsi_premium_responsive_custom_'+(custom_id-1)+'_display').attr('id','sfsi_premium_responsive_'+(custom_id-1)+'_display').attr('name','sfsi_premium_responsive_custom_'+(custom_id-1)+'_display').attr('data-custom-index',(custom_id-1));
        // 		target.find('.sfsi_premium_responsive_icon_item_container').removeClass('sfsi_premium_responsive_icon_custom_'+custom_id+'_container').addClass('sfsi_premium_responsive_icon_custom_'+(custom_id-1)+'_container');
        // 		target.find('.sfsi_premium_responsive_input').attr('name','sfsi_premium_responsive_custom_'+(custom_id-1)+'_input');
        // 		target.find('.sfsi_premium_responsive_url_input').attr('name','sfsi_premium_responsive_custom_'+(custom_id-1)+'_url_input');
        // 		target.find('.sfsi_premium_bg-color-picker').attr('name','sfsi_plus_responsive_icon_'+(custom_id-1)+'_bg_color');
        // 		target.find('.sfsi_premium_logo_upload sfsi_premium_logo_custom_'+custom_id+'_upload').removeClass('sfsi_premium_logo_upload sfsi_premium_logo_custom_'+custom_id+'_upload').addClass('sfsi_premium_logo_upload sfsi_premium_logo_custom_'+(custom_id-1)+'_upload');
        // 		target.find('input[type="sfsi_premium_responsive_icons_custom_'+custom_id+'_icon"]').attr('name','input[type="sfsi_premium_responsive_icons_custom_'+(custom_id-1)+'_icon"]');
        // 		target.find('.sfsi_premium_responsive_custom_delete_btn').attr('data-id',''+(custom_id-1));
        // 	}
        // });
        // // sfsi_premium_backend_section_beforeafter_set_fixed_width();
        //    // jQuery(window).on('resize',sfsi_premium_backend_section_beforeafter_set_fixed_width);
        // var new_block=jQuery('.sfsi_premium_responsive_custom_icon_container').clone();
        // jQuery('.sfsi_premium_responsive_custom_icon_container').remove();
        // jQuery('.sfsi_premium_responsive_default_icon_container').parent().append(last_block).append();
        // jQuery('.sfsi_premium_responsive_default_icon_container').parent().append(new_block);
        // return false;
    })
}

function sfsi_premium_responsive_icon_counter_tgl(hide, show, ref = null) {
    if (null !== hide && '' !== hide) {
        jQuery('.' + hide).hide();
    }
    if (null !== show && '' !== show) {
        jQuery('.' + show).show();
    }
}

function sfsi_premium_responsive_toggle_count() {
    var data = jQuery('input[name="sfsi_plus_responsive_icon_show_count"]:checked').val();
    var data2 = jQuery('input[name="sfsi_plus_display_counts"]:checked').val();
    /* console.log('toggleer ',data,data2); */
    if (data2 == "yes" && 'yes' == data) {
        jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icons_count').css('display', 'inline-block');
        jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container').removeClass('sfsi_premium_responsive_without_counter_icons').addClass('sfsi_premium_responsive_with_counter_icons');
        sfsi_premium_resize_icons_container();
    } else {
        jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icons_count').css('display', 'none');
        jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container').removeClass('sfsi_premium_responsive_with_counter_icons').addClass('sfsi_premium_responsive_without_counter_icons');
        sfsi_premium_resize_icons_container();
    }
}

function sfsi_premium_responsive_toggle_count() {
    var data = jQuery('input[name="sfsi_plus_sticky_bar_counts"]:checked').val();
    var data2 = jQuery('input[name="sfsi_plus_display_counts"]:checked').val();
    /* console.log('toggleer ',data,data2); */
    if (data2 == "yes" && 'yes' == data) {
        jQuery('.sfsi_premium_sticky_bar_preview .sfsi_premium_responsive_icons_count').css('display', 'inline-block');
        jQuery('.sfsi_premium_sticky_bar_preview .sfsi_premium_icons_container').removeClass('sfsi_premium_responsive_without_counter_icons').addClass('sfsi_premium_responsive_with_counter_icons');
        sfsi_premium_resize_icons_container();
    } else {
        jQuery('.sfsi_premium_sticky_bar_preview .sfsi_premium_responsive_icons_count').css('display', 'none');
        jQuery('.sfsi_premium_sticky_bar_preview .sfsi_premium_icons_container').removeClass('sfsi_premium_responsive_with_counter_icons').addClass('sfsi_premium_responsive_without_counter_icons');
        sfsi_premium_resize_icons_container();
    }
}

function sfsi_premium_responsive_open_url(event) {
    var _current = jQuery(event.target);
    var _currentParent = _current.parent();
    _currentParent.find('.sfsi_premium_responsive_custom_url_hide').show();
    _currentParent.find('.sfsi_premium_responsive_default_url_hide').show();
    _currentParent.find('.sfsi_premium_responsive_url_input').show();
    _current.hide();
}

function sfsi_premium_responsive_icon_hide_responsive_options() {
    jQuery('.sfsiplus_PostsSettings_section').show();
    jQuery('.sfsi_plus_choose_post_types_section').show();
    jQuery('.sfsi_plus_choose_post_types_section_for_responsive').hide();
    jQuery('.sfsi_premium_not_responsive').show();
    // jQuery('.sfsiplus_icn_listing8.sfsi_plus_closerli').hide();
}

function sfsi_premium_responsive_icon_show_responsive_options() {
    jQuery('.sfsiplus_PostsSettings_section').hide();
    // jQuery('.sfsiplus_PostsSettings_section').show();
    jQuery('.sfsi_plus_choose_post_types_section').show();
    jQuery('.sfsi_plus_choose_post_types_section_for_responsive').show();
    jQuery('.sfsi_premium_not_responsive').hide();
    window.sfsi_premium_fittext_shouldDisplay = true;
    jQuery('.sfsi_premium_responsive_icon_preview a').each(function (index, a_container) {
        if (jQuery(a_container).css('display') !== "none") {
            sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
        }
    })
    sfsi_premium_resize_icons_container();
}

function sfsi_premium_scroll_to_div(option_id, scroll_selector) {
    jQuery('#' + option_id + '.ui-accordion-header[aria-selected="false"]').click() //opened the option
    //scroll to it.
    if (scroll_selector && scroll_selector !== '') {
        scroll_selector = scroll_selector;
    } else {
        scroll_selector = '#' + option_id + '.ui-accordion-header';
    }
    jQuery('html, body').stop().animate({
        scrollTop: jQuery(scroll_selector).offset().top
    }, 1000);
}

function sfsi_premium_fitText(container) {
    /* 	console.log(container,container.parent().parent(),container.parent().parent().hasClass('sfsi_premium_icons_container_box_fixed_container')); */
    if (container.parent().parent().hasClass('sfsi_premium_icons_container_box_fixed_container')) {
        /* console.log(window.sfsi_premium_fittext_shouldDisplay); */
        if (window.sfsi_premium_fittext_shouldDisplay === true) {
            if (jQuery('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() == "Fully responsive") {
                var all_icon_width = jQuery('.sfsi_premium_responsive_icons .sfsi_premium_icons_container').width();
                var total_active_icons = jQuery('.sfsi_premium_responsive_icons .sfsi_premium_icons_container a').filter(function (i, icon) {
                    return jQuery(icon).css('display') && (jQuery(icon).css('display').toLowerCase() !== "none");
                }).length;

                var distance_between_icon = jQuery('input[name="sfsi_premium_responsive_icons_settings_margin"]').val()
                var container_width = ((all_icon_width - distance_between_icon) / total_active_icons) - 5;
                container_width = (container_width - distance_between_icon);
            } else {
                var container_width = container.width();
            }
            // var container_img_width = container.find('img').width();
            var container_img_width = 70;
            // var span=container.find('span').clone();
            var span = container.find('span');
            // var span_original_width = container.find('span').width();
            var span_original_width = container_width - (container_img_width)
            span
                // .css('display','inline-block')
                .css('white-space', 'nowrap')
            // .css('width','auto')
            ;
            var span_flatted_width = span.width();
            if (span_flatted_width == 0) {
                span_flatted_width = span_original_width;
            }
            span
                // .css('display','inline-block')
                .css('white-space', 'unset')
            // .css('width','auto')
            ;
            var shouldDisplay = ((undefined === window.sfsi_premium_fittext_shouldDisplay) ? true : window.sfsi_premium_fittext_shouldDisplay = true);
            var fontSize = parseInt(span.css('font-size'));

            if (6 > fontSize) {
                fontSize = 20;
            }

            var computed_fontSize = (Math.floor((fontSize * span_original_width) / span_flatted_width));

            if (computed_fontSize < 8) {
                shouldDisplay = false;
                window.sfsi_premium_fittext_shouldDisplay = false;
                computed_fontSize = 20;
            }
            span.css('font-size', Math.min(computed_fontSize, 20));
            span
                // .css('display','inline-block')
                .css('white-space', 'nowrap')
            // .css('width','auto')
            ;
            if (shouldDisplay) {
                span.show();
            } else {
                span.hide();
                jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icon_item_container  span').hide();
            }
        }
    } else {
        var span = container.find('span');
        /* 	console.log(span); */
        span.css('font-size', 'initial');
        span.show();
    }

}

function sfsi_premium_fixedWidth_fitText(container) {
    return;
    /* console.log(sfsi_premium_fittext_shouldDisplay); */
    if (window.sfsi_premium_fittext_shouldDisplay === true) {
        if (jQuery('select[name="sfsi_premium_responsive_icons_settings_icon_width_type"]').val() == "Fixed icon width") {
            var all_icon_width = jQuery('.sfsi_premium_responsive_icons .sfsi_premium_icons_container').width();
            var total_active_icons = jQuery('.sfsi_premium_responsive_icons .sfsi_premium_icons_container a').filter(function (i, icon) {
                return jQuery(icon).css('display') && (jQuery(icon).css('display').toLowerCase() !== "none");
            }).length;
            var distance_between_icon = jQuery('input[name="sfsi_premium_responsive_icons_settings_margin"]').val()
            var container_width = ((all_icon_width - distance_between_icon) / total_active_icons) - 5;
            container_width = (container_width - distance_between_icon);
        } else {
            var container_width = container.width();
        }
        // var container_img_width = container.find('img').width();
        var container_img_width = 70;
        // var span=container.find('span').clone();
        var span = container.find('span');
        // var span_original_width = container.find('span').width();
        var span_original_width = container_width - (container_img_width)
        span
            // .css('display','inline-block')
            .css('white-space', 'nowrap')
        // .css('width','auto')
        ;
        var span_flatted_width = span.width();
        if (span_flatted_width == 0) {
            span_flatted_width = span_original_width;
        }
        span
            // .css('display','inline-block')
            .css('white-space', 'unset')
        // .css('width','auto')
        ;
        var shouldDisplay = undefined === window.sfsi_premium_fittext_shouldDisplay ? true : window.sfsi_premium_fittext_shouldDisplay = true;
        ;
        var fontSize = parseInt(span.css('font-size'));

        if (6 > fontSize) {
            fontSize = 15;
        }

        var computed_fontSize = (Math.floor((fontSize * span_original_width) / span_flatted_width));

        if (computed_fontSize < 8) {
            shouldDisplay = false;
            window.sfsi_premium_fittext_shouldDisplay = false;
            computed_fontSize = 15;
        }
        span.css('font-size', Math.min(computed_fontSize, 15));
        span
            // .css('display','inline-block')
            .css('white-space', 'nowrap')
        // .css('width','auto')
        ;
        // var heightOfResIcons = jQuery('.sfsi_premium_responsive_icon_item_container').height();

        //   if(heightOfResIcons < 17){
        //     span.show();
        //   }else{
        //     span.hide();
        //   }

        if (shouldDisplay) {
            span.show();
        } else {
            span.hide();
        }
    }
}

function sfsi_premium_resize_icons_container() {
    // resize icon container based on the size of count
    sfsi_premium_cloned_icon_list = jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container').clone();
    sfsi_premium_cloned_icon_list.removeClass('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_with_counter_icons').addClass('sfsi_premium_responsive_cloned_list');
    sfsi_premium_cloned_icon_list.css('width', '100%');
    jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container').parent().append(sfsi_premium_cloned_icon_list);

    // sfsi_premium_cloned_icon_list.css({
    //       position: "absolute",
    //       left: "-10000px"
    //   }).appendTo("body");
    actual_width = sfsi_premium_cloned_icon_list.width();
    count_width = jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_responsive_icons_count').width();
    jQuery('.sfsi_premium_responsive_cloned_list').remove();
    sfsi_premium_inline_style = jQuery('.sfsi_premium_responsive_icon_preview').attr('style');
    // remove_width
    sfsi_premium_inline_style = `${sfsi_premium_inline_style}`.replace(/width:auto($|!important|)(;|$)/g, '').replace(/width:\s*(-|)\d*\s*(px|%)\s*($|!important|)(;|$)/g, '');
    if (!(jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container').hasClass('sfsi_premium_responsive_without_counter_icons') && jQuery('.sfsi_premium_icons_container').hasClass('sfsi_premium_icons_container_box_fixed_container'))) {
        sfsi_premium_inline_style += "width:" + (actual_width - count_width - 1) + 'px!important;'
    } else {
        sfsi_premium_inline_style += "width:auto!important;";
    }
    jQuery('.sfsi_premium_responsive_icon_preview .sfsi_premium_icons_container').attr('style', sfsi_premium_inline_style);


}

function sfsi_premium_save_export() {
    var nonce = SFSI("#sfsi_premium_save_export").attr("data-nonce");
    var data = {
        action: "save_export",
        nonce: nonce
    };
    SFSI.ajax({
        url: sfsi_premium_ajax_object.ajax_url,
        type: "post",
        data: data,
        success: function (s) {
            /*console.log(s);*/
            if (s == "wrong_nonce") {
                sfsiplus_showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 6);
                global_error = 1;
            } else {
                var date = new Date();
                var timestamp = date.getTime();
                var blob = new Blob([JSON.stringify(s, null, 2)], {
                    type: 'application/json'
                });
                var url = URL.createObjectURL(blob);
                let link = document.createElement("a");
                link.href = url;
                link.download = "sfsi_premium_export_options" + timestamp + ".json"
                link.innerText = "Open the array URL";
                document.body.appendChild(link);
                link.click();
                (sfsiplus_showErrorSuc("success", "Settings Exported !", 10));
            }
        }
    });

}

function sfsi_premium_recheck_license() {
    var nonce = SFSI("#sfsi_premium_recheck_license").attr("data-nonce");
    var data = {
        action: "recheck_license",
        nonce: nonce
    };
    SFSI.ajax({
        url: sfsi_premium_ajax_object.ajax_url,
        type: "post",
        data: data,
        success: function (s) {
            var res = JSON.parse(s);
            /*console.log(res, res.status);*/
            if (res.status) {
                SFSI("#sfsi_premium_recheck_license").parents('.error').hide();
                window.location.reload();
            } else {
                alert('Seems like your license key expired, you need to renew it or get new one.\n\nPlease follow the incructions on the notice to renew.\nThank you! :)');
            }

        }
    });
}

function importFileOpen() {
    /*console.log('file open');*/
    SFSI('.open_file').click();
    init();
}

function init() {
    document.getElementById('sfsi_premium_file_input').addEventListener('change', handleFileSelect, false);
}

function handleFileSelect(event) {
    const reader = new FileReader()
    reader.onload = handleFileLoad;
    reader.readAsText(event.target.files[0])
    console.log(reader);
}

/*function handleFileLoad(event) {
	var imports = JSON.parse(event.target.result);
	for (var key in imports) {
		if (imports['sfsi_plus_installDate']) {
			if (imports['option1'] && imports['option2'] ) {
				var options = imports[key];
				if(imports.hasOwnProperty('sfsi_plus_footer_sec')){
					var optionKeys = key.split('_');
					if(optionKeys[1] == 'plus'){
							optionKeys[1] = 'premium';
							optionKeys.join("_");

					}
				}
				for (var key in options) {
					var splitKeys = key.split('_');
					if (splitKeys[1] == 'plus') {
						console.log(`${splitKeys[1]}`,key);
					}
				}
			}
		}

	};*/

function handleFileLoad(event) {
    var imports = JSON.parse(event.target.result);
    var import_type;
    for (var key in imports) {
        if (imports['sfsi_plus_installDate'] || imports['sfsi_plus_pluginVersion']) {
            import_type = "plus";
        } else if (imports['sfsi_premium_installDate']) {
            import_type = "premium";
        } else if (imports['sfsi_pluginVersion']) {
            import_type = "old";
        } else {
            import_type = "cannot determine";
        }
    }
    ;
    console.log(import_type);
    var nonce = SFSI("#sfsi_premium_file_input").attr("data-nonce");
    var data = {
        action: "file_input",
        imports,
        import_type: import_type,
        nonce: nonce
    };
    /*console.log(data);*/
    SFSI.ajax({
        url: sfsi_premium_ajax_object.ajax_url,
        type: "post",
        data: data,
        dataType: "json",
        async: !0,
        success: function (s) {
            /*console.log(s,'lsdfkmvm');*/

            if (s == "wrong_nonce") {
                sfsiplus_showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 9);
                sfsiplus_afterLoad();
            } else {
                /*console.log( s);*/
                "success" == s ? (sfsiplus_showErrorSuc("success", "Settings Imported !", 10), sfsipluscollapse("#sfsi_premium_file_input"),
                    sfsi_plus_depened_sections(true)) : (global_error = 1, sfsiplus_showErrorSuc("error", "Unkown error , please try again", 2),
                    return_value = !1);
                window.location.reload();
            }
        }
    });
}

function sfsi_premium_pinterest_custom_icon(e) {
    console.log("sfsi_premium_pinterest_custom_icon", e);
    var curval = jQuery(e.target).val();
    console.log("curval", curval);
    if (curval == "custom") {
        e.preventDefault();
        var send_attachment_bkp = wp.media.editor.send.attachment;

        var frame = wp.media({
            title: 'Custom Pinterest Icon',
            button: {
                text: 'Upload'
            },
            multiple: false // Set to true to allow multiple files to be selected
        });

        frame.on('select', function () {
            // Get media attachment details from the frame state
            var attachment = frame.state().get('selection').first().toJSON();
            var url = attachment.url.split("/");
            var fileName = url[url.length - 1];
            var fileArr = fileName.split(".");
            var fileType = fileArr[fileArr.length - 1];
            console.log(attachment.url);

            if (fileType != undefined && (fileType == 'gif' || fileType == 'jpeg' || fileType == 'jpg' || fileType == 'png')) {
                window.pinterest_custom_type = curval;
                console.log("frame select", curval, window.pinterest_custom_type);
                jQuery('.sfsi-premium-pinterest-custom').show();
                jQuery('.sfsi-premium-pinterest-custom img').attr("src", attachment.url);
                jQuery('.sfsi-premium-pinterest-custom input').val(attachment.url);
                wp.media.editor.send.attachment = send_attachment_bkp;
            } else {
                alert("Please Select an image file.");
                frame.open();
            }
        }.bind(e));

        // Finally, open the modal on click
        frame.open();
        frame.on("close", function () {
            setTimeout((data) => {
                /*console.log("frame closed",data,curval,window.pinterest_custom_type);*/
                if (curval !== window.pinterest_custom_type) {
                    jQuery(e.target).val(window.pinterest_custom_type);
                }
            }, 500);
        });
    } else {
        jQuery('.sfsi-premium-pinterest-custom').hide();
        window.pinterest_custom_type = curval;
    }
}

sfsi_premium_sticky_bar_define_url();

function sfsi_premium_sticky_bar_define_url() {
    jQuery('.sfsi_premium_sticky_default_icon_container .sfsi_premium_sticky_default_url_toggler').click(function (event) {
        event.preventDefault();
        sfsi_premium_sticky_open_url(event)
    });
    jQuery('.sfsi_premium_sticky_default_icon_container .sfsi_premium_sticky_default_url_hide').click(function (event) {
        event.preventDefault();
        // sfsi_premium_sticky_open_url(event);
        var _currentParent = jQuery(event.target).parent().parent();
        _currentParent.find('.sfsi_premium_sticky_custom_url_hide').hide();
        _currentParent.find('.sfsi_premium_sticky_url_input').hide();
        _currentParent.find('.sfsi_premium_sticky_default_url_hide').hide();
        _currentParent.find('.sfsi_premium_sticky_url_input').val("");
        _currentParent.find('.sfsi_premium_sticky_default_url_toggler').show();
        _currentParent.find('.sfsi_premium_sticky_custom_url_toggler').show();
    });

}

function sfsi_premium_sticky_open_url(event) {
    var _current = jQuery(event.target);
    var _currentParent = _current.parent();
    _currentParent.find('.sfsi_premium_sticky_custom_url_hide').show();
    _currentParent.find('.sfsi_premium_sticky_default_url_hide').show();
    _currentParent.find('.sfsi_premium_sticky_url_input').show();
    _current.hide();
}

jQuery('a.sfsi_premium_sticky_custom_delete_btn').click(function (event) {
    event.preventDefault();
    var icon_num = jQuery(this).attr('data-id');
    console.log(event, icon_num);

    //reset the current block;
    // var last_block = jQuery('.sfsi_premium_responsive_custom_icon_4_container').clone();
    var cur_block = jQuery('.sfsi_premium_sticky_custom_icon_' + icon_num + '_container');
    cur_block.find('.sfsi_premium_sticky_custom_delete_btn').hide();
    cur_block.find('input[name="sfsi_premium_sticky_custom_' + icon_num + '_added"]').val('no');
    cur_block.find('.sfsi_premium_sticky_custom_' + icon_num + '_added').attr('value', 'no');
    cur_block.find('.radio_section.tb_4_ck .checkbox').click();
    cur_block.hide();

    if (icon_num <= 4) {
        SFSI('.sfsi_premium_sticky_custom_check_option').show();
    }

    if (icon_num > 0) {
        var prev_block = jQuery('.sfsi_premium_sticky_custom_icon_' + (icon_num - 1) + '_container');
        prev_block.find('.sfsi_premium_sticky_custom_delete_btn').show();
    }

});

var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
var eventer = window[eventMethod];
var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";
eventer(messageEvent, function (e) {
    console.log("received from popup", e);

}, false);

function sfsi_premium_open_bitly_newwindow(e) {
    /*console.log(location);*/
    var curtarget = jQuery(e.target).attr('target');
    if (curtarget !== 'bitly') {
        e.preventDefault();
        var location = jQuery(e.target).attr("href");
        window.bitlyauth = window.open(location, 'bitly');
        jQuery(e.target).attr('target', 'bitly');
        jQuery(e.target).click();
        var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
        var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";
        window.bitlyauth.addEventListener(eventMethod, function (e) {
            console.log(e);
        });
        console.log(window.bitlyauth);
        window.bitlyauth.addEventListener("close", function (e) {
            console.log(e);
        })
    }
}
