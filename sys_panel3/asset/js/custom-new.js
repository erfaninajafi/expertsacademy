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
function addNewItem() {
  var content = $("#list-items").val();
  tinymce.activeEditor.execCommand(
    "mceInsertContent",
    false,
    "{" + content + "}"
  );
}
jQuery(document).ready(function () {

  jQuery('.formatnumber').on('keyup', function (e) {
    if (e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) { //Prevent if push prev or next keys
        var _this = this,
            num = _this.value.replace(/\,/g, '');
        if (!isNaN(num)) {
            num = num.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g, '$1,');
            num = num.split('').reverse().join('').replace(/^[\,]/, '');
            _this.value = num;
            // store current positions in variables
            var start = _this.selectionStart,
                end = _this.selectionEnd;
            // restore from variables...
            _this.setSelectionRange(start, end);
        } else {
            alert('Only Number!');
            _this.value = _this.value.replace(/[^\d\.]*/g, '');
        }
    }

})

  jQuery(".skills").addClass("active");
  jQuery(".skills .skill .skill-bar span").each(function () {
    jQuery(this).animate(
      {
        width: jQuery(this).parent().attr("data-bar") + "%",
      },
      1000
    );
    jQuery(this).append(
      "<b>" + jQuery(this).parent().attr("data-bar") + "%</b>"
    );
  });
  setTimeout(function () {
    jQuery(".skills .skill .skill-bar span b").animate({ opacity: "1" }, 1000);
  }, 2000);

  Array.prototype.shuffle = function () {
    let m = this.length,
      i;
    while (m) {
      i = (Math.random() * m--) >>> 0;
      [this[m], this[i]] = [this[i], this[m]];
    }
    return this;
  };

  jQuery("#select_all").on("click", function () {
    if ($(this).prop("checked")) {
      let minnum = 10,
        maxnum = 10;
      let rand = Math.min(
        maxnum,
        Math.floor(Math.random() * ($(".checkrandom").length - 1 - minnum)) +
          minnum
      );
      //create our keys array
      let keyArray = [...Array($(".checkrandom").length).keys()]
        .shuffle()
        .slice(0, rand);
      keyArray.forEach((chk_i, i) => {
        if (i < rand) $($(".checkrandom").get(chk_i)).prop("checked", true);
      });
    } else {
      $(".checkrandom").prop("checked", false);
    }
  });

  jQuery(".animated-progress span").each(function () {
    $(this).animate(
      {
        width: $(this).attr("data-progress") + "%",
      },
      1000
    );
    jQuery(this).text($(this).attr("data-progress") + "%");
  });

  jQuery(".contractstatus").on("change", function () {
    var val = jQuery(this).val();
    jQuery.ajax({
      type: "POST",
      url: "ajax.php?do=contractstatus",
      data: {
        val: val,
      },
      success: function (data) {
        alert(data);
      },
    });
    return false;
  });

  jQuery(".documentstatus").on("change", function () {
    var val = jQuery(this).val();
    jQuery.ajax({
      type: "POST",
      url: "ajax.php?do=documentstatus",
      data: {
        val: val,
      },
      success: function (data) {
        alert(data);
      },
    });
    return false;
  });

  //   jQuery(".plan").on("change", function () {
  //     var val = jQuery(this).val();
  //     jQuery.ajax({
  //       type: "POST",
  //       url: "ajax.php?do=planstatus",
  //       data: {
  //         val: val,
  //       },
  //       success: function (data) {
  //         alert(data);
  //       },
  //     });
  //     return false;
  //   });

  jQuery("select[name='model']").on("change", function () {
    var val = jQuery(this).val();
    jQuery.ajax({
      type: "POST",
      url: "ajax.php?do=planmodel",
      data: {
        val: val,
      },
      success: function (data) {
        $(".resultplan").html(data);
      },
    });
    return false;
  });

  jQuery(".plus").click(function () {
    var content = jQuery(".price_input").html();
    jQuery(".result").append(content);
  });

  jQuery(".delete-item").click(function () {
    var dataId = jQuery(this).attr("data");
    jQuery("#d" + dataId).html("");
  });
  jQuery("#formwork > .sendstatus").click(function (e) {
    e.preventDefault();
    //jQuery(this).attr("data-update");
    var userid = jQuery(this).parent("form").find("#userid").val();
    var contract = jQuery(this).parent("form").find("#contract").val();
    var status = JSON.stringify(
      jQuery(this).parent("form").find(".status").val()
    );
    jQuery.ajax({
      type: "POST",
      url: "ajax.php?do=mentor",
      data: {
        status: status,
        userid: userid,
        contract: contract,
      },
      success: function (data) {
        alert(data);
      },
    });
    return false;
  });

  jQuery(".show-image").click(function (e) {
    e.preventDefault();
    var status = jQuery(this).attr("data-status");
    var id = jQuery(this).attr("id");
    var $row = jQuery(this).closest("tr");
    jQuery.ajax({
      type: "POST",
      url: "ajax.php?do=userupdate",
      data: {
        status: status,
        id: id,
      },
      success: function (data) {
        if (data === "1") {
          $row.css("background", "");
          jQuery("#" + id).removeClass("no-receive");
          jQuery("#" + id).addClass("receive");
        } else {
          // alert("no");
          $row.css("background", "yellow");
          jQuery("#" + id).removeClass("receive");
          jQuery("#" + id).addClass("no-receive");
        }
      },
    });
    //return data;
  });
  jQuery(".confirmation").on("click", function () {
    return confirm("Are you sure?");
  });
  jQuery(".change-box").on("click", function () {
    var val = jQuery(this).closest("tr").attr("id");
    var $row = jQuery(this).closest("tr");
    jQuery.ajax({
      type: "POST",
      url: "ajax.php?do=changebox",
      data: { val: val },
      success: function (data) {
        if (data === "1") {
          $row.css("background", "");
          jQuery("#box-" + val).removeClass("no-receive");
          jQuery("#box-" + val).addClass("receive");
          jQuery("#box-" + val)
            .parent("td")
            .find("span")
            .text("دریافت شده");
        } else {
          // alert("no");
          $row.css("background", "yellow");
          jQuery("#box-" + val).removeClass("receive");
          jQuery("#box-" + val).addClass("no-receive");
          jQuery("#box-" + val)
            .parent("td")
            .find("span")
            .text("دریافت نشده");
        }
      },
    });
    return false;
  });
  jQuery("#checkusername").keyup(function (e) {
    var id = $(this).val();
    var section = $(this).attr("data_section");
    jQuery.post(
      "ajax.php?do=checkusername",
      { val: id, lng: "fa", section: section },
      function (data) {
        jQuery("#checkusernameresult").html(data);
      }
    );
  });

  jQuery(".label-section label").on("selectstart", function (event) {
    event.preventDefault();
  });
  jQuery("#showfield").on("change", function () {
    var val = jQuery(this).val();
    if (val === "a") {
      jQuery(".formatshow").css({ display: "block" });
      jQuery(".fileuploadshow").css({ display: "none" });
      jQuery(".radioshow").css({ display: "none" });
      jQuery(".chrshow").css({ display: "block" });
      jQuery(".checkshow").css({ display: "none" });
    } else if (val === "d") {
      jQuery(".radioshow").css({ display: "block" });
      jQuery(".chrshow").css({ display: "none" });
      jQuery(".formatshow").css({ display: "none" });
      jQuery(".checkshow").css({ display: "block" });
      jQuery(".fileuploadshow").css({ display: "none" });
    } else if (val === "c") {
      jQuery(".radioshow").css({ display: "none" });
      jQuery(".checkshow").css({ display: "block" });
      jQuery(".chrshow").css({ display: "none" });
      jQuery(".formatshow").css({ display: "none" });
      jQuery(".fileuploadshow").css({ display: "none" });
    } else if (val === "e") {
      jQuery(".fileuploadshow").css({ display: "block" });
      jQuery(".radioshow").css({ display: "none" });
      jQuery(".chrshow").css({ display: "none" });
      jQuery(".formatshow").css({ display: "none" });
      jQuery(".checkshow").css({ display: "none" });
    } else if (val === "f") {
      jQuery(".fileuploadshow").css({ display: "none" });
      jQuery(".radioshow").css({ display: "none" });
      jQuery(".chrshow").css({ display: "none" });
      jQuery(".formatshow").css({ display: "none" });
      jQuery(".checkshow").css({ display: "none" });
    } else {
      jQuery(".radioshow").css({ display: "none" });
      jQuery(".fileuploadshow").css({ display: "none" });
      jQuery(".chrshow").css({ display: "block" });
      jQuery(".checkshow").css({ display: "none" });
      jQuery(".formatshow").css({ display: "none" });
    }
  });

  jQuery("#cat").on("change", function () {
    var code = jQuery(this).val();
    jQuery.ajax({
      type: "POST",
      url: "ajax.php?do=subcat",
      data: { code: code },
      dataType: "html",
      success: function (data) {
        jQuery("#subview").html(data);
        jQuery("#run").show();
      },
    });
    return false;
  });

  jQuery("#run").on("click", function () {
    var code = jQuery(this).parent().find("#subview").val();
    jQuery.ajax({
      type: "POST",
      url: "ajax.php?do=showfestival",
      data: { code: code },
      dataType: "html",
      success: function (data) {
        jQuery("#report_result").html(data);
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
