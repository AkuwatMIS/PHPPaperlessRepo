<?php

if(empty( $count_as_status['pending'])) {
    $count_as_status['pending'] = 0;
}
if(empty( $count_as_status['processed'])) {
    $count_as_status['processed']=0;
}
if(empty($count_as_status['approved'])) {
    $count_as_status['approved']=0;
}
if(empty($count_as_status['rejected'])) {
    $count_as_status['rejected']=0;
}
?>
<?php
if(Yii::$app->user->identity->role->item_name == 'RC'){
    ?>
    <div class="row">
        <div class="col-sm-3">
            <article class="statistic-box purple">
                <div>
                    <div class="number"><?= $count_as_status['pending'] ?></div>
                    <div class="caption"><div>Pending Requests</div></div>
                    <div class="percent">
                    </div>
                </div>
            </article>
        </div>
        <div class="col-sm-3">
            <article class="statistic-box yellow">
                <div>
                    <div class="number"><?= $count_as_status['processed']  ?></div>
                    <div class="caption"><div>Processed Requests</div></div>
                    <div class="percent">
                    </div>
                </div>
            </article>
        </div>
        <div class="col-sm-3">
            <article class="statistic-box red">
                <div>
                    <div class="number"><?= $count_as_status['rejected'] ?></div>
                    <div class="caption"><div>Rejected Requests</div></div>
                </div>
            </article>
        </div>
    </div>
<?php }else {?>

    <div class="row">
        <div class="col-sm-3">
            <article class="statistic-box green">
                <div>
                    <div class="number"><?= $count_as_status['pending']  ?></div>
                    <?php
                    ?>
                    <div class="caption"><div>Pending Requests</div></div>

                </div>
            </article>
        </div>
        <div class="col-sm-3">
            <article class="statistic-box purple">
                <div>
                    <div class="number"><?= $count_as_status['approved']  ?></div>
                    <div class="caption"><div>Approved Requests</div></div>
                    <div class="percent">
                    </div>
                </div>
            </article>
        </div>
        <div class="col-sm-3">
            <article class="statistic-box yellow">
                <div>
                    <div class="number"><?= $count_as_status['processed'] ?></div>
                    <div class="caption"><div>Processed Requests</div></div>
                </div>
            </article>
        </div>
        <div class="col-sm-3">
            <article class="statistic-box red">
                <div>
                    <div class="number"><?= $count_as_status['rejected'] ?></div>
                    <div class="caption"><div>Rejected Requests</div></div>
                </div>
            </article>
        </div>
    </div>
<?php } ?>