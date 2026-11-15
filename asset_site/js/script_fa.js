function projectUrl(path) {
  var base = /^\/expertsacademy(?:\/|$)/.test(window.location.pathname)
    ? "/expertsacademy/"
    : "/";
  return base + path.replace(/^\/+/, "");
}

function scrollMore() {
  var b = "";
  if (
    ((b = $(window).width() > 480 ? 300 : 1100),
    $(window).scrollTop() + $(window).height() > $(document).height() - b)
  ) {
    var c = $('[id^="myData_"]').length,
      a = $("#allData").val();
    $(window).unbind("scroll"),
      a != c &&
        ($("#loaderImg").html(
          '<img src="' + projectUrl("asset_site/images/ajax-loader.gif") + '">'
        ),
        $("#loaderImg").show(),
        loadMoreData(c));
  }
}
function loadMoreData(c) {
  var cat = $("#cat").val();
  $.ajax({
    url: projectUrl("ajax_site.php?do=getdata"),
    type: "post",
    async: !1,
    cache: !1,
    data: { cat: cat, offset: +c },
    success: function (j) {
      $("#loaderImg").empty(),
        $("#loaderImg").hide(),
        $(".content_ajax").append(j);
    },
    error: function (j) {
      alert("Data Erorr");
    },
  }).done(function () {
    $(window).bind("scroll", function () {
      scrollMore();
    });
  });
}

function ajaxProduct(id) {
  $(".modal-body").html("");
  return (
    $.ajax({
      type: "POST",
      url: projectUrl("ajax_site.php?do=product"),
      data: { val: id },
      dataType: "html",
      success: function (data) {
        $(".modal-body").html(data);
        $("#result").modal("show");
      },
    }),
    !1
  );
}

