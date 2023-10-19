$(document).ready(function () {
    $('.first-button').on('click', function () {
        $('.animated-icon1').toggleClass('open');
        $('.collapse').toggleClass('show');
    });


    $('.type-company-select select').on('change', function (){
        let typeCompany = $(this).val();

        slideInputCompany(typeCompany);
    });

    function slideInputCompany(typeCompany)
    {
        if(typeCompany == 1)
        {
            $('.ogrn').slideDown();
            $('.ogrnIp').slideUp();
            $('.inn').slideDown();
        }

        if(typeCompany == 2)
        {
            $('.ogrn').slideUp();
            $('.ogrnIp').slideDown();
            $('.inn').slideDown();
        }

        if(typeCompany == 3)
        {
            $('.ogrn').slideUp();
            $('.ogrnIp').slideUp();
            $('.inn').slideUp();
        }
    }

    let typeCompany = $('.type-company-select select').val();

    slideInputCompany(typeCompany);
});

$(document).on('click', '.btn-edit-comment, .btn-delete-comment', function (){
    let commentId = $(this).closest('.comment').attr('id');
    let message = $(this).closest('.comment').find('article').text();

    $('.comment-id input').val(commentId);
    $('.modal textarea').val(message.trim());
});

$(document).on('click', '.save-edit-comment', function (){

    let form = $("#update-comment");
    let data = form.serialize();

    $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),
        data: data,
        beforeSend: function(){
            $('.save-edit-comment').html('<div class="spinner-border spinner-border-sm" role="status">\n' +
                '  <span class="visually-hidden">Загрузка...</span>\n' +
                '</div>');
        },
        success: function(data){
            location.reload();
        },
    });

    return false;
})

$(document).on('click', '.delete-comment', function (){

    let form = $("#delete-comment");
    let data = form.serialize();

    $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),
        data: data,
        beforeSend: function(){
            $('.delete-comment').html('<div class="spinner-border spinner-border-sm" role="status">\n' +
                '  <span class="visually-hidden">Загрузка...</span>\n' +
                '</div>');
        },
        success: function(data){
            location.reload();
        },
    });

    return false;
});



