<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <title>登录-领导干部听课平台</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport' />
    
    <!--[if lt IE 9]>
    <script src='/jwcld/Public/assets/javascripts/html5shiv.js' type='text/javascript'></script>
    <![endif]-->
    <link href='/jwcld/Public/assets/stylesheets/bootstrap/bootstrap.css' media='all' rel='stylesheet' type='text/css' />
    <link href='/jwcld/Public/assets/stylesheets/bootstrap/bootstrap-responsive.css' media='all' rel='stylesheet' type='text/css' />
    <!-- / jquery ui -->
    <link href='/jwcld/Public/assets/stylesheets/jquery_ui/jquery-ui-1.10.0.custom.css' media='all' rel='stylesheet' type='text/css' />
    <link href='/jwcld/Public/assets/stylesheets/jquery_ui/jquery.ui.1.10.0.ie.css' media='all' rel='stylesheet' type='text/css' />
    <!-- / switch buttons -->
    <link href='/jwcld/Public/assets/stylesheets/plugins/bootstrap_switch/bootstrap-switch.css' media='all' rel='stylesheet' type='text/css' />
    <!-- / xeditable -->
    <link href='/jwcld/Public/assets/stylesheets/plugins/xeditable/bootstrap-editable.css' media='all' rel='stylesheet' type='text/css' />
    <link href='/jwcld/Public/assets/stylesheets/plugins/common/bootstrap-wysihtml5.css' media='all' rel='stylesheet' type='text/css' />
    <!-- / wysihtml5 (wysywig) -->
    <link href='/jwcld/Public/assets/stylesheets/plugins/common/bootstrap-wysihtml5.css' media='all' rel='stylesheet' type='text/css' />
    <!-- / jquery file upload -->
    <link href='/jwcld/Public/assets/stylesheets/plugins/jquery_fileupload/jquery.fileupload-ui.css' media='all' rel='stylesheet' type='text/css' />
    <!-- / full calendar -->
    <link href='/jwcld/Public/assets/stylesheets/plugins/fullcalendar/fullcalendar.css' media='all' rel='stylesheet' type='text/css' />
    <!-- / select2 -->
    <link href='/jwcld/Public/assets/stylesheets/plugins/select2/select2.css' media='all' rel='stylesheet' type='text/css' />
    <!-- / mention -->
    <link href='/jwcld/Public/assets/stylesheets/plugins/mention/mention.css' media='all' rel='stylesheet' type='text/css' />
    <!-- / tabdrop (responsive tabs) -->
    <link href='/jwcld/Public/assets/stylesheets/plugins/tabdrop/tabdrop.css' media='all' rel='stylesheet' type='text/css' />
    <!-- / jgrowl notifications -->
    <link href='/jwcld/Public/assets/stylesheets/plugins/jgrowl/jquery.jgrowl.min.css' media='all' rel='stylesheet' type='text/css' />
    <!-- / datatables -->
    <link href='/jwcld/Public/assets/stylesheets/plugins/datatables/bootstrap-datatable.css' media='all' rel='stylesheet' type='text/css' />
    <!-- / dynatrees (file trees) -->
    <link href='/jwcld/Public/assets/stylesheets/plugins/dynatree/ui.dynatree.css' media='all' rel='stylesheet' type='text/css' />
    <!-- / color picker -->
    <link href='/jwcld/Public/assets/stylesheets/plugins/bootstrap_colorpicker/bootstrap-colorpicker.css' media='all' rel='stylesheet' type='text/css' />
    <!-- / datetime picker -->
    <link href='/jwcld/Public/assets/stylesheets/plugins/bootstrap_datetimepicker/bootstrap-datetimepicker.min.css' media='all' rel='stylesheet' type='text/css' />
    <!-- / daterange picker) -->
    <link href='/jwcld/Public/assets/stylesheets/plugins/bootstrap_daterangepicker/bootstrap-daterangepicker.css' media='all' rel='stylesheet' type='text/css' />
    <!-- / flags (country flags) -->
    <link href='/jwcld/Public/assets/stylesheets/plugins/flags/flags.css' media='all' rel='stylesheet' type='text/css' />
    <!-- / slider nav (address book) -->
    <link href='/jwcld/Public/assets/stylesheets/plugins/slider_nav/slidernav.css' media='all' rel='stylesheet' type='text/css' />
    <!-- / fuelux (wizard) -->
    <link href='/jwcld/Public/assets/stylesheets/plugins/fuelux/wizard.css' media='all' rel='stylesheet' type='text/css' />
    <!-- / flatty theme -->
    <link href='/jwcld/Public/assets/stylesheets/light-theme.css' id='color-settings-body-color' media='all' rel='stylesheet' type='text/css' />
    <!-- / demo -->
    <link href='/jwcld/Public/assets/stylesheets/demo.css' media='all' rel='stylesheet' type='text/css' />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body class='contrast-red sign-in contrast-background'>
