<?php
use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\BorrowersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$js.='
 var chart = new CanvasJS.Chart("chartContainer'.$branch_id.'", {
	theme: "light2", // "light1", "light2", "dark1", "dark2"
	animationEnabled: true,
	title:{
		text: "Overdue Breakdown"   
	},
	axisX: {
		interval: 1,
		intervalType: "month",
		valueFormatString: "#",
        lineColor: "#C24642",
	},
	axisY:{
		title: "No of Overdue",
		valueFormatString: "#0"
	},
	data: [{        
		type: "line",
		markerSize: 12,
		xValueFormatString: "MMM, YYYY",
		yValueFormatString: "###.#",
		dataPoints: [   
		{ x: new Date(2022, 06, 0) , y: '.$model[0]['overdue'].', indexLabel: "'.$model[0]['overdue'].'", markerType: "triangle", markerColor: "#6B8E23" },
        { x: new Date(2022, 07, 0) , y: '.$model[1]['overdue'].', indexLabel: "'.$model[1]['overdue'].'", markerType: "triangle", markerColor: "tomato" },
        { x: new Date(2022, 08, 0) , y: '.$model[2]['overdue'].', indexLabel: "'.$model[2]['overdue'].'", markerType: "triangle", markerColor: "#6B8E23" },
        { x: new Date(2022, 09, 0) , y: '.$model[3]['overdue'].', indexLabel: "'.$model[3]['overdue'].'", markerType: "triangle", markerColor: "tomato" },
        { x: new Date(2022, 10, 0) , y: '.$model[4]['overdue'].', indexLabel: "'.$model[4]['overdue'].'", markerType: "triangle", markerColor: "6B8E23" },
        { x: new Date(2022, 11, 0) , y: '.$model[5]['overdue'].', indexLabel: "'.$model[5]['overdue'].'", markerType: "triangle", markerColor: "tomato" },
        { x: new Date(2022, 12, 0) , y: '.$model[6]['overdue'].', indexLabel: "'.$model[6]['overdue'].'", markerType: "triangle", markerColor: "#6B8E23" },
		]
	}]
});
chart.render();
';
$this->registerJs($js);
?>
<div id="<?php echo rand(10,100)?> "><div id="chartContainer<?php echo $branch_id?>" style="height: 300px; width: 100%;"></div></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>


<!--{ x: new Date(2021, 01, 0) , y: '.$model[0]['overdue'].', indexLabel: "Jan", markerType: "triangle", markerColor: "#6B8E23" },-->
<!--{ x: new Date(2021, 02, 0) , y: '.$model[1]['overdue'].', indexLabel: "Feb", markerType: "triangle", markerColor: "tomato" },-->
<!--{ x: new Date(2021, 03, 0) , y: '.$model[2]['overdue'].', indexLabel: "Mar", markerType: "triangle", markerColor: "#6B8E23" },-->
<!--{ x: new Date(2021, 04, 0) , y: '.$model[3]['overdue'].', indexLabel: "Apr", markerType: "triangle", markerColor: "tomato" },-->
<!--{ x: new Date(2021, 05, 0) , y: '.$model[4]['overdue'].', indexLabel: "May", markerType: "triangle", markerColor: "6B8E23" },-->
<!--{ x: new Date(2021, 06, 0) , y: '.$model[5]['overdue'].', indexLabel: "Jun", markerType: "triangle", markerColor: "tomato" },-->