<?php
namespace console\controllers;

use common\components\Helpers\StructureHelper;
use common\models\Areas;
use common\models\Branches;
use common\models\BranchProjectsMapping;
use common\models\Regions;
use common\models\StructureTransfer;
use yii\console\Controller;
class StructureTransferController extends Controller
{


    public function actionTransfer()
    {
        $structure_transfers = StructureTransfer::find()->where(['status'=>0])->all();
        foreach ($structure_transfers as $structure_transfer){
            if($structure_transfer->obj_type == 'branches'){
                $old_area = Areas::find()->where(['id'=>$structure_transfer->old_value])->one();
                $new_area = Areas::find()->where(['id'=>$structure_transfer->new_value])->one();
                StructureHelper::transferBranch($structure_transfer->obj_id,$old_area,$new_area);
            }else if($structure_transfer->obj_type == 'areas'){
                $old_region = Regions::find()->where(['id'=>$structure_transfer->old_value])->one();
                $new_region = Regions::find()->where(['id'=>$structure_transfer->new_value])->one();
                StructureHelper::transferArea($structure_transfer->obj_id,$old_region,$new_region);
            }else if($structure_transfer->obj_type == 'regions'){
                /*$old_region = Regions::find()->where(['id'=>$structure_transfer->old_value])->one();
                $new_region = Regions::find()->where(['id'=>$structure_transfer->new_value])->one();
                StructureHelper::transferArea($structure_transfer->obj_id,$old_region,$new_region);*/
            }
        }
    }
    public function actionProjectMap($project_id)
    {
        $branches=Branches::find()->all();
        foreach ($branches as $br){
            $map=new BranchProjectsMapping();
            $map->project_id=$project_id;
            $map->branch_id=$br->id;
            $map->assigned_to=0;
            $map->created_by=0;
            $map->save();
        }
    }
}