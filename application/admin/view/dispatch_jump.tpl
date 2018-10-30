<!DOCTYPE html>
<!--[if IE 9]>         <html class="ie9 no-focus" lang="zh"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-focus" lang="zh"> <!--<![endif]-->
<head>
    <meta charset="utf-8">

    <title>跳转提示 | {:config('web_site_title')} - DolphinPHP</title>

    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">

    <!-- Icons -->
    <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
    <link rel="shortcut icon" href="__STATIC__/img/favicons/favicon.png">
    <!-- END Icons -->

    <!-- Stylesheets -->
    <!-- Bootstrap and OneUI CSS framework -->
    <link rel="stylesheet" href="__ADMIN_CSS__/bootstrap.min.css">
    <link rel="stylesheet" href="__ADMIN_CSS__/oneui.css">
    <link rel="stylesheet" href="__ADMIN_CSS__/dolphin.css">
    <!-- END Stylesheets -->
</head>
<style>
.loading {
     -webkit-animation: fadein 2s;
     -moz-animation: fadein 2s;
     -o-animation: fadein 2s;
     animation: fadein 2s
}
@-moz-keyframes fadein {
     from {
          opacity: 0
     }
     to {
          opacity: 1
     }
}
@-webkit-keyframes fadein {
     from {
          opacity: 0
     }
     to {
          opacity: 1
     }
}
@-o-keyframes fadein {
     from {
          opacity: 0
     }
     to {
          opacity: 1
     }
}
@keyframes fadein {
     from {
          opacity: 0
     }
     to {
          opacity: 1
     }
}
.spinner-wrapper {
     position: absolute;
     top: 0;
     left: 0;
     z-index: 300;
     height: 100%;
     min-width: 100%;
     min-height: 100%;
     background: rgba(255,255,255,0.93)
}
.spinner-text {
     position: absolute;
     top: 45%;
     left: 50%;
     margin-left: -100px;
     margin-top: 2px;
     color: #000;
     letter-spacing: 1px;
     font-size: 20px;
     font-family: Arial
}
.spinner {
     position: absolute;
     top: 45%;
     left: 50%;
     display: block;
     margin-left: -160px;
     width: 1px;
     height: 1px;
     border: 20px solid rgba(255,0,0,1);
     -webkit-border-radius: 50px;
     -moz-border-radius: 50px;
     border-radius: 50px;
     border-left-color: transparent;
     border-right-color: transparent;
     -webkit-animation: spin 1.5s infinite;
     -moz-animation: spin 1.5s infinite;
     animation: spin 1.5s infinite
}
@-webkit-keyframes spin {
     0%,100% {
          -webkit-transform: rotate(0deg) scale(1)
     }
     50% {
          -webkit-transform: rotate(720deg) scale(0.6)
     }
}
@-moz-keyframes spin {
     0%,100% {
          -moz-transform: rotate(0deg) scale(1)
     }
     50% {
          -moz-transform: rotate(720deg) scale(0.6)
     }
}
@-o-keyframes spin {
     0%,100% {
          -o-transform: rotate(0deg) scale(1)
     }
     50% {
          -o-transform: rotate(720deg) scale(0.6)
     }
}
@keyframes spin {
     0%,100% {
          transform: rotate(0deg) scale(1)
     }
     50% {
          transform: rotate(720deg) scale(0.6)
     }
}
</style>
<body>
<!-- Error Content -->
<div class="content bg-white text-center pulldown overflow-hidden">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3">
            <!-- Error Titles -->
            <h1 class="font-w300 {$code? 'text-success' : 'text-city'} push-10 animated flipInX"><i class="fa fa-{$code? 'check' : 'times'}-circle"></i> <?php echo(strip_tags($msg));?></h1>
            <p class="font-w300 push-20 animated fadeInUp">页面自动 <a id="href" href="<?php echo($url);?>">跳转</a> 等待时间： <b id="wait"><?php echo($wait);?></b>秒</p>

			<div class="push-50">
                <a class="btn btn-minw btn-rounded btn-success" href="<?php echo($url);?>"><i class="fa fa-external-link-square"></i> 立即跳转</a>
                <button class="btn btn-minw btn-rounded btn-warning" type="button" onclick="stop()"><i class="fa fa-ban"></i> 禁止跳转</button>
                <a class="btn btn-minw btn-rounded btn-default" href="{$Request.baseFile}"><i class="fa fa-home"></i> 返回首页</a>
            </div>
            <!-- END Error Titles -->

        </div>
    </div>
</div>
<!-- END Error Content -->

<!-- Error Footer -->
<div class="content pulldown text-muted text-center">
    极简 · 极速 · 极致<br>
    海豚PHP，让开发更简单！<br>
    由 <a class="link-effect" href="http://www.dolphinphp.com">DolphinPHP</a> 强力驱动 <a class="link-effect" href="http://www.zrthink.com">卓锐软件</a> 倾情奉献
</div>
<!-- END Error Footer -->

<script type="text/javascript">
    (function(){
        var wait = document.getElementById('wait'),
            href = document.getElementById('href').href;
        var interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time <= 0) {
                location.href = href;
                clearInterval(interval);
            };
        }, 1000);

        // 禁止跳转
        window.stop = function (){
            clearInterval(interval);
        }
    })();
</script>
</body>
</html>