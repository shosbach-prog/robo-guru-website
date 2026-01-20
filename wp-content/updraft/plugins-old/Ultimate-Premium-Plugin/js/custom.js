if ('undefined' !== typeof jQuery && null != jQuery) {

  jQuery.fn.extend({

    sfsipluscss: function (styleName, value) {
      this[0].style.setProperty(styleName, value, 'important');
    },

    sfsi_plus_align_icons_center_orientation: function (_centerPosition) {

      function applyOrientation() {

        var elemF = jQuery('#sfsi_plus_floater');

        if (elemF.length > 0) {

          switch (_centerPosition) {
            case 'center-right':
            case 'center-left':
              var toptalign = (jQuery(window).height() - elemF.height()) / 2;
              elemF.css('top', toptalign);
              break;

            case 'center-top':
            case 'center-bottom':
              var leftalign = (jQuery(window).width() - elemF.width()) / 2;
              elemF.css('left', leftalign);
              break;
          }
        }
      }

      var prev_onresize = window.onresize;
      window.onresize = function (event) {

        if ('function' === typeof prev_onload) {
          prev_onresize(), applyOrientation();
        } else {
          applyOrientation();
        }
      }
    }
  });

  jQuery("#sfsi_plus_wDivothrWid").find("p:empty").remove();

  /*! flip - v1.1.2 - 2016-10-20
   * https://github.com/nnattawat/flip
   * Copyright (c) 2016 Nattawat Nonsung; Licensed MIT */
  ! function (a) {
    var b = function () {
        var a, b = document.createElement("fakeelement"),
          c = {
            transition: "transitionend",
            OTransition: "oTransitionEnd",
            MozTransition: "transitionend",
            WebkitTransition: "webkitTransitionEnd"
          };
        for (a in c)
          if (void 0 !== b.style[a]) return c[a]
      },
      c = function (b, c, d) {
        this.setting = {
          axis: "y",
          reverse: !1,
          trigger: "click",
          speed: 500,
          forceHeight: !1,
          forceWidth: !1,
          autoSize: !0,
          front: ".front",
          back: ".back"
        }, this.setting = a.extend(this.setting, c), "string" != typeof c.axis || "x" !== c.axis.toLowerCase() && "y" !== c.axis.toLowerCase() || (this.setting.axis = c.axis.toLowerCase()), "boolean" == typeof c.reverse && (this.setting.reverse = c.reverse), "string" == typeof c.trigger && (this.setting.trigger = c.trigger.toLowerCase());
        var e = parseInt(c.speed);
        isNaN(e) || (this.setting.speed = e), "boolean" == typeof c.forceHeight && (this.setting.forceHeight = c.forceHeight), "boolean" == typeof c.forceWidth && (this.setting.forceWidth = c.forceWidth), "boolean" == typeof c.autoSize && (this.setting.autoSize = c.autoSize), ("string" == typeof c.front || c.front instanceof a) && (this.setting.front = c.front), ("string" == typeof c.back || c.back instanceof a) && (this.setting.back = c.back), this.element = b, this.frontElement = this.getFrontElement(), this.backElement = this.getBackElement(), this.isFlipped = !1, this.init(d)
      };
    a.extend(c.prototype, {
      flipDone: function (a) {
        var c = this;
        c.element.one(b(), function () {
          c.element.trigger("flip:done"), "function" == typeof a && a.call(c.element)
        })
      },
      flip: function (a) {
        if (!this.isFlipped) {
          this.isFlipped = !0;
          var b = "rotate" + this.setting.axis;
          this.frontElement.css({
            transform: b + (this.setting.reverse ? "(-180deg)" : "(180deg)"),
            "z-index": "0"
          }), this.backElement.css({
            transform: b + "(0deg)",
            "z-index": "1"
          }), this.flipDone(a)
        }
      },
      unflip: function (a) {
        if (this.isFlipped) {
          this.isFlipped = !1;
          var b = "rotate" + this.setting.axis;
          this.frontElement.css({
            transform: b + "(0deg)",
            "z-index": "1"
          }), this.backElement.css({
            transform: b + (this.setting.reverse ? "(180deg)" : "(-180deg)"),
            "z-index": "0"
          }), this.flipDone(a)
        }
      },
      getFrontElement: function () {
        return this.setting.front instanceof a ? this.setting.front : this.element.find(this.setting.front)
      },
      getBackElement: function () {
        return this.setting.back instanceof a ? this.setting.back : this.element.find(this.setting.back)
      },
      init: function (a) {
        var b = this,
          c = b.frontElement.add(b.backElement),
          d = "rotate" + b.setting.axis,
          e = 2 * b.element["outer" + ("rotatex" === d ? "Height" : "Width")](),
          f = {
            perspective: e,
            position: "relative"
          },
          g = {
            transform: d + "(" + (b.setting.reverse ? "180deg" : "-180deg") + ")",
            "z-index": "0",
            position: "relative"
          },
          h = {
            "backface-visibility": "hidden",
            "transform-style": "preserve-3d",
            position: "absolute",
            "z-index": "1",
            "opacity": "1"
          };
        b.setting.forceHeight ? c.outerHeight(b.element.height()) : b.setting.autoSize && (h.height = "100%"), b.setting.forceWidth ? c.outerWidth(b.element.width()) : b.setting.autoSize && (h.width = "100%"), (window.chrome || window.Intl && Intl.v8BreakIterator) && "CSS" in window && (f["-webkit-transform-style"] = "preserve-3d"), c.css(h).find("*").css({
          "backface-visibility": "hidden"
        }), b.element.css(f), b.backElement.css(g), setTimeout(function () {
          var d = b.setting.speed / 1e3 || .5;
          c.css({
            transition: "all " + d + "s ease-out"
          }), "function" == typeof a && a.call(b.element)
        }, 20), b.attachEvents()
      },
      clickHandler: function (b) {
        b || (b = window.event), this.element.find(a(b.target).closest('button, a, input[type="submit"]')).length || (this.isFlipped ? this.unflip() : this.flip())
      },
      hoverHandler: function () {
        var b = this;
        b.element.off("mouseleave.flip"), b.flip(), setTimeout(function () {
          b.element.on("mouseleave.flip", a.proxy(b.unflip, b)), b.element.is(":hover") || b.unflip()
        }, b.setting.speed + 150)
      },
      attachEvents: function () {
        var b = this;
        "click" === b.setting.trigger ? b.element.on(a.fn.tap ? "tap.flip" : "click.flip", a.proxy(b.clickHandler, b)) : "hover" === b.setting.trigger && (b.element.on("mouseenter.flip", a.proxy(b.hoverHandler, b)), b.element.on("mouseleave.flip", a.proxy(b.unflip, b)))
      },
      flipChanged: function (a) {
        this.element.trigger("flip:change"), "function" == typeof a && a.call(this.element)
      },
      changeSettings: function (a, b) {
        var c = this,
          d = !1;
        if (void 0 !== a.axis && c.setting.axis !== a.axis.toLowerCase() && (c.setting.axis = a.axis.toLowerCase(), d = !0), void 0 !== a.reverse && c.setting.reverse !== a.reverse && (c.setting.reverse = a.reverse, d = !0), d) {
          var e = c.frontElement.add(c.backElement),
            f = e.css(["transition-property", "transition-timing-function", "transition-duration", "transition-delay"]);
          e.css({
            transition: "none"
          });
          var g = "rotate" + c.setting.axis;
          c.isFlipped ? c.frontElement.css({
            transform: g + (c.setting.reverse ? "(-180deg)" : "(180deg)"),
            "z-index": "0"
          }) : c.backElement.css({
            transform: g + (c.setting.reverse ? "(180deg)" : "(-180deg)"),
            "z-index": "0"
          }), setTimeout(function () {
            e.css(f), c.flipChanged(b)
          }, 0)
        } else c.flipChanged(b)
      }
    }), a.fn.flip = function (b, d) {
      return "function" == typeof b && (d = b), "string" == typeof b || "boolean" == typeof b ? this.each(function () {
        var c = a(this).data("flip-model");
        "toggle" === b && (b = !c.isFlipped), b ? c.flip(d) : c.unflip(d)
      }) : this.each(function () {
        if (a(this).data("flip-model")) {
          var e = a(this).data("flip-model");
          !b || void 0 === b.axis && void 0 === b.reverse || e.changeSettings(b, d)
        } else a(this).data("flip-model", new c(a(this), b || {}, d))
      }), this
    }
  }(jQuery);

  var SFSI = jQuery;
}
jQuery(document).ready(function (e) {

  jQuery("#sfsi_plus_floater").attr("data-top", jQuery(document).height());
  src = [];
  var imgSrc = [];
  SFSI('body img').each(function (index) {
    var src = SFSI(this).attr('src') || "";
    if ( src && src.substring(0, 5) == "data:" ) {
      if( SFSI( this ).is( '[data-src]' ) ) {
        src = SFSI(this).attr( 'data-src' );
      } else {
        srcset = SFSI(this).attr('srcset');
        if ( srcset ) {
          if (src.indexOf(' ') !== false) {
            src = srcset.substring(0, src.indexOf(' '));
          } else {
            src = srcset;
          }
        }
      }
    }
    var height = SFSI(this).height();
    var width = SFSI(this).width();
    var image_title = SFSI(this).attr('title') || "";
    var alt = SFSI(this).attr('alt') || "";
    var no_pin = SFSI(this).attr('data-pin-nopin') || "";
    var no_pin_old = SFSI(this).attr('nopin') || "";
    var title = '';

    if (src !== "" && !src.startsWith("javascript") && height > 100 && width > 100 && no_pin_old !== "nopin" && no_pin !== "true") {
      imgSrc.push({
        src: src,
        title: title && "" !== title ? title : (image_title && "" !== image_title ? image_title : alt)
      });
    }
  });

  if(imgSrc.length === 0)
    // SFSI('.sfsi_premium_pinterest_create').hide();

  /*Whatsapp sharing*/
  jQuery('.clWhatsapp').each(function () {

    /*Get title to be shared*/
    var title = encodeURIComponent(jQuery(this).attr('data-text'));

    /*Get link to be shared*/
    var link = encodeURIComponent(jQuery(this).attr('data-url'));

    /*Get custom whatsappmessage to be shared entered by user*/
    var customtxt = jQuery(this).attr('data-customtxt');

    var customtxt = customtxt.replace("${title}", title);
    var customtxt = customtxt.replace("${link}", link);
    var customtxt = customtxt.replace(/['"]+/g, ''); /*Remove single & double quotes*/

    var whats_app_message = title + " - " + link;
    var whatsapp_url = "https://api.whatsapp.com/send?text=" + customtxt;

    jQuery(this).attr('href', whatsapp_url);
  });
  jQuery('.sf_pinit>a').each(function (index, ref) {
    var href = ref.href;
    var data_pin_href = ref['data-pin-href'];
    if (href) {
      query_vars = sfsi_premium_getUrlVars(href);
      var media_encoded = query_vars['media'];
      media = decodeURI(media_encoded);
      if (media.startsWith('/') && !(media.startsWith('//'))) {
        var current_domain = window.location.protocol + '//' + window.location.hostname;
        if (window.location.port && window.location.port !== "") {
          current_domain = current_domain + ':' + window.location.port;
        }
        href = href.slice(0, href.indexOf('?')) + '?url=' + current_domain + query_vars['url'] + '&media=' + current_domain + media + '&description=' + decodeURI(query_vars['description']);
        /*href.replace(media_encoded,current_domain+media_encoded);*/
        ref.href = href;
      }
    }
  })

});

function sfsi_premium_getUrlVars(url) {
  var vars = [],
    hash;
  var hashes = url.slice(url.indexOf('?') + 1).split('&');
  for (var i = 0; i < hashes.length; i++) {
    hash = hashes[i].split('=');
    vars.push(hash[0]);
    vars[hash[0]] = hash[1];
  }
  return vars;
}

function sfsiplus_showErrorSuc(s, i, e) {
  if ("error" == s) var t = "errorMsg";
  else var t = "sucMsg";
  return SFSI(".tab" + e + ">." + t).html(i), SFSI(".tab" + e + ">." + t).show(), SFSI(".tab" + e + ">." + t).effect("highlight", {}, 5e3), setTimeout(function () {
    SFSI("." + t).slideUp("slow")
  }, 5e3), !1
}

function sfsiplus_beForeLoad() {
  SFSI(".loader-img").show(), SFSI(".save_button >a").html("Saving..."), SFSI(".save_button >a").css("pointer-events", "none")
}

function sfsi_plus_make_popBox() {
  var s = 0;
  SFSI(".plus_sfsi_sample_icons >li").each(function () {
    "none" != SFSI(this).css("display") && (s = 1)
  }), 0 == s ? SFSI(".sfsi_plus_Popinner").hide() : SFSI(".sfsi_plus_Popinner").show(), "" != SFSI('input[name="sfsi_plus_popup_text"]').val() ? (SFSI(".sfsi_plus_Popinner >h2").html(SFSI('input[name="sfsi_plus_popup_text"]').val()), SFSI(".sfsi_plus_Popinner >h2").show()) : SFSI(".sfsi_plus_Popinner >h2").hide(), SFSI(".sfsi_plus_Popinner").css({
    "border-color": SFSI('input[name="sfsi_plus_popup_border_color"]').val(),
    "border-width": SFSI('input[name="sfsi_plus_popup_border_thickness"]').val(),
    "border-style": "solid"
  }), SFSI(".sfsi_plus_Popinner").css("background-color", SFSI('input[name="sfsi_plus_popup_background_color"]').val()), SFSI(".sfsi_plus_Popinner h2").css("font-family", SFSI("#sfsi_plus_popup_font").val()), SFSI(".sfsi_plus_Popinner h2").css("font-style", SFSI("#sfsi_plus_popup_fontStyle").val()), SFSI(".sfsi_plus_Popinner >h2").css("font-size", parseInt(SFSI('input[name="sfsi_plus_popup_fontSize"]').val())), SFSI(".sfsi_plus_Popinner >h2").css("color", SFSI('input[name="sfsi_plus_popup_fontColor"]').val() + " !important"), "yes" == SFSI('input[name="sfsi_plus_popup_border_shadow"]:checked').val() ? SFSI(".sfsi_plus_Popinner").css("box-shadow", "12px 30px 18px #CCCCCC") : SFSI(".sfsi_plus_Popinner").css("box-shadow", "none")
}

function sfsi_plus_stick_widget(s) {
  0 == sfsiplus_initTop.length && (SFSI(".sfsi_plus_widget").each(function (s) {
    sfsiplus_initTop[s] = SFSI(this).position().top
  }));
  var i = SFSI(window).scrollTop(),
    e = [],
    t = [];
  SFSI(".sfsi_plus_widget").each(function (s) {
    e[s] = SFSI(this).position().top, t[s] = SFSI(this)
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
    })
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
    }), c >= 0 && e[c] < i)
  }
}

