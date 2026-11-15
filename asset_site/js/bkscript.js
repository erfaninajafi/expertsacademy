$(document).ready(function() {    

$(".convert").bind("keydown input",function() {
                    var $value = $(this).val();
                       var newValue="";
                        for (var i=0;i<$value.length;i++)
                        {
                            var ch=$value.charCodeAt(i);
                            if (ch>=1776 && ch<=1785)
                            {
                                // european digit range
                                var newChar=ch-1728    ;
                                newValue=newValue+String.fromCharCode(newChar);
                            }
                            else
                                newValue=newValue+String.fromCharCode(ch);
                        }
                            $(this).val(newValue);
                        
                });

    $("img").addClass("img-responsive");
 // dialog close
    $('.dialog .close').click(function() {
        $(this).parent().fadeOut(500);
        return false;
    });
// favorit ajax
$(".heart").click(function() {
var code = $(this).attr("data-code");               
        $.ajax({
        type: "POST",
        url: "http://www.0009.ir/send.php?do=favorite",
        context: this,
        data: {'code': code},
        dataType: 'html',
                success: function(data){
                    if(data === 'add')
                    {
                       $(this).addClass("heartactive");
                    }
                    else if(data === 'set')
                    {
                       $(this).addClass("heartactive");
                    }
                    else {
                        $(this).removeClass("heartactive");
                    }
                    var check = $('body').find('.heartactive').length;
                    if(check === 0)
                    {
                       $(".icon-heart").removeClass("icon-heart3"); 
                    }
                    else {
                        $(".icon-heart").addClass("icon-heart3");
                    }

                }
        });

    return false;
    });
                 
            $("#login").click(function() {
        
            var username = $("#username").val();
            var password = $("#passwordlogin").val();
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/igm;
            if(username == '')
                {
                    $('.show_msg').text("ایمیل خود را صحیح وارد نمایید");
                }
                if(password == '')
                {
                    $('.show_msg').text("پسورد خود را وارد نمایید");
                }
                else if (!re.test(username)) 
                {
                    $('.show_msg').text("ایمیل خود را صحیح وارد نمایید");
                }
            else
                {   
                    $.ajax({
                    type: "POST",
                    url: "http://www.0009.ir/send.php?do=login_ajax",
                    data: {'username': username,
                           'password' : password},
                            success: function(data){
                                if(data == 'error')
                                {
                                   $('.show_msg').text("کاربری با این مشخصات پیدا نشد"); 
                                }
else if(data == 'error2')
                                {
                                   $('.show_msg').text("حساب شما تایید نشده است"); 
                                   window.location.href = 'http://www.0009.ir/account/verify/';
                                }
                                else {
                                    window.location.href = 'http://www.0009.ir/mypanel/';
                                }
                            }
                    });
                }
                return false;
                });
                
                
                $("#forgot").click(function() {
        
            var email = $("#email").val();
            var lng = $("#lng").val();
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/igm;
            if(email=='')
                {
                    $('.show_msg').text("ایمیل خود را صحیح وارد نمایید");
                }
                else if (!re.test(email)) 
                {
                    $('.show_msg').text("ایمیل خود را صحیح وارد نمایید");
                }
            else
                {   
                    $.ajax({
                    type: "POST",
                    url: "http://www.0009.ir/send.php?do=forgotpop",
                    data: {'email': email,'lng':lng},
                            success: function(data){
                                if(data == 'error')
                                {
                                   $('.show_msg').text("نام کاربری یا کلمه عبور اشتباه است"); 
                                }
                                else {
                                    $('.show_msg').text("کلمه عبور جدید برای شما ارسال گردید");
window.location.href = 'http://www.0009.ir/account/login/';
                                }
                            }
                    });
                }
                return false;
                });
                
                
    $("#openlogin").on("click" , function () {
       $("#ajax_box").css("display","table"); 
    });
    $(".close_form").on("click" , function () {
       $("#ajax_box").hide(); 
    });
    $('.forgot').on("click" , function () {
       $(".login_field").hide();
       $(".forgotpassword").show();
    });
    $('.backlogin').on("click" , function () {
       $(".login_field").show();
       $(".forgotpassword").hide();
    });
    
    $("#open_city").on("click" , function () {
       $("#citylist").css("display","table"); 
    });
    $(".close_form").on("click" , function () {
       $("#citylist").hide(); 
    });
    $("#open_city2").on("click" , function () {
       $("#citylist").css("display","table"); 
    });
    $("#open_report").on("click" , function () {
       $("#report").css("display","table");
       $(".close_form").on("click" , function () {
       $("#report").hide(); 
         });
    });
    
    
    
$("#error").delay(3200).fadeOut(1200);
               
        $('body').on('click', '#upload', function(e){
        e.preventDefault();
        var formData = new FormData($(this).parents('form')[0]);

        $.ajax({
            url: 'http://www.0009.ir/send.php?do=upload',
            type: 'POST',
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                return myXhr;
            },
            success: function (data) {
                alert("Data Uploaded: "+data);
            },
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        });
            return false;
        });
        
        $("#cat").on("change",function (){
           var code = $(this).val();
           console.log(code);
            $.ajax({
            type: "POST",
            url: 'http://www.0009.ir/send.php?do=subcat',
            data: {'code': code},
            dataType: 'html',
            success: function (data) {
                $("#subview").html(data);
            }
        });
            return false;
        });


  $("#state").on("change",function (){
           var code = $(this).val();
            $.ajax({
            type: "POST",
            url: 'http://www.0009.ir/send.php?do=state',
            data: {'code': code},
            dataType: 'html',
            success: function (data) {
                $("#city").html(data);
            }
        });
            return false;
        });
        
        $("#city").on("change",function (){
           var code = $(this).val();
            $.ajax({
            type: "POST",
            url: 'http://www.0009.ir/send.php?do=city',
            data: {'code': code},
            dataType: 'html',
            success: function (data) {
                $("#area").html(data);
            }
        });
            return false;
        });


       $('.btn-default').filter('[for="filestyle-5"]').addClass('red');
       $('.btn-default').filter('[for="filestyle-5"]').children("span:first").addClass('icon-video2');


// check img
   $('.file-img').each(function(){
       
       var thisimg = $(this).find("img").attr("id");
       var thisspan = $(this).find("span:last").attr("class");
       var thisinput = $(this).find("input:last");
       
        $("."+thisspan).on('click',function() {
        $(this).parent().find("img").attr("src",'').hide();
        thisinput.val('');
        $("."+thisspan).hide();
        });
         $(this).find('INPUT[type="file"]').change(function () {
                var size = this.files[0].size;
                var ext = this.value.match(/\.(.+)$/)[1];
                
                if(size > 400000)
                {
                    alert('سایز فایل شما باید حداکثر ۴۰۰ کیلو بایت باشد');
                        $("#"+thisimg).hide();
                        $("."+thisspan).hide();
                        thisinput.val('');
                        this.value = '';
                    return;
                }
                switch (ext) {
                    case 'jpg':
                    case 'jpeg':
                    case 'JPG':
                    case 'JPEG':
//                        $('#submit_item').attr('disabled', false);
                        break;
                        default:
                        alert('لطفا فقط تصاویر با فرمت jpg انتخاب کنید');
                        $(this).val('');
                        $("#"+thisimg).hide();
                        $("."+thisspan).hide();
                        thisinput.val('');
                        this.value = '';
                    }
                });
        });
        
 // check film     
        $('.file-film').each(function(){
       var thisimg = $(this).find("img").attr("id");
       var thisspan = $(this).find("span:last").attr("class");
       var thisinput = $(this).find("input:last");
       
        $('.'+thisspan).on('click',function() {
        $(this).parent().find("img").hide();
        thisinput.val('');
        $('.'+thisspan).hide();
        
        });
         $(this).find('INPUT[name="film"]').change(function () {
                var size = this.files[0].size;
                var ext = this.value.match(/\.(.+)$/)[1];
                if(size > 3000000)
                {
                    alert('سایز فایل شما باید حداکثر ۳ مگا بایت باشد');
                        $("#"+thisimg).hide();
                        $("."+thisspan).hide();
                        thisinput.val('');
                        this.value = '';
                    return;
                }
                switch (ext) {
                    case 'mp4':
                    case 'MP4':
                        break;
                        default:
                        alert('لطفا فقط فیلم با فرمت mp4 انتخاب کنید');
                        $(this).val('');
                        $("#"+thisimg).hide();
                        $("."+thisspan).hide();
                        thisinput.val('');
                        this.value = '';
                    }
                });
        });
        
// add item

   $("#myForm").submit(function (e) {
       e.preventDefault();
       var formData = new FormData($(this)[0]);
   $.ajax({
        url: 'http://www.0009.ir/send.php?do=add',
        type: 'POST',
        data: formData,
        async: false,
        dataType: "JSON",
    beforeSend: function(){
     
      $('.ajax_data').show();
   },
    complete: function() {
        $('.ajax_data').hide();
        window.location.href ="http://www.0009.ir/panel/history/??";
    },
        cache: false,
        contentType: false,
        processData: false
    });

    return false;
   });    
        
// item renew

    $('.label-success').on("click" , function () {
        var code = $(this).closest('tr').prop('id');
        $.ajax({
            type: "POST",
            url: 'http://www.0009.ir/send.php?do=renew',
            data: {'code': code},
            dataType: 'html',
            success: function (data) {
                var arr = data.split('-');
                $("#"+code).find('.label-success').hide();
                $("#"+code).find('.status').html(arr[0]);
                $("#"+code).find('.expire').html(arr[1]);
            }
        });
            return false;
            
    });
    
// item hide 

    $('.label-warning').on("click" , function () {
        var code = $(this).closest('tr').prop('id');
        $.ajax({
            type: "POST",
            url: 'http://www.0009.ir/send.php?do=hide',
            data: {'code': code},
            dataType: 'html',
            success: function (data) {
                var arr = data.split('-');
                $("#"+code).find('.status').html(arr[0]);
                $("#"+code).find('.label-warning').html(arr[1]);

            }
        });
            return false;  
    });
    
    
    
// delete image 

    $('.del').on("click" , function () {
        var imgname = $(this).attr('data-img');
        var datacode = $(this).attr('data-code');
        if (confirm("آیا مطمئن هستید میخواهید این تصویر را حذف نمایید ؟")){
        $.ajax({
            type: "POST",
            url: 'http://www.0009.ir/send.php?do=deletefile',
            data: {'imgname': imgname , 'datacode' : datacode},
            dataType: 'html',
            success: function (data) {
                
                window.location.href = data;
            }
        });
            return false;  
            } 
    });
    
// delete item 

    $('.label-danger').on("click" , function () {
        if (confirm("آیا مطمئن هستید میخواهید این آیتم را حذف نمایید ؟")){
        var code = $(this).closest('tr').prop('id');
        $.ajax({
            type: "POST",
            url: 'http://www.0009.ir/send.php?do=delete',
            data: {'code': code},
            dataType: 'html',
            success: function () {
                window.location.href ="http://www.0009.ir/panel/history/";
            }
        });
            return false; 
            }
    });
    
    
    $('.video-show').on("click" , function (){
        
        var code = $(this).attr("data-code");
        $.ajax({
            type: "POST",
            url: 'http://www.0009.ir/send.php?do=video',
            data: {'code': code},
            dataType: 'html',
            success: function (data) {
                $("#video").css("display","table");
                $(".centered-content").css({"text-align":"center","background":"none"}).html(data);
            }
        });
            return false;
            
        
    });
    
    $(".close_form").on ("click" , function () {
        $('#video').css("display","none");
    }); 
    
    
// show video 
    $(".show-film").on ("click" , function () {
        $('.bx-wrapper').css("display","none");
        $('.video-system').css("display","table");
    });  
    
    
// hide video 
    $(".video-close").on ("click" , function () {
        $('.bx-wrapper').css("display","table");
        $('.video-system').css("display","none");
    }); 
// item update 
    
    $("#Formupdate").submit(function (e) {
       e.preventDefault();
       var formData = new FormData($(this)[0]);
        $.ajax({
        url: 'http://www.0009.ir/send.php?do=update',
        type: 'POST',
        data: formData,
        async: false,
        dataType: "JSON",
    beforeSend: function(){
     
      $('.ajax_data').show();
   },
    complete: function() {
        $('.ajax_data').hide();
        window.location.href ="http://www.0009.ir/panel/history/??";
    },
        cache: false,
        contentType: false,
        processData: false
    });

    return false;
   });
         
    
$('#search_title').autocomplete({
    source: function( request, response ) {
            $.ajax({
                    url : 'http://www.0009.ir/send.php?do=ajaxsearch',
                    type: "POST",
                    dataType: "json",
                            data: {
                               keyword: request.term,
                               type: 'title'
                            },
                             success: function(data) {
                                     response( $.map( data, function( item ) {
                                            return {
                                                    label: item,
                                                    value: item
                                            }
                                    }));
                            }
            });
    },
    autoFocus: true,
    minLength: 0      	
  });
          
    
    $("#sendreport").submit(function (e) {
       e.preventDefault();
       var formData = new FormData($(this)[0]);
        $.ajax({
             url: 'http://www.0009.ir/ajax.php?do=sendreport',
             type: 'POST',
             data: formData,
             processData: false,
             contentType: false,
             dataType: "html",  
         success: function(data) {
             $(".centered-content").html(data); 
         }
         });

         return false;
        });
                      
    });
    
     
    $(window).scroll(function(){
	scrollMore();
});

