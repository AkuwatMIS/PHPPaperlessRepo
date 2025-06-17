<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Actions */
/* @var $form yii\widgets\ActiveForm */
?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3">
            <?= Html::label('Branch'); ?>
            <?= Html::dropDownList('branch','',$branches, ['prompt' => 'Select Branch', 'id' => 'branch','class' =>'form-control']); ?>
        </div>
        <div class="col-sm-3">
            <?= Html::label('Team'); ?>
            <?php echo DepDrop::widget([
                'name' => 'team',
                'options' => ['id'=>'team'],
                'pluginOptions' => [
                    'depends' => ['branch'],
                    'initialize' => true,
                    'initDepends' => ['branch'],
                    'placeholder' => 'Select Team',
                    'url' => Url::to(['/structure/fetch-team-by-branch'])
                ]
            ]);  ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?= Html::label('Field Area'); ?>
                <input type="text" id="address" style="width: 500px;" class="form-control"></input>
        </div>
    </div>

    <div id="map"></div>
    <div class="row">
        <div class="col-sm-8">
            <div id="listing"></div>
        </div>
    </div>
</div>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?key=AIzaSyD5vhqpbUx6YQTWXrnjt1MXwh7rkI27OkI&sensor=false&libraries=places&language=en-AU"></script>
<script>

    var counter = 0;
    var counter_ = 0;
    var records = {};
    var places = {};
    var flag = 0;
    var places_ids = [];
    var record_list = {};
    var autocomplete = new google.maps.places.Autocomplete($("#address")[0], {});

    autocomplete.addListener('place_changed', function() {

        var place = autocomplete.getPlace();
        var pyrmont = new google.maps.LatLng(place.geometry.location.lat(),place.geometry.location.lng());
        var request = {
            location: pyrmont,
            radius: '1000'
        };

        var service = new google.maps.places.PlacesService($("#address")[0], {});
        service.nearbySearch(request,function(results, status) {
            if (status === google.maps.places.PlacesServiceStatus.OK) {
                //console.log(results);
                for (var i = 0; i < results.length; i++) {
                    counter = results.length;
                    var r = {};
                    var req = {};

                    var req =  {
                        placeId: results[i].place_id,
                        fields: ['formatted_address']
                    }

                    places_ids[i] = results[i].place_id;
                    r['latitude'] = results[i].geometry.location.lat();
                    r['longitude'] = results[i].geometry.location.lng();
                    r['formatted_address'] = "address";
                    records[results[i].place_id] = r;

                }
               /* var geocoder = new google.maps.Geocoder;
                var latlng = {lat: r['latitude'], lng: r['longitude']};
                geocoder.geocode({'location': latlng}, function(rsl, status) {
                    if (status === 'OK') {
                        r['formatted_address'] = rsl.formatted_address;
                        console.log(rsl.formatted_address);
                        /!* records[rsl.formatted_address] = r;*!/
                    }
                });*/
                getdetails();
            }
        });
    });
 function getdetails()
 {
     //console.log(records);
     $.each(places_ids, function (index, value) {
         var service1 = new google.maps.places.PlacesService($("#map")[0], {});
         var req =  {
             placeId: value,
             fields: ['formatted_address','name']
         }
         service1.getDetails(req, function (results, status) {
              if (status === google.maps.places.PlacesServiceStatus.OK) {
                 var k = {};
                 k['longitude'] = records[value]['longitude'];
                 k['latitude'] = records[value]['latitude'];
                 k['formatted_address'] = results.formatted_address;
                 record_list[value] = k;
                 //records[value]['formatted_address'] = res.formatted_address;
             }

             //console.log(Object.keys(record_list).length);
             counter_ = counter_ + 1;
              if(counter_ == places_ids.length)
              {
                  finaldata();
              }

         });
     });
 }

 function finaldata() {
     var branch_id = $("#branch").val();
     var team_id = $("#team").val();
     //console.log(record_list);
     $.post({
         url: 'upload',
         type: 'post',
         dataType: 'html',
         data: {keylist: JSON.stringify(record_list), team_id : team_id},
         success: function (data) {
               $('#listing').html(data);
               deleteData();
         },
     });
 }

 function deleteData()
 {
     var team_id = $("#team").val();
     $('.delete').on('click', function(e) {
         e.preventDefault();
         var area = $(this).closest('tr').children('td.areas').text();

         $.post({
             url: 'delete',
             type: 'post',
             dataType: 'html',
             data: {field_area: area, team_id: team_id},
             success: function (data) {
                 $('#listing').html(data);
                 alert('area delete successfully');
                 deleteData();
             },
         });
     });
 }

</script>