function sfsi_plus_float_widget(s) {

  function iplus() {
    rplus = "Microsoft Internet Explorer" === navigator.appName ? aplus - document.documentElement.scrollTop : aplus - window.pageYOffset,
    Math.abs(rplus) > 0 ? (window.removeEventListener("scroll", iplus),
    aplus -= rplus * oplus,
    SFSI("#sfsi_plus_floater").css({
      top: Math.round((aplus + t).toString()) + "px",
      transition: "all 0.6s ease 0s"
    }), setTimeout(iplus, n)) : window.addEventListener("scroll", iplus, !1)
    /*console.log((aplus + t).toString(),aplus,t);*/

  }

  SFSI(window).scroll(function () {

    var documentheight = SFSI("#sfsi_plus_floater").attr("data-top");
    var fltrhght = parseInt(SFSI("#sfsi_plus_floater").height());
    var fltrtp = parseInt(SFSI("#sfsi_plus_floater").css("top"));
    if (parseInt(fltrhght) + parseInt(fltrtp) <= documentheight) {
      window.addEventListener("scroll", iplus, !1)
    } else {
      window.removeEventListener("scroll", iplus);
      SFSI("#sfsi_plus_floater").css("top", documentheight + "px")
    }
  });
  if ("center" == s) {
    var t = (jQuery(window).height() - SFSI("#sfsi_plus_floater").height()) / 2

  } else if ("bottom" == s) {
    var t = jQuery(window).height() - (SFSI("#sfsi_plus_floater").height() + parseInt(SFSI('#sfsi_plus_floater').css('margin-bottom')));
  } else {
    var t = parseInt(s)
  }
  var n = 50,
    oplus = .1,
    aplus = 0,
    rplus = 0
}

function sfsi_plus_shuffle() {
  var s = [];
  SFSI(".sfsi_premium_wicons").each(function (i) {
    SFSI(this).text().match(/^\s*$/) || (s[i] = "<div class='" + SFSI(this).attr("class") + "'>" + SFSI(this).html() + "</div>", SFSI(this).fadeOut("slow"), SFSI(this).insertBefore(SFSI(this).prev(".sfsi_premium_wicons")), SFSI(this).fadeIn("slow"))
  }), s = sfsiplus_Shuffle(s), $("#sfsi_plus_wDiv").html("");
  for (var i = 0; i < testArray.length; i++) $("#sfsi_plus_wDiv").append(s[i]);
}

function sfsi_plus_shuffle_new() {
  var $ = window.jQuery;

  return SFSI('.sfsi_plus_wDiv').each(function (index, container) {
    var s = [];
    s = SFSI(container).find(".sfsi_premium_wicons");
    s = sfsiplus_Shuffle(s);
    SFSI(container).html("");
    for (var i = 0; i < s.length; i++) {
      SFSI(s[i]).css('transform', 'none');
      SFSI(s[i]).css('position', 'relative');
      SFSI(container).append(s[i]);
    }
  })

}

function sfsiplus_Shuffle(s) {
  for (var i, e, t = s.length; t; i = parseInt(Math.random() * t), e = s[--t], s[t] = s[i], s[i] = e);
  return s
}

function sfsi_plus_setCookie(name, value, time) {
  var date = new Date();
  date.setTime(date.getTime() + (time * 1000));
  var expires = "; expires=" + date.toGMTString();
  document.cookie = name + "=" + value + expires + "; path=/"
}

function sfsi_plus_getCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for (var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') c = c.substring(1, c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length)
  }
  return null
}

function sfsi_plus_eraseCookie(name) {
  sfsi_plus_setCookie(name, "", -1)
}

function sfsi_plus_hideFooter() {}
window.onerror = function () {}, SFSI = jQuery, SFSI(window).on('load', function () {
  SFSI("#sfpluspageLoad").fadeOut(2e3)
});

var global_error = 0;

