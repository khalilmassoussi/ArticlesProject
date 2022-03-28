import $ from 'jquery';

$(document).ready(function () {
    // Load Comments & Sub Comments
    $.ajax({
        method: 'GET',
        url: Routing.generate('app_comment_by_article', {id: $('.commentsList').attr('id')}),
    })
        .done(function (data) {
            $.each(data, function (index, value) {
                var comment = '<li id="comment' + index + '" class="list-group-item">' + value.text + '<a href="#" class="commentRespond btn btn-primary float-end" data-id="' + value.id + '">Répondre</a><div class="responseDiv" style="display: none"><input type="text" name="commentText" class="form-control"><a href="#" class="addSubComment btn btn-sm btn-primary" data-id="' + value.id + '">Envoyer</a></div>' +
                    '<div class="rating"> <input type="radio" name="' + value.id + '" value="5" id="Rate' + value.id + '5" class="radioRating"><label for="Rate' + value.id + '5">☆</label> <input type="radio" name="' + value.id + '" value="4" id="Rate' + value.id + '4" class="radioRating"><label for="Rate' + value.id + '4">☆</label> <input type="radio" name="' + value.id + '" value="3" id="Rate' + value.id + '3" class="radioRating"><label for="Rate' + value.id + '3">☆</label> <input type="radio" name="' + value.id + '" value="2" id="Rate' + value.id + '2" class="radioRating"><label for="Rate' + value.id + '2">☆</label><input type="radio" name="' + value.id + '" value="1" id="Rate' + value.id + '1" class="radioRating"><label for="Rate' + value.id + '1">☆</label> </div></li>';
                var subComments = '';
                $('.commentsList').prepend(comment);
                // Check rates
                if (value.ratings.length > 0) {
                    $.each(value.ratings, function (index, rating) {
                        if (rating.user.id == $('input[name=user_id]').val()) {
                            $("#Rate" + value.id + rating.rate).attr('checked', 'checked');
                        }
                    })
                }
                if (value.comments.length > 0) {
                    $.each(value.comments, function (index, value) {
                        subComments += '<li>' + value.text + '</li>';
                    })
                    $('#comment' + index).append('<ul>' + subComments + '</ul>');
                }
            })
        });

    // Show Response Input
    var shown = false;
    $('.commentsList').delegate('.commentRespond', 'click', function (e) {
        e.preventDefault();
        if (shown == false) {
            $(this).parent().find('.responseDiv').show();
            shown = true;
        } else {
            $(this).parent().find('.responseDiv').hide();
            shown = false;
        }
    })
    // Rating
    $(document).delegate('input:radio', 'change', function (e) {
        console.log($(this).parent().parent().find('.commentRespond').data('id'));
        $.ajax({
            method: 'POST',
            url: Routing.generate('add_rate'),
            data: {
                comment_id: $(this).parent().parent().find('.commentRespond').data('id'),
                rate: $(this).val(),
                user_id: $('input[name=user_id]').val(),

            },
            success: function (data) {
                alert('Note a été ajouté avec succes')
            },
            error: function (data) {
                alert('Vous avez déjà noté ce commentaire');
            }
        })
    });

    // Add Response Request
    $('.commentsList').delegate('.addSubComment', 'click', function (e) {
        e.preventDefault();
        let id = $(this).data('id');
        console.log($(this).parent().find('input').val());
        $.ajax({
            method: 'POST',
            url: Routing.generate('app_comment_create'),
            data: {
                text: $(this).parent().find('input').val(),
                article_id: $('input[name=article_id]').val(),
                user_id: $('input[name=user_id]').val(),
                parent_id: $(this).data('id'),
            },
            success: function (data) {
                location.reload();
            }
        })
    })

    // Add Comment Request
    $('#addComment').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            method: 'POST',
            url: Routing.generate('app_comment_create'),
            data: $('form[name="commentaire"]').serialize(),
            success: function (data) {
                location.reload();
            }
        })
    })
});