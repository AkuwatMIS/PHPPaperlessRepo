$("document").ready(function(){

    var project_id = $("#applications-project_id").val();

    if (project_id != "" && project_id != null) {
        $.ajax({
            type: "POST",
            url: '/projects/form?id=' + project_id,
            success: function (data) {
                clear_header();
                clear_details();

                var obj = $.parseJSON(data);
                if (obj && obj.length > 0) {
                    render_header(obj[0].table);
                    $.each(obj, function (key, value) {
                        if (value.type == "single-select" /*|| value.type == "multi-select"*/) {
                            render_select(value.table, value.column, value.place_holder, value.answers_values, 'required');
                        }
                        else if (value.type == "multi-select") {
                            render_checkbox(value.table, value.column, value.place_holder, value.answers_values, value.is_mendatory);
                            //render_select2(value.table,value.column,value.place_holder,value.answers_values,'required');
                        }
                        else if (value.type == "date_picker") {
                            render_date(value.table, value.column, value.place_holder, value.default_visibility, value.is_mendatory);
                        }
                        else {
                            render_input(value.table, value.column, value.place_holder, 'required');
                        }
                    });
                    if (project_id == '17') {
                        var duration=$("#projectstevta-duration_of_diploma").val();
                        var type=duration+'_type-of-diploma';
                        $.ajax({
                            type: "POST",
                            url: '/projects/get-answers-values?answers_values='+type,
                            success: function(data){
                                var obj = $.parseJSON(data);
                                $('#projectstevta-type_of_diploma')
                                    .find('option')
                                    .remove();
                                for (var i = 0; i < obj.length; i++) {
                                    var option = document.createElement("option");
                                    option.value = obj[i].value;
                                    option.text = obj[i].label;
                                    //$('#projectdetailstevta-type_of_diploma').val('');
                                    $('#projectstevta-type_of_diploma').append(option);
                                }
                            }
                        });
                    }
                }
            }
        });
    }

    $("#SIDB-disability_SIDB").change(function(){
        var project_id = $("#applications-project_id").val();

        $.ajax({
            type: "POST",
            url: '/projects/form?id='+project_id,
            success: function(data){
                clear_header();
                clear_details();

                var obj = $.parseJSON(data);
                if(obj && obj.length > 0){
                    render_header(obj[0].table);
                    $.each(obj, function(key,value) {
                        if (value.type == "single-select" /*|| value.type == "multi-select"*/) {
                            render_select(value.table, value.column, value.place_holder, value.answers_values, 'required');
                        }
                        else if (value.type == "multi-select") {
                            render_checkbox(value.table, value.column, value.place_holder, value.answers_values, value.is_mendatory);
                            //render_select2(value.table,value.column,value.place_holder,value.answers_values,'required');
                        }
                        else if (value.type == "date_picker") {
                            render_date(value.table, value.column, value.place_holder, value.default_visibility, value.is_mendatory);
                        }
                        else {
                            render_input(value.table, value.column, value.place_holder, 'required');
                        }
                    });
                    if (project_id == '1') {
                        $(".field-projectspsic-institute").hide();
                        $(".field-projectspsic-other_name").hide();
                    }
                    if (project_id == '17') {
                        var duration=$("#projectstevta-duration_of_diploma").val();
                        var type=duration+'_type-of-diploma';
                        $.ajax({
                            type: "POST",
                            url: '/projects/get-answers-values?answers_values='+type,
                            success: function(data){
                                var obj = $.parseJSON(data);
                                $('#projectstevta-type_of_diploma')
                                    .find('option')
                                    .remove();
                                for (var i = 0; i < obj.length; i++) {
                                    var option = document.createElement("option");
                                    option.value = obj[i].value;
                                    option.text = obj[i].label;
                                    //$('#projectdetailstevta-type_of_diploma').val('');
                                    $('#projectstevta-type_of_diploma').append(option);
                                }
                            }
                        });
                    }
                }
            }
        });

    });

    $("#applications-project_id").change(function(){
        var project_id = $("#applications-project_id").val();

        if (project_id === "71" || project_id === "106") {
            clear_header();
            clear_details()
        }else {
            $.ajax({
                type: "POST",
                url: '/projects/form?id='+project_id,
                success: function(data){
                    clear_header();
                    clear_details();

                    var obj = $.parseJSON(data);
                    if(obj && obj.length > 0){
                        render_header(obj[0].table);
                        $.each(obj, function(key,value) {
                            if (value.type == "single-select" /*|| value.type == "multi-select"*/) {
                                render_select(value.table, value.column, value.place_holder, value.answers_values, 'required');
                            }
                            else if (value.type == "multi-select") {
                                render_checkbox(value.table, value.column, value.place_holder, value.answers_values, value.is_mendatory);
                                //render_select2(value.table,value.column,value.place_holder,value.answers_values,'required');
                            }
                            else if (value.type == "date_picker") {
                                render_date(value.table, value.column, value.place_holder, value.default_visibility, value.is_mendatory);
                            }
                            else {
                                render_input(value.table, value.column, value.place_holder, 'required');
                            }
                        });
                        if (project_id == '1') {
                            $(".field-projectspsic-institute").hide();
                            $(".field-projectspsic-other_name").hide();
                        }
                        if (project_id == '17') {
                            var duration=$("#projectstevta-duration_of_diploma").val();
                            var type=duration+'_type-of-diploma';
                            $.ajax({
                                type: "POST",
                                url: '/projects/get-answers-values?answers_values='+type,
                                success: function(data){
                                    var obj = $.parseJSON(data);
                                    $('#projectstevta-type_of_diploma')
                                        .find('option')
                                        .remove();
                                    for (var i = 0; i < obj.length; i++) {
                                        var option = document.createElement("option");
                                        option.value = obj[i].value;
                                        option.text = obj[i].label;
                                        //$('#projectdetailstevta-type_of_diploma').val('');
                                        $('#projectstevta-type_of_diploma').append(option);
                                    }
                                }
                            });
                        }
                        if(project_id === '77' || project_id === '78' || project_id === '79'){
                            $(".field-projectskpp-trainee_type").hide();
                            $(".field-projectskpp-trainee_name").hide();
                            $(".field-projectskpp-trainee_guardian").hide();
                            $(".field-projectskpp-trainee_cnic").hide();
                            $(".field-projectskpp-trainee_relation").hide();
                            $(".field-projectskpp-want_sehat_card").hide();

                            $(".field-projectsagriculturekpp-kpp_trainee_type").hide();
                            $(".field-projectsagriculturekpp-kpp_trainee_name").hide();
                            $(".field-projectsagriculturekpp-kpp_trainee_guardian").hide();
                            $(".field-projectsagriculturekpp-kpp_trainee_cnic").hide();
                            $(".field-projectsagriculturekpp-kpp_trainee_relation").hide();
                            $(".field-projectsagriculturekpp-kpp_want_sehat_card").hide();
                        }

                        if(project_id === '79'){
                            $("#projectsagriculturekpp-kpp_land_area_size").attr('type','number');
                            $("#projectsagriculturekpp-kpp_land_area_size").attr('step','1.0');
                            $("#projectsagriculturekpp-kpp_land_area_size").attr('max',12.5);
                        }
                    }
                }
            });
        }

    });

    // $("#applications-activity_id").change(function(){
    //     var selected_project_id = $("#applications-project_id").val();
    //     var activity_selected_value =  $("#applications-activity_id").val();
    //     if(selected_project_id === "106"){
    //         if(activity_selected_value === "23"){
    //             clear_header();
    //             clear_details();
    //         }else {
    //         }
    //     }
    // });

    $("#projectdetailsagriculture-crop_type").change(function(){
        var crop_type = $("#projectdetailsagriculture-crop_type").val();
        $.ajax({
            type: "POST",
            url: '/projects/croptype?id='+crop_type,
            success: function(data){
                var obj = $.parseJSON(data);
            }
        });
    });

    $("#projectsagriculturekpp-kpp_crop_type").change(function(){
        var crop_type = $("#projectsagriculturekpp-kpp_crop_type").val();
        $.ajax({
            type: "POST",
            url: '/projects/croptype?id='+crop_type,
            success: function(data){
                var obj = $.parseJSON(data);
            }
        });
    });

    document.addEventListener('change', function (e) {
        if (e.target.id == 'projectstevta-duration_of_diploma') {
            var duration=$("#projectstevta-duration_of_diploma").val();
            var type=duration+'_type-of-diploma';
            $.ajax({
                type: "POST",
                url: '/projects/get-answers-values?answers_values='+type,
                success: function(data){
                    var obj = $.parseJSON(data);
                    $('#projectstevta-type_of_diploma')
                        .find('option')
                        .remove();
                    for (var i = 0; i < obj.length; i++) {
                        var option = document.createElement("option");
                        option.value = obj[i].value;
                        option.text = obj[i].label;
                        //$('#projectdetailstevta-type_of_diploma').val('');
                        $('#projectstevta-type_of_diploma').append(option);
                    }
                }
            });
        }
        else if(e.target.id == 'projectsagriculture-crop_type'){
            var type=$("#projectsagriculture-crop_type").val();
            var type=type+'_crops';
            $.ajax({
                type: "POST",
                url: '/projects/get-answers-values?answers_values='+type,
                success: function(data){
                    var obj = $.parseJSON(data);
                    var myNode = document.getElementById("projects_agriculture-crops");
                    /*while (myNode.firstChild) {
                        myNode.removeChild(myNode.firstChild);
                    }*/
                    if($("#projectsagriculture-crops").length == 0) {
                        myNode.remove();
                        render_checkbox("projectsagriculture", "crops", "Select Crop",obj, "required");
                    }
                    else{
                        $("#projectsagriculture-crops").remove();
                        render_checkbox("projectsagriculture", "crops", "Select Crop",obj, "required");

                    }

                }
            });
        }else if(e.target.id == 'projectspsic-diploma_holder'){
            var is_diploma_holder=$("#projectspsic-diploma_holder").val();
            if(is_diploma_holder==1){
                $(".field-projectspsic-institute").show();
            }else{
                $("#projectspsic-institute").val('');
                $("#projectspsic-other_name").val('');
                $(".field-projectspsic-institute").hide();
                $(".field-projectspsic-other_name").hide();
            }
        }else if(e.target.id == 'projectspsic-institute'){
            var inst_name=$("#projectspsic-institute").val();
            if(inst_name=='other'){
                $(".field-projectspsic-other_name").show();
            }else{
                $("#projectspsic-other_name").val('');
                $(".field-projectspsic-other_name").hide();
            }
        }else if(e.target.id == 'projectskpp-training_required'){
            var is_training_required=$("#projectskpp-training_required").val();
            if(is_training_required==1){
                $(".field-projectskpp-trainee_type").show();
            }else{
                $(".field-projectskpp-trainee_type").hide();
            }
        }else if(e.target.id == 'projectskpp-trainee_type'){
            var is_trainee_type=$("#projectskpp-trainee_type").val();
            if(is_trainee_type=='other'){
                $(".field-projectskpp-trainee_name").show();
                $(".field-projectskpp-trainee_guardian").show();
                $(".field-projectskpp-trainee_cnic").show();
                $(".field-projectskpp-trainee_relation").show();
            }else{
                $(".field-projectskpp-trainee_name").hide();
                $(".field-projectskpp-trainee_guardian").hide();
                $(".field-projectskpp-trainee_cnic").hide();
                $(".field-projectskpp-trainee_relation").hide();
            }
        }else if(e.target.id == 'projectskpp-has_sehat_card'){
            var has_sehat_card=$("#projectskpp-has_sehat_card").val();
            if(has_sehat_card==0){
                $(".field-projectskpp-want_sehat_card").show();
            }else{
                $(".field-projectskpp-want_sehat_card").hide();
            }
        }else if(e.target.id == 'projectskpp-trainee_cnic'){
            $("#projectskpp-trainee_cnic").mask('99999-9999999-9');
        }else if(e.target.id == 'projectsagriculturekpp-kpp_training_required'){
            var is_training_required=$("#projectsagriculturekpp-kpp_training_required").val();
            if(is_training_required==1){
                $(".field-projectsagriculturekpp-kpp_trainee_type").show();
            }else{
                $(".field-projectsagriculturekpp-kpp_trainee_type").hide();
            }
        }else if(e.target.id == 'projectsagriculturekpp-kpp_trainee_type'){
            var is_trainee_type=$("#projectsagriculturekpp-kpp_trainee_type").val();
            if(is_trainee_type=='other'){
                $(".field-projectsagriculturekpp-kpp_trainee_name").show();
                $(".field-projectsagriculturekpp-kpp_trainee_guardian").show();
                $(".field-projectsagriculturekpp-kpp_trainee_cnic").show();
                $(".field-projectsagriculturekpp-kpp_trainee_relation").show();
            }else{
                $(".field-projectsagriculturekpp-kpp_trainee_name").hide();
                $(".field-projectsagriculturekpp-kpp_trainee_guardian").hide();
                $(".field-projectsagriculturekpp-kpp_trainee_cnic").hide();
                $(".field-projectsagriculturekpp-kpp_trainee_relation").hide();
            }
        }else if(e.target.id == 'projectsagriculturekpp-kpp_has_sehat_card'){
            var has_sehat_card=$("#projectsagriculturekpp-kpp_has_sehat_card").val();
            if(has_sehat_card==0){
                $(".field-projectsagriculturekpp-kpp_want_sehat_card").show();
            }else{
                $(".field-projectsagriculturekpp-kpp_want_sehat_card").hide();
            }
        }else if(e.target.id == 'projectsagriculturekpp-kpp_trainee_cnic'){
            $("#projectsagriculturekpp-kpp_trainee_cnic").mask('99999-9999999-9');
        }else if(e.target.id == 'projectsagriculturekpp-kpp_crop_type'){
            var type=$("#projectsagriculturekpp-kpp_crop_type").val();
            var type=type+'_crops';
            $.ajax({
                type: "POST",
                url: '/projects/get-answers-values?answers_values='+type,
                success: function(data){
                    var obj = $.parseJSON(data);
                    var myNode = document.getElementById("projects_agriculture_kpp-kpp_crops");
                    /*while (myNode.firstChild) {
                        myNode.removeChild(myNode.firstChild);
                    }*/
                    if($("#projectsagriculturekpp-kpp_crops").length == 0) {
                        myNode.remove();
                        render_checkbox("projectsagriculturekpp", "kpp_crops", "Select Crop",obj, "required");
                    }
                    else{
                        $("#projectsagriculturekpp-kpp_crops").remove();
                        render_checkbox("projectsagriculturekpp", "kpp_crops", "Select Crop",obj, "required");

                    }

                }
            });
        }else if(e.target.id == 'projectsagriculturekpp-kpp_land_area_type'){
            var land_type = $("#projectsagriculturekpp-kpp_land_area_type").val();

            if(land_type == 'Acre'){
                $("#projectsagriculturekpp-kpp_land_area_size").attr('type','number');
                $("#projectsagriculturekpp-kpp_land_area_size").attr('step','1.0');
                $("#projectsagriculturekpp-kpp_land_area_size").attr('max',12.5);
            }else if(land_type == 'Kanals'){
                $("#projectsagriculturekpp-kpp_land_area_size").attr('type','number');
                $("#projectsagriculturekpp-kpp_land_area_size").attr('step','1.0');
                $("#projectsagriculturekpp-kpp_land_area_size").attr('max',100);
            }else {
                $("#projectsagriculturekpp-kpp_land_area_size").attr('type','number');
                $("#projectsagriculturekpp-kpp_land_area_size").attr('step','1.0');
                $("#projectsagriculturekpp-kpp_land_area_size").attr('max',2000);
            }
        }else if(e.target.id == 'projectsagriculturekpp-kpp_land_area_size'){
            var land_type_check = $("#projectsagriculturekpp-kpp_land_area_type").val();
            var land_size_check = $("#projectsagriculturekpp-kpp_land_area_size").val();

            if(land_type_check == 'Acre'){
                if(land_size_check>12.5){
                    alert('Value greater than 12.5 is not allowed');
                    $("#projectsagriculturekpp-kpp_land_area_size").val("");
                }
            }else if(land_type_check == 'Kanals'){
                if(land_size_check>100){
                    alert('Value greater than 100 is not allowed');
                    $("#projectsagriculturekpp-kpp_land_area_size").val("");
                }
            }else {
                if(land_size_check>2000){
                    alert('Value greater than 2000 is not allowed');
                    $("#projectsagriculturekpp-kpp_land_area_size").val("");
                }
            }
        }else if(e.target.id == 'appraisalsagriculture-crop_type'){
            var type=$("#appraisalsagriculture-crop_type").val();
            var type=type+'_crops';
            $.ajax({
                type: "POST",
                url: '/projects/get-answers-values?answers_values='+type,
                success: function(data){
                    var obj = $.parseJSON(data);
                    var myNode = document.getElementById("appraisals_agriculture-crops");
                    /*while (myNode.firstChild) {
                        myNode.removeChild(myNode.firstChild);
                    }*/
                    if($("#appraisalsagriculture-crops").length == 0) {
                        myNode.remove();
                        render_checkbox("appraisalsagriculture", "crops", "Select Crop",obj, "required");
                    }
                    else{
                        $("#appraisalsagriculture-crops").remove();
                        render_checkbox("appraisalsagriculture", "crops", "Select Crop",obj, "required");

                    }

                }
            });
        }


    }, false);
    /*$("#projectdetailstevta-duration_of_diploma").change(function(){
        alert('a');
    });*/
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
        var t = document.createTextNode(model_caps);
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
    function render_checkbox(model,column,placeholder,answers_values,required) {
        model_caps = ucwords(model);
        model_remove_underscores = model.replace(/_/g,'');


        var col_lg_3 = document.createElement("div");
        col_lg_3.classList.add('col-lg-6');
        col_lg_3.setAttribute("id", model+"-"+column);

        //col_lg_3.append("\n");
        //col_lg_3.append(document.createElement("hr"));

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
    function render_input(model,column,placeholder,required) {

        model_caps = ucwords(model);
        model_remove_underscores = model.replace(/_/g,'');

        var col_lg_3 = document.createElement("div");
        col_lg_3.classList.add('col-lg-4');

        var form_group = document.createElement("div");
        form_group.classList.add('form-group');
        form_group.classList.add('field-'+model_remove_underscores+'-'+column);
        form_group.classList.add(required);
        col_lg_3.appendChild(form_group);

        var label = document.createElement("label");
        label.classList.add("control-label");
        label.setAttribute("for", model_remove_underscores+'-'+column);
        var label_text = document.createTextNode(placeholder);
        label.appendChild(label_text)
        form_group.appendChild(label);

        var input = document.createElement("input");
        input.setAttribute("type", "text");
        input.setAttribute("placeholder", placeholder);
        input.setAttribute("id", model_remove_underscores+'-'+column);
        input.classList.add("form-control");
        input.setAttribute("name", model_caps+'['+column+']');
        input.setAttribute("aria-required", "true");
        form_group.appendChild(input);

        var help_block = document.createElement("div");
        help_block.classList.add("help-block");
        form_group.appendChild(help_block);

        document.getElementById("project-details").appendChild(col_lg_3);
    }
    function render_select(model,column,placeholder,answers_values,required) {
        /*alert(column);
        alert(answers_values[0].value);*/
        model_caps = ucwords(model);
        model_remove_underscores = model.replace(/_/g,'');

        var col_lg_3 = document.createElement("div");
        col_lg_3.classList.add('col-lg-4');

        var form_group = document.createElement("div");
        form_group.classList.add('form-group');
        form_group.classList.add('field-'+model_remove_underscores+'-'+column);
        form_group.classList.add(required);
        col_lg_3.appendChild(form_group);

        var label = document.createElement("label");
        label.classList.add("control-label");
        label.setAttribute("for", model_remove_underscores+'-'+column);
        var label_text = document.createTextNode(placeholder);
        label.appendChild(label_text);
        form_group.appendChild(label);

        var selectList = document.createElement("select");
        selectList.setAttribute("type", "text");
        selectList.setAttribute("placeholder", placeholder);
        selectList.setAttribute("id", model_remove_underscores+'-'+column);
        selectList.classList.add("form-control");
        selectList.setAttribute("name", model_caps+'['+column+']');
        selectList.setAttribute("aria-required", "true");
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
    function render_date(model,column,placeholder,visiblility,required) {

        model_caps = ucwords(model);
        model_remove_underscores = model.replace(/_/g,'');

        var col_lg_3 = document.createElement("div");
        col_lg_3.classList.add('col-lg-3');
        col_lg_3.classList.add('field-'+model_remove_underscores+'-'+column);
        /*if(visiblility=="gone") {
            col_lg_3.setAttribute("style", 'display:none');
        }*/
        var form_group = document.createElement("div");
        form_group.classList.add('form-group');
        //form_group.classList.add('field-'+model_remove_underscores+'-'+column);

        //form_group.classList.add(required);
        col_lg_3.appendChild(form_group);

        var label = document.createElement("label");
        label.classList.add("control-label");
        label.setAttribute("for", model_remove_underscores+'-'+column);

        var label_text = document.createTextNode("Year");
        label.appendChild(label_text)
        form_group.appendChild(label);

        var input = document.createElement("input");
        input.setAttribute("type", "date");
        input.setAttribute("placeholder", placeholder);
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


});