SFSI(document).ready(function (s) {
  SFSI("body").on("click", ".mailchimpSubscription", function () {
    var nonce = SFSI(this).attr('data-nonce');
    SFSI.ajax({
      url: sfsi_premium_ajax_object.ajax_url,
      type: "post",
      data: {
        action: "mailchimpSubscription",
        nonce: nonce
      },
      async: !0,
      dataType: "json",
      success: function (s) {
        alert(s)
      }
    })
  });

  SFSI("head").append('<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />'), SFSI("head").append('<meta http-equiv="Pragma" content="no-cache" />'), SFSI("head").append('<meta http-equiv="Expires" content="0" />'), SFSI(document).click(function (s) {
    var i = SFSI(".sfsi_plus_FrntInner_changedmonad"),
      e = SFSI(".sfsi_plus_wDiv"),
      t = SFSI("#at15s");
    i.is(s.target) || 0 !== i.has(s.target).length || e.is(s.target) || 0 !== e.has(s.target).length || t.is(s.target) || 0 !== t.has(s.target).length || i.fadeOut()
  }), SFSI(".sfsi_plus_outr_div").find(".addthis_button").mousemove(function () {
    var s = SFSI(".sfsi_plus_outr_div").find(".addthis_button").offset().top + 10;
    SFSI("#at15s").css({
      top: s + "px",
      left: SFSI(".sfsi_plus_outr_div").find(".addthis_button").offset().left + "px"
    })
    /*}), SFSI("div#sfsiplusid_linkedin").find(".icon4").find("a").find("img").mouseover(function() {
    SFSI(this).attr("src", sfsi_premium_ajax_object.plugin_url + "images/visit_icons/linkedIn_hover.svg")
    }), SFSI("div#sfsiplusid_linkedin").find(".icon4").find("a").find("img").mouseleave(function() {
    SFSI(this).attr("src", sfsi_premium_ajax_object.plugin_url + "images/visit_icons/linkedIn.svg")*/
  }), SFSI("div#sfsiplusid_youtube").find(".icon1").find("a").find("img").mouseover(function () {
    var src_selected_lang = SFSI(this).attr("src");
    SFSI(this).attr("src", src_selected_lang);
  }), SFSI("div#sfsiplusid_youtube").find(".icon1").find("a").find("img").mouseleave(function () {
    var src_selected_lang = SFSI(this).attr("src");
    SFSI(this).attr("src", src_selected_lang);
  }), SFSI("div#sfsiplusid_facebook").find(".icon1").find("a").find("img").mouseover(function () {
    SFSI(this).css("opacity", "0.9")
  }), SFSI("div#sfsiplusid_facebook").find(".icon1").find("a").find("img").mouseleave(function () {
    SFSI(this).css("opacity", "1")
  }), SFSI("div#sfsiplusid_twitter .cstmicon1 a img, div#sfsiplusid_twitter .icon1 a img, div#sfsiplusid_twitter .icon2 a img, div#sfsiplusid_facebook .icon3 a img, div#sfsiplusid_youtube .icon1 a img, div#sfsiplusid_pinterest .icon1 a img, div#sfsiplusid_pinterest .icon2 a img, div#sfsiplusid_yummly .icon1 a img, div#sfsiplusid_yummly .icon2 a img, div#sfsiplusid_linkedin .icon4 a img, div#sfsiplusid_linkedin .icon2 a img, div#sfsiplusid_mix .icon1 a img, div#sfsiplusid_mix .icon2 a img, div#sfsiplusid_ok .icon1 a img, div#sfsiplusid_ok .icon2 a img, div#sfsiplusid_ok .icon3 a img, div#sfsiplusid_telegram .icon1 a img, div#sfsiplusid_telegram .icon2 a img, div#sfsiplusid_vk .icon1 a img, div#sfsiplusid_vk .icon2 a img, div#sfsiplusid_weibo .icon1 a img, div#sfsiplusid_weibo .icon2 a img, div#sfsiplusid_wechat .icon1 a img, div#sfsiplusid_wechat .icon2 a img, div#sfsiplusid_xing .icon1 a img, div#sfsiplusid_xing .icon2 a img").mouseover(function () {
    SFSI(this).css("opacity", "0.9")
  }), SFSI("div#sfsiplusid_twitter .cstmicon1 a img, div#sfsiplusid_twitter .icon1 a img, div#sfsiplusid_twitter .icon2 a img, div#sfsiplusid_facebook .icon3 a img, div#sfsiplusid_youtube .icon1 a img, div#sfsiplusid_pinterest .icon1 a img, div#sfsiplusid_pinterest .icon2 a img, div#sfsiplusid_yummly .icon1 a img, div#sfsiplusid_yummly .icon2 a img, div#sfsiplusid_linkedin .icon4 a img, div#sfsiplusid_linkedin .icon2 a img, div#sfsiplusid_mix .icon1 a img, div#sfsiplusid_mix .icon2 a img, div#sfsiplusid_ok .icon1 a img, div#sfsiplusid_ok .icon2 a img, div#sfsiplusid_ok .icon3 a img, div#sfsiplusid_telegram .icon1 a img, div#sfsiplusid_telegram .icon2 a img, div#sfsiplusid_vk .icon1 a img, div#sfsiplusid_vk .icon2 a img, div#sfsiplusid_weibo .icon1 a img, div#sfsiplusid_weibo .icon2 a img, div#sfsiplusid_wechat .icon1 a img, div#sfsiplusid_wechat .icon2 a img, div#sfsiplusid_xing .icon1 a img, div#sfsiplusid_xing .icon2 a imgg").mouseleave(function () {
    SFSI(this).css("opacity", "1")
  }), SFSI(".pop-up").on("click", function () {
    ("fbex-s2" == SFSI(this).attr("data-id") || "googlex-s2" == SFSI(this).attr("data-id") || "linkex-s2" == SFSI(this).attr("data-id")) && (SFSI("." + SFSI(this).attr("data-id")).hide(), SFSI("." + SFSI(this).attr("data-id")).css("opacity", "1"), SFSI("." + SFSI(this).attr("data-id")).css("z-index", "1000")), SFSI("." + SFSI(this).attr("data-id")).show("slow")
  }), SFSI(document).on("click", '#close_popup', function () {
    SFSI(".read-overlay").hide("slow")
  });
  var e = 0;
  sfsi_plus_make_popBox(), SFSI('input[name="sfsi_plus_popup_text"] ,input[name="sfsi_plus_popup_background_color"],input[name="sfsi_plus_popup_border_color"],input[name="sfsi_plus_popup_border_thickness"],input[name="sfsi_plus_popup_fontSize"],input[name="sfsi_plus_popup_fontColor"]').on("keyup", sfsi_plus_make_popBox), SFSI('input[name="sfsi_plus_popup_text"] ,input[name="sfsi_plus_popup_background_color"],input[name="sfsi_plus_popup_border_color"],input[name="sfsi_plus_popup_border_thickness"],input[name="sfsi_plus_popup_fontSize"],input[name="sfsi_plus_popup_fontColor"]').on("focus", sfsi_plus_make_popBox), SFSI("#sfsi_plus_popup_font ,#sfsi_plus_popup_fontStyle").on("change", sfsi_plus_make_popBox), SFSI(document).on("click", '.radio', function () {
      var s = SFSI(this).parent().find("input:radio:first");
      "sfsi_plus_popup_border_shadow" == s.attr("name") && sfsi_plus_make_popBox()
    }),
    /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ? SFSI(document).on("click", "img.sfsi_premium_wicon", function (s) {
      if (!SFSI(this).hasClass('sfsi_premium_pinterest_icon')) {
        s.stopPropagation();
      }
      var i = SFSI("#sfsi_plus_floater_sec").val();
      var iconPos = SFSI(this).parents(".sfsi_premium_wicons").offset().top - SFSI(window).scrollTop();
      var tooltipExists = SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").length > 0;
      var automatic_tooltip = SFSI(this).parentsUntil("div").siblings("div.sfsi_premium_tooltip_automatic").length > 0;
      if (iconPos < 130) {
        if (tooltipExists && automatic_tooltip) {
          SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").addClass('sfsi_premium_tooltip_bottom sfsi_premium_tooltip_bottom_adjust');
          SFSI(".sfsi_premium_tooltip_bottom_adjust").parents(".sfsiplus_inerCnt").css("position", "unset");
          /*SFSI(".sfsi_premium_tooltip_bottom_adjust").parents(".sfsi_premium_tooltip_align_automatic").css("position", "unset");*/
        }
      } else if (iconPos > (screen.height + 10)) {
        if (tooltipExists && automatic_tooltip) {
          SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").addClass('sfsi_premium_tooltip_top');
        }
      } else {
        if (tooltipExists && automatic_tooltip) {
          SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").removeClass('sfsi_premium_tooltip_bottom').removeClass('sfsi_premium_tooltip_bottom');
        }
      }
      SFSI("div.sfsi_premium_wicons").css("z-index", "1"), SFSI(this).parent().parent().parent().siblings("div.sfsi_premium_wicons").find(".sfsiplus_inerCnt").find("div.sfsi_plus_tool_tip_2").hide(), SFSI(this).parent().parent().parent().parent().siblings("li").length > 0 && (SFSI(this).parent().parent().parent().parent().siblings("li").find("div.sfsi_plus_tool_tip_2").css("z-index", "0"), SFSI(this).parent().parent().parent().parent().siblings("li").find("div.sfsi_premium_wicons").find(".sfsiplus_inerCnt").find("div.sfsi_plus_tool_tip_2").hide()), SFSI(this).parent().parent().parent().css("z-index", "10000001"), SFSI(this).parent().parent().css({
        "z-index": "999"
      }), SFSI(this).attr("data-effect") && "fade_in" == SFSI(this).attr("data-effect") && (SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
        opacity: 1,
        "z-index": 10
      }), SFSI(this).parent().css("opacity", "1")), SFSI(this).attr("data-effect") && "fade_out" == SFSI(this).attr("data-effect") && (SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
        opacity: '0.6',
        "z-index": 10
      }), SFSI(this).parent().css("opacity", "0.6")), SFSI(this).attr("data-effect") && "scale" == SFSI(this).attr("data-effect") && (SFSI(this).parent().addClass("scale"), SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
        opacity: 1,
        "z-index": 10
      }), SFSI(this).parent().css("opacity", "1")), SFSI(this).attr("data-effect") && "combo" == SFSI(this).attr("data-effect") && (SFSI(this).parent().addClass("scale"), SFSI(this).parent().css("opacity", "1"), SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
        opacity: 1,
        "z-index": 10
      })), SFSI(this).attr("data-effect") && "combo-fade-out-scale" == SFSI(this).attr("data-effect") && (SFSI(this).parent().addClass("scale"), SFSI(this).parent().css("opacity", "0.6"), SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
        opacity: 0.6,
        "z-index": 10
      })), ("top-left" == i || "top-right" == i) && SFSI(this).parent().parent().parent().parent("#sfsi_plus_floater").length > 0 && "sfsi_plus_floater" == SFSI(this).parent().parent().parent().parent().attr("id") ? (SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2"), SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").find("span.bot_arow").addClass("top_big_arow"), SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
        opacity: 1,
        "z-index": 10,
      }), SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").show()) : (SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").find("span.bot_arow").removeClass("top_big_arow"), SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2"), SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
          opacity: 1,
          "z-index": 1e3
        }),
        SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").show()
      )
    }) : SFSI(document).on("mouseenter", "img.sfsi_premium_wicon", function () {
      var s = SFSI("#sfsi_plus_floater_sec").val();

      /* Comment to 16.0 */
      //SFSI("div.sfsi_premium_wicons").css("z-index", "1");

      SFSI(this).parent().parent().parent().siblings("div.sfsi_premium_wicons").find(".sfsiplus_inerCnt").find("div.sfsi_plus_tool_tip_2").hide();
      if (SFSI(this).parent().parent().parent().parent().siblings("li").length > 0) {
        SFSI(this).parent().parent().parent().parent().siblings("li").find("div.sfsi_plus_tool_tip_2").css("z-index", "0");
        SFSI(this).parent().parent().parent().parent().siblings("li").find("div.sfsi_premium_wicons").find(".sfsiplus_inerCnt").find("div.sfsi_plus_tool_tip_2").hide();
      }
      SFSI(this).parent().parent().parent().css("z-index", "1000000"), SFSI(this).parent().parent().css({
        "z-index": "999"
      });
      SFSI(this).attr("data-effect") && "fade_in" == SFSI(this).attr("data-effect") && (SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
        opacity: 1,
        "z-index": 10,
      }), SFSI(this).parent().css("opacity", "1"));
      SFSI(this).attr("data-effect") && "fade_out" == SFSI(this).attr("data-effect") && (SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
        opacity: "0.6",
        "z-index": 10
      }), SFSI(this).parent().css("opacity", "0.6"));
      SFSI(this).attr("data-effect") && "scale" == SFSI(this).attr("data-effect") && (SFSI(this).parent().addClass("scale"), SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
        opacity: 1,
        "z-index": 10
      }), SFSI(this).parent().css("opacity", "1"))
      SFSI(this).attr("data-effect") && "combo" == SFSI(this).attr("data-effect") && (SFSI(this).parent().addClass("scale"), SFSI(this).parent().css("opacity", "1"), SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
        opacity: 1,
        "z-index": 10,

      }))
      SFSI(this).attr("data-effect") && "combo-fade-out-scale" == SFSI(this).attr("data-effect") && (SFSI(this).parent().addClass("scale"), SFSI(this).parent().css("opacity", "0.6"), SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
        opacity: "0.6",
        "z-index": 10
      }))
      if (("top-left" == s || "top-right" == s) && SFSI(this).parent().parent().parent().parent("#sfsi_plus_floater").length > 0 && "sfsi_plus_floater" == SFSI(this).parent().parent().parent().parent().attr("id")) {
        SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2");
        SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").find("span.bot_arow").addClass("top_big_arow");
        SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
          opacity: 1,
          "z-index": 10,
        })
        var iconPos = SFSI(this).parents(".sfsi_premium_wicons").offset().top - SFSI(window).scrollTop();
        var tooltipExists = SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").length > 0;
        var automatic_tooltip = SFSI(this).parentsUntil("div").siblings("div.sfsi_premium_tooltip_automatic").length > 0;

        if (iconPos < 130) {
          if (tooltipExists && automatic_tooltip) {
            SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").addClass('sfsi_premium_tooltip_bottom sfsi_premium_tooltip_bottom_adjust');
            SFSI(".sfsi_premium_tooltip_bottom_adjust").parents(".sfsiplus_inerCnt").css("position", "unset");
            /*SFSI(".sfsi_premium_tooltip_bottom_adjust").parents(".sfsi_premium_tooltip_align_automatic").css("position", "unset");*/
          }
        } else if (iconPos > (screen.height + 10)) {
          if (tooltipExists && automatic_tooltip) {
            SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").addClass('sfsi_premium_tooltip_top');
          }
        } else {
          if (tooltipExists && automatic_tooltip) {
            SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").removeClass('sfsi_premium_tooltip_bottom').removeClass('sfsi_premium_tooltip_bottom');
          }
        }
        SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").show();
      } else {
        SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").find("span.bot_arow").removeClass("top_big_arow");
        SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2");
        SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").css({
          opacity: 1,
          "z-index": 10
        })
        var iconPos = SFSI(this).parents(".sfsi_premium_wicons").offset()
          ? SFSI(this).parents(".sfsi_premium_wicons").offset().top  - SFSI(window).scrollTop()
          : 0 - SFSI(window).scrollTop();
        var tooltipExists = SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").length > 0;
        var automatic_tooltip = SFSI(this).parentsUntil("div").siblings("div.sfsi_premium_tooltip_automatic").length > 0;
        if (iconPos < 130) {
          if (tooltipExists && automatic_tooltip) {
            SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").addClass('sfsi_premium_tooltip_bottom sfsi_premium_tooltip_bottom_adjust');
            SFSI(".sfsi_premium_tooltip_bottom_adjust").parents(".sfsiplus_inerCnt").css("position", "unset");
            /*SFSI(".sfsi_premium_tooltip_bottom_adjust").parents(".sfsi_premium_tooltip_align_automatic").css("position", "unset");*/
          }
        } else if (iconPos > (screen.height + 10)) {
          if (tooltipExists && automatic_tooltip) {
            SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").addClass('sfsi_premium_tooltip_top');
          }
        } else {
          if (tooltipExists && automatic_tooltip) {
            SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").removeClass('sfsi_premium_tooltip_bottom').removeClass('sfsi_premium_tooltip_bottom');
          }
        }
        SFSI(this).parentsUntil("div").siblings("div.sfsi_plus_tool_tip_2").show()
      }
    }), SFSI("div.sfsi_premium_wicons").on("mouseleave", function () {
      SFSI(this).children("div.sfsiplus_inerCnt").children("a.sficn").attr("data-effect") && "fade_in" == SFSI(this).children("div.sfsiplus_inerCnt").children("a.sficn").attr("data-effect") && SFSI(this).children("div.sfsiplus_inerCnt").find("a.sficn").css("opacity", "0.6"),
        SFSI(this).children("div.sfsiplus_inerCnt").children("a.sficn").attr("data-effect") && "fade_out" == SFSI(this).children("div.sfsiplus_inerCnt").children("a.sficn").attr("data-effect") && SFSI(this).children("div.sfsiplus_inerCnt").find("a.sficn").css("opacity", "1"),
        SFSI(this).children("div.sfsiplus_inerCnt").children("a.sficn").attr("data-effect") && "scale" == SFSI(this).children("div.sfsiplus_inerCnt").children("a.sficn").attr("data-effect") && SFSI(this).children("div.sfsiplus_inerCnt").find("a.sficn").removeClass("scale"),
        SFSI(this).children("div.sfsiplus_inerCnt").children("a.sficn").attr("data-effect") && "combo" == SFSI(this).children("div.sfsiplus_inerCnt").children("a.sficn").attr("data-effect") && (SFSI(this).children("div.sfsiplus_inerCnt").find("a.sficn").css("opacity", "0.6"),
          SFSI(this).children("div.sfsiplus_inerCnt").find("a.sficn").removeClass("scale")
        ),
        SFSI(this).children("div.sfsiplus_inerCnt").children("a.sficn").attr("data-effect") && "combo-fade-out-scale" == SFSI(this).children("div.sfsiplus_inerCnt").children("a.sficn").attr("data-effect") && (SFSI(this).children("div.sfsiplus_inerCnt").find("a.sficn").css("opacity", "1"),
          SFSI(this).children("div.sfsiplus_inerCnt").find("a.sficn").removeClass("scale")
        ),
        "sfsiplusid_google" == SFSI(this).children("div.sfsiplus_inerCnt").find("a.sficn").attr("id") ? SFSI("body").on("click", function () {
          SFSI(this).children(".sfsiplus_inerCnt").find("div.sfsi_plus_tool_tip_2").hide()
        }) : (SFSI(this).css({
            "z-index": "0"
          }),
          SFSI(this).children(".sfsiplus_inerCnt").find("div.sfsi_plus_tool_tip_2").hide()
        )
    }),
    SFSI("body").on("click", function () {
      SFSI(".sfsiplus_inerCnt").find("div.sfsi_plus_tool_tip_2").hide()
    }), SFSI(".adminTooltip >a").on("hover", function () {
      SFSI(this).offset().top, SFSI(this).parent("div").find("div.sfsi_plus_tool_tip_2_inr").css("opacity", "1"), SFSI(this).parent("div").find("div.sfsi_plus_tool_tip_2_inr").show()
    }), SFSI(".adminTooltip").on("mouseleave", function () {
      "none" != SFSI(".sfsi_plus_gpls_tool_bdr").css("display") && 0 != SFSI(".sfsi_plus_gpls_tool_bdr").css("opacity") ? SFSI(".pop_up_box").on("click", function () {
        SFSI(this).parent("div").find("div.sfsi_plus_tool_tip_2_inr").css("opacity", "0"), SFSI(this).parent("div").find("div.sfsi_plus_tool_tip_2_inr").hide()
      }) : (SFSI(this).parent("div").find("div.sfsi_plus_tool_tip_2_inr").css("opacity", "0"), SFSI(this).parent("div").find("div.sfsi_plus_tool_tip_2_inr").hide())
    }), SFSI(".expand-area").on("click", function () {
      "Read more" == SFSI(this).text() ? (SFSI(this).siblings("p").children("label").fadeIn("slow"), SFSI(this).text("Collapse")) : (SFSI(this).siblings("p").children("label").fadeOut("slow"), SFSI(this).text("Read more"))
    }), SFSI(".sfsi_plus_wDiv").length > 0 && setTimeout(function () {
      var s = parseInt(SFSI(".sfsi_plus_wDiv").height()) + 15 + "px";
      SFSI(".sfsi_plus_holders").each(function () {
        SFSI(this).css("height", s)
      });
      SFSI(".sfsi_plus_widget").css("min-height", "auto")
    }, 200);
    jQuery(document).find('.wp-block-ultimate-social-media-plus-sfsi-plus-share-block').each(function (index, target) {
      var actual_target = jQuery(target).find('.sfsi_plus_block');
      var align = jQuery(actual_target).attr('data-align');
      var maxPerRow = jQuery(actual_target).attr('data-count');
      var iconType = jQuery(actual_target).attr('data-icon-type');
      jQuery.ajax({
          'url': sfsi_premium_ajax_object.json_url+'wp-json/usm-premium-icons/v1/icons/?url=' + encodeURI(decodeURI(window.location.href)) + '&ractangle_icon=' + ('round' == iconType ? 0 : ('responsive' == iconType) ? 2 : 1),
          'method': 'GET'
          /*'data':{'is_admin':true,'share_url':'/'}*/
      }).done((response) => {
          jQuery(actual_target).html(response);
          if (iconType == 'round') {
              sfsi_plus_changeIconWidth(maxPerRow, target, align);
          } else {
              if ('center' === align) {
                  jQuery(target).find('.sfsi_plus_block_text_before_icon').css({
                      'display': 'inherit'
                  });
              }
              jQuery(target).css({
                  'text-align': align
              });
          }
          if (window.gapi) {
              window.gapi.plusone.go();
              window.gapi.plus.go();
              window.gapi.ytsubscribe.go();
          };
          if (window.twttr) {
              window.twttr.widgets.load();
          };
          if (window.IN && window.IN.parse) {
              window.IN.parse();
          };
          if (window.addthis) {
              if (window.addthis.toolbox) {
                  window.addthis.toolbox('.addthis_button.sficn');
              } else {
                  window.addthis.init();
                  window.addthis.toolbox('.addthis_button.sficn');
              }
          };
          if (window.PinUtils) {
              window.PinUtils.build();
          };
          if (window.FB) {
              if (window.FB.XFBML) {
                  window.FB.XFBML.parse();
              }
          };
      }).fail((response) => {
          jQuery(actual_target).html(response.responseText.replace('/\\/g', ''));
      });
  });
  if (undefined !== window.location.hash) {
      switch (window.location.hash) {
          case '#ui-id-3':
              jQuery('#ui-id-3').click();
          case '#ui-id-1':
              jQuery('#ui-id-1').click();
      }
  }
  var sfsi_premium_responsive_icon_bg_color = {
    "Buffer": "#151515",
    "Facebook": "#336699",
    "Flicker": "#FF0084",
    "Follow": "#00B04E",
    "Google": "#DD4B39",
    "Houzz": "#7BC044",
    "Google": "#DD4B39",
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
    "Pocket": "casts#F44336",
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
    "Google": "#F6F6F6",
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
});

