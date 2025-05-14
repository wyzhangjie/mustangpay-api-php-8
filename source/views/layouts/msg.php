<?php
/**
 * @var $this \yii\web\View
 * @var $data array
 */
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$url = is_array($data['url']) ? Url::to($data['url']) : $data['url'];
$iconClassList = [
    'success' => 'fa fa-check fa-lg',
    'danger' => 'fa fa-close fa-lg',
    'info' => 'fa fa-info fa-lg',
];
$this->title = Yii::t('app.view', 'System Prompt');
$supMsg = Yii::t('app.view', 'Click here to return to the previous page');
$homeMsg = Yii::t('app.view', 'Click here to return to the homepage');
?>
<style type="text/css">

    .alert-success {
        color: #3c763d;
        background-color: #dff0d8;
        border-color: #d6e9c6;
    }
    .alert-danger {
        color: #a94442;
        background-color: #f2dede;
        border-color: #ebccd1;
    }
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
        text-align: center;
    }
</style>
<div class="row mar-top" >
    <div class="col-md-8 col-md-offset-2">
        <div class="alert alert-<?= $data['type'] ?>">
            <h4 class="mar-btm">
                <i class="<?= ArrayHelper::getValue($iconClassList, $data['type']) ?>"></i>
                <?= $data['message'] ?>
            </h4>
            <?php if ($url): ?>
                <p><?= Html::a(Yii::t('app.view','If your browser does not automatically redirect, please click on this link'), $url, ['class' => 'alert-link']) ?></p>
                <script type="text/javascript" reload="1">
                    setTimeout("window.location.href ='<?= $url; ?>';", <?= $data['time']; ?>);
                </script>
            <?php else: ?>
                <script type="text/javascript">
                    var browser = {}, ua = navigator.userAgent.toLowerCase();
                    browser.firefox = /firefox/.test(ua);
                    browser.chrome = /chrome/.test(ua);
                    browser.opera = /opera/.test(ua);
                    browser.ie = /msie/.test(ua);

                    if(!browser.ie) browser.ie = /trident 7.0/.test(ua);

                    if (history.length > (browser.ie ? 0 : 1)) {
                        document.write('<p><a href="javascript:history.back()" class="alert-link">[ <?= $supMsg;?> ]</a></p>');
                    } else {
                        document.write('<p"><a href="./" class="alert-link">[ <?= $homeMsg;?> ]</a></p>');
                    }
                </script>
            <?php endif; ?>
        </div>
    </div>
</div>
