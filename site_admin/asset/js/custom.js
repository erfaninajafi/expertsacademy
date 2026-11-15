// get parameter from current url
function getUrlVars() {
  var vars = [],
    hash;
  var hashes = window.location.href
    .slice(window.location.href.indexOf("?") + 1)
    .split("&");
  for (var i = 0; i < hashes.length; i++) {
    hash = hashes[i].split("=");
    vars.push(hash[0]);
    vars[hash[0]] = hash[1];
  }
  return vars;
}

// start cat ajax
function Cat() {
  var cat_id = $("#cat option:selected").val();
  $("#sub_sub_cat :input").hide();
  $.ajax({
    url: "ajax.php",

    data: { cat_id: cat_id },
    type: "post",
    success: function (output) {
      $("#sub_cat").html(output);
    },
  });
}
function Sub_Cat() {
  var sub_cat_id = $("#sub_cat option:selected").val();
  $.ajax({
    url: "ajax.php",

    data: { sub_cat_id: sub_cat_id },
    type: "post",
    success: function (output) {
      $("#sub_sub_cat").html(output);
    },
  });
}
// end cat ajax
//*******
//*******
// start show and hide error session

jQuery(document).ready(function () {
  jQuery(".item").on("click", function () {
    var item = jQuery(this).val();
    jQuery.ajax({
      url: "ajax.php?do=itemUpdate",
      type: "POST",
      data: {
        item: item,
      },
      success: function (data) {
        alert(data);
      },
    });
  });
  jQuery(".formatnumber").on("keyup", function (e) {
    if (
      e.keyCode != 37 &&
      e.keyCode != 38 &&
      e.keyCode != 39 &&
      e.keyCode != 40
    ) {
      //Prevent if push prev or next keys
      var _this = this,
        num = _this.value.replace(/\,/g, "");
      if (!isNaN(num)) {
        num = num
          .toString()
          .split("")
          .reverse()
          .join("")
          .replace(/(?=\d*\.?)(\d{3})/g, "$1,");
        num = num.split("").reverse().join("").replace(/^[\,]/, "");
        _this.value = num;
        // store current positions in variables
        var start = _this.selectionStart,
          end = _this.selectionEnd;
        // restore from variables...
        _this.setSelectionRange(start, end);
      } else {
        alert("Only Number!");
        _this.value = _this.value.replace(/[^\d\.]*/g, "");
      }
    }
  });

  jQuery(".plus").click(function () {
    var content = jQuery(".price_input").html();
    jQuery(".result").append(content);
  });

  jQuery(".delete-item").click(function () {
    var dataId = jQuery(this).attr("data");
    jQuery("#d" + dataId).html("");
  });

  jQuery(".checkname").keyup(function (e) {
    var title = jQuery(this).val();
    var code = jQuery(this).data("code");
    var section = jQuery(this).data("section");
    var lng = jQuery(this).data("lng");
    jQuery.post(
      "ajax.php?do=checkname",
      { title: title, code: code, section: section, lng: lng },
      function (data) {
        jQuery(".checknameresult-" + lng).html(data);
      }
    );
  });
  jQuery(".checknamecat").keyup(function (e) {
    var title = jQuery(this).val();
    var code = jQuery(this).data("code");
    var section = jQuery(this).data("section");
    var lng = jQuery(this).data("lng");
    jQuery.post(
      "ajax.php?do=checknamecat",
      { title: title, code: code, section: section, lng: lng },
      function (data) {
        jQuery(".checknameresult-" + lng).html(data);
      }
    );
  });

  jQuery(".checkall").click(function () {
    jQuery('input[type="checkbox"]').prop("checked", this.checked);
  });

  jQuery("#cat").on("change", function () {
    var code = jQuery(this).val();
    console.log(code);
    jQuery.ajax({
      type: "POST",
      url: "send.php?do=subcat",
      data: { code: code },
      dataType: "html",
      success: function (data) {
        jQuery("#subview").html(data);
      },
    });
    return false;
  });

  jQuery(".btn-default").filter('[for="filestyle-5"]').addClass("red");
  jQuery(".btn-default")
    .filter('[for="filestyle-5"]')
    .children("span:first")
    .addClass("icon-video2");

  // check img
  jQuery(".file-img").each(function () {
    var thisimg = jQuery(this).find("img").attr("id");
    var thisspan = jQuery(this).find("span:last").attr("class");
    var thisinput = jQuery(this).find("input:last");

    jQuery("." + thisspan).on("click", function () {
      jQuery(this).parent().find("img").attr("src", "").hide();
      thisinput.val("");
      jQuery("." + thisspan).hide();
    });
    jQuery(this)
      .find('INPUT[type="file"]')
      .change(function () {
        var size = this.files[0].size;
        var ext = this.value.match(/\.(.+)$/)[1];

        if (size > 3000000) {
          alert("سایز فایل شما باید حداکثر 3 مگابایت بایت باشد");
          jQuery("#" + thisimg).hide();
          jQuery("." + thisspan).hide();
          thisinput.val("");
          this.value = "";
          return;
        }
        switch (ext) {
          case "jpg":
          case "jpeg":
          case "JPG":
          case "JPEG":
            //                        jQuery('#submit_item').attr('disabled', false);
            break;
          default:
            alert("لطفا فقط تصاویر با فرمت jpg انتخاب کنید");
            jQuery(this).val("");
            jQuery("#" + thisimg).hide();
            jQuery("." + thisspan).hide();
            thisinput.val("");
            this.value = "";
        }
      });
  });

  // check film
  jQuery(".file-film").each(function () {
    var thisimg = jQuery(this).find("img").attr("id");
    var thisspan = jQuery(this).find("span:last").attr("class");
    var thisinput = jQuery(this).find("input:last");

    jQuery("." + thisspan).on("click", function () {
      jQuery(this).parent().find("img").hide();
      thisinput.val("");
      jQuery("." + thisspan).hide();
    });
    jQuery(this)
      .find('INPUT[name="film"]')
      .change(function () {
        var size = this.files[0].size;

        var ext = this.value.match(/\.(.+)$/)[1];

        if (size > 10000000) {
          alert("سایز فایل شما باید حداکثر 10 مگا بایت باشد");
          jQuery("#" + thisimg).hide();
          jQuery("." + thisspan).hide();
          thisinput.val("");
          this.value = "";
          return;
        }
        switch (ext) {
          case "mp4":
          case "MP4":
          case "MOV":
          case "mov":
            break;
          default:
            alert("لطفا فقط فیلم با فرمت mp4 یا mov انتخاب کنید");
            jQuery(this).val("");
            jQuery("#" + thisimg).hide();
            jQuery("." + thisspan).hide();
            thisinput.val("");
            this.value = "";
        }
      });
  });

  jQuery(".del").on("click", function () {
    var imgname = jQuery(this).attr("data-img");
    var datacode = jQuery(this).attr("data-code");
    if (confirm("آیا مطمئن هستید میخواهید این تصویر را حذف نمایید ؟")) {
      jQuery.ajax({
        type: "POST",
        url: "send.php?do=deletefile2",
        data: { imgname: imgname, datacode: datacode },
        dataType: "html",
        success: function (data) {
          window.location.href = data;
        },
      });
      return false;
    }
  });

  jQuery("#Formupdate").submit(function (e) {
    e.preventDefault();
    var formData = new FormData(jQuery(this)[0]);
    jQuery.ajax({
      url: "send.php?do=update2",
      type: "POST",
      data: formData,
      async: false,
      dataType: "JSON",
      beforeSend: function () {
        jQuery(".ajax_data").show();
      },
      complete: function () {
        jQuery(".ajax_data").hide();
        window.location.href = "system/agahi.php";
      },
      cache: false,
      contentType: false,
      processData: false,
    });

    return false;
  });

  // error show
  jQuery(".error").find("span").show().delay(2000).fadeOut();

  jQuery("#state").on("change", function () {
    var code = jQuery(this).val();
    jQuery.ajax({
      type: "POST",
      url: "ajax.php?do=state",
      data: { code: code },
      dataType: "html",
      success: function (data) {
        jQuery("#city").html(data);
      },
    });
    return false;
  });

  jQuery("#city").on("change", function () {
    var code = jQuery(this).val();
    jQuery.ajax({
      type: "POST",
      url: "ajax.php?do=city",
      data: { code: code },
      dataType: "html",
      success: function (data) {
        jQuery("#area").html(data);
      },
    });
    return false;
  });
});
