<?php
$data = file_get_contents('php://input');
$data=json_decode($data,true);
foreach ($data as $detail) {
    $application_check = $conn->query('select * from borrowers where application_id='.$detail['application_id'].'');
    $exists= $application_check->fetch();
    if(!empty($exists)){
        $query_detail = $conn->prepare('update `borrowers`
                 set 
                  `name`=  "' . $detail['name'] . '",
                  `parentage`= "' . $detail['parentage'] . '",
                  `cnic` = "' . $detail['cnic'] . '",
                  `parentage_type`= "' . $detail['parentage_type'] . '",
                  `gender`= "' . $detail['gender'] . '",
                  `dob`="' . $detail['dob'] . '",
                  `education`="' . $detail['education'] . '",
                  `marital_status`="' . $detail['marital_status'] . '",
                  `family_member_name`="' . $detail['family_member_name'] . '",
                  `family_member_cnic`= "' . $detail['family_member_cnic'] . '",
                  `religion`= "' . $detail['religion'] . '",
                  `project`= "' . $detail['project'] . '",
                  `application_no`= "' . $detail['application_no'] . '",
                  `product`="' . $detail['product'] . '",
                  `purpose`= "' . $detail['purpose'] . '",
                  `sub_purpose`= "' . $detail['sub_purpose'] . '",
                  `application_date`="' . $detail['application_date'] . '",
                  `loan_amount`= "' . $detail['loan_amount'] . '",
                  `disbursed_amount`="' . $detail['disbursed_amount'] . '",
                  `sanction_no`= "' . $detail['sanction_no'] . '",
                  `region`="' . $detail['region'] . '",
                  `area`="' . $detail['area'] . '",
                  `branch`= "' . $detail['branch'] . '",
                  `city`="' . $detail['city'] . '",
                  `district`= "' . $detail['district'] . '",
                  `latitude`="' . $detail['latitude'] . '",
                  `longitude`= "' . $detail['longitude'] . '",
                  `province`="' . $detail['province'] . '",
                  `address`="' . $detail['address'] . '",
                  `mobile_no`="' . $detail['mobile_no'] . '",
                  `status`="' . $detail['status'] . '",
                  `member_id`="' . $detail['member_id'] . '",
                  `application_id`= "' . $detail['application_id'] . '",
                  `image_path`="' . $detail['image_path'] . '",
                  `province`="' . $detail['province'] . '",
                  `completion_percent`="' . $detail['completion_percent'] . '",
                  `total_visits`="' . $detail['visits_count'] . '",
                  `last_visit_date`="' . $detail['last_visit_date'] . '"
                  WHERE application_id=' . $detail['application_id'] . '
                
     ');
        $query_detail->execute();
    }else {
        $query_detail = $conn->prepare('INSERT INTO `borrowers`
                 ( `name`, `parentage`, `cnic`, 
                  `parentage_type`, `gender`, `dob`, 
                  `education`, `marital_status`, 
                  `family_member_name`, `family_member_cnic`,
                  `religion`, `project`, `application_no`, `product`,
                  `purpose`, `sub_purpose`, `application_date`, `loan_amount`,
                  `disbursed_amount`, `sanction_no`, `region`, `area`, `branch`,
                  `city`, `district`, `latitude`, `longitude`, `province`, `address`,
                  `mobile_no`, `status`, `member_id`, `application_id`,`completion_percent`,
                  `image_path`,`total_visits`,`last_visit_date`) VALUES (
                   "' . $detail['name'] . '",
                  "' . $detail['parentage'] . '",
                  "' . $detail['cnic'] . '",
                  "' . $detail['parentage_type'] . '",
                  "' . $detail['gender'] . '",
                  "' . $detail['dob'] . '",
                  "' . $detail['education'] . '",
                  "' . $detail['marital_status'] . '",
                  "' . $detail['family_member_name'] . '",
                  "' . $detail['family_member_cnic'] . '",
                  "' . $detail['religion'] . '",
                  "' . $detail['project'] . '",
                  "' . $detail['application_no'] . '",
                  "' . $detail['product'] . '",
                  "' . $detail['purpose'] . '",
                  "' . $detail['sub_purpose'] . '",
                  "' . $detail['application_date'] . '",
                  "' . $detail['loan_amount'] . '",
                  "' . $detail['disbursed_amount'] . '",
                  "' . $detail['sanction_no'] . '",
                  "' . $detail['region'] . '",
                  "' . $detail['area'] . '",
                  "' . $detail['branch'] . '",
                  "' . $detail['city'] . '",
                  "' . $detail['district'] . '",
                  "' . $detail['latitude'] . '",
                  "' . $detail['longitude'] . '",
                  "' . $detail['province'] . '",
                  "' . $detail['address'] . '",
                  "' . $detail['mobile_no'] . '",
                  "' . $detail['status'] . '",
                  "' . $detail['member_id'] . '",
                  "' . $detail['application_id'] . '",
                  "' . $detail['completion_percent'] . '",
                  "' . $detail['image_path'] . '",
                  "' . $detail['visits_count'] . '",
                  "' . $detail['last_visit_date'] . '"
                   )
     ');
        $query_detail->execute();

    }
}
echo json_encode(['response'=>'success']);
?>