function scrollMore(){
	if($(window).scrollTop() == ($(document).height() - $(window).height())){

	var offset = $('[id^="myData_"]').length; // here we find out total records on page.
	var records = $(".allData").text();
        if($(".allData").attr("data-section") != '')
        {
            var datasection = $(".allData").attr("data-section");
        }
        else {
            var datasection = '';
        }
        if($(".allData").attr("data-value") != '' )
        {
            var datavalue = $(".allData").attr("data-value");
        }
        else {
            var datavalue = '';
        }
	$(window).unbind("scroll");
		if(records != offset){
			$("#loaderImg").html('<img src="http://www.0009.ir/asset/images/ajax-loader.gif" />');
			$("#loaderImg").show();
			loadMoreData(offset,datasection,datavalue);
		}
	}
}

function loadMoreData(offset,datasection,datavalue){
	$.ajax({
		type: 'get',
		async:false,
		url: 'http://www.0009.ir/ajax.php?do=getdata',
		data: 'offset='+offset+'&section='+datasection+'&value='+datavalue,
		success: function(data){
			$("#loaderImg").empty();
			$("#loaderImg").hide();
			$(".content_ajax").append(data);
		},
		error: function(data){
			alert("خطا در سیستم"+data);
		}
	}).done(function(){
		$(window).bind("scroll",function(){
		scrollMore();	
	});
	});
}
function postRefId(refIdValue) {
			var form = document.createElement("form");
			form.setAttribute("method", "POST");
			form.setAttribute("action", "https://bpm.shaparak.ir/pgwchannel/startpay.mellat");         
			form.setAttribute("target", "_self");
			var hiddenField = document.createElement("input");              
			hiddenField.setAttribute("name", "RefId");
			hiddenField.setAttribute("value", refIdValue);
			form.appendChild(hiddenField);

			document.body.appendChild(form);         
			form.submit();
			document.body.removeChild(form);
		}