<div id='wrapper'>
    <div class='application'>
        <div class='application-content'>
            <a href="sign_in.html">
                <span>领导干部听课平台</span>
            </a>
        </div>
    </div>
    <div class='controls'>
        <div class='caret'></div>
        <div class='form-wrapper' style='height: 250px;'>
            <h1 class='text-center'>登录&nbsp;&nbsp;|&nbsp;&nbsp;Sign in</h1>
            <div class="span4" style="height:20px;"></div>
            <div class="span4" style="margin:auto; text-align:center;">
                <a href="<?php echo U('Login/inside');?>" class="btn btn-success btn-large" style="width:150px;">校内用户</a>
            </div>
            <div class="span4" style="height:25px;"></div>
            <div class="span4" style="margin:auto; text-align:center;">
                <a href="<?php echo U('Login/outside');?>" class="btn btn-success btn-large" style="width:150px;">校外用户</a>
            </div>
        </div>
        <div class='row-fluid'>
            <div class="span12" style="text-align: center;">
                <p style="color:red; font-weight: bold; font-size: 16px;">为了最好使用效果，请使用IE9版本以上或者Chrome浏览器，同时关闭兼容性试图，谢谢~</p>
            </div>
        </div>
    </div>
    <div class='login-action text-center'>
        <a href="http://jwc.bnu.edu.cn/">
            <strong>北京师范大学教务处</strong>
        </a>
    </div>
