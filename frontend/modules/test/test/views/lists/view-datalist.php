<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Lists */
?>

<div class="lists-view">

    <table class="table table-bordered">
        <thead>
        <th>Value</th>
        <th>Label</th>
        </thead>
        <tbody id="sortable" >
        <?php
        foreach ($model as $key => $lists_model)
        { ?>
            <tr id='item-<?=$lists_model->id?>'>
                <td><?=$lists_model->value?></td>
                <td><?=$lists_model->label?></td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>

<?php
$this->registerJs("

        $(document).ready(function(){
            $( '#sortable' ).sortable();
        });
        $( 'tbody' ).sortable({
            
            cancel: ':input,button,[contenteditable]',
            
            axis: 'y',
           
            update: function (event, ui) {
             
                var data = $(this).sortable('serialize');
               
                $.ajax({
                    data: {key: data},
                    type: 'POST',
                    url: 'sort',
                    success: function(response) {
                    // alert(response);
                }
                });
            }
        });
        $( 'tbody' ).disableSelection();
    ");
?>
