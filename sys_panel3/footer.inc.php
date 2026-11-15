</div>
<footer class="content-footer footer bg-footer-theme">
</footer>
<div class="content-backdrop fade"></div>
</div>
</div>
</div>
<div class="layout-overlay layout-menu-toggle"></div>
</div>

<script src="asset/vendor/libs/popper/popper.js"></script>
<script src="asset/vendor/js/bootstrap.js"></script>
<script src="asset/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="asset/vendor/js/menu.js"></script>
<script src="asset/vendor/libs/apex-charts/apexcharts.js"></script>
<script src="asset/js/main.js"></script>
<script src="asset/js/dashboards-analytics.js"></script>
<script src="asset/js/select2.min.js"></script>
<script async defer src="https://buttons.github.io/buttons.js"></script>
<script src="asset/js/persianDatepicker.js"></script>
<script src="asset/js/parsley.js"></script>
<script src="asset/js/i18n/fa.js"></script>
<script src="asset/js/custom-new.js?ver=<?= rand(10, 1000) ?>"></script>
<script type="text/javascript">
    $(function() {
        $(".select2").select2();
        $(".datepicker").persianDatepicker({
            cellWidth: 38,
            cellHeight: 30,
            fontSize: 12,
        });
        $('.select2ajax').select2({
            placeholder: 'Select an item',
            ajax: {
                url: 'ajax.php?do=select2ajax',
                dataType: 'json',
                delay: 250,
                data: function(data) {
                    var thissection = $(this).data("name");
                    return {
                        searchTerm: data.term,
                        section: thissection
                    };
                },
                processResults: function(response) {
                    return {
                        results: response
                    };
                },
                cache: true
            }
        });
    });
</script>


<?php
echo '<script src="asset/editor/tinymce/tinymce.min.js"></script>';

?>
<script type="text/javascript">
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
</script>
<script type="text/javascript">
    $(function() {
        $("input[name='fld5']").change(function() {
            if ($(this).val() == "other") {
                $("#other").show();
            } else {
                $("#other").hide();
                $("#other").val('');
            }

        });
        $("input[name='fld63']").change(function() {
            if ($(this).val() == "other") {
                $("#other").show();
            } else {
                $("#other").hide();
                $("#other").val('');
            }

        });

        $("input[name='fld19']").change(function() {
            if ($(this).val() == "other2") {
                $("#other2").show();
            } else {
                $("#other2").hide();
                $("#other2").val('');
            }
        });

        $("input[name='fld77']").change(function() {
            if ($(this).val() == "other2") {
                $("#other2").show();
            } else {
                $("#other2").hide();
                $("#other2").val('');
            }
        });

        $("input[name='type']").change(function() {
            if ($(this).val() == "2") {
                $("#expiredate").show();
            } else {
                $("#expiredate").hide();
                $("#expiredate").val('');
            }
        });

        $(".datepicker").persianDatepicker({
            cellWidth: 38,
            cellHeight: 30,
            fontSize: 12,
        });

        $("[data-mask]").inputmask();
        $("[data-mask]").css("direction", "ltr");
        var $sections = $('.form-section');

        function navigateTo(index) {
            // Mark the current section with the class 'current'
            $sections
                .removeClass('current')
                .eq(index)
                .addClass('current');
            // Show only the navigation buttons that make sense for the current section:
            $('.form-navigation .previous').toggle(index > 0);
            var atTheEnd = index >= $sections.length - 1;
            $('.form-navigation .next').toggle(!atTheEnd);
            $('.form-navigation [type=submit]').toggle(atTheEnd);
        }

        function curIndex() {
            // Return the current index by looking at which section has the class 'current'
            return $sections.index($sections.filter('.current'));
        }

        // Previous button is easy, just go back
        $('.form-navigation .previous').click(function() {
            navigateTo(curIndex() - 1);
        });

        // Next button goes forward iff current block validates
        $('.form-navigation .next').click(function() {
            if ($('#register').parsley().validate({
                    group: 'block-' + curIndex()
                }))
                navigateTo(curIndex() + 1);
        });

        // Prepare sections by setting the `data-parsley-group` attribute to 'block-0', 'block-1', etc.
        $sections.each(function(index, section) {
            $(section).find(':input').attr('data-parsley-group', 'block-' + index);
        });
        navigateTo(0); // Start at the beginning
    });
</script>
</body>

</html>