
$("document").ready(function(){
    /*if( $("#applications-appraisal_id").val()!="") {
     var appraisal_id = $("#applications-appraisal_id").val();
     var application_id = $("#applications-id").val();
     $.ajax({
     type: "POST",
     url: '/appraisals/form?id=' + appraisal_id + '&&application_id=' + application_id,
     success: function (data) {
     clear_header();
     clear_details();

     var obj = $.parseJSON(data);
     if (obj && obj.length > 0) {
     render_header(obj[0].table);
     $.each(obj, function (key, value) {

     if (value.answers != '') {
     render_select(value.table, value.column, value.place_holder, value.answers_values, 'required');
     } else {
     render_input(value.table, value.column, value.place_holder, value.default_visibility, 'required');
     }
     });
     }
     }
     });
     }*/
    var appraisal_id = $("#applications-appraisal_id").val();
    if(appraisal_id!="" && appraisal_id!=null) {
        var application_id = $("#applications-id").val();
        $.ajax({
            type: "POST",
            url: '/appraisals/form?id=' + appraisal_id + '&&application_id=' + application_id,
            success: function (data) {
                clear_header();
                clear_details();

                var obj = $.parseJSON(data);
                if (obj.success_type == 'false') {
                    //alert('a');
                    alert(obj.message);
                }
                else {
                    if (obj && obj.length > 0) {
                        render_header(obj[0].table);
                        $.each(obj, function (key, value) {
                            if (value.type == "single-select" || value.type == "radio_group") {
                                render_select(value.table, value.column, value.place_holder, value.answers_values, value.is_mendatory,value.label);
                            }
                            else if (value.type === "multi-select") {
                                 if(value.type_reset === "reverse"){
                                     render_input(value.table, value.column, value.place_holder, value.default_visibility, value.is_mendatory, 'text');
                                 }else{
                                //       render_input(value.table, value.column, value.place_holder, value.default_visibility, value.is_mendatory, value.format);
                                    render_checkbox(value.table, value.column, value.place_holder, value.answers_values, value.is_mendatory);
                                 }
                            }
                            else if (value.type == "date_picker") {
                                render_date(value.table, value.column, value.place_holder, value.default_visibility, value.is_mendatory,value.label);
                            }
                            else if (value.type == "recycler-view") {
                                //render_recycler_view(value.table, value.column, value.place_holder, value.default_visibility, value.is_mendatory,value.label);
                            }
                            else if (value.type == "add-more") {
                                render_recycler_view(value.table, value.column, value.place_holder, value.default_visibility, value.is_mendatory,value.label,value.id);
                            }
                            else {
                                render_input(value.table, value.column, value.place_holder, value.default_visibility, value.is_mendatory, value.format);
                            }
                        });
                        if (appraisal_id == '1') {
                            $.ajax({
                                type: "POST",
                                url: '/appraisals/get-project-id?application_id=' + application_id,
                                success: function (data) {
                                    var obj = $.parseJSON(data);
                                    if(obj.project_id=='3'){
                                        $(".field-appraisalssocial-domestic_assets").show();
                                        $(".field-appraisalssocial-house_condition").show();
                                        $(".field-appraisalssocial-live_stock_income").show();
                                        $(".field-appraisalssocial-through_cultivation").show();
                                        $(".field-appraisalssocial-pension").show();
                                        $(".field-appraisalssocial-who_will_earn").hide();
                                        $(".field-appraisalssocial-earning_person_name").hide();
                                        $(".field-appraisalssocial-earning_person_cnic").hide();

                                        $('#appraisalssocial-domestic_assets').attr('required', 'required');
                                        $('#appraisalssocial-house_condition').attr('required', 'required');
                                        $('#appraisalssocial-live_stock_income').attr('required', 'required');
                                        $('#appraisalssocial-through_cultivation').attr('required', 'required');
                                        $('#appraisalssocial-pension').attr('required', 'required');
                                    }else if(obj.project_id=='52'){
                                        $(".field-appraisalssocial-client_contribution").show();
                                        $('#appraisalssocial-client_contribution').attr('required', 'required');
                                        $("#appraisalssocial-earning_person_cnic").inputmask("99999-9999999-9");
                                        $('#appraisalssocial-earning_person_name').attr('required', 'required');
                                        $('#appraisalssocial-earning_person_cnic').attr('required', 'required');
                                        $(".field-appraisalssocial-domestic_assets").hide();
                                        $(".field-appraisalssocial-house_condition").hide();
                                        $(".field-appraisalssocial-live_stock_income").hide();
                                        $(".field-appraisalssocial-through_cultivation").hide();
                                        $(".field-appraisalssocial-pension").hide();
                                        $(".field-appraisalssocial-who_will_earn").show();
                                        $(".field-appraisalssocial-earning_person_name").show();
                                        $(".field-appraisalssocial-earning_person_cnic").show();
                                    }else if(obj.project_id=='59'){
                                        $("#appraisalssocial-source_of_income").val('both');
                                        $(".field-appraisalssocial-source_of_income").hide();

                                        $("#appraisalssocial-land_size").val(0);
                                        $(".field-appraisalssocial-land_size").hide();

                                        $("#appraisalssocial-business_income").val('0');
                                        $(".field-appraisalssocial-business_income").hide();

                                        $("#appraisalssocial-job_income").val('0');
                                        $(".field-appraisalssocial-job_income").hide();

                                        $("#appraisalssocial-house_rent_income").val('0');
                                        $(".field-appraisalssocial-house_rent_income").hide();

                                        $("#appraisalssocial-other_income").val('0');
                                        $(".field-appraisalssocial-other_income").hide();

                                        $("#appraisalssocial-utility_bills").val('0');
                                        $(".field-appraisalssocial-utility_bills").hide();

                                        $("#appraisalssocial-medical_expenses").val('0');
                                        $(".field-appraisalssocial-medical_expenses").hide();

                                        $("#appraisalssocial-kitchen_expenses").val('0');
                                        $(".field-appraisalssocial-kitchen_expenses").hide();

                                        $("#appraisalssocial-other_expenses").val('0');
                                        $(".field-appraisalssocial-other_expenses").hide();

                                        $("#appraisalssocial-monthly_savings").val('no_saving');
                                        $(".field-appraisalssocial-monthly_savings").hide();

                                        $("#appraisalssocial-other_loan").val('0');
                                        $(".field-appraisalssocial-other_loan").hide();

                                        $("#appraisalssocial-educational_expenses").val(0);
                                        $(".field-appraisalssocial-educational_expenses").hide();

                                        $("#appraisalssocial-who_will_earn").val('self');
                                        $(".field-appraisalssocial-who_will_earn").hide();

                                        $(".field-appraisalssocial-income_before_corona").show();
                                        $(".field-appraisalssocial-income_after_corona").show();
                                        $(".field-appraisalssocial-expenses_in_corona").show();
                                        $(".field-appraisalssocial-description").show();

                                    }else{
                                        $(".field-appraisalssocial-domestic_assets").hide();
                                        $(".field-appraisalssocial-house_condition").hide();
                                        $(".field-appraisalssocial-live_stock_income").hide();
                                        $(".field-appraisalssocial-through_cultivation").hide();
                                        $(".field-appraisalssocial-pension").hide();
                                        $(".field-appraisalssocial-who_will_earn").hide();
                                        $(".field-appraisalssocial-earning_person_name").hide();
                                        $(".field-appraisalssocial-earning_person_cnic").hide();
                                    }
                                }
                            });
                            $("#total").show();
                            $(".field-appraisalssocial-house_rent_amount").hide();
                            //$(".field-appraisalssocial-loan_amount").show();
                            //$(".field-appraisalssocial-date_of_maturity").show();
                            $(".field-appraisalssocial-total_household_income").hide();
                            $(".field-appraisalssocial-total_expenses").hide();
                            $(".field-appraisalssocial-total_family_members").hide();
                            //$('#appraisalssocial-amount').attr('required', 'required');
                            //$('#appraisalssocial-loan_amount').attr('required', 'required');
                            $('.field-appraisalssocial-business_income').show();
                            $('.field-appraisalssocial-job_income').show();

                            $('#appraisalssocial-date_of_maturity').removeAttr('required');
                            $('#appraisalssocial-land_size').attr('required', 'required');
                            $('#appraisalssocial-ladies').attr('required', 'required');
                            $('#appraisalssocial-gents').attr('required', 'required');
                            $('#appraisalssocial-business_income').attr('required', 'required');
                            $('#appraisalssocial-job_income').attr('required', 'required');
                            $('#appraisalssocial-house_rent_income').attr('required', 'required');
                            $('#appraisalssocial-other_income').attr('required', 'required');
                            $('#appraisalssocial-utility_bills').attr('required', 'required');
                            $('#appraisalssocial-educational_expenses').attr('required', 'required');
                            $('#appraisalssocial-medical_expenses').attr('required', 'required');
                            $('#appraisalssocial-kitchen_expenses').attr('required', 'required');
                            $('#appraisalssocial-other_expenses').attr('required', 'required');
                        }
                        if (appraisal_id == '2') {
                            $("#total").hide();
                            $('#appraisalsbusiness-running_capital_amount').attr('required', 'required');
                            $('#appraisalsbusiness-fixed_business_assets_amount').attr('required', 'required');
                            $('#appraisalsbusiness-business_expenses_amount').attr('required', 'required');
                            $('#appraisalsbusiness-new_required_assets_amount').attr('required', 'required');
                        }
                        if (appraisal_id == '4') {
                            /*$('#appraisalshousing-estimated_start_date').datepicker({
                                setDate: new Date(),
                                format: 'dd/mm/yyyy',
                                todayHighlight: true,
                                autoclose: true,
                                startDate: '-0m',
                                minDate: 0,
                                onSelect: function(text, dt) {
                                    $('#end').datepicker('option', 'minDate', text);
                                }
                            });*/
                            $("#total").hide();
                            $(".field-appraisalshousing-living_duration").hide();
                            $(".field-appraisalshousing-duration_type").hide();
                            /*$(".field-appraisalshousing-residential_area").hide();*/
                            $(".field-appraisalshousing-no_of_rooms").hide();
                            $(".field-appraisalshousing-no_of_kitchens").hide();
                            $(".field-appraisalshousing-no_of_toilets").hide();

                            $("#appraisalshousing-living_duration").val(0);
                            $("#appraisalshousing-duration_type").val('months');
                            /*$("#appraisalshousing-residential_area").val(0);*/
                            $("#appraisalshousing-no_of_rooms").val(0);
                            $("#appraisalshousing-no_of_kitchens").val(0);
                            $("#appraisalshousing-no_of_toilets").val(0);
                        }
                    }
                }
            }
        });
    }

    $("#applications-appraisal_id").change(function(){
        var appraisal_id = $("#applications-appraisal_id").val();
        var application_id=$("#applications-id").val();
        $.ajax({
            type: "POST",
            url: '/appraisals/form?id='+appraisal_id+'&&application_id='+application_id,
            success: function(data){
                clear_header();
                clear_details();

                var obj = $.parseJSON(data);
                if (obj.success_type == 'false') {
                    //alert('a');
                    alert(obj.message);
                }
                else {
                    if (obj && obj.length > 0) {
                        render_header(obj[0].table);
                        $.each(obj, function (key, value) {
                            if (value.type == "single-select" || value.type == "radio_group") {
                                render_select(value.table, value.column, value.place_holder, value.answers_values, value.is_mendatory,value.label);
                            }
                            else if (value.type == "multi-select") {
                                if(value.type_reset === "reverse"){
                                    render_input(value.table, value.column, value.place_holder, value.default_visibility, value.is_mendatory, 'text');
                                }else{
                                // render_input(value.table, value.column, value.place_holder, value.default_visibility, value.is_mendatory, value.format);
                                     render_checkbox(value.table, value.column, value.place_holder, value.answers_values, value.is_mendatory);
                                }

                            }
                            else if (value.type == "date_picker") {
                                render_date(value.table, value.column, value.place_holder, value.default_visibility, value.is_mendatory,value.label);
                            }
                            else if (value.type == "recycler-view") {
                                //render_recycler_view(value.table, value.column, value.place_holder, value.default_visibility, value.is_mendatory,value.label);
                            }
                            else if (value.type == "add-more") {
                                render_recycler_view(value.table, value.column, value.place_holder, value.default_visibility, value.is_mendatory,value.label,value.id);
                            }
                            else {
                                render_input(value.table, value.column, value.place_holder, value.default_visibility, value.is_mendatory, value.format);
                            }
                        });
                        if (appraisal_id == '1') {

                            $.ajax({
                                type: "POST",
                                url: '/appraisals/get-project-id?application_id=' + application_id,
                                success: function (data) {
                                    var obj = $.parseJSON(data);
                                    if(obj.project_id=='3'){
                                        $(".field-appraisalssocial-domestic_assets").show();
                                        $(".field-appraisalssocial-house_condition").show();
                                        $(".field-appraisalssocial-live_stock_income").show();
                                        $(".field-appraisalssocial-through_cultivation").show();
                                        $(".field-appraisalssocial-pension").show();
                                        $(".field-appraisalssocial-who_will_earn").hide();
                                        $(".field-appraisalssocial-earning_person_name").hide();
                                        $(".field-appraisalssocial-earning_person_cnic").hide();

                                        $('#appraisalssocial-domestic_assets').attr('required', 'required');
                                        $('#appraisalssocial-house_condition').attr('required', 'required');
                                        $('#appraisalssocial-live_stock_income').attr('required', 'required');
                                        $('#appraisalssocial-through_cultivation').attr('required', 'required');
                                        $('#appraisalssocial-pension').attr('required', 'required');
                                    }else if(obj.project_id=='52'){
                                        $(".field-appraisalssocial-client_contribution").show();
                                        $('#appraisalssocial-client_contribution').attr('required', 'required');
                                        $(".field-appraisalssocial-who_will_earn").show();
                                        $(".field-appraisalssocial-earning_person_name").show();
                                        $(".field-appraisalssocial-earning_person_cnic").show();
                                        $("#appraisalssocial-earning_person_cnic").inputmask("99999-9999999-9");
                                        $('#appraisalssocial-earning_person_name').attr('required', 'required');
                                        $('#appraisalssocial-earning_person_cnic').attr('required', 'required');

                                        $(".field-appraisalssocial-domestic_assets").hide();
                                        $(".field-appraisalssocial-house_condition").hide();
                                        $(".field-appraisalssocial-live_stock_income").hide();
                                        $(".field-appraisalssocial-through_cultivation").hide();
                                        $(".field-appraisalssocial-pension").hide();
                                    }else if(obj.project_id=='59'){
                                        $("#appraisalssocial-source_of_income").val('both');
                                        $(".field-appraisalssocial-source_of_income").hide();

                                        $("#appraisalssocial-land_size").val(0);
                                        $(".field-appraisalssocial-land_size").hide();

                                        $("#appraisalssocial-business_income").val('0');
                                        $(".field-appraisalssocial-business_income").hide();

                                        $("#appraisalssocial-job_income").val('0');
                                        $(".field-appraisalssocial-job_income").hide();

                                        $("#appraisalssocial-house_rent_income").val('0');
                                        $(".field-appraisalssocial-house_rent_income").hide();

                                        $("#appraisalssocial-other_income").val('0');
                                        $(".field-appraisalssocial-other_income").hide();

                                        $("#appraisalssocial-utility_bills").val('0');
                                        $(".field-appraisalssocial-utility_bills").hide();

                                        $("#appraisalssocial-medical_expenses").val('0');
                                        $(".field-appraisalssocial-medical_expenses").hide();

                                        $("#appraisalssocial-kitchen_expenses").val('0');
                                        $(".field-appraisalssocial-kitchen_expenses").hide();

                                        $("#appraisalssocial-other_expenses").val('0');
                                        $(".field-appraisalssocial-other_expenses").hide();

                                        $("#appraisalssocial-monthly_savings").val('no_saving');
                                        $(".field-appraisalssocial-monthly_savings").hide();

                                        $("#appraisalssocial-other_loan").val('0');
                                        $(".field-appraisalssocial-other_loan").hide();

                                        $("#appraisalssocial-educational_expenses").val(0);
                                        $(".field-appraisalssocial-educational_expenses").hide();

                                        $("#appraisalssocial-who_will_earn").val('self');
                                        $(".field-appraisalssocial-who_will_earn").hide();


                                        $(".field-appraisalssocial-income_before_corona").show();
                                        $(".field-appraisalssocial-income_after_corona").show();
                                        $(".field-appraisalssocial-expenses_in_corona").show();
                                        $(".field-appraisalssocial-description").show();

                                    }else{
                                        $(".field-appraisalssocial-domestic_assets").hide();
                                        $(".field-appraisalssocial-house_condition").hide();
                                        $(".field-appraisalssocial-live_stock_income").hide();
                                        $(".field-appraisalssocial-through_cultivation").hide();
                                        $(".field-appraisalssocial-pension").hide();
                                        $(".field-appraisalssocial-who_will_earn").hide();
                                        $(".field-appraisalssocial-earning_person_name").hide();
                                        $(".field-appraisalssocial-earning_person_cnic").hide();
                                    }
                                }
                            });
                            $("#total").show();
                            $(".field-appraisalssocial-house_rent_amount").hide();
                            //$(".field-appraisalssocial-loan_amount").show();
                            //$(".field-appraisalssocial-date_of_maturity").show();
                            $(".field-appraisalssocial-total_household_income").hide();
                            $(".field-appraisalssocial-total_expenses").hide();
                            $(".field-appraisalssocial-total_family_members").hide();
                            $(".field-appraisalssocial-amount").hide();
                            //$('#appraisalssocial-amount').attr('required', 'required');
                            //$('#appraisalssocial-loan_amount').attr('required', 'required');
                            $('.field-appraisalssocial-business_income').show();
                            $('.field-appraisalssocial-job_income').show();

                            $('#appraisalssocial-date_of_maturity').removeAttr('required');
                            $('#appraisalssocial-land_size').attr('required', 'required');
                            $('#appraisalssocial-ladies').attr('required', 'required');
                            $('#appraisalssocial-gents').attr('required', 'required');
                            $('#appraisalssocial-business_income').attr('required', 'required');
                            $('#appraisalssocial-job_income').attr('required', 'required');
                            $('#appraisalssocial-house_rent_income').attr('required', 'required');
                            $('#appraisalssocial-other_income').attr('required', 'required');
                            $('#appraisalssocial-utility_bills').attr('required', 'required');
                            $('#appraisalssocial-educational_expenses').attr('required', 'required');
                            $('#appraisalssocial-medical_expenses').attr('required', 'required');
                            $('#appraisalssocial-kitchen_expenses').attr('required', 'required');
                            $('#appraisalssocial-other_expenses').attr('required', 'required');
                        }
                        if (appraisal_id == '2') {
                            $("#total").hide();
                            $('#appraisalsbusiness-running_capital_amount').attr('required', 'required');
                            $('#appraisalsbusiness-fixed_business_assets_amount').attr('required', 'required');
                            $('#appraisalsbusiness-business_expenses_amount').attr('required', 'required');
                            $('#appraisalsbusiness-new_required_assets_amount').attr('required', 'required');
                        }
                        if (appraisal_id == '4') {
                              /*$('#appraisalshousing-estimated_start_date').datepicker({
                                setDate: new Date(),
                                format: 'dd/mm/yyyy',
                                todayHighlight: true,
                                autoclose: true,
                                startDate: '-0m',
                                minDate: 0,
                                onSelect: function(text, dt) {
                                    $('#end').datepicker('option', 'minDate', text);
                                }
                              });*/
                            $("#total").hide();
                            $(".field-appraisalshousing-living_duration").hide();
                            $(".field-appraisalshousing-duration_type").hide();
                            /*$(".field-appraisalshousing-residential_area").hide();*/
                            $(".field-appraisalshousing-no_of_rooms").hide();
                            $(".field-appraisalshousing-no_of_kitchens").hide();
                            $(".field-appraisalshousing-no_of_toilets").hide();

                            $("#appraisalshousing-living_duration").val(0);
                            $("#appraisalshousing-duration_type").val('months');
                           /* $("#appraisalshousing-residential_area").val(0);*/
                            $("#appraisalshousing-no_of_rooms").val(0);
                            $("#appraisalshousing-no_of_kitchens").val(0);
                            $("#appraisalshousing-no_of_toilets").val(0);
                        }

                    }
                }
            }
        });
    });
    document.addEventListener('click', function (e) {
        if(e.target.id == 'recycler-view'){
            var class_name=e.target.className.split(" ", 1);
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: '/appraisals/recycler-form?field=' + class_name  ,
                success: function (data) {
                    var obj = $.parseJSON(data);
                    if (obj.success_type == 'false') {
                        //alert('a');
                        alert(obj.message);
                    }
                    else {
                        if (obj && obj.length > 0) {
                            var count=document.querySelectorAll('.one_two_many_count').length;
                            var recycler_div = document.createElement("div");
                            recycler_div.setAttribute("id",'one_two_many_count_'+count);
                            recycler_div.setAttribute("class",'one_two_many_count');
                            /*recycler_div.style.border = '5px solid #f5f5f5';
                             recycler_div.style.width = '1px 5 5px 5';*/
                            document.getElementById(class_name).appendChild(recycler_div);
                            $.each(obj, function (key, value) {
                                var count=document.querySelectorAll('.one_two_many_count').length;

                                if (value.type == "single-select" || value.type == "radio_group") {
                                    render_select_recycler(value.table, value.column, value.place_holder, value.answers_values, value.is_mendatory, value.label,'one_two_many_count_'+(count-1),count);
                                }
                                else if (value.type == "multi-select") {
                                    if(value.type_reset === "reverse"){
                                        render_input(value.table, value.column, value.place_holder, value.default_visibility, value.is_mendatory, 'text');
                                    }else{
                                        render_checkbox_recycler(value.table, value.column, value.place_holder, value.answers_values, value.is_mendatory,'one_two_many_count_'+(count-1),count);
                                    }
                                 }
                                 else if (value.type == "date_picker") {
                                 render_date_recycler(value.table, value.column, value.place_holder, value.default_visibility, value.is_mendatory, value.label,'one_two_many_count_'+(count-1),count);
                                 }
                                 else {
                                 render_input_recycler(value.table, value.column, value.place_holder, value.default_visibility, value.is_mendatory, value.format,'one_two_many_count_'+(count-1),count);
                                 }
                            });
                            var br_tag1 = document.createElement("br");
                            document.getElementById('one_two_many_count_'+count).appendChild(br_tag1);
                            var br_tag2 = document.createElement("br");
                            document.getElementById('one_two_many_count_'+count).appendChild(br_tag2);
                            /*var br_tag3 = document.createElement("br");
                            document.getElementById('one_two_many_count_'+count).appendChild(br_tag3);
                            var br_tag4 = document.createElement("br");
                            document.getElementById('one_two_many_count_'+count).appendChild(br_tag4);*/
                            /*var br_tag5 = document.createElement("br");
                            document.getElementById('one_two_many_count_'+count).appendChild(br_tag5);
                            var br_tag6 = document.createElement("br");
                            document.getElementById('one_two_many_count_'+count).appendChild(br_tag6);
                            var br_tag7 = document.createElement("br");
                            document.getElementById('one_two_many_count_'+count).appendChild(br_tag7);
                            var br_tag8 = document.createElement("br");
                            document.getElementById('one_two_many_count_'+count).appendChild(br_tag8);
                            var br_tag9 = document.createElement("br");
                            document.getElementById('one_two_many_count_'+count).appendChild(br_tag9);*/
                        }
                    }
                }
            });
        }
    });
    document.addEventListener('change', function (e) {

        if (e.target.id == 'appraisalssocial-house_ownership') {
            var houseownership = $("#appraisalssocial-house_ownership").val();
            if (houseownership == 'rented') {
                $(".field-appraisalssocial-house_rent_amount").show();
                $('#appraisalssocial-house_rent_amount').attr('required', 'required');

            } else {
                $('#appraisalssocial-house_rent_amount').removeAttr('required');
                $("#appraisalssocial-house_rent_amount").val('');
                $(".field-appraisalssocial-house_rent_amount").hide();
            }
        }
        else if (e.target.id == 'appraisalssocial-monthly_savings') {
            var saving = $("#appraisalssocial-monthly_savings").val();
            if (saving == 'no_saving') {

                $('#appraisalssocial-amount').removeAttr('required');
                $('#appraisalssocial-date_of_maturity').removeAttr('required');

                $("#appraisalssocial-amount").val('');
                $(".field-appraisalssocial-amount").hide();

                $("#appraisalssocial-date_of_maturity").val('');
                $(".field-appraisalssocial-date_of_maturity").hide();

            }else if(saving=='committee'){
                $('#appraisalssocial-amount').attr('required', 'required');
                $(".field-appraisalssocial-amount").show();

                $('#appraisalssocial-date_of_maturity').attr('required', 'required');
                $(".field-appraisalssocial-date_of_maturity").show();
            }
            else {
                $('#appraisalssocial-amount').attr('required', 'required');
                $(".field-appraisalssocial-amount").show();
                $('#appraisalssocial-date_of_maturity').removeAttr('required');
                $("#appraisalssocial-date_of_maturity").val('');
                $(".field-appraisalssocial-date_of_maturity").hide();
            }
        }
        else if (e.target.id == 'appraisalssocial-other_loan') {
            var otherloan = $("#appraisalssocial-other_loan").val();
            if (otherloan == '0') {
                $("#appraisalssocial-loan_amount").val('');
                $(".field-appraisalssocial-loan_amount").hide();
                $('#appraisalssocial-loan_amount').removeAttr('required');
            } else {
                $('#appraisalssocial-loan_amount').attr('required', 'required');
                $(".field-appraisalssocial-loan_amount").show();
            }
        }
        else if (e.target.id == 'appraisalssocial-source_of_income') {
            var source = $("#appraisalssocial-source_of_income").val();
            if (source == 'employment') {
                $(".field-appraisalssocial-job_income").show();
                $('#appraisalssocial-job_income').attr('required','required');

                $("#appraisalssocial-business_income").val('');
                $(".field-appraisalssocial-business_income").hide();
                $('#appraisalssocial-business_income').removeAttr('required');
            } else if(source=="business") {
                $(".field-appraisalssocial-business_income").show();
                $('#appraisalssocial-business_income').attr('required','required');

                $("#appraisalssocial-job_income").val('');
                $(".field-appraisalssocial-job_income").hide();
                $('#appraisalssocial-job_income').removeAttr('required');
            }else{
                $(".field-appraisalssocial-job_income").show();
                $('#appraisalssocial-job_income').attr('required','required');

                $(".field-appraisalssocial-business_income").show();
                $('#appraisalssocial-business_income').attr('required','required');
            }
        }
        else if (e.target.id == 'appraisalshousing-property_type') {
            if(e.target.value=='house'){
                $(".field-appraisalshousing-living_duration").show();
                $(".field-appraisalshousing-duration_type").show();
                /*$(".field-appraisalshousing-residential_area").show();*/
                $(".field-appraisalshousing-no_of_rooms").show();
                $(".field-appraisalshousing-no_of_kitchens").show();
                $(".field-appraisalshousing-no_of_toilets").show();
            }
            else {
                $(".field-appraisalshousing-living_duration").hide();
                $(".field-appraisalshousing-duration_type").hide();
                /*$(".field-appraisalshousing-residential_area").hide();*/
                $(".field-appraisalshousing-no_of_rooms").hide();
                $(".field-appraisalshousing-no_of_kitchens").hide();
                $(".field-appraisalshousing-no_of_toilets").hide();
            }

        }
        else if (e.target.id == 'appraisalssocial-business_income' || e.target.id == 'appraisalssocial-job_income' || e.target.id == 'appraisalssocial-house_rent_income' || e.target.id == 'appraisalssocial-other_income' || e.target.id == 'appraisalssocial-live_stock_income'  || e.target.id == 'appraisalssocial-pension' || e.target.id == 'appraisalssocial-through_cultivation') {
            var job_income = $.isNumeric($("#appraisalssocial-job_income").val()) ?  $("#appraisalssocial-job_income").val(): 0;
            var business_income = $.isNumeric($("#appraisalssocial-business_income").val()) ?  $("#appraisalssocial-business_income").val(): 0;
            var house_rent_income = $.isNumeric($("#appraisalssocial-house_rent_income").val()) ?  $("#appraisalssocial-house_rent_income").val(): 0;
            var other_income = $.isNumeric($("#appraisalssocial-other_income").val()) ?  $("#appraisalssocial-other_income").val(): 0;
            var live_stock_income = $.isNumeric($("#appraisalssocial-live_stock_income").val()) ?  $("#appraisalssocial-live_stock_income").val(): 0;
            var pension = $.isNumeric($("#appraisalssocial-pension").val()) ?  $("#appraisalssocial-pension").val(): 0;
            var through_cultivation = $.isNumeric($("#appraisalssocial-through_cultivation").val()) ?  $("#appraisalssocial-through_cultivation").val(): 0;

            var total=parseInt(job_income) + parseInt(business_income)+ parseInt(house_rent_income)+ parseInt(other_income)+ parseInt(live_stock_income)+ parseInt(pension)+parseInt(through_cultivation);

            $("#total-income").text('');
            $("#total-income").append(total);
        }
        else if (e.target.id == 'appraisalssocial-utility_bills' || e.target.id == 'appraisalssocial-educational_expenses' || e.target.id == 'appraisalssocial-medical_expenses' || e.target.id == 'appraisalssocial-kitchen_expenses' || e.target.id == 'appraisalssocial-other_expenses' || e.target.id == 'appraisalssocial-amount' || e.target.id == 'appraisalssocial-loan_amount' || e.target.id == 'appraisalssocial-house_rent_amount') {
            var educational_expenses = $.isNumeric($("#appraisalssocial-educational_expenses").val()) ?  $("#appraisalssocial-educational_expenses").val(): 0;
            var medical_expenses = $.isNumeric($("#appraisalssocial-medical_expenses").val()) ?  $("#appraisalssocial-medical_expenses").val(): 0;
            var kitchen_expenses = $.isNumeric($("#appraisalssocial-kitchen_expenses").val()) ?  $("#appraisalssocial-kitchen_expenses").val(): 0;
            var utility_bills = $.isNumeric($("#appraisalssocial-utility_bills").val()) ?  $("#appraisalssocial-utility_bills").val(): 0;
            var other_expenses = $.isNumeric($("#appraisalssocial-other_expenses").val()) ?  $("#appraisalssocial-other_expenses").val(): 0;

            var house_rent_amount = $.isNumeric($("#appraisalssocial-house_rent_amount").val()) ?  $("#appraisalssocial-house_rent_amount").val(): 0;
            var loan_amount = $.isNumeric($("#appraisalssocial-loan_amount").val()) ?  $("#appraisalssocial-loan_amount").val(): 0;
            var saving = $.isNumeric($("#appraisalssocial-amount").val()) ?  $("#appraisalssocial-amount").val(): 0;
            var others=(parseInt(house_rent_amount) + parseInt(loan_amount) + parseInt(saving));

            var expenses=(parseInt(others)+parseInt(educational_expenses) + parseInt(medical_expenses) + parseInt(kitchen_expenses) + parseInt(utility_bills) + parseInt(other_expenses));


            $("#total-expenses").text('');
            $("#total-expenses").append(expenses);
        } else if (e.target.id == 'appraisalssocial-who_will_earn') {
            if(e.target.value=='self') {
                $(".field-appraisalssocial-earning_person_name").hide();
                $(".field-appraisalssocial-earning_person_cnic").hide();
                $("#appraisalssocial-earning_person_cnic").val('');
                $("#appraisalssocial-earning_person_name").val('');
                $('#appraisalssocial-earning_person_cnic').removeAttr('required');
                $('#appraisalssocial-earning_person_name').removeAttr('required');
            }else{
                $(".field-appraisalssocial-earning_person_name").show();
                $(".field-appraisalssocial-earning_person_cnic").show();
                $('#appraisalssocial-earning_person_name').attr('required','required');
                $('#appraisalssocial-earning_person_cnic').attr('required','required');
            }
        }

    }, false);
    var form = document.getElementById("w0");
    document.getElementById("btn-create").addEventListener("click", function (event) {
        if ($("#applications-appraisal_id").val() == '1') {
            var educational_expenses = $.isNumeric($("#appraisalssocial-educational_expenses").val()) ?  $("#appraisalssocial-educational_expenses").val(): 0;
            var medical_expenses = $.isNumeric($("#appraisalssocial-medical_expenses").val()) ?  $("#appraisalssocial-medical_expenses").val(): 0;
            var kitchen_expenses = $.isNumeric($("#appraisalssocial-kitchen_expenses").val()) ?  $("#appraisalssocial-kitchen_expenses").val(): 0;
            var utility_bills = $.isNumeric($("#appraisalssocial-utility_bills").val()) ?  $("#appraisalssocial-utility_bills").val(): 0;
            var other_expenses = $.isNumeric($("#appraisalssocial-other_expenses").val()) ?  $("#appraisalssocial-other_expenses").val(): 0;
            var expenses=(parseInt(educational_expenses) + parseInt(medical_expenses) + parseInt(kitchen_expenses) + parseInt(utility_bills) + parseInt(other_expenses));

            var house_rent_amount = $.isNumeric($("#appraisalssocial-house_rent_amount").val()) ?  $("#appraisalssocial-house_rent_amount").val(): 0;
            var loan_amount = $.isNumeric($("#appraisalssocial-loan_amount").val()) ?  $("#appraisalssocial-loan_amount").val(): 0;
            var others=(parseInt(house_rent_amount) + parseInt(loan_amount));

            var saving = $.isNumeric($("#appraisalssocial-amount").val()) ?  $("#appraisalssocial-amount").val(): 0;

            var job_income = $.isNumeric($("#appraisalssocial-job_income").val()) ?  $("#appraisalssocial-job_income").val(): 0;
            var business_income = $.isNumeric($("#appraisalssocial-business_income").val()) ?  $("#appraisalssocial-business_income").val(): 0;
            var house_rent_income = $.isNumeric($("#appraisalssocial-house_rent_income").val()) ?  $("#appraisalssocial-house_rent_income").val(): 0;
            var live_stock_income = $.isNumeric($("#appraisalssocial-live_stock_income").val()) ?  $("#appraisalssocial-live_stock_income").val(): 0;
            var pension = $.isNumeric($("#appraisalssocial-pension").val()) ?  $("#appraisalssocial-pension").val(): 0;
            var through_cultivation = $.isNumeric($("#appraisalssocial-through_cultivation").val()) ?  $("#appraisalssocial-through_cultivation").val(): 0;

            var other_income = $.isNumeric($("#appraisalssocial-other_income").val()) ?  $("#appraisalssocial-other_income").val(): 0;
            var income=(parseInt(job_income) + parseInt(business_income) + parseInt(house_rent_income) + parseInt(other_income)+ parseInt(live_stock_income)+ parseInt(pension)+parseInt(through_cultivation));
            if(parseInt(income)!=(parseInt(expenses)+parseInt(others)+parseInt(saving))){
                alert("(Expenses + Savings + Others) Not Equal to "+ income);
                event.preventDefault();
            }
            var earning_hands = $.isNumeric($("#appraisalssocial-no_of_earning_hands").val()) ?  $("#appraisalssocial-no_of_earning_hands").val(): 0;
            var males = $.isNumeric($("#appraisalssocial-gents").val()) ?  $("#appraisalssocial-gents").val(): 0;
            var females = $.isNumeric($("#appraisalssocial-ladies").val()) ?  $("#appraisalssocial-ladies").val(): 0;
            var member=parseInt(males)+parseInt(females);
            if(earning_hands>member){
                alert("Earning hands more then total family members");
                event.preventDefault();
            }

        }
        /*else if($("#applications-appraisal_id").val() == '2'){
         alert("a");
         var theForm = $('#w0');
         sessionStorage.setItem('formHTML', JSON.stringify(theForm.clone(true).html()));
         }*/

    });
    function clear_header() {
        var myNode = document.getElementById("project-header");
        while (myNode.firstChild) {
            myNode.removeChild(myNode.firstChild);
        }
    }
    function render_header(model) {
        model_caps = ucwords(model);
        model_caps = model_caps.replace(/([A-Z])/g, ' $1');

        var tbl = document.createElement("div");
        tbl.classList.add('tbl');

        var tbl_row = document.createElement("div");
        tbl_row.classList.add('tbl-row');
        tbl.appendChild(tbl_row);

        var tbl_cell = document.createElement("div");
        tbl_cell.classList.add('tbl-cell');
        tbl_row.appendChild(tbl_cell);

        var h4 = document.createElement("h4");
        var myarr = model_caps.split(" ");
        var t = document.createTextNode(myarr[2] + " " + myarr[1]);

        h4.appendChild(t)
        tbl_cell.appendChild(h4);

        document.getElementById("project-header").appendChild(tbl);
    }
    function clear_details() {
        var myNode = document.getElementById("project-details");
        while (myNode.firstChild) {
            myNode.removeChild(myNode.firstChild);
        }
    }
    function render_input(model,column,placeholder,visiblility,required,format) {

        model_caps = ucwords(model);
        model_remove_underscores = model.replace(/_/g,'');

        var col_lg_3 = document.createElement("div");
        col_lg_3.classList.add('col-lg-3');
        col_lg_3.classList.add('field-'+model_remove_underscores+'-'+column);
        if(visiblility=="gone") {
            col_lg_3.setAttribute("style", 'display:none');
        }
        var form_group = document.createElement("div");
        form_group.classList.add('form-group');
        //form_group.classList.add('field-'+model_remove_underscores+'-'+column);

        form_group.classList.add(required);
        //form_group.classList.add(has-error);
        col_lg_3.appendChild(form_group);

        var label = document.createElement("label");
        label.classList.add("control-label");
        label.setAttribute("for", model_remove_underscores+'-'+column);
        /*if(visiblility ==  "gone") {
         label.setAttribute("style", 'display:none');
         }*/
        var label_text = document.createTextNode(placeholder);
        label.appendChild(label_text)
        form_group.appendChild(label);

        var input = document.createElement("input");
        if(format=="number"){
            if(column=='earning_person_cnic'){
                input.setAttribute("type", 'text');
                input.setAttribute("maxlength", '15');
            }else{
                input.setAttribute("type", format);
                input.setAttribute("min", 0);
            }
        }else{
                input.setAttribute("type", format);
        }
        input.setAttribute("placeholder", placeholder);
        input.setAttribute("id", model_remove_underscores+'-'+column);
        input.classList.add("form-control");
        input.classList.add(model_remove_underscores+'-'+column);
        input.setAttribute("name", model_caps+'['+column+']');
        input.setAttribute("aria-required", "true");
        if(required=="1") {
            input.setAttribute("required", 'true');
        }
        form_group.appendChild(input);

        var help_block = document.createElement("div");
        help_block.classList.add("help-block");
        form_group.appendChild(help_block);

        document.getElementById("project-details").appendChild(col_lg_3);
    }
    function render_select(model,column,placeholder,answers_values,required,label1) {
        /*alert(column);
         alert(answers_values[0].value);*/
        model_caps = ucwords(model);
        model_remove_underscores = model.replace(/_/g,'');

        var col_lg_3 = document.createElement("div");
        col_lg_3.classList.add('col-lg-3');
        col_lg_3.classList.add('field-'+model_remove_underscores+'-'+column);

        var form_group = document.createElement("div");
        form_group.classList.add('form-group');
        form_group.classList.add('field-'+model_remove_underscores+'-'+column);
        //form_group.setAttribute("style", 'display:'+visiblility+';');
        form_group.classList.add(required);
        col_lg_3.appendChild(form_group);

        var label = document.createElement("label");
        label.classList.add("control-label");
        label.setAttribute("for", model_remove_underscores+'-'+column);
        if(placeholder==''){
            var label_text = document.createTextNode(label1);
        }
        else{
            var label_text = document.createTextNode(placeholder);
        }
        label.appendChild(label_text)
        form_group.appendChild(label);

        var selectList = document.createElement("select");
        selectList.setAttribute("type", "text");
        selectList.setAttribute("placeholder", placeholder);
        selectList.setAttribute("id", model_remove_underscores+'-'+column);
        selectList.classList.add("form-control");
        selectList.setAttribute("name", model_caps+'['+column+']');
        selectList.setAttribute("aria-required", "true");
        /*if(required=="1") {
         input.setAttribute("required", 'true');
         }*/
        form_group.appendChild(selectList);


        //Create and append the options
        for (var i = 0; i < answers_values.length; i++) {
            var option = document.createElement("option");
            option.value = answers_values[i].value;
            option.text = answers_values[i].label;
            selectList.appendChild(option);
        }

        var help_block = document.createElement("div");
        help_block.classList.add("help-block");
        form_group.appendChild(help_block);

        document.getElementById("project-details").appendChild(col_lg_3);
    }
    function render_select2(model,column,placeholder,answers_values,required) {
        /*alert(column);
         alert(answers_values[0].value);*/
        model_caps = ucwords(model);
        model_remove_underscores = model.replace(/_/g,'');


        var col_lg_3 = document.createElement("div");
        col_lg_3.classList.add('col-lg-3');
        col_lg_3.classList.add('field-'+model_remove_underscores+'-'+column);

        var select2List = document.createElement("select");
        select2List.setAttribute("type", "text");
        //selectList.setAttribute("placeholder", placeholder);
        select2List.setAttribute("id", model_remove_underscores+'-'+column);
        select2List.classList.add("select2");
        select2List.classList.add('select2-selection');
        select2List.classList.add('select2-selection--multiple');
        select2List.setAttribute("role", "combobox");
        // select2List.classList.add("select2-hidden-accessible");

        select2List.setAttribute("name", model_caps+'['+column+']');
        select2List.setAttribute("multiple", "");
        select2List.setAttribute("tabindex", "-1");
        select2List.setAttribute("aria-hidden", "true");
        if(required=="1") {
            input.setAttribute("required", 'true');
        }
        col_lg_3.appendChild(select2List);
        //Create and append the options
        for (var i = 0; i < answers_values.length; i++) {
            var option = document.createElement("option");
            option.value = answers_values[i].value;
            option.text = answers_values[i].label;
            select2List.appendChild(option);
        }

        var span= document.createElement("span");
        span.classList.add('selection');
        span.classList.add('select2-container');
        span.classList.add('select2-container--default');
        span.classList.add('select2-container--below');
        span.classList.add('select2-container--focus');
        span.classList.add('select2-container--open');
        span.setAttribute("dir", "ltr");
        span.setAttribute("style", 'width:664.5px;');

        var span1= document.createElement("span");
        span1.classList.add('selection');

        var span2= document.createElement("span");
        span2.classList.add('select2-selection');
        span2.classList.add('select2-selection--multiple');
        span2.setAttribute("role", "combobox");
        span2.setAttribute("aria-haspopup", "true");
        span2.setAttribute("aria-expanded", "true");
        span2.setAttribute("tabindex", "-1");
        span2.setAttribute("aria-owns", "select2-05up-result-c0v0-Quant Verbal");
        span2.setAttribute("aria-activedescendant", "-1");

        var ul= document.createElement("ul");
        ul.classList.add('select2-selection__rendered');

        var li= document.createElement("li");
        li.classList.add('select2-search');
        li.classList.add('select2-search--inline');

        var input = document.createElement("input");
        input.classList.add('select2-search__field');
        input.setAttribute("type", "search");
        input.setAttribute("tabindex", "0");
        input.setAttribute("autocomplete", "off");
        input.setAttribute("autocorrect", "off");
        input.setAttribute("autocapitalize", "off");
        input.setAttribute("spellcheck", "false");
        input.setAttribute("role", "textbox");
        input.setAttribute("aria-autocomplete", "list");
        input.setAttribute("placeholder", "");
        input.setAttribute("style", 'width:0.75em;');

        li.appendChild(input);


        ul.appendChild(li);

        span2.appendChild(ul);

        span1.appendChild(span2);

        span.appendChild(span1);

        var span3= document.createElement("span");
        span3.classList.add('dropdown-wrapper');
        span3.setAttribute("aria-hidden", "true");
        span.appendChild(span3);
        col_lg_3.appendChild(span);


        var help_block = document.createElement("div");
        help_block.classList.add("help-block");
        col_lg_3.appendChild(help_block);

        document.getElementById("project-details").appendChild(col_lg_3);
    }

    //checboxtList.setAttribute("id",column+"["+answers_values[i].value+"] );
    //checboxtList.setAttribute("name",column+"["+answers_values[i].value+"]");
    function render_checkbox(model,column,placeholder,answers_values,required) {
        /*alert(column);
         alert(answers_values[0].value);*/
        model_caps = ucwords(model);
        model_remove_underscores = model.replace(/_/g,'');


        var col_lg_3 = document.createElement("div");
        col_lg_3.classList.add('col-lg-12');
        col_lg_3.classList.add('field-'+model_remove_underscores+'-'+column);
        //col_lg_3.append("\n");
        if(column === 'crops'){
            col_lg_3.setAttribute("id", model+"-"+column);
        }else {
            col_lg_3.append(document.createElement("hr"));
        }

        var heading = document.createElement("label");
        heading.classList.add("control-label");
        var heading_text = document.createTextNode(placeholder);
        heading.appendChild(heading_text)
        col_lg_3.append(heading);

        //Create and append the options
        var form_group = document.createElement("div");
        form_group.classList.add('checkbox');


        for (var i = 0; i < answers_values.length; i++) {

            var col_sm_2=document.createElement("div");
            col_sm_2.classList.add("col-sm-2");

            var checboxtList = document.createElement("input");
            checboxtList.setAttribute("type", "checkbox");
            checboxtList.setAttribute("placeholder", placeholder);
            checboxtList.setAttribute("id", column+"["+answers_values[i].value+"]");
            checboxtList.setAttribute("name",column+"["+answers_values[i].value+"]");

            col_sm_2.appendChild(checboxtList);

            var label = document.createElement("label");
            label.classList.add("control-label");
            label.setAttribute("for", column+"["+answers_values[i].value+"]");
            var label_text = document.createTextNode(answers_values[i].value);
            label.appendChild(label_text)
            col_sm_2.appendChild(label);


            form_group.appendChild(col_sm_2);


        }
        col_lg_3.appendChild(form_group);

        var help_block = document.createElement("div");
        help_block.classList.add("help-block");
        form_group.appendChild(help_block);

        document.getElementById("project-details").appendChild(col_lg_3);
    }

    function render_date(model,column,placeholder,visiblility,required,label1) {

        model_caps = ucwords(model);
        model_remove_underscores = model.replace(/_/g,'');

        var col_lg_3 = document.createElement("div");
        col_lg_3.classList.add('col-lg-3');
        col_lg_3.classList.add('field-'+model_remove_underscores+'-'+column);
        if(visiblility=="gone") {
            col_lg_3.setAttribute("style", 'display:none');
        }
        var form_group = document.createElement("div");
        form_group.classList.add('form-group');
        //form_group.classList.add('field-'+model_remove_underscores+'-'+column);

        //form_group.classList.add(required);
        col_lg_3.appendChild(form_group);

        var label = document.createElement("label");
        label.classList.add("control-label");
        label.setAttribute("for", model_remove_underscores+'-'+column);
        if(placeholder==''){
            var label_text = document.createTextNode(label1);
        }
        else{
            var label_text = document.createTextNode(placeholder);
        }

        label.appendChild(label_text)
        form_group.appendChild(label);

        var input = document.createElement("input");
        input.setAttribute("placeholder", placeholder);
        if(column=='estimated_start_date') {
            input.setAttribute("type", "text");
        }else{
            input.setAttribute("type", "date");
        }
        input.setAttribute("id", model_remove_underscores+'-'+column);
        input.classList.add("form-control");
        input.classList.add(model_remove_underscores+'-'+column);
        input.classList.add("hasDatepicker");

        input.setAttribute("name", model_caps+'['+column+']');
        input.setAttribute("aria-required", "true");
        input.setAttribute("required", 'true');
        /*if(required=="1") {
         input.setAttribute("required", 'true');
         }*/
        form_group.appendChild(input);
        var help_block = document.createElement("div");
        help_block.classList.add("help-block");
        form_group.appendChild(help_block);

        document.getElementById("project-details").appendChild(col_lg_3);
    }
    function render_recycler_view(model,column,placeholder,visiblility,required,format,id) {

        model_caps = ucwords(model);
        model_remove_underscores = model.replace(/_/g,'');
        column_remove_underscores = column.replace(/_/g,' ');

        var recycler_div = document.createElement("div");
        recycler_div.setAttribute("id",id);
        recycler_div.classList.add('col-sm-12');
        //recycler_div.style.backgroundColor='#eceff4';

        var heading = document.createElement("div");
        heading.setAttribute("id","heading");
        heading.classList.add('row');

        var col_lg_heading = document.createElement("div");
        col_lg_heading.setAttribute("id","button-div");
        col_lg_heading.classList.add('col-sm-12');
        heading.appendChild(col_lg_heading);

        var button = document.createElement("BUTTON");
        button.classList.add(/*'field-'+model_remove_underscores+'-'+*/id);
        button.classList.add("btn");
        button.classList.add("btn-sucsess");
        button.classList.add("float-right");
        button.setAttribute("id",'recycler-view');
        button.innerHTML=placeholder;
        button.style.marginLeft = "1.3%";
        heading.appendChild(button);

        recycler_div.appendChild(heading);


        document.getElementById("project-details").appendChild(recycler_div);
    }
    function remove_underscores(input) {
        return input.replace('_','');
    }
    function ucwords(input) {
        var words = input.split('_'),
            output = [];
        for (var i = 0, len = words.length; i < len; i += 1) {
            output.push(words[i][0].toUpperCase() +
                words[i].toLowerCase().substr(1));
        }

        return output.join('');
    }