function sfsi_plus_update_iconcount() {
  SFSI(".wp-block-ultimate-social-media-plus-sfsi-plus-share-block").each(function () {
      var icon_count = SFSI(this).find(".sfsi_plus_block").attr('data-count');
      var icon_align = SFSI(this).find(".sfsi_plus_block").attr('data-align');
      /*sfsi_plus_changeIconWidth(icon_count,this);*/

      if (jQuery(this).find('.sfsiplus_norm_row').length < 1) {
          setTimeout(function () {
              sfsi_plus_changeIconWidth(icon_count, this, icon_align);
          }, 1000);
      } else {
          sfsi_plus_changeIconWidth(icon_count, this, icon_align);
      }
  });
}

function sfsi_plus_changeIconWidth(per_row = null, target, icon_align) {
  var iconWidth = parseInt(jQuery(target).find('.sfsiplus_norm_row div').css('width')) || 40;
  var iconMargin = parseInt(jQuery(target).find('.sfsiplus_norm_row div').css('margin-left')) || 0;

  var wrapperWidth = (iconWidth + iconMargin) * per_row;
  jQuery(target).find('.sfsiplus_norm_row').css({
      'width': wrapperWidth + 'px'
  });
  jQuery(target).find('.sfsi_plus_block').css({
      'width': wrapperWidth + 'px'
  });
  jQuery(target).find('.sfsi_plus_block_text_before_icon').css({
      'padding-top': '12px'
  });
  if ('center' === icon_align) {
      jQuery(target).find('.sfsi_plus_block_text_before_icon').css({
          'display': 'inherit'
      });
      jQuery(target).css({
        'text-align': icon_align,
      });
  } else {
    jQuery(target).css({
      'text-align': icon_align,
      'float': icon_align
    });
  }
}

