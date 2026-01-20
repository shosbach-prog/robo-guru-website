function sfsi_plus_float_widget(s) {

    function iplus() {
      rplus = "Microsoft Internet Explorer" === navigator.appName ? aplus - document.documentElement.scrollTop : aplus - window.pageYOffset, Math.abs(rplus) > 0 ? (window.removeEventListener("scroll", iplus), aplus -= rplus * oplus, SFSI("#sfsi_plus_floater").css({
        top: ((aplus + t).toString()) + "px"
      }), setTimeout(iplus, n)) : window.addEventListener("scroll", iplus, !1)
    }
  
    SFSI(window).scroll(function () {
  
      var documentheight = SFSI(document).height();
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