//////recycler view form render////
    function render_select_recycler(model,column,placeholder,answers_values,required,label1,div,count) {
        /*alert(column);
         alert(answers_values[0].value);*/
        model_caps = ucwords(model);
        model_remove_underscores = model.replace(/_/g,'');

        var col_lg_3 = document.createElement("div");
        col_lg_3.classList.add('col-lg-3');

        var form_group = document.createElement("div");
        form_group.classList.add('form-group');
        form_group.classList.add('field-'+model_remove_underscores+'-'+column);
        //form_group.setAttribute("style", 'display:'+visiblility+';');
        form_group.classList.add(required);
        col_lg_3.appendChild(form_group);

        var label = document.createElement("label");
        label.classList.add("control-label");
        label.setAttribute("for", model_remove_underscores+'-'+column);
        if(placeholder==''){
            var label_text = document.createTextNode(label1);
        }
        else{
            var label_text = document.createTextNode(placeholder);
        }
        label.appendChild(label_text)
        form_group.appendChild(label);

        var selectList = document.createElement("select");
        selectList.setAttribute("type", "text");
        selectList.setAttribute("placeholder", placeholder);
        selectList.setAttribute("id", model_remove_underscores+'-'+column);
        selectList.classList.add("form-control");
        selectList.setAttribute("name",model_caps+"[sub-from]"+'['+count+']'+'['+column+']');
        selectList.setAttribute("aria-required", "true");
        /*if(required=="1") {
         input.setAttribute("required", 'true');
         }*/
        form_group.appendChild(selectList);


        //Create and append the options
        for (var i = 0; i < answers_values.length; i++) {
            var option = document.createElement("option");
            option.value = answers_values[i].value;
            option.text = answers_values[i].label;
            selectList.appendChild(option);
        }

        var help_block = document.createElement("div");
        help_block.classList.add("help-block");
        form_group.appendChild(help_block);

        document.getElementById(div).appendChild(col_lg_3);
    }
    function render_checkbox_recycler(model,column,placeholder,answers_values,required,div,count) {
        /*alert(column);
         alert(answers_values[0].value);*/
        model_caps = ucwords(model);
        model_remove_underscores = model.replace(/_/g,'');


        var col_lg_3 = document.createElement("div");
        col_lg_3.classList.add('col-lg-12');
        col_lg_3.classList.add('field-'+model_remove_underscores+'-'+column);
        //col_lg_3.append("\n");
        col_lg_3.append(document.createElement("hr"));

        var heading = document.createElement("label");
        heading.classList.add("control-label");
        var heading_text = document.createTextNode(placeholder);
        heading.appendChild(heading_text)
        col_lg_3.append(heading);

        //Create and append the options
        var form_group = document.createElement("div");
        form_group.classList.add('checkbox');




        for (var i = 0; i < answers_values.length; i++) {

            var col_sm_2=document.createElement("div");
            col_sm_2.classList.add("col-sm-2");

            var checboxtList = document.createElement("input");
            checboxtList.setAttribute("type", "checkbox");
            checboxtList.setAttribute("placeholder", placeholder);
            checboxtList.setAttribute("id", column+"["+answers_values[i].value+"]");
            checboxtList.setAttribute("name",column+"["+answers_values[i].value+"]");

            col_sm_2.appendChild(checboxtList);

            var label = document.createElement("label");
            label.classList.add("control-label");
            label.setAttribute("for", column+"["+answers_values[i].value+"]");
            var label_text = document.createTextNode(answers_values[i].value);
            label.appendChild(label_text)
            col_sm_2.appendChild(label);


            form_group.appendChild(col_sm_2);


        }
        col_lg_3.appendChild(form_group);

        var help_block = document.createElement("div");
        help_block.classList.add("help-block");
        form_group.appendChild(help_block);

        document.getElementById(div).appendChild(col_lg_3);
    }
    function render_date_recycler(model,column,placeholder,visiblility,required,label1,div,count) {

        model_caps = ucwords(model);
        model_remove_underscores = model.replace(/_/g,'');

        var col_lg_3 = document.createElement("div");
        col_lg_3.classList.add('col-lg-3');
        col_lg_3.classList.add('field-'+model_remove_underscores+'-'+column);
        if(visiblility=="gone") {
            col_lg_3.setAttribute("style", 'display:none');
        }
        var form_group = document.createElement("div");
        form_group.classList.add('form-group');
        //form_group.classList.add('field-'+model_remove_underscores+'-'+column);

        //form_group.classList.add(required);
        col_lg_3.appendChild(form_group);

        var label = document.createElement("label");
        label.classList.add("control-label");
        label.setAttribute("for", model_remove_underscores+'-'+column);
        if(placeholder==''){
            var label_text = document.createTextNode(label1);
        }
        else{
            var label_text = document.createTextNode(placeholder);
        }

        label.appendChild(label_text)
        form_group.appendChild(label);

        var input = document.createElement("input");
        input.setAttribute("type", "date");
        input.setAttribute("placeholder", placeholder);
        input.setAttribute("id", model_remove_underscores+'-'+column);
        input.classList.add("form-control");
        input.classList.add(model_remove_underscores+'-'+column);
        input.classList.add("hasDatepicker");

        input.setAttribute("name",model_caps+"[sub-from]"+'['+count+']'+'['+column+']');
        //input.setAttribute("aria-required", "true");
        //input.setAttribute("required", 'true');
        /*if(required=="1") {
         input.setAttribute("required", 'true');
         }*/
        form_group.appendChild(input);
        var help_block = document.createElement("div");
        help_block.classList.add("help-block");
        form_group.appendChild(help_block);

        document.getElementById(div).appendChild(col_lg_3);
    }
    function render_input_recycler(model,column,placeholder,visiblility,required,format,div,count) {

        model_caps = ucwords(model);
        model_remove_underscores = model.replace(/_/g,'');

        var col_lg_3 = document.createElement("div");
        col_lg_3.classList.add('col-lg-3');
        col_lg_3.classList.add('field-'+model_remove_underscores+'-'+column);
        if(visiblility=="gone") {
            col_lg_3.setAttribute("style", 'display:none');
        }
        var form_group = document.createElement("div");
        form_group.classList.add('form-group');
        //form_group.classList.add('field-'+model_remove_underscores+'-'+column);

        form_group.classList.add(required);
        //form_group.classList.add(has-error);
        col_lg_3.appendChild(form_group);

        var label = document.createElement("label");
        label.classList.add("control-label");
        label.setAttribute("for", model_remove_underscores+'-'+column);
        /*if(visiblility ==  "gone") {
         label.setAttribute("style", 'display:none');
         }*/
        var label_text = document.createTextNode(placeholder);
        label.appendChild(label_text)
        form_group.appendChild(label);

        var input = document.createElement("input");
        input.setAttribute("type", format);
        if(format=="number"){
            input.setAttribute("min", 0);
        }
        input.setAttribute("placeholder", placeholder);
        input.setAttribute("id", model_remove_underscores+'-'+column);
        input.classList.add("form-control");
        input.classList.add(model_remove_underscores+'-'+column);
        input.setAttribute("name", model_caps+"[sub-from]"+'['+count+']'+'['+column+']');
        input.setAttribute("aria-required", "true");
        if(required=="1") {
            input.setAttribute("required", 'true');
        }
        form_group.appendChild(input);

        var help_block = document.createElement("div");
        help_block.classList.add("help-block");
        form_group.appendChild(help_block);

        document.getElementById(div).appendChild(col_lg_3);
    }
});