</div>
<!-- / jquery -->
<script src='/jwcld/Public/assets/javascripts/jquery/jquery.min.js' type='text/javascript'></script>
<script src='/jwcld/Public/js/jquery.md5.js' type='text/javascript'></script>
<!-- / jquery mobile events (for touch and slide) -->
<script src='/jwcld/Public/assets/javascripts/plugins/mobile_events/jquery.mobile-events.min.js' type='text/javascript'></script>
<!-- / jquery migrate (for compatibility with new jquery) -->
<script src='/jwcld/Public/assets/javascripts/jquery/jquery-migrate.min.js' type='text/javascript'></script>
<!-- / jquery ui -->
<script src='/jwcld/Public/assets/javascripts/jquery_ui/jquery-ui.min.js' type='text/javascript'></script>
<!-- / bootstrap -->
<script src='/jwcld/Public/assets/javascripts/bootstrap/bootstrap.min.js' type='text/javascript'></script>
<script src='/jwcld/Public/assets/javascripts/plugins/flot/excanvas.js' type='text/javascript'></script>
<!-- / sparklines -->
<script src='/jwcld/Public/assets/javascripts/plugins/sparklines/jquery.sparkline.min.js' type='text/javascript'></script>
<!-- / flot charts -->
<script src='/jwcld/Public/assets/javascripts/plugins/flot/flot.min.js' type='text/javascript'></script>
<script src='/jwcld/Public/assets/javascripts/plugins/flot/flot.resize.js' type='text/javascript'></script>
<script src='/jwcld/Public/assets/javascripts/plugins/flot/flot.pie.js' type='text/javascript'></script>
<!-- / bootstrap switch -->
<script src='/jwcld/Public/assets/javascripts/plugins/bootstrap_switch/bootstrapSwitch.min.js' type='text/javascript'></script>
<!-- / fullcalendar -->
<script src='/jwcld/Public/assets/javascripts/plugins/fullcalendar/fullcalendar.min.js' type='text/javascript'></script>
<!-- / datatables -->
<script src='/jwcld/Public/assets/javascripts/plugins/datatables/jquery.dataTables.min.js' type='text/javascript'></script>
<script src='/jwcld/Public/assets/javascripts/plugins/datatables/jquery.dataTables.columnFilter.js' type='text/javascript'></script>
<!-- / wysihtml5 -->
<script src='/jwcld/Public/assets/javascripts/plugins/common/wysihtml5.min.js' type='text/javascript'></script>
<script src='/jwcld/Public/assets/javascripts/plugins/common/bootstrap-wysihtml5.js' type='text/javascript'></script>
<!-- / select2 -->
<script src='/jwcld/Public/assets/javascripts/plugins/select2/select2.js' type='text/javascript'></script>
<!-- / color picker -->
<script src='/jwcld/Public/assets/javascripts/plugins/bootstrap_colorpicker/bootstrap-colorpicker.min.js' type='text/javascript'></script>
<!-- / mention -->
<script src='/jwcld/Public/assets/javascripts/plugins/mention/mention.min.js' type='text/javascript'></script>
<!-- / input mask -->
<script src='/jwcld/Public/assets/javascripts/plugins/input_mask/bootstrap-inputmask.min.js' type='text/javascript'></script>
<!-- / fileinput -->
<script src='/jwcld/Public/assets/javascripts/plugins/fileinput/bootstrap-fileinput.js' type='text/javascript'></script>
<!-- / modernizr -->
<script src='/jwcld/Public/assets/javascripts/plugins/modernizr/modernizr.min.js' type='text/javascript'></script>
<!-- / retina -->
<script src='/jwcld/Public/assets/javascripts/plugins/retina/retina.js' type='text/javascript'></script>
<!-- / fileupload -->
<script src='/jwcld/Public/assets/javascripts/plugins/fileupload/tmpl.min.js' type='text/javascript'></script>
<script src='/jwcld/Public/assets/javascripts/plugins/fileupload/load-image.min.js' type='text/javascript'></script>
<script src='/jwcld/Public/assets/javascripts/plugins/fileupload/canvas-to-blob.min.js' type='text/javascript'></script>
<script src='/jwcld/Public/assets/javascripts/plugins/fileupload/jquery.iframe-transport.min.js' type='text/javascript'></script>
<script src='/jwcld/Public/assets/javascripts/plugins/fileupload/jquery.fileupload.min.js' type='text/javascript'></script>
<script src='/jwcld/Public/assets/javascripts/plugins/fileupload/jquery.fileupload-fp.min.js' type='text/javascript'></script>
<script src='/jwcld/Public/assets/javascripts/plugins/fileupload/jquery.fileupload-ui.min.js' type='text/javascript'></script>
<script src='/jwcld/Public/assets/javascripts/plugins/fileupload/jquery.fileupload-init.js' type='text/javascript'></script>
<!-- / timeago -->
<script src='/jwcld/Public/assets/javascripts/plugins/timeago/jquery.timeago.js' type='text/javascript'></script>
<!-- / slimscroll -->
<script src='/jwcld/Public/assets/javascripts/plugins/slimscroll/jquery.slimscroll.min.js' type='text/javascript'></script>
<!-- / autosize (for textareas) -->
<script src='/jwcld/Public/assets/javascripts/plugins/autosize/jquery.autosize-min.js' type='text/javascript'></script>
<!-- / charCount -->
<script src='/jwcld/Public/assets/javascripts/plugins/charCount/charCount.js' type='text/javascript'></script>
<!-- / validate -->
<script src='/jwcld/Public/assets/javascripts/plugins/validate/jquery.validate.min.js' type='text/javascript'></script>
<script src='/jwcld/Public/assets/javascripts/plugins/validate/additional-methods.js' type='text/javascript'></script>
<!-- / naked password -->
<script src='/jwcld/Public/assets/javascripts/plugins/naked_password/naked_password-0.2.4.min.js' type='text/javascript'></script>
<!-- / nestable -->
<script src='/jwcld/Public/assets/javascripts/plugins/nestable/jquery.nestable.js' type='text/javascript'></script>
<!-- / tabdrop -->
<script src='/jwcld/Public/assets/javascripts/plugins/tabdrop/bootstrap-tabdrop.js' type='text/javascript'></script>
<!-- / jgrowl -->
<script src='/jwcld/Public/assets/javascripts/plugins/jgrowl/jquery.jgrowl.min.js' type='text/javascript'></script>
<!-- / bootbox -->
<script src='/jwcld/Public/assets/javascripts/plugins/bootbox/bootbox.min.js' type='text/javascript'></script>
<!-- / inplace editing -->
<script src='/jwcld/Public/assets/javascripts/plugins/xeditable/bootstrap-editable.min.js' type='text/javascript'></script>
<script src='/jwcld/Public/assets/javascripts/plugins/xeditable/wysihtml5.js' type='text/javascript'></script>
<!-- / ckeditor -->
<script src='/jwcld/Public/assets/javascripts/plugins/ckeditor/ckeditor.js' type='text/javascript'></script>
<!-- / filetrees -->
<script src='/jwcld/Public/assets/javascripts/plugins/dynatree/jquery.dynatree.min.js' type='text/javascript'></script>
<!-- / datetime picker -->
<script src='/jwcld/Public/assets/javascripts/plugins/bootstrap_datetimepicker/bootstrap-datetimepicker.js' type='text/javascript'></script>
<!-- / daterange picker -->
<script src='/jwcld/Public/assets/javascripts/plugins/bootstrap_daterangepicker/moment.min.js' type='text/javascript'></script>
<script src='/jwcld/Public/assets/javascripts/plugins/bootstrap_daterangepicker/bootstrap-daterangepicker.js' type='text/javascript'></script>
<!-- / max length -->
<script src='/jwcld/Public/assets/javascripts/plugins/bootstrap_maxlength/bootstrap-maxlength.min.js' type='text/javascript'></script>
<!-- / dropdown hover -->
<script src='/jwcld/Public/assets/javascripts/plugins/bootstrap_hover_dropdown/twitter-bootstrap-hover-dropdown.min.js' type='text/javascript'></script>
<!-- / slider nav (address book) -->
<script src='/jwcld/Public/assets/javascripts/plugins/slider_nav/slidernav-min.js' type='text/javascript'></script>
<!-- / fuelux -->
<script src='/jwcld/Public/assets/javascripts/plugins/fuelux/wizard.js' type='text/javascript'></script>
<!-- / flatty theme -->
<script src='/jwcld/Public/assets/javascripts/nav.js' type='text/javascript'></script>
<script src='/jwcld/Public/assets/javascripts/tables.js' type='text/javascript'></script>
<script src='/jwcld/Public/assets/javascripts/theme.js' type='text/javascript'></script>
<!-- / demo -->
<script src='/jwcld/Public/assets/javascripts/demo/jquery.mockjax.js' type='text/javascript'></script>
<script src='/jwcld/Public/assets/javascripts/demo/inplace_editing.js' type='text/javascript'></script>
<script src='/jwcld/Public/assets/javascripts/demo/charts.js' type='text/javascript'></script>
<script src='/jwcld/Public/assets/javascripts/demo/demo.js' type='text/javascript'></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on("click","#submit",function(){
            if ($("#username").val() != "" && $("#password").val() != "") {
                return true;
            }else{
                $("#info").show();
                setTimeout(function(){$("#info").hide();},2500);
                return false;
            }
        });
    });
</script>
</body>
</html>