function sfsi_plus_new_window_popup(event) {

  event.preventDefault();

  var target = SFSI(event.target);

  if (target.tagName !== "a") {
    target = target.parents('a');
  }

  var url = target.attr('href');

  if (undefined != url && null != url && url.length > 0) {

    var cond1 = (new RegExp('https://x.com/intent/post')).test(url);
    /*console.log(cond1, url, !cond1 && "javascript:void(0);" != url);*/
    if ("javascript:void(0);" != url) {
      var x = (jQuery(window).width() - 520) / 2;
      var y = (jQuery(window).height() - 570) / 2;
      window.open(url, 'window_popup', 'height=570,width=520,location=1,status=1,left=' + x + ',top=' + y + ',scrollbars=1');
    }
  }
}
var sfsiplus_initTop = new Array();
window.sfsi_premium_fittext_shouldDisplay = true;

/*image hover icon*/
function sfsi_hover_icon_handler() {
  var api_link = document.querySelectorAll('link[rel="https://api.w.org/"]');
  if (api_link.length > 0) {
    var api_root = document.querySelectorAll('link[rel="https://api.w.org/"]')[0].getAttribute('href');
  }
  var is_archive = SFSI("body").hasClass("archive");
  var is_date = SFSI("body").hasClass("date");
  var is_author = SFSI("body").hasClass("author");

  if (undefined !== api_root) {
    SFSI.ajax({
      'url': api_root + 'usm-premium-icons/v1/hover_icon_setting/',
      'method': 'GET',
      'data': {
        'url': window.location.href,
        'is_archive': is_archive ? 'yes' : 'no',
        'is_date': is_date ? 'yes' : 'no',
        'is_author': is_author ? 'yes' : 'no',

      }
    }).then(function (sfsi_plus_result) {
      /*settings=JSON.parse(result);
      settings = result;*/
      if (undefined !== sfsi_plus_result.icon && sfsi_plus_result.icon.length > 0) {
        if (undefined === window.sfsi_premium) {
          window.sfsi_premium = {
            img_hover_setting: sfsi_plus_result
          }
        } else {
          window.sfsi_premium.img_hover_setting = sfsi_plus_result;
        }
        if (sfsi_plus_result.type == "regular") {
          sfsi_register_img_hover_handler();
        } else {

          sfsi_premium_pinterest_absolute(sfsi_plus_result);
        }
      }
    });
  } else {
    SFSI.ajax({
      'url': sfsi_premium_ajax_object.ajax_url,
      'type': 'POST',
      async: !0,
      dataType: "json",
      'data': {
        'action': 'premium_hover_icon_settings',
        'url': window.location.href,
        'is_archive': is_archive ? 'yes' : 'no',
        'is_date': is_date ? 'yes' : 'no',
        'is_author': is_author ? 'yes' : 'no',
      }
    }).then(function (sfsi_plus_result) {
      /*settings = result;*/
      if (undefined !== sfsi_plus_result.icon && sfsi_plus_result.icon.length > 0) {
        if (undefined === window.sfsi_premium) {
          window.sfsi_premium = {
            img_hover_setting: sfsi_plus_result
          }
        } else {
          window.sfsi_premium.img_hover_setting = sfsi_plus_result;
        }
        if (sfsi_plus_result.type == "regular") {
          sfsi_register_img_hover_handler();
        } else {
          sfsi_premium_pinterest_absolute(sfsi_plus_result);
        }
      }

    });
  }

  function sfsi_register_img_hover_handler() {

    var ismobile = navigator.userAgent.match(/ipad|iphone|ipod|android/i) != null;
    var device_check = false;
    try {
      var slength = "undefined" !== typeof window.sfsi_premium.img_hover_setting.show_on.length ?
        window.sfsi_premium.img_hover_setting.show_on.length : 0;

      if (slength > 0) {

        if (ismobile) {
          for (var i = 0; i < slength; i++) {
            if (window.sfsi_premium.img_hover_setting.show_on[i] === 'mobile') {
              device_check = true;
            }
          }
        } else {
          for (var i = 0; i < slength; i++) {
            if (window.sfsi_premium.img_hover_setting.show_on[i] === 'desktop') {
              device_check = true;
            }
          }
        }
      }
    } catch (e) {

    }

    SFSI(document).on('mouseover touchstart', 'img', function () {
      if (
        SFSI(this).attr('data-pin-nopin') !== 'true' &&
        !SFSI(this).hasClass('sfsi_premium_wicon') &&
        (
          SFSI(this).width() > parseInt(window.sfsi_premium.img_hover_setting.width) &&
          SFSI(this).height() > parseInt(window.sfsi_premium.img_hover_setting.height)
        ) &&
        SFSI(this).parents('.sfsi_premium_image_hover_container').length == 0 &&
        window.sfsi_premium &&
        window.sfsi_premium.img_hover_setting &&
        device_check
      ) {
        var settings = window.sfsi_premium.img_hover_setting;
        var container = document.createElement('div');
        container.className = "sfsi_premium_image_hover_container";
        var parent = jQuery(this).parent();
        var icons_container = document.createElement('div');
        icons_container.className = "sfsi_premium_image_hover_icon_container";
        var margin_top = 5;
        var margin_h = 5;
        var margin_v = 5;

        /* For right align image */
        var rightAlign = false;
        if ( SFSI(this).hasClass( 'alignright' ) ) {
          rightAlign = true;
        }

        if (window.sfsi_premium.img_hover_setting.icon_type === "small-rectangle") {
          margin_top = 2;
          margin_bottom = 2;
          margin_h = 2;
          margin_v = 0;
        }
        if (window.sfsi_premium.img_hover_setting['placement'] === 'bottom-right') {
          icons_container.style = "position:absolute;right:5px;bottom:5px";
        } else if (window.sfsi_premium.img_hover_setting['placement'] === 'top-right') {
          icons_container.style = "position:absolute;right:5px;top:" + margin_top + "px;";
        } else if (window.sfsi_premium.img_hover_setting['placement'] === 'bottom-left') {
          icons_container.style = "position:absolute;left:5px;bottom:5px;";
        } else {
          icons_container.style = "position:absolute;left:5px;top:" + margin_top + 'px';
        }
        var target_image_src = SFSI(this).attr('src');
        if (target_image_src.substring(0, 5) == "data:") {
          srcset = SFSI(this).attr('srcset');
          if ( srcset ) {
            if (target_image_src.indexOf(' ') !== false) {
              target_image_src = srcset.substring(0, target_image_src.indexOf(' '));
            } else {
              target_image_src = srcset;
            }
          }
        }
        var target_image_title = SFSI(this).attr('title') || SFSI(this).attr('alt') || SFSI('meta[property="og:title"]').attr('content');
        var current_url = window.location.href;
        var image_width = SFSI(this).width();
        var image_height = SFSI(this).height();
        var image_with_figure = false;
        if (SFSI(this).parent('figure').length === 1) {
          image_with_figure = true;
        }
        settings.icon.forEach(function (icon_setting) {
          var icon = document.createElement('a');
          target_image_title = encodeURIComponent(target_image_title).replace('+', '%20');
          target_image_title = target_image_title.replace('#', '%23');
          icon.href = icon_setting.share_url_template + encodeURIComponent(current_url) + '&media=' + encodeURIComponent(target_image_src) + '&description=' + target_image_title;

          if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            icon.setAttribute( "data-pin-custom", "true" );
            icon.setAttribute( "onclick", "sfsi_plus_new_window_popup(event)" );
          } else {
            icon.target = "_blank";
          }
          icon.className = "sfsi_open_window";
          icon.innerHTML = icon_setting.icon;
          icons_container.appendChild(icon);
        });
        container.appendChild(icons_container);
        if (SFSI(this).parent('a').length === 1) {
          var target = SFSI(this).parent().clone();
          target.addClass('sfsi_premium_hover_img');
          container.appendChild(target[0]);
          SFSI(this).parent().replaceWith(container);
        } else {
          var target = SFSI(this).clone();
          target.addClass('sfsi_premium_hover_img');
          container.appendChild(target[0]);
          SFSI(this).replaceWith(container);
        }
        container_2 = parent.find('.sfsi_premium_image_hover_icon_container');
        var container_height = SFSI(container_2).height();
        var container_width = SFSI(container_2).width();
        if (container_height > image_height && image_with_figure === true) {
          image_with_figure = false;
        }

        var icon_margin_top = SFSI(container).find('.sfsi_premium_image_hover_icon_container').css('top');
        var icon_margin_bottom = SFSI(container).find('.sfsi_premium_image_hover_icon_container').css('bottom');
        var icon_margin_right = SFSI(container).find('.sfsi_premium_image_hover_icon_container').css('right');
        /*if(icon_margin_top!=="0px" && image_with_figure==false){
            SFSI(container).find('.sfsi_premium_image_hover_icon_container').css('margin-top',((container_height-image_height)+parseInt(icon_margin_top))+'px' )
        }*/
        if (icon_margin_bottom !== "0px" && image_with_figure == false && window.sfsi_premium.img_hover_setting.icon_type !== 'square') {
          SFSI(container).find('.sfsi_premium_image_hover_icon_container').css('bottom', (((container_height - image_height) / 2) + margin_top) + 'px')
        }
        var adjustment_h = (container_width - image_width) / 2;
        if (adjustment_h < 0) {
          adjustment_h = adjustment_h * -1;
        }
        /*if (icon_margin_right !== "0px" && image_with_figure == false) {
          SFSI(container).find('.sfsi_premium_image_hover_icon_container').css('right', ((adjustment_h) + margin_top) + 'px')
        }*/

        if( rightAlign ) {
          jQuery('.sfsi_premium_image_hover_container').addClass( 'alignright' );
        }

        jQuery('.sfsi_premium_image_hover_container > img').css('width', '100%');
        jQuery('.sfsi_premium_image_hover_container').css({'display': 'block', 'width': image_width+'px'});

        /*console.log(jQuery('.sfsi_premium_image_hover_container').width(), jQuery('.sfsi_premium_image_hover_container img.sfsi_premium_hover_img, .sfsi_premium_image_hover_container .sfsi_premium_hover_img img').width());*/
        if (window.sfsi_premium.img_hover_setting['placement'] === 'bottom-right') {
          jQuery('.sfsi_premium_image_hover_icon_container').css('right', ((jQuery('.sfsi_premium_image_hover_container').width() - jQuery('.sfsi_premium_image_hover_container img.sfsi_premium_hover_img, .sfsi_premium_image_hover_container .sfsi_premium_hover_img img').width()) + margin_h) + 'px');
          jQuery('.sfsi_premium_image_hover_icon_container').css('bottom', (((jQuery('.sfsi_premium_image_hover_container').height() - jQuery('.sfsi_premium_image_hover_container img.sfsi_premium_hover_img, .sfsi_premium_image_hover_container .sfsi_premium_hover_img img').height()) / 2) + margin_v) + 'px');
        } else if (window.sfsi_premium.img_hover_setting['placement'] === 'top-right') {
          jQuery('.sfsi_premium_image_hover_icon_container').css('right', ((jQuery('.sfsi_premium_image_hover_container').width() - jQuery('.sfsi_premium_image_hover_container img.sfsi_premium_hover_img, .sfsi_premium_image_hover_container .sfsi_premium_hover_img img').width()) + margin_h) + 'px');
          jQuery('.sfsi_premium_image_hover_icon_container').css('top', (((jQuery('.sfsi_premium_image_hover_container').height() - jQuery('.sfsi_premium_image_hover_container img.sfsi_premium_hover_img, .sfsi_premium_image_hover_container .sfsi_premium_hover_img img').height()) / 2) + margin_v) + 'px');
        } else if (window.sfsi_premium.img_hover_setting['placement'] === 'bottom-left') {
          /*jQuery('.sfsi_premium_image_hover_icon_container').css('left',(( (jQuery('.sfsi_premium_image_hover_container').width() - jQuery('.sfsi_premium_image_hover_container img.sfsi_premium_hover_img, .sfsi_premium_image_hover_container .sfsi_premium_hover_img img').width())/2)+margin_h) +'px');*/
          jQuery('.sfsi_premium_image_hover_icon_container').css('bottom', (((jQuery('.sfsi_premium_image_hover_container').height() - jQuery('.sfsi_premium_image_hover_container img.sfsi_premium_hover_img, .sfsi_premium_image_hover_container .sfsi_premium_hover_img img').height()) / 2) + margin_v) + 'px');
        } else {
          /*jQuery('.sfsi_premium_image_hover_icon_container').css('left',(( (jQuery('.sfsi_premium_image_hover_container').width() - jQuery('.sfsi_premium_image_hover_container img.sfsi_premium_hover_img, .sfsi_premium_image_hover_container .sfsi_premium_hover_img img').width())/2)+margin_h) +'px');
          jQuery('.sfsi_premium_image_hover_icon_container').css('top',(( (jQuery('.sfsi_premium_image_hover_container').height() - jQuery('.sfsi_premium_image_hover_container img.sfsi_premium_hover_img, .sfsi_premium_image_hover_container .sfsi_premium_hover_img img').height())/2)+margin_v) +'px');*/
        }

      }
      SFSI(document).on('click', 'a.sfsi_open_window', function (event) {
        event.preventDefault();
        var url = SFSI(this).attr('href');
        window.open(url, "Share This Image", "width=800,height=350,status=0,toolbar=0,menubar=0,location=1,scrollbars=1");
      })
    });
    SFSI(document).on('mouseleave', '.sfsi_premium_image_hover_container', function () {

      SFSI(this).children('img').first().css( 'width', '' );
      var restore_img = SFSI(this).find('.sfsi_premium_hover_img');
      restore_img.removeClass('sfsi_premium_hover_img');
      SFSI(this).replaceWith(restore_img);
    });
  }

}
SFSI(document).ready(function () {
  SFSI(document).on('click', 'a.sfsi_open_window', function (event) {
    event.preventDefault();
    var url = SFSI(this).attr('href');
    window.open(url, "Share This Image", "width=800,height=350,status=0,toolbar=0,menubar=0,location=1,scrollbars=1");
  })
  var sfsi_premium_responsive_icon_bg_color = {
    "Buffer": "#151515",
    "Facebook": "#336699",
    "Flicker": "#FF0084",
    "Follow": "#16CB30",
    "GooglePlus": "#DD4B39",
    "Houzz": "#7BC044",
    "Google": "#DD4B39",
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
    "Pocket Cast": "#F44336",
    "Next": "#19975D",
    "Home Advisor": "#F69020",
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
    "Google": "#F6F6F6",
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
  // jQuery(document).on('mouseenter', '.sfsi_premium_icons_container a', function () {
  //   jQuery(this).css('opacity', 0.8);
  // })
  jQuery(document).on('mouseleave', '.sfsi_premium_icons_container a', function () {
    jQuery(this).css('opacity', 1);
  })

  jQuery('.sfsi_premium_icons_container a').each(function (index, a_container) {
    if (jQuery(a_container).css('display') !== "none") {
      sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
    }
  });
  var sfsi_premium_rtime;
  var sfsi_premium_timeout = false;
  var sfsi_premium_delta = 500;
  window.sfsi_premium_fittext_shouldDisplay = true;
  jQuery(window).resize(function () {
    sfsi_premium_rtime = new Date();
    /*console.log('resize', sfsi_premium_timeout, sfsi_premium_rtime, sfsi_premium_delta);*/
    if (sfsi_premium_timeout === false) {
      sfsi_premium_timeout = true;
      setTimeout(sfsi_premium_resizeend, sfsi_premium_delta);
    }
  });

  function sfsi_premium_resizeend() {
    /*console.log((new Date()).getTime(), sfsi_premium_rtime.getTime());*/
    if ((new Date()).getTime() - sfsi_premium_rtime.getTime() < sfsi_premium_delta) {
      /*console.log('sfsi_premium resize reset');*/
      setTimeout(sfsi_premium_resizeend, sfsi_premium_delta);
    } else {
      sfsi_premium_timeout = false;
      /*console.log('sfsi_premium resize executed');*/
      sfsi_premium_resize_icons_container();
      jQuery('.sfsi_premium_icons_container a').each(function (index, a_container) {
        if (jQuery(a_container).css('display') !== "none") {
          sfsi_premium_fitText(jQuery(a_container).find('.sfsi_premium_responsive_icon_item_container'));
        }
      });
    }
  }
  sfsi_premium_resize_icons_container();

  /* Copy link */
  function sfsi_copyLink(str) {

    try {

      const el = document.createElement('textarea');
      el.value = str;
      el.setAttribute('readonly', '');
      el.style.position = 'absolute';
      el.style.left = '-9999px';
      document.body.appendChild(el);
      el.select();
      document.execCommand('copy');
      document.body.removeChild(el);

      // Confirmation of copy success e.g. alert or notification
      showSuccessAlert();

      return true;

    } catch (e) {

      console.log(e);

      // Rejection notice of copy faliure e.g. alert or notification

      return false;

    }

  }

  function showSuccessAlert() {
    var alert = document.getElementById("success-alert");
    alert.style.display = "block";
    alert.style.animation = "intro-animation 1s";

    setTimeout(function() {
      alert.style.animation = "fade-out 2s";
      setTimeout(function() {
        alert.style.display = "none";
        alert.style.animation = "none"; // Reset the animation
      }, 2000); // Adjust the timing to match the fade-out animation duration
    }, 3000); // Show the alert for 3 seconds (you can adjust the timing)
  }

  document.querySelectorAll('a.sfsi_copylink').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {

      e.preventDefault();
      sfsi_copyLink(window.location.href);

    });
  });
});

