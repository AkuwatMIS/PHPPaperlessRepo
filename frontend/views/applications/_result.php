<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Applications */

?>

<!--<table class="table table-bordered">
    <tbody>
    <?php /*foreach ($d['attributes'] as $att){
        if(!in_array($att['name'], ['server_id','sync_status','is_image_sync','id','description_image','application_id','approved_on','approved_by','longitude','latitude'])){*/?>
        <tr>
            <th><?php /*echo ucwords(str_replace('_', ' ', $att['name'])) */?></th>
            <td>
                <?php
/*                if(is_string($model->$relation->$att['name']) && !empty($model->$relation->$att['name']) && isset($model->$relation->$att['name'])){
                    echo $model->$relation->$att['name'];
                }
                echo $model->$relation->$att['name'];
                */?>
            </td>
        </tr>
    <?php /*} }*/?>
    </tbody>
</table>-->
<?php
//print_r($relation);
if($relation=='appraisalsSocial'){?>
    <table class="table table-bordered">
    <tbody>
    <!--<tr>
        <th>Poverty Index</th>
        <td><?php /*echo $model->$relation->poverty_index; */?></td>
    </tr>-->
    <tr>
        <th>House Ownership</th>
        <td><?php echo $model->$relation->house_ownership ?></td>
    </tr>
    <tr>
        <th>House Rent Amount</th>
        <td><?php echo isset($model->$relation->house_rent_amount) ?number_format($model->$relation->house_rent_amount):''?></td>
    </tr>
    <tr>
        <th>Land Size</th>
        <td><?php echo isset($model->$relation->land_size) ? ($model->$relation->land_size):''?></td>
    </tr>
    <tr>
        <th>Total Family Members</th>
        <td><?php echo isset($model->$relation->total_family_members) ?number_format($model->$relation->total_family_members):''?></td>
    </tr>
    <tr>
        <th>Ladies</th>
        <td><?php echo isset($model->$relation->ladies) ?number_format($model->$relation->ladies):''?></td>
    </tr>
    <tr>
        <th>Gents</th>
        <td><?php echo isset($model->$relation->gents) ?number_format($model->$relation->gents):''?></td>
    </tr>
    <tr>
        <th>Source of Income</th>
        <td><?php echo isset($model->$relation->source_of_income) ?($model->$relation->source_of_income):''?></td>
    </tr>
    <tr>
        <th>Total Household Income</th>
        <td><?php echo isset($model->$relation->total_household_income) ?number_format($model->$relation->total_household_income):''?></td>
    </tr>
    <tr>
        <th>Educational Expenses</th>
        <td><?php echo isset($model->$relation->educational_expenses) ?number_format($model->$relation->educational_expenses):''?></td>
    </tr>
    <tr>
        <th>Medical Expenses</th>
        <td><?php echo isset($model->$relation->medical_expenses) ?number_format($model->$relation->medical_expenses):''?></td>
    </tr>
    <tr>
        <th>Kitchen Eexpenses</th>
        <td><?php echo isset($model->$relation->kitchen_expenses) ?number_format($model->$relation->kitchen_expenses):''?></td>
    </tr>
    <tr>
        <th>Monthly Savings</th>
        <td><?php echo isset($model->$relation->monthly_savings) ?($model->$relation->monthly_savings):''?></td>
    </tr>
    <tr>
        <th>Amount</th>
        <td><?php echo isset($model->$relation->amount) ?number_format($model->$relation->amount):''?></td>
    </tr>
    <tr>
        <th>Other Expenses</th>
        <td><?php echo isset($model->$relation->other_expenses) ?number_format($model->$relation->other_expenses):''?></td>
    </tr>
    <tr>
        <th>Total Expenses</th>
        <td><?php echo isset($model->$relation->total_expenses) ?number_format($model->$relation->total_expenses):''?></td>
    </tr>
    <tr>
        <th>Other Loan</th>
        <td><?php echo isset($model->$relation->other_loan) ?($model->$relation->other_loan):''?></td>
    </tr>
    <tr>
        <th>Loan Amount</th>
        <td><?php echo isset($model->$relation->loan_amount) ?number_format($model->$relation->loan_amount):''?></td>
    </tr>
    <tr>
        <th>Business Income</th>
        <td><?php echo isset($model->$relation->business_income) ?number_format($model->$relation->business_income):''?></td>
    </tr>
    <tr>
        <th>Job Income</th>
        <td><?php echo isset($model->$relation->job_income) ?number_format($model->$relation->job_income):''?></td>
    </tr>
    <tr>
        <th>House Rent Income</th>
        <td><?php echo isset($model->$relation->house_rent_income) ?number_format($model->$relation->house_rent_income):''?></td>
    </tr>
    <tr>
        <th>Other Income</th>
        <td><?php echo isset($model->$relation->other_income) ?number_format($model->$relation->other_income):''?></td>
    </tr>
    <tr>
        <th>Economic Dealings</th>
        <td><?php echo isset($model->$relation->economic_dealings) ?($model->$relation->economic_dealings):''?></td>
    </tr>
    <tr>
        <th>Social Behaviour</th>
        <td><?php echo isset($model->$relation->social_behaviour) ?($model->$relation->social_behaviour):''?></td>
    </tr>
    </tbody>
</table>
<?php }else if($relation=='appraisalsBusiness'){?>
    <table class="table table-bordered">
        <tbody>
        <tr>
            <th>Place of Business</th>
            <td><?php echo $model->$relation->place_of_business ?></td>
        </tr>
        <tr>
            <th>Business Assets</th>
            <td><?php echo isset($model->$relation->fixed_business_assets) ? ($model->$relation->fixed_business_assets) : '' ?></td>
        </tr>
        <tr>
            <th>Business Assets Amount</th>
            <td><?php echo isset($model->$relation->fixed_business_assets_amount) ? number_format($model->$relation->fixed_business_assets_amount) : '' ?></td>
        </tr>
        <tr>
            <th>Fixed Business Assets</th>
            <td><?php echo isset($model->$relation->business_expenses) ? ($model->$relation->business_expenses) : '' ?></td>
        </tr>
        <tr>
            <th>Business Assets Amount</th>
            <td><?php echo isset($model->$relation->business_expenses_amount) ? number_format($model->$relation->business_expenses_amount) : '' ?></td>
        </tr>
        <tr>
            <th>New Required Assets</th>
            <td><?php echo isset($model->$relation->new_required_assets) ? ($model->$relation->new_required_assets) : '' ?></td>
        </tr>
        <tr>
            <th>New Required Assets Amount</th>
            <td><?php echo isset($model->$relation->new_required_assets_amount) ? number_format($model->$relation->new_required_assets_amount) : '' ?></td>
        </tr>
        <tr>
            <th>Running Capital</th>
            <td><?php echo isset($model->$relation->running_capital) ? ($model->$relation->running_capital) : '' ?></td>
        </tr>
        <tr>
            <th>Running Capital Amount</th>
            <td><?php echo isset($model->$relation->running_capital_amount) ? number_format($model->$relation->running_capital_amount) : '' ?></td>
        </tr>

        </tbody>
    </table>
<?php }else if($relation=='appraisalsHousing'){?>
    <table class="table table-bordered">
        <tbody>
        <tr>
            <th>Property Type</th>
            <td><?php echo $model->$relation->property_type ?></td>
        </tr>
        <tr>
            <th>Ownership</th>
            <td><?php echo isset($model->$relation->ownership) ? ($model->$relation->ownership) : '' ?></td>
        </tr>
        <tr>
            <th>Land Aea</th>
            <td><?php echo isset($model->$relation->land_area) ? ($model->$relation->land_area) : '' ?></td>
        </tr>
        <tr>
            <th>Residential Area</th>
            <td><?php echo isset($model->$relation->residential_area) ? ($model->$relation->residential_area) : '' ?></td>
        </tr>
        <tr>
            <th>No of Rooms</th>
            <td><?php echo isset($model->$relation->no_of_rooms) ? number_format($model->$relation->no_of_rooms) : '' ?></td>
        </tr>
        <tr>
            <th>No of Kitchens</th>
            <td><?php echo isset($model->$relation->no_of_kitchens) ? ($model->$relation->no_of_kitchens) : '' ?></td>
        </tr>
        <tr>
            <th>No of Toilets</th>
            <td><?php echo isset($model->$relation->no_of_toilets) ? number_format($model->$relation->no_of_toilets) : '' ?></td>
        </tr>
        <tr>
            <th>Purchase Price</th>
            <td><?php echo isset($model->$relation->purchase_price) ? number_format($model->$relation->purchase_price) : '' ?></td>
        </tr>
        <tr>
            <th>Current Price</th>
            <td><?php echo isset($model->$relation->current_price) ? number_format($model->$relation->current_price) : '' ?></td>
        </tr>

        </tbody>
    </table>
<?php }else if($relation=='appraisalsLivestock'){?>
    <table class="table table-bordered">
        <tbody>
        <tr>
            <th>Animal Type</th>
            <td><?php echo $model->$relation->animal_type ?></td>
        </tr>
        <tr>
            <th>Business Type</th>
            <td><?php echo isset($model->$relation->business_type) ? ($model->$relation->business_type) : '' ?></td>
        </tr>
        <tr>
            <th>Business Condition</th>
            <td><?php echo isset($model->$relation->business_condition) ? ($model->$relation->business_condition) : '' ?></td>
        </tr>
        <tr>
            <th>Business Place</th>
            <td><?php echo isset($model->$relation->business_place) ? ($model->$relation->business_place) : '' ?></td>
        </tr>
        <tr>
            <th>Business Address</th>
            <td><?php echo isset($model->$relation->business_address) ? $model->$relation->business_address : '' ?></td>
        </tr>
        <tr>
            <th>Used Land Type</th>
            <td><?php echo isset($model->$relation->used_land_type) ? ($model->$relation->used_land_type) : '' ?></td>
        </tr>
        <tr>
            <th>Used Land Size</th>
            <td><?php echo isset($model->$relation->used_land_size) ? number_format($model->$relation->used_land_size) : '' ?></td>
        </tr>
        <tr>
            <th>Available Amount</th>
            <td><?php echo isset($model->$relation->available_amount) ? number_format($model->$relation->available_amount) : '' ?></td>
        </tr>
        <tr>
            <th>Required Amount</th>
            <td><?php echo isset($model->$relation->required_amount) ? number_format($model->$relation->required_amount) : '' ?></td>
        </tr>
        <tr>
            <th>Monthly Income</th>
            <td><?php echo isset($model->$relation->monthly_income) ? number_format($model->$relation->monthly_income) : '' ?></td>
        </tr>
        <tr>
            <th>Expected Income</th>
            <td><?php echo isset($model->$relation->expected_income) ? number_format($model->$relation->expected_income) : '' ?></td>
        </tr>

        </tbody>
    </table>
<?php  }else if($relation=='appraisalsAgriculture'){ ?>
    <table class="table table-bordered">
        <tbody>
        <tr>
            <th>Water Analysis</th>
            <td><?php echo $model->$relation->water_analysis ?></td>
        </tr>
        <tr>
            <th>Soil Analysis</th>
            <td><?php echo isset($model->$relation->soil_analysis) ? ($model->$relation->soil_analysis) : '' ?></td>
        </tr>
        <tr>
            <th>Laser Level</th>
            <td><?php echo isset($model->$relation->laser_level) ? ($model->$relation->laser_level) : '' ?></td>
        </tr>
        <tr>
            <th>Irrigation Source</th>
            <td><?php echo isset($model->$relation->irrigation_source) ? ($model->$relation->irrigation_source) : '' ?></td>
        </tr>
        <tr>
            <th>Other Source</th>
            <td><?php echo isset($model->$relation->other_source) ? $model->$relation->other_source : '' ?></td>
        </tr>
        <tr>
            <th>Crop Year</th>
            <td><?php echo isset($model->$relation->crop_year) ? ($model->$relation->crop_year) : '' ?></td>
        </tr>
        <tr>
            <th>Crop Production</th>
            <td><?php echo isset($model->$relation->crop_production) ? ($model->$relation->crop_production) : '' ?></td>
        </tr>
        <tr>
            <th>Resources</th>
            <td><?php echo isset($model->$relation->resources) ? ($model->$relation->resources) : '' ?></td>
        </tr>
        <tr>
            <th>Expenses</th>
            <td><?php echo isset($model->$relation->expenses) ? ($model->$relation->expenses) : '' ?></td>
        </tr>
        <tr>
            <th>Available Resources</th>
            <td><?php echo isset($model->$relation->available_resources) ? ($model->$relation->available_resources) : '' ?></td>
        </tr>
        <tr>
            <th>Required Resources</th>
            <td><?php echo isset($model->$relation->required_resources) ? ($model->$relation->required_resources) : '' ?></td>
        </tr>

        <tr>
            <th>Agriculture Appraisal Address</th>
            <td><?php echo isset($model->$relation->agriculture_appraisal_address) ? ($model->$relation->agriculture_appraisal_address) : '' ?></td>
        </tr>
        <tr>
            <th>Description</th>
            <td><?php echo isset($model->$relation->description) ? ($model->$relation->description) : '' ?></td>
        </tr>
        <tr>
            <th>Latitude</th>
            <td><?php echo isset($model->$relation->latitude) ? ($model->$relation->latitude) : '' ?></td>
        </tr>
        <tr>
            <th>Longitude</th>
            <td><?php echo isset($model->$relation->longitude) ? ($model->$relation->longitude) : '' ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?php echo isset($model->$relation->status) ? ($model->$relation->status) : '' ?></td>
        </tr>

        <tr>
            <th>Required Machinery Detail</th>
            <td><?php echo isset($model->$relation->required_machinery_detail) ? ($model->$relation->required_machinery_detail) : '' ?></td>
        </tr>
        <tr>
            <th>Expected Sale Price</th>
            <td><?php echo isset($model->$relation->expected_sale_price) ? ($model->$relation->expected_sale_price) : '' ?></td>
        </tr>
        <tr>
            <th>Expected Expenses</th>
            <td><?php echo isset($model->$relation->expected_expenses) ? ($model->$relation->expected_expenses) : '' ?></td>
        </tr>
        <tr>
            <th>Expected Savings</th>
            <td><?php echo isset($model->$relation->expected_savings) ? ($model->$relation->expected_savings) : '' ?></td>
        </tr>

        <tr>
            <th>Crop Type</th>
            <td><?php echo isset($model->$relation->crop_type) ? ($model->$relation->crop_type) : '' ?></td>
        </tr>
        <tr>
            <th>Crops</th>
            <td><?php echo isset($model->$relation->crops) ? ($model->$relation->crops) : '' ?></td>
        </tr>
        <tr>
            <th>Owner</th>
            <td><?php echo isset($model->$relation->owner) ? ($model->$relation->owner) : '' ?></td>
        </tr>
        <tr>
            <th>Land Area Size</th>
            <td><?php echo isset($model->$relation->land_area_size) ? ($model->$relation->land_area_size) : '' ?></td>
        </tr>
        <tr>
            <th>Land Area Type</th>
            <td><?php echo isset($model->$relation->land_area_type) ? ($model->$relation->land_area_type) : '' ?></td>
        </tr>
        </tbody>
    </table>
   <?php }
    ?>