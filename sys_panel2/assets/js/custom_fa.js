function projectUrl(path) {
  var base = /^\/expertsacademy(?:\/|$)/.test(window.location.pathname)
    ? "/expertsacademy/"
    : "/";
  return base + path.replace(/^\/+/, "");
}

$(document).ready(function () {


  var star = $("#rating_star").attr("value");
  $("#rating_star_show").codexworld_rating_widget({ starLength: "5", initialValue: star, imageDirectory: "assets/img", inputAttr: "name" });

  $(".checkcodefile").keyup(function (e) {
    var title = $(this).val();
    var code = jQuery(this).data("code");
    $.post('ajax.php?do=checkcodefile', { 'title': title,'code':code}, function (data) {
      $(".checkcodefileresult").html(data);
    });
});


  $('.selectfile').change(function(){
    var thisId = $(this).attr("data");
    var thisVal = this.value;
    var changeFileName = thisVal.replace(/^.*[\\\/]/, '');

var filename = getFileName(thisVal);


    //alert(this.files[0].name);
    // var result=$(a).text().split("/");
    $('#'+thisId).val(this.files[0].name);
});

  $(".select2").select2();

  $(".selectmulti").select2({
    multiple: true,
});

  Array.prototype.shuffle = function () {
    let m = this.length,
      i;
    while (m) {
      i = (Math.random() * m--) >>> 0;
      [this[m], this[i]] = [this[i], this[m]];
    }
    return this;
  };
  $("#select_all").on("click", function () {
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

  $("select[name='model']").on("change", function () {
    var val = $(this).val();
    $.ajax({
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

  $("#formwork > .sendstatus").click(function (e) {
    e.preventDefault();
    //jQuery(this).attr("data-update");
    var userid = $(this).parent("form").find("#userid").val();
    var contract = $(this).parent("form").find("#contract").val();
    var status = JSON.stringify(
      $(this).parent("form").find(".status").val()
    );
    $.ajax({
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


  $("#selectmentor > .sendmentor").click(function (e) {
    e.preventDefault();
    //jQuery(this).attr("data-update");
    var userid = $(this).parent("form").find("#userid").val();
    var contract = $(this).parent("form").find("#contract").val();
    var mentor = $(this).parent("form").find(".mentor").val();
    $.ajax({
      type: "POST",
      url: "ajax.php?do=addmentor",
      data: {
        mentor: mentor,
        userid: userid,
        contract: contract,
      },
      success: function (data) {
        alert(data);
      },
    });
    return false;
  });


  $(".plus-add").click(function () {
    var content = $(".price_input").html();
    $(".result").append(content);
  });

  $(".delete-item").click(function () {
    var dataId = $(this).attr("data");
    $("#d" + dataId).html("");
  });


  $(".checkmentor").on("click", function (event) {
    event.preventDefault();
    var mentor = $("select[name='mentor'").val();
    var contract = $("input[name='contract'").val();
    
    $.ajax({
      type: "POST",
      url: projectUrl("sys_panel2/ajax.php?do=checkmentor"),
      data: { mentor: mentor,contract:contract },
      dataType: "html",
      success: function (data) {
        $(".ajaxresult").html(data);
      },
    });
  });


  $("input[name='specialmohtava']").on("change", function (event) {
    event.preventDefault();
    var val = $(this).val();
    $.ajax({
      type: "POST",
      url: projectUrl("sys_panel2/ajax.php?do=specialmohtava"),
      data: { val: val },
      dataType: "html",
      success: function (data) {
        alert(data);
      },
    });
  });


  $(".checkboxajax").on("click", function (event) {
    event.preventDefault();
    var dataName = $(this).data("name");
    var dataId = $(this).data("id");
    $.ajax({
      type: "POST",
      url: projectUrl("sys_panel2/ajax.php?do=checkbox"),
      data: { dataName: dataName, dataId: dataId },
      dataType: "html",
      success: function (data) {
        alert(data);
      },
    });
  });

  $(".selectcontent").on("click", function (event) {
    event.preventDefault();
    $(".result").html("");
    var title = $("input[name='title'").val();
    var ghaleb = $("select[name='ghaleb'").val();
    var department = $("select[name='department'").val();
    var creator = $("select[name='creator'").val();
    var producer = $("select[name='producer'").val();
    $.ajax({
      type: "POST",
      url: projectUrl("sys_panel2/ajax.php?do=selectcontent"),
      data: { title: title, ghaleb: ghaleb,department:department,creator:creator,producer:producer },
      dataType: "html",
      success: function (data) {
        $(".result").html(data);
      },
    });
  });



  $(".contractstatus").on("change", function () {
    var val = $(this).val();
    $.ajax({
      type: "POST",
      url: projectUrl("sys_panel2/ajax.php?do=contractstatus"),
      data: {
        val: val,
      },
      success: function (data) {
        alert(data);
      },
    });
    return false;
  });





  $(".ajaxinterview").on("click", function (event) {
    event.preventDefault();
    var contract = $(this).data("contract");
    var code = $(this).data("code");
    var lng = $(this).data("lng");
    $.ajax({
      type: "POST",
      url: projectUrl("sys_panel2/ajax.php?do=interview"),
      data: { contract: contract, code: code, lng : lng },
      dataType: "html",
      success: function (data) {
        $(".ajaxresult").html(data);
  
      },
    });
  });


$(".ajaxlink").on("click", function (event) {
  event.preventDefault();
  var contract = $(this).data("contract");
  var lng = $(this).data("lng");
  $.ajax({
    type: "POST",
    url: projectUrl("sys_panel2/ajax.php?do=contract"),
    data: { contract: contract, lng : lng },
    dataType: "html",
    success: function (data) {
      $(".ajaxresult").html(data);

    },
  });
});


$(".ajaxchecklist").on("click", function (event) {
  event.preventDefault();
  var contract = $(this).data("contract");
  var lng = $(this).data("lng");
  $.ajax({
    type: "POST",
    url: projectUrl("sys_panel2/ajax.php?do=ajaxchecklist"),
    data: { contract: contract, lng : lng },
    dataType: "html",
    success: function (data) {
      $(".ajaxresult").html(data);

    },
  });
});


$(".ajaxkarfarma").on("click", function (event) {
  event.preventDefault();
  var contract = $(this).data("contract");
  var lng = $(this).data("lng");
  $.ajax({
    type: "POST",
    url: projectUrl("sys_panel2/ajax.php?do=karfarma"),
    data: { contract: contract, lng : lng },
    dataType: "html",
    success: function (data) {
      $(".ajaxresult").html(data);

    },
  });
});


$("select[name='file']").on("change", function (event) {
  event.preventDefault();
  var val = $(this).val();
  $(".showfile").html("<a href=shenasname.php?do=view&code="+val+" target=_blank>شناسنامه فایل</a>");
});
$("select[name='class']").on("change", function (event) {
  event.preventDefault();
  var val = $(this).val();
  $(".showfile").html("<a href=courseparent.php?code="+val+" target=_blank>نمایش دوره</a>");
});

$(".ajaxkarfarmamenti").on("click", function (event) {
  event.preventDefault();
  var contract = $(this).data("contract");
  var lng = $(this).data("lng");
  var skill = $(this).data("skill");
  $.ajax({
    type: "POST",
    url: projectUrl("sys_panel2/ajax.php?do=ajaxkarfarmamenti"),
    data: { contract: contract, lng : lng, skill : skill },
    dataType: "html",
    success: function (data) {
      $(".ajaxresult").html(data);

    },
  });
});


  $("#savesetting").on("click", function (event) {
    event.preventDefault();
    var color = $("input[name='theme-color']").val();

    if ($("input[name='fluid']").prop("checked")) {
      fluid = "1";
    } else {
      fluid = "0";
    }

    var navbarStyle = $("input[name='navbarStyle']").val();
    $.ajax({
      type: "POST",
      url: projectUrl("sys_panel2/ajax.php?do=setting"),
      data: { color: color, fluid: fluid, navbarStyle: navbarStyle },
      dataType: "html",
      success: function (data) {
        $(".setting-error").show();
        $(".setting-error").delay(2000).fadeOut("slow");
      },
    });
  });

  $(".datepicker").persianDatepicker({
    cellWidth: 38,
    cellHeight: 30,
    fontSize: 12,
  });


  $(".shayestegi").on("click", function () {
    var contract = $(this).attr("data-contract");
    var skill = $(this).attr("data-skill");
    var lng = $(this).attr("data-lng");
    var user = $(this).attr("data-user");
    $(".modal-body").html("");
    return (
      $.ajax({
        type: "POST",
        url: projectUrl("sys_panel2/ajax.php?do=shayestegi"),
        data: { contract: contract ,skill:skill, lng:lng ,user:user},
        dataType: "html",
        success: function (data) {
          $(".modal-body").html(data);
          $("#result").modal("show");
        },
      }),
      !1
    );
  });


  $(".timelinecontent").on("click", function () {
    var contract = $(this).attr("data-contract");
    var skill = $(this).attr("data-skill");
    var lng = $(this).attr("data-lng");
    var user = $(this).attr("data-user");
    $(".modal-body").html("");
    return (
      $.ajax({
        type: "POST",
        url: projectUrl("sys_panel2/ajax.php?do=timeline"),
        data: { contract: contract ,skill:skill, lng:lng ,user:user},
        dataType: "html",
        success: function (data) {
          $(".modal-body").html(data);
          $("#result").modal("show");
        },
      }),
      !1
    );
  });


  $(".description").on("click", function () {
    var val = $(this).attr("data-value");
    $(".modal-body").html("");
    return (
      $.ajax({
        type: "POST",
        url: projectUrl("sys_panel2/ajax.php?do=description"),
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


  $(".item-info").on("click", function () {
    var val = $(this).attr("data-value");
    $(".modal-body").html("");
    return (
      $.ajax({
        type: "POST",
        url: projectUrl("sys_panel2/ajax.php?do=description"),
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

  $(".item-info-interview").on("click", function () {
    var val = $(this).attr("data-value");
    $(".modal-body").html("");
    return (
      $.ajax({
        type: "POST",
        url: projectUrl("sys_panel2/ajax.php?do=desinterview"),
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



  $(".circle_percent").each(function () {
    var $this = $(this),
      $dataV = $this.data("percent"),
      $dataDeg = $dataV * 3.6,
      $round = $this.find(".round_per");
    $round.css("transform", "rotate(" + parseInt($dataDeg + 180) + "deg)");
    $this.append(
      '<div class="circle_inbox"><span class="percent_text"></span></div>'
    );
    $this.prop("Counter", 0).animate(
      { Counter: $dataV },
      {
        duration: 2000,
        easing: "swing",
        step: function (now) {
          $this.find(".percent_text").text(Math.ceil(now) + "");
        },
      }
    );
    if ($dataV >= 51) {
      $round.css("transform", "rotate(" + 360 + "deg)");
      setTimeout(function () {
        $this.addClass("percent_more");
      }, 1000);
      setTimeout(function () {
        $round.css("transform", "rotate(" + parseInt($dataDeg + 180) + "deg)");
      }, 1000);
    }
  });

  $("#imageUpload").change(function () {
    readURL(this);
  });
});

function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      $("#imagePreview").css(
        "background-image",
        "url(" + e.target.result + ")"
      );
      $("#imagePreview").hide();
      $("#imagePreview").fadeIn(650);
    };
    reader.readAsDataURL(input.files[0]);
  }
}

var controls = [
  "play",
  "progress",
  "captions",
  "mute",
  "volume",
  "airplay",
  "fullscreen",
  "settings",
];
//var setting = ['captions', 'quality', 'speed', 'loop'];
const players = Plyr.setup("video", {
  controls,
});

const player = new Plyr("audio", {});

window.player = player;


tinymce.init({
  selector: ".editor",
  language: 'fa',
  directionality: 'rtl',
  // width : 850 ,
  height: 250,
  content_css: "asset/css/custom_content.css?ver=107",
  convert_urls: false,
  plugins: [
      "advlist autolink lists link image charmap print preview hr anchor pagebreak",
      "searchreplace wordcount visualblocks visualchars code fullscreen",
      "insertdatetime media nonbreaking save table contextmenu directionality",
      "emoticons template paste textcolor colorpicker textpattern"
  ],
  toolbar1: "insertfile undo redo | styleselect | bold italic | bullist numlist | link image | print preview media | forecolor backcolor emoticons",
  toolbar2: "alignleft aligncenter alignright alignjustify",
  image_advtab: true,
  templates: [{
          title: 'Test template 1',
          content: 'Test 1'
      },
      {
          title: 'Test template 2',
          content: 'Test 2'
      }
  ]
});

function promptFile(contentType, multiple) {
  var input = document.createElement("input");
  input.type = "file";
  input.multiple = multiple;
  input.accept = contentType;
  return new Promise(function(resolve) {
    document.activeElement.onfocus = function() {
      document.activeElement.onfocus = null;
      setTimeout(resolve, 500);
    };
    input.onchange = function() {
      var files = Array.from(input.files);
      if (multiple)
        return resolve(files);
      resolve(files[0]);
    };
    input.click();
  });
}
function promptFilename(a) {
  promptFile().then(function(file) {
    document.querySelector("#"+a).innerText = file && file.name || "no file selected";
  });
}

function getFileName(file) {
  if (file) {
    // جدا کردن هر چیزی که بین آخرین / و قبل از آخرین . است
    var startIndex = (file.indexOf('\\') >= 0 ? file.lastIndexOf('\\') : file.lastIndexOf('/'));
    var filename = file.substring(startIndex);
    if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
        filename = filename.substring(1);
    }
    return filename;
  }
  return "";
}

function processRating(val, attrVal) {
       $.ajax({
          type: "POST",
          url: projectUrl("ajax.php?do=rating"),
          data: "postID=" + attrVal + "&ratingPoints=" + val,
          success: function (data) {
              "ok" == data ? alert("رتبه شما به این فایل :  " + val) : alert("شما قبلا به این فایل امتیاز داده اید!");
          },
      });
}