function force_initialize_fb_icons() {
  if (window.FB && window.FB.XFBML && window.FB.XFBML.parse) {
    try {
      window.FB.XFBML.parse();
    } catch (e) {
      window.fbAsyncInit = function () {
        FB.init({
          status: true,
          cookie: true,
          xfbml: true,
          version: 'v3.0'
        })
      }
      window.fbAsyncInit();
      window.FB.XFBML.parse();
    }
  } else {
    setTimeout(force_initialize_fb_icons, 1000);
  }
}

function sfsi_premium_wechat_follow(url) {
  if (jQuery('.sfsi_premium_wechat_scan').length == 0) {
    jQuery('body').append(
      "<div class='sfsi_premium_wechat_scan sfsi_premium_overlay show'>" +
      "<div class='sfsi_premium_inner_display'>" +
      '<a class="close_btn" href="" onclick="event.preventDefault();close_overlay(\'.sfsi_premium_wechat_scan\')" >×</a>' +
      "<img src='" + url + "' style='max-width:90%;max-height:90%' />" +
      "</div>" +
      "</div>"
    );
  } else {
    jQuery('.sfsi_premium_wechat_scan').removeClass('hide').addClass('show');
  }
}

function close_overlay(selector) {
  if (typeof selector === "undefined") {
    selector = '.sfsi_premium_overlay';
  }
  jQuery(selector).removeClass('show').addClass('hide').hide();
}

