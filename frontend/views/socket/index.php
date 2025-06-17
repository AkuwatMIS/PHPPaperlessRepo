<?php

use yii\bootstrap\Modal;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ApplicationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Applications';
$this->params['breadcrumbs'][] = $this->title;

$js = "
    var conn = new WebSocket('ws://beta.akhuwat.org.pk:8800');
    conn.onmessage = function(e) {
        console.log('Response:' + e.data);
    };
    var data = [
        {
            temp_id:'2', 
            parent_id:'14652', 
            parent_type:'members', 
            image_type:'left_thumb',
            image_data:'AwFVKIgAAAIAAgACAAIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAANkiJeNJdjvm8ZpV52IBB+K4gM/1yIzf9RmY4fFKSd31oppb8JqhwXGa5avzu1lB8PNcHfYsCmnxaHo90mmGIdS6CknU4jT902pmd9UhANenM55tpWkOQbaDOQ+22/ELs3n1V4D4tLuRWLi5kbFGH5IJRiORWU4Xk5KZV5bDVQeW65EBk5O9P5Ob7TuT5Akvksm6G0ULpn1Q5CQVUVOlmzAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA='
        }
    ];
    
   
    conn.onopen = function(e) {
        console.log('ping');
        conn.send(JSON.stringify(data));
    };
    
";
$this->registerJs($js);

?>
<img src="/socket/index"/>