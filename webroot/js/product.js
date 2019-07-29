
// CKEDITOR.replace('description');
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrfToken"]').attr('content')
    }
});

$("#category").change(function(){
    var category_id = $(this).val();
    // alert(category_id);
    $.ajax({
        url: '/admin/products/searchCate',
        method: 'POST',
        data: {category_id: category_id}
    }).done(function(rep){
        console.log(rep);
        $("#product_table tbody tr").remove();
        $("#product_table tbody").html(rep);
    });
});

$(".input").click(function(){
    $(this).find(".error").css({"display": "none"});
});

// $("input[name=file]").change(function() {
//     var files = document.getElementById("file").files;
//     var formData = new FormData();

//     for (var i = 0; i < files.length; i++)
//     {
//         formData.append('name[]', files[i].name);
//         formData.append('size[]', files[i].size);
//         formData.append('type[]', files[i].type);
//     }

//     $.ajax({
//         url: '/admin/products/uploadImage',
//         method: 'POST',
//         data: formData,
//         contentType: false,
//         processData: false
//     }).done(function(rep){
//         console.log(typeof rep);
//         for(var i=0;i<rep.length;i++)
//         {
//           $('#image_preview').append("<img name='"+rep[i]+"' src='"+URL.createObjectURL(event.target.files[i])+"'><br>");
//         }
//     });
// });
