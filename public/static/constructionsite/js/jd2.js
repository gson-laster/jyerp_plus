$(function(){  
        var id = $('#id').val();
        $.ajax({
            type: "GET",
            async: false,
            url: "/admin.php/constructionsite/schedule/tech/id/"+id,
            success: function(data){
                $("#form_group_obj").after(data);
            }
        }); 
})