function sfsi_premium_wechat_share(url) {
  if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
    sfsi_premium_wechat_share_mobile(url);
  } else {
    if (jQuery('.sfsi_premium_wechat_follow_overlay').length == 0) {
      jQuery('body').append(
        "<div class='sfsi_premium_wechat_follow_overlay sfsi_premium_overlay show'>" +
        "<div class='sfsi_premium_inner_display'>" +
        '<a class="close_btn" href="" onclick="event.preventDefault();close_overlay(\'.sfsi_premium_wechat_follow_overlay\')" >×</a>' +
        "<div style='width:95%;max-width:500px; min-height:80%;background-color:#fff;margin:0 auto;margin:10% auto;padding: 20px 0;'>" +
        "<div style='width:90%;margin: 0 auto;text-align:center'>" +
        "<div class='sfsi_premium_wechat_qr_display' style='display:inline-block'>" +
        "</div>" +
        "</div>" +
        "<div style='width:80%;margin:10px auto 0 auto;text-align:center;font-weight:900;font-size:25px;'>" +
        '"Scan QR Code" in WeChat and press ··· to share!' +
        "</div>" +
        "</div>" +
        "</div>" +
        "</div>"
      );
      new QRCode(jQuery('.sfsi_premium_wechat_follow_overlay .sfsi_premium_wechat_qr_display')[0], encodeURI(decodeURI(window.location.href)))
      jQuery('.sfsi_premium_wechat_follow_overlay .sfsi_premium_wechat_qr_display img').attr('nopin', 'nopin')
    } else {
      jQuery('.sfsi_premium_wechat_scan').removeClass('hide').addClass('show');
    }
  }
}

function sfsi_premium_wechat_share_mobile(url) {
  if (jQuery('.sfsi_premium_wechat_follow_overlay').length == 0) {
    jQuery('body').append(
      "<div class='sfsi_premium_wechat_follow_overlay sfsi_premium_overlay show'>" +
      "<div class='sfsi_premium_inner_display'>" +
      '<a class="close_btn sfsi_premium_wechat_mobile_share_close_btn" href="" onclick="event.preventDefault();close_overlay(\'.sfsi_premium_wechat_follow_overlay\')" >×</a>' +
      "<div style='width:95%; min-height:80%;background-color:#fff;margin:0 auto;margin:10% auto;padding: 20px 0;'>" +
      "<div style='width:90%;margin: 0 auto;'>" +
      "<input type='text' value='" + encodeURI(decodeURI(window.location.href)) + "' style='width:100%;padding:7px 0;text-align:center' />" +
      "</div>" +
      "<div style='width:80%;margin:10px auto 0 auto'>" +
      "<div style='width:50%;display:inline-block;text-align:center' class='sfsi_premium_upload_butt_container' >" +
      "<button onclick='sfsi_copy_text_parent_input(event)' class='upload_butt' >Copy</button>" +
      "</div>" +
      "<div style='width:50%;display:inline-block;text-align:center' class='sfsi_premium_upload_butt_container' >" +
      "<a href='weixin://' class='upload_butt'>Open WeChat</a>" +
      "</div>" +
      "</div>" +
      "</div>" +
      "</div>" +
      "</div>"
    );
  } else {
    jQuery('.sfsi_premium_wechat_scan').removeClass('hide').addClass('show');
  }
}

function sfsi_copy_text_parent_input(event) {
  var target = jQuery(event.target);
  input_target = target.parent().parent().parent().find('input');
  input_target.select();
  document.execCommand('copy');
}

function sfsi_premium_fitText(container) {
  if (container.parent().parent().hasClass('sfsi_premium_icons_container_box_fixed_container')) {
    /*console.log(window.sfsi_premium_fittext_shouldDisplay);*/
    if (window.sfsi_premium_fittext_shouldDisplay === true) {
      var container_width = container.width();
      /*var container_img_width = container.find('img').width();*/
      var container_img_width = 65;
      /*var span=container.find('span').clone();*/
      var span = container.find('span');
      /*var span_original_width = container.find('span').width();*/
      var span_original_width = container_width - (container_img_width)
      span
        /*.css('display','inline-block')*/
        .css('white-space', 'nowrap')
        /*.css('width','auto')*/
      ;
      var span_flatted_width = span.width();
      if (span_flatted_width == 0) {
        span.css('display', "inline");
        span_flatted_width = span.width();
        if (span_flatted_width == 0) {
          span.css('font-size', 20);
          span_flatted_width = span.width();
          span.css('font-size', 0);
        }
        span.css('display', "none");

      }
      span
        /*.css('display','inline-block')*/
        .css('white-space', 'unset')
        /*.css('width','auto')*/
      ;
      var shouldDisplay = true;
      var fontSize = parseInt(span.css('font-size'));
      if (0 == fontSize) {
        fontSize = 15;
      }
      var computed_fontSize = (Math.floor((fontSize * span_original_width) / span_flatted_width));

      if (computed_fontSize < 8) {
        shouldDisplay = false;
        window.sfsi_premium_fittext_shouldDisplay = false;
      }
      /*console.log(fontSize, span_original_width, span_flatted_width, computed_fontSize, shouldDisplay);*/

      span.css('font-size', Math.min(computed_fontSize, 15));
      span
        /*.css('display','inline-block')*/
        .css('white-space', 'nowrap')
        /*.css('width','auto')*/
      ;
      if (shouldDisplay) {
        span.show();
      } else {
        span.hide();
        jQuery('.sfsi_premium_responsive_icon_item_container  span').hide();
      }
    }
  }
}

function sfsi_premium_resize_icons_container() {
  jQuery('.sfsi_premium_responsive_icons').each(function (index, container_elem) {
    var container = jQuery(container_elem);
    /*console.log(container.find('.sfsi_premium_icons_container').hasClass('sfsi_premium_icons_container_box_fully_container'), container.find('.sfsi_premium_icons_container').hasClass('sfsi_premium_responsive_without_counter_icons'))
    if((!container.find('.sfsi_premium_icons_container').hasClass('sfsi_premium_icons_container_box_fully_container'))&&(!container.find('.sfsi_premium_icons_container').hasClass('sfsi_premium_responsive_without_counter_icons'))) {
        var actual_width=container.width();
        var count_width=container.find('.sfsi_premium_responsive_icons_count').width();
        // jQuery('.sfsi_premium_responsive_cloned_list').remove();
        var sfsi_premium_inline_style=container.attr('style');
        // remove_width
        sfsi_premium_inline_style=sfsi_premium_inline_style.replace(/width:\s*(-|)\d*\s*(px|%)\s*($|!important|)(;|$)/g,'');
        sfsi_premium_inline_style=sfsi_premium_inline_style.replace(/width:\s*auto\s*($|!important|)(;|$)/g,'');
        // sfsi_premium_inline_style.replace('width:\s*\d*\s*(px|%)\s*($|!important|)(;|$)','');
        var should_width=(actual_width-count_width-27);
        sfsi_premium_inline_style+="width:"+should_width+'px!important;';
        var abc = container.find('a').filter(function(index,icons){
            return 'none'!==jQuery(icons).css('display');
        });
        scrollWidth=container_elem.scrollWidth;
        var sfsi_premium_icons_active = abc.length;
        container.attr('style',sfsi_premium_inline_style);
        // jQuery('.sfsi_premium_icons_container').css('display','flex');
    }else */
    if (container.find('.sfsi_premium_icons_container').hasClass('sfsi_premium_icons_container_box_fully_container') && container.find('.sfsi_premium_icons_container').hasClass('sfsi_premium_responsive_without_counter_icons')) {
      var sfsi_premium_inline_style = container.find('.sfsi_premium_icons_container').attr('style');
      sfsi_premium_inline_style = sfsi_premium_inline_style.replace(/width:\s*auto\s*($|!important|)(;|$)/g, '');
      sfsi_premium_inline_style = sfsi_premium_inline_style.replace(/width:\s*(-|)\d*\s*(px|%)\s*($|!important|)(;|$)/g, '');
      sfsi_premium_inline_style += "width:auto!important;";
      container.find('.sfsi_premium_icons_container').attr('style', sfsi_premium_inline_style);
    }
  });
}

SFSI(document).ready(function () {
  SFSI(".sfsiplus_inerCnt a.sficn").click(function (event) {
    if (SFSI(this).attr('href') == '' || SFSI(this).attr('href') == '#') {
      event.preventDefault();
    }
  });
});

