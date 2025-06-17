$("document").ready(function(){
    $('body').on('beforeSubmit', 'form.AddTeams', function () {
        var form = $(this);
        // submit form
        $.ajax({
            url: '/branches/teams',
            type: 'post',
            data: form.serialize(),

            success: function (response) {
                var obj = JSON.parse(response);
                if(obj.status_type == 'success'){
                    location.reload(true);
                }else{
                    alert('Team Not Added Successfullly!')
                }
            }
        });
        return false;

    });
    $('body').on('beforeSubmit', 'form.AddFields', function () {
        var form = $(this);
        // submit form
        $.ajax({
            url: '/branches/fields',
            type: 'post',
            data: form.serialize(),

            success: function (response) {
                var obj = JSON.parse(response);
                if(obj.status_type == 'success'){
                    location.reload(true);
                }else{
                    alert('Field Not Added Successfullly!')
                }
            }
        });
        return false;

    });
    $('body').on('beforeSubmit', 'form.DeleteField', function () {
        var r = confirm("Are You Sure You want to delete?");
        if (r == true) {
            var form = $(this);
            // submit form
            $.ajax({
                url: '/branches/delete-field',
                type: 'post',
                data: form.serialize(),

                success: function (response) {
                    var obj = JSON.parse(response);
                    if (obj.status_type == 'success') {
                        location.reload(true);
                    } else {
                        alert('Field Not Deleted Successfullly!')
                    }
                }
            });
            return false;
        }

    });
    $('body').on('beforeSubmit', 'form.DeleteTeam', function () {
        var r = confirm("Are You Sure You want to delete?");
        if (r == true) {
            var form = $(this);
            // submit form
            $.ajax({
                url: '/branches/delete-team',
                type: 'post',
                data: form.serialize(),

                success: function (response) {
                    var obj = JSON.parse(response);
                    if(obj.status_type == 'success'){
                        location.reload(true);
                    }else{
                        alert('Team Not Deleted Successfullly!')
                    }
                }
            });
            return false;
        }
    });
});