$(document).ready(function () {
  $("form.js-contact-form input[name='section']").on("change", function () {
    var $form = $(this).closest("form.js-contact-form");
    var thisVal = $(this).val();
    if (thisVal == "1") {
      $form.find(".request").show();
      $form.find(".job, .jobresult, .jobresulttwo").hide();
    } else {
      $form.find(".request, .jobresult, .jobresulttwo").hide();
      $form.find(".job").show();
    }
  });
  $("form.js-contact-form input[name='kind']").on("change", function () {
    var $form = $(this).closest("form.js-contact-form");
    if ($(this).val() == "1") {
      $form.find(".jobresult").show();
      $form.find(".jobresulttwo").hide();
    } else {
      $form.find(".jobresult").hide();
      $form.find(".jobresulttwo").show();
    }
  });

  $(".open-modal").click(function () {
    const videoId = $(this).data("id");
    $.ajax({
      url: projectUrl("ajax_site.php?do=modal-video"),
      type: "POST",
      data: { id: videoId },
      success: function (response) {
        $("#videoContainer").html(response);
        $("#videoModal").modal("show");
      },
      error: function () {
        alert("خطا در بارگذاری ویدیو.");
      },
    });
  });

  $("form.js-contact-form").submit(function (event) {
    event.preventDefault();

    var $form = $(this);
    var postData = new FormData(this);

    $.ajax({
      type: "POST",
      url: projectUrl("ajax_site.php?do=contact"),
      data: postData,
      processData: false,
      contentType: false,
      cache: false,
      timeout: 8e5,
      success: function (data) {
        $form.find(".msgform").html(data);
      },
    });
  });

  $("form#job").submit(function (event) {
    event.preventDefault();
    var str = $("form").serializeArray(),
      postData = new FormData();
    $.each(str, function (i, val) {
      postData.append(val.name, val.value);
    }),
      $("input[name=send]").prop("disabled", !0),
      $.ajax({
        type: "POST",
        url: projectUrl("ajax_site.php?do=job"),
        data: postData,
        processData: !1,
        contentType: !1,
        cache: !1,
        timeout: 8e5,
        success: function (data) {
          $(".msgform").html(data);
        },
      });
  });

  $("form.faq-form").submit(function (event) {
    event.preventDefault();

    var $form = $(this),
      $submit = $form.find("input[name=send]"),
      $status = $form.find(".msgform"),
      postData = new FormData(this);

    $submit.prop("disabled", true);
    $status.empty();

    $.ajax({
      type: "POST",
      url: projectUrl("ajax_site.php?do=faq"),
      data: postData,
      processData: false,
      contentType: false,
      cache: false,
      timeout: 8e5,
      success: function (data) {
        $status.html(data);
      },
      error: function () {
        $status.text($form.attr("data-error-message"));
      },
      complete: function () {
        $submit.prop("disabled", false);
      },
    });
  });

  // حذف ویدیو هنگام بستن modal
  $("#videoModal").on("hidden.bs.modal", function () {
    $("#videoContainer").html("");
  });

  $(".tooltipicon")
    .hover(
      function (event) {
        var titleText = $(this).attr("data");
        $(this).data("tipText", titleText).removeAttr("data");

        $('<p class="tooltip"></p>')
          .text(titleText)
          .appendTo("body")
          .css("top", event.pageY - 5 + "px")
          .css("left", event.pageX + 20 + "px")
          .fadeIn("slow");
      },
      function () {
        $(this).attr("data", $(this).data("tipText"));
        $(".tooltip").remove();
      }
    )
    .mousemove(function (event) {
      $(".tooltip")
        .css("top", event.pageY - 5 + "px")
        .css("left", event.pageX + 20 + "px");
    });

  $(window).scroll(function () {
    document.getElementById("scroll") && scrollMore();
  });

  $(".ajaxproduct").on("click", function () {
    var val = $(this).attr("data");
    $(".modal-body").html("");
    return (
      $.ajax({
        type: "POST",
        url: projectUrl("ajax_site.php?do=product"),
        data: { val: val },
        dataType: "html",
        success: function (data) {
          $(".modal-body").html(data);
          $("#result").modal("show");
        },
      }),
      !1
    );
  });

  $(".menuIcon").on("click", function () {
    $(this).toggleClass("active");
    $(".sideMenu").toggleClass("active");
  });
  var $ul = $(".sidebar-navigation > ul");
  $ul.find("li a").click(function (e) {
    var $li = $(this).parent();
    if ($li.find("ul").length > 0) {
      e.preventDefault();
      if ($li.hasClass("selected")) {
        $li.removeClass("selected").find("li").removeClass("selected");
        $li.find("ul").slideUp(400);
        $li.find("a em").removeClass("mdi-flip-v");
      } else {
        if ($li.parents("li.selected").length == 0) {
          $ul.find("li").removeClass("selected");
          $ul.find("ul").slideUp(400);
          $ul.find("li a em").removeClass("mdi-flip-v");
        } else {
          $li.parent().find("li").removeClass("selected");
          $li.parent().find("> li ul").slideUp(400);
          $li.parent().find("> li a em").removeClass("mdi-flip-v");
        }
        $li.addClass("selected");
        $li.find(">ul").slideDown(400);
        $li.find(">a>em").addClass("mdi-flip-v");
      }
    }
  });

  $(".sidebar-navigation > ul ul").each(function (i) {
    if ($(this).find(">li>ul").length > 0) {
      var paddingLeft = $(this)
        .parent()
        .parent()
        .find(">li>a")
        .css("padding-right");
      var pIntPLeft = parseInt(paddingLeft);
      var result = pIntPLeft + 10;
      $(this)
        .find(">li>a")
        .css({ "padding-right": result, "font-weight": "800px" });
    } else if ($(this).find(">li>ul").length > 0) {
      var paddingLeft = $(this)
        .parent()
        .parent()
        .find(">li>a")
        .css("padding-right");
      var pIntPLeft = parseInt(paddingLeft);
      var result = pIntPLeft + 20;
      $(this)
        .find(">li>a")
        .css({ "padding-right": result, "font-weight": "800px" });
    } else {
      var paddingLeft = $(this)
        .parent()
        .parent()
        .find(">li>a")
        .css("padding-left");
      var pIntPLeft = parseInt(paddingLeft);
      var result = pIntPLeft + 40;
      $(this)
        .find(">li>a")
        .css({ "padding-right": result, "font-weight": "800px" })
        .parent()
        .addClass("selected--last");
    }
  });

  var a = ["۰", "١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩"];
  $(".translate").text(function (c, b) {
    for (var d = b.split(""), c = 0; c < d.length; c++)
      /\d/.test(d[c]) && (d[c] = a[d[c]]);
    return d.join("");
  });
});
