<?php
/**
 * @var $this \yii\web\View
 */
$this->context->layout= false;
$msg = Yii::t('app.view','Sorry, the site has been paused');
?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?= $msg . ' - ' . $this->context->params['site_title']?></title>
        <style>
        html,body,div,h1,*{margin:0;padding:0;}
        body{
            background-color:#fefefe;
            color:#333
        }
        .box{
            width:580px;
            margin:0 auto;
        }
        h1{
            font-size:20px;
            text-align:center;
            background:url(data:image/png;base64,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) no-repeat top center;
            padding-top:160px;
            margin-top:30%;
            font-weight:normal;
        }

        </style>
    </head>
    <body>
        <div class="box">
            <h1><?=  $this->context->params['system_info']?></h1>
        </div>
    </body>
</html>
