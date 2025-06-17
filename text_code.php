<?php
/**
 * Created by PhpStorm.
 * User: asif.ghulamrasool
 * Date: 5/16/2024
 * Time: 4:06 PM
 */

//                        $schedule78 = Schedules::find()
//                            ->where(['loan_id' => $loan->id])
//                            ->andWhere(['>', 'due_date', strtotime(date("2024-06-10"))])
//                            ->andWhere(['<', 'due_date', strtotime(date("2024-09-10"))])
//                            ->all();
//
//                        $schedule56 = Schedules::find()
//                            ->where(['loan_id' => $loan->id])
//                            ->andWhere(['>', 'due_date', strtotime(date("2024-04-10"))])
//                            ->andWhere(['<', 'due_date', strtotime(date("2024-07-10"))])
//                            ->all();
//
//                        $extraAmount78 = 0;
//                        $extraAmount56 = 0;
//                        foreach ($schedule78 as $model) {
//                            $extraAmount78 = $extraAmount78+$model->charges_schdl_amnt_tax;
//                        }
//                        foreach ($schedule56 as $model) {
//                            $extraAmount56 = $extraAmount56+$model->charges_schdl_amnt_tax;
//                        }
//
//                        $extraAmount78 = $extraAmount78-$extraAmount56;
//
//                        $scheduleAbove8 = Schedules::find()
//                            ->where(['loan_id' => $loan->id])
//                            ->andWhere(['>', 'due_date', strtotime(date("2024-08-10"))])
//                            ->count('id');
//
//                        $scheduleAboveModel8 = Schedules::find()
//                            ->where(['loan_id' => $loan->id])
//                            ->andWhere(['>', 'due_date', strtotime(date("2024-08-10"))])
//                            ->all();
//                        $extraAmount78 = round($extraAmount78/$scheduleAbove8);
//
//                        foreach ($scheduleAboveModel8 as $model) {
//                            if($model->due_date  >= strtotime(date("2024-08-10"))){
//                                $taxAmount = $model->charges_schdl_amnt_tax;
//                                $model->charges_schdl_amnt_tax = $taxAmount+$extraAmount78;
//                                $model->save();
//                            }
//                        }







//foreach ($request['Guarantors'] as $key => $guarantor) {
//    if (!empty($guarantor['cnic'])) {
//        $modelGuarantor = Guarantors::find()
//            ->where(['cnic' => $guarantor['cnic']])
//            ->andWhere(['deleted' => 0])
//            ->one();
//        if(!empty($modelGuarantor) && $modelGuarantor!=null){
//            $guarantor_save = GroupHelper::saveGuarantor($guarantor, $group->id, 1,$loan_check,$application->id);
//            $modelLoan = Loans::find()
//                ->where(['group_id' => $modelGuarantor->group_id])
//                ->andWhere(['in','status', ['collected','not collected','pending']])
//                ->one();
//            if(!empty($modelLoan) && $modelLoan!=null){
//                $group->addError('id', 'Guarantor is already attached with active loan!');
//                $flag = false;
//                $guar[] = $guarantor_save;
//            }else{
//                $modelApplication = Applications::find()
//                    ->where(['group_id' => $modelGuarantor->group_id])
//                    ->andWhere(['status' => 'approved'])
//                    ->one();
//                if(!empty($modelApplication) && $modelApplication!=null){
//                    $group->addError('id', 'Guarantor is already attached with active application!');
//                    $flag = false;
//                    $guar[] = $guarantor_save;
//                }else{
//                    if (isset($guarantor_save->id)) {
//                        $guar[] = $guarantor_save;
//                    } else {
//                        $group->addError('id', 'Guarantor Not saved!');
//                        $flag = false;
//                        $guar[] = $guarantor_save;
//                    }
//                }
//            }
//        }else{
//            $guarantor_save = GroupHelper::saveGuarantor($guarantor, $group->id, 1,$loan_check,$application->id);
//            if (isset($guarantor_save->id)) {
//                $guar[] = $guarantor_save;
//
//            } else {
//                $flag = false;
//                $guar[] = $guarantor_save;
//
//                /*foreach($guarantor_save as $_key=>$_val){
//                    Yii::$app->request->post()['Guarantors'][$key]->addError($_key, $_val[0]);
//                }*/
//            }
//        }
//
//    }
//}