function escapeDoubleQuotes(str) {
  return str.replace(/\\([\s\S])|(")/g, "\\$1$2"); /* thanks @slevithan!*/
}

function sfsi_premium_pinterest_modal_images(url, title) {

  var imgSrc = [];
  var page_title;

  page_title = SFSI('meta[property="og:title"]').attr('content');

  SFSI('body img').each(function (index) {
    var src = SFSI(this).attr('src') || "";
    if ( src && src.substring(0, 5) == "data:" ) {
      if( SFSI( this ).is( '[data-src]' ) ) {
        src = SFSI(this).attr( 'data-src' );
      } else {
        srcset = SFSI(this).attr('srcset');
        if ( srcset ) {
          if (src.indexOf(' ') !== false) {
            src = srcset.substring(0, src.indexOf(' '));
          } else {
            src = srcset;
          }
        }
      }
    }
    var height = SFSI(this).height();
    var width = SFSI(this).width();
    var image_title = SFSI(this).attr('title') || "";
    var alt = SFSI(this).attr('alt') || "";
    var no_pin = SFSI(this).attr('data-pin-nopin') || "";
    var no_pin_old = SFSI(this).attr('nopin') || "";

    if (src !== "" && !src.startsWith("javascript") && height > 100 && width > 100 && no_pin_old !== "nopin" && no_pin !== "true") {
      imgSrc.push({
        src: src,
        title: title && "" !== title ? title : (image_title && "" !== image_title ? image_title : alt)
      });
    }
  });

  sfsi_premium_pinterest_modal(imgSrc);
  event.preventDefault();

  /* Remove duplicate */
  if ( imgSrc.length > 0 ) {
    var uniquesimgSrc = [];
    var itemsFound = {};
    for(var i = 0, l = imgSrc.length; i < l; i++) {
        var stringified = JSON.stringify(imgSrc[i]);
        if(itemsFound[stringified]) { continue; }
        uniquesimgSrc.push(imgSrc[i]);
        itemsFound[stringified] = true;
    }
  }

  /* Check click state for popup */
  var popup_elements = '';
  if( SFSI('.sfsi_premium_pinterest_create').hasClass( 'pinterest_new_window') ) {
    popup_elements = "onclick='sfsi_plus_new_window_popup(event)'";
  }

  if( SFSI('.sfsi_premium_pinterest_create').hasClass( 'pinterest_new_tab') ) {
    popup_elements = "target='_blank'";
  }

  SFSI.each(uniquesimgSrc, function (index, val) {
      /*console.log('discrip',val);*/
      SFSI('.sfsi_premium_flex_container').append('<div><a '+popup_elements+' href="http://www.pinterest.com/pin/create/button/?url=' + url + '&media=' + val.src + '&description=' + encodeURIComponent(val.title ? val.title : page_title).replace('+', '%20').replace("#", "%23") + '"><img  data-pin-nopin="true" src="' + val.src + '"><span class="sfsi_premium_pinterest_overlay"><img data-pin-nopin="true" height="30" width="30" src="' + window.sfsi_premium_ajax_object.plugin_url + '/images/pinterest.png" /></span></a></div>');
    }),
    event.preventDefault();
}

function sfsi_premium_pinterest_modal(imgs) {
  /*if (jQuery('.sfsi_premium_wechat_follow_overlay').length == 0) {*/
  var modalTitle =  ( imgs==null || imgs.length==0) ? "No images found to pin on Pinterest":"Pin It on Pinterest";

  jQuery('body').append(
    "<div class='sfsi_premium_wechat_follow_overlay sfsi_premium_overlay show'>" +
    "<div class='sfsi_premium_inner_display'>" +
    '<a class="close_btn" href="" onclick="event.preventDefault();close_overlay(\'.sfsi_premium_wechat_follow_overlay\')" >×</a>' +
    "<div style='width:95%;max-width:500px; min-height:80%;background-color:#fff;margin:0 auto;margin:10% auto;padding: 20px 0;border-radius: 20px;'>" +
    "<h4 style='margin-left:10px;'>"+modalTitle+"</h4>" +
    "<div class='sfsi_premium_flex_container'>" +

    "</div>" +
    "</div>" +
    "</div>" +
    "</div>"
  );
}

function sfsi_premium_pinterest_absolute(icon) {

  var style_icon = "display:none";
  var margin_top = 5;
  var margin_h = 5;
  var margin_v = 5;
  var ismobile = navigator.userAgent.match(/ipad|iphone|ipod|android/i) != null;
  var page_title = SFSI('meta[property="og:title"]').attr('content');
  if ((ismobile == false && icon.show_on.includes('desktop'))) {
    SFSI("body").append('<div class="sfsi_premium_pinterest_absolute" style="' + style_icon + '"><a class="sfsi_premium_hover_link" href="">' + icon.icon[0].icon + '</a></div>');

    SFSI("body img").mouseover(function (e) {
      SFSI('.sfsi_premium_pinterest_absolute').show();
      var src = SFSI(this).attr('src') || "";

      if ( src && src.substring(0, 5) == "data:") {
        if( SFSI( this ).is( '[data-src]' ) ) {
          src = SFSI(this).attr( 'data-src' );
        } else {
          srcset = SFSI(this).attr('srcset');
          if ( srcset ) {
            if (src.indexOf(' ') !== false) {
              src = srcset.substring(0, src.indexOf(' '));
            } else {
              src = srcset;
            }
          }
        }
      }

      var height = SFSI(this).height();
      var width = SFSI(this).width();
      var offset = SFSI(this).offset();
      var right = offset.left + width;
      var bottom = offset.top + width;
      var left = offset.left;
      var top = offset.top;
      button_width = 40;
      button_height = 40;
      if (icon.placement == "bottom-left") {
        top = offset.top + height - button_height - 5;
        left = left + 5;
      } else if (icon.placement == "top-right") {
        left = offset.left + width - button_width - 5;
        top = top + 5;
      } else if (icon.placement == "bottom-right") {
        top = offset.top + height - button_height - 5;
        left = offset.left + width - button_width - 5;
      } else {
        top = top + 5;
        left = left + 5;
      }
      var no_pin_old = SFSI(this).attr('nopin');
      var no_pin = SFSI(this).attr('data-pin-nopin');

      if (src !== "" && !src.startsWith("javascript") &&
        height >= parseFloat(icon.height) && width >= parseFloat(icon.width) && no_pin_old !== "nopin" && no_pin !== "true") {
        SFSI('.sfsi_premium_pinterest_absolute').css({
          "position": "absolute",
          "left": left,
          "top": top,
          "z-index": 99999,
        });
        var target;
        if (icon.page == "tab") {
          target = "_blank";
        } else {
          target = "_self";
        }
        /*console.log(icon);*/
        var desc = icon.description;
        if (desc == "") {
          desc = page_title;
        }
        var share_link = icon.icon[0].share_url_template;
        share_link = share_link + encodeURI(window.location);
        share_link = share_link + "&media=" + encodeURI(src);
        share_link = share_link + "&description=" + encodeURI(desc).replace("+", "%20").replace('#', "%23");

        if ("window" == icon.page) {
          SFSI('.sfsi_premium_pinterest_absolute .sfsi_premium_hover_link').attr({
            'href': share_link,
            'onclick': 'sfsi_plus_new_window_popup(event)'
          });
        } else {
          SFSI('.sfsi_premium_pinterest_absolute .sfsi_premium_hover_link').attr({
            'href': share_link,
            'target': target
          });
        }
      }
    });
    SFSI("body img").mouseout(function (e) {
      SFSI('.sfsi_premium_pinterest_absolute').hide();
    });
  } else if (ismobile == true && icon.show_on.includes('mobile')) {
    SFSI("body img").each(function (index, data) {

      var src = SFSI(this).attr('src') || "";
      if ( src && src.substring(0, 5) == "data:" ) {
        if( SFSI( this ).is( '[data-src]' ) ) {
          src = SFSI(this).attr( 'data-src' );
        } else {
          srcset = SFSI(this).attr('srcset');
          if ( srcset ) {
            if (src.indexOf(' ') !== false) {
              src = srcset.substring(0, src.indexOf(' '));
            } else {
              src = srcset;
            }
          }
        }
      }

      var height = SFSI(data).height();
      var width = SFSI(data).width();
      var offset = SFSI(data).offset();
      /*console.log(offset,data);*/
      var right = offset.left + width;
      var bottom = offset.top + width;
      var left = offset.left;
      var top = offset.top;
      button_width = 40;
      button_height = 40;
      if (icon.placement == "bottom-left") {
        top = offset.top + height - button_height - 5;
        left = left + 5;
      } else if (icon.placement == "top-right") {
        left = offset.left + width - button_width - 5;
        top = top + 5;
      } else if (icon.placement == "bottom-right") {
        top = offset.top + height - button_height - 5;
        left = offset.left + width - button_width - 5;
      } else {
        top = top + 5;
        left = left + 5;
      }
      var no_pin_old = SFSI(data).attr('nopin');
      var no_pin = SFSI(data).attr('data-pin-nopin');
      if (
        src !== "" &&
        !src.startsWith("javascript") &&
        height >= parseFloat(icon.height) &&
        width >= parseFloat(icon.width) &&
        no_pin_old !== "nopin" &&
        no_pin !== "true" &&
        data.style.dispaly !== "none" &&
        !data.src.includes('gravatar.com')
      ) {
        /*console.log(data,index);*/
        SFSI("body").append('<div class="sfsi_premium_pinterest_absolute_mobile sfsi_premium_pinterest_absolute_' + index + '" style="" ><a class="sfsi_premium_hover_link" href="">' + icon.icon[0].icon + '</a></div>');
        SFSI('.sfsi_premium_pinterest_absolute_' + index).css({
          "position": "absolute",
          "left": left,
          "top": top,
          "z-index": 99999,
        });
        var target;
        if (icon.page == "tab") {
          target = "_blank";
        } else {
          target = "_self";
        }
        /*console.log(icon);*/
        var desc = icon.description;
        if (desc == "") {
          desc = page_title;
        }
        var share_link = icon.icon[0].share_url_template;
        share_link = share_link + encodeURI(window.location);
        share_link = share_link + "&media=" + encodeURI(src);
        share_link = share_link + "&description=" + encodeURI(desc);

        if ("window" == icon.page) {
          SFSI('.sfsi_premium_pinterest_absolute_' + index + ' .sfsi_premium_hover_link').attr({
            'href': share_link,
            'onclick': 'sfsi_plus_new_window_popup(event)'
          });
        } else {
          SFSI('.sfsi_premium_pinterest_absolute_' + index + ' .sfsi_premium_hover_link').attr({
            'href': share_link,
            'target': target
          });
        }
      }
    });
  }
}

SFSI(document).ready(function () {
  SFSI('.sfsiaftrpstwpr a img').each(function (index) {
    var src = SFSI(this).attr('src');
    if (src && src.substring(0, 5) == "data:") {
      srcset = SFSI(this).attr('data-src');
      /*if(src.indexOf(' ')!==false){
        src = srcset.substring(0,src.indexOf(' '));
      }else{
        src = srcset;
      }*/
      src = srcset;
    }else if(src && src.substring(0, 6) == "image/" && SFSI(this).is('[data-src]')){
      srcset = SFSI(this).attr('data-src');
      src = srcset;
    }
    SFSI(this).attr('src', src);
  });
});

/*SFSI(document).ready(function() {
  SFSI('.sfsi_premium_wicons').tooltipster({
    interactive: true,
    trigger: 'hover',
    functionInit: function(instance, helper) {
      var $origin = jQuery(helper.origin);
      var tooltipdiv =$origin.find('.sfsi_plus_inside');
      console.log(tooltipdiv);
      if(tooltipdiv.length>0){
        instance.content = jQuery(tooltipdiv[0].().detach();
        console.log(instance.content);
      }else{
        instance.close();
      }
      // console.log($origin);
    }
  });
});*/
SFSI(document).ready(function(){
  var isMobile = window.matchMedia("only screen and (max-width: 760px)");

  if (isMobile.matches) {
    if(SFSI(".extra-hatom-entry-title").prev().text() == "Footnotes"){
      SFSI(".extra-hatom-entry-title").prev().css({"position": "relative", "top": "71px"});
    }

  }
});
