function ajax(url, data) {
    $.ajax({
        url: url,
        type: "post",
        data: data,
        datatype: 'json',
        success: function (data) {
            return data;
        },
        error: function () {
            return null;
        }
    });

}
function Toast(message, time) {
    Materialize.toast(message, time);
}

function login() {
    var data = $('#loginForm').serialize();
    Toast(data, 6000);
    $.ajax({
        url: "./login",
        type: "post",
        data: data,
        datatype: 'json',
        success: function (data) {
            if (data.success != true) {
                Toast(data.message, 4000);
            } else {
                window.location.replace("./home.php");
                Toast("Welcome Back :)", 6000);
            }
        },
        error: function () {
            Toast('Oops Something Went Wrong', 6000);
            return null;
        }
    });
}

function register() {
    var data = $('#loginForm').serialize();
    $.ajax({
        url: "./register",
        type: "post",
        data: data,
        datatype: 'json',
        success: function (data) {
            if (data.success != true) {
                Toast(data.message, 4000);
            } else {
                window.location.replace("./index.php");
                Toast("Your account has been created successfully ", 6000);
            }
        },
        error: function () {
            Toast('Oops Something Went Wrong', 6000);
            return null;
        }
    });
}


function postsCard(item) {
Toast(item.FeedFile,8000);
    var card =
        '<div class="card">' +
        '<div class="row author-card valign-wrapper">' +
        '<div class="col s2 center-align">' +
        '<img src="./image/profile/' + item.holderImage + '" alt="" class="circle responsive-img user-image">' +
        '</div>' +
        '<div class="col s10 name-date-col">' +
        '<span class="author-name"><b>' + item.holderUserName + '</b></span><br>' +
        '<span class="post-date">' + item.Date + '</span>' +
        '</div>' +
        '</div>';
    if (item.FeedFile != null) {
        if (item.FeedType == "1") {
            card += '<div class="card-image">';
            card += '<img class="materialboxed"';
            if (item.FeedStatus != null) {
                card += ' data-caption="' + item.FeedStatus + '"';
            }
            if (item.FeedFile != null) {
                card += ' src="./image/home/' + item.FeedFile + '">';
            }


            card += '</div>';
        } else if (item.FeedType == "2") {
            if (item.FeedVideoThumbnail != null) {
                card += '<div class="card-image">';
                card += '<img class="materialboxed"';
                if (item.FeedStatus != null) {
                    card += ' data-caption="' + item.FeedStatus + '"';
                }
                if (item.FeedFile != null) {
                    card += ' src="./image/home/' + item.FeedFile + '">';
                }

                card += '</div>';
            }

        }
    }

    if (item.FeedStatus != null) {
        card += '<div class="card-content">' +
        '<p>' + item.FeedStatus + '</p>' +
        '</div>';
    }
    if (item.Place != null) {
        card += '<div class="center-align" style="color:#2196F3">' +
        '<p><i class="small mdi-maps-place"></i>' + item.Place + '</p>' +
        '</div>';
    }
    if (item.Link != null) {
        if (item.Link.type == 'youtube') {
            card += '<div class="video-container">';
            card += '<iframe width="853" height="480" src="https://www.youtube.com/embed/' + item.Link.link + '?rel=0" frameborder="0" allowfullscreen></iframe>';
            card += '</div>';
        } else {
            card += '<div class="col s12 m6"><div class="card blue lighten-1">';
            card += '<div class="card-content white-text">';
            card += '<span class="card-title">' + item.Link.title + '</span>';
            card += '<div class="card-content"><p>' + item.Link.desc + '</p></div>';
            card += '<img src="' + item.Link.image + '">';
            card += '</div></div></div>';
            card += '<div class="card-content center"><a target="_blank" href="' + item.Link.link + '">' + item.Link.title + '</a></div>';
            eval("$('.materialboxed').materialbox();");
        }
    }
    card += '<div class="card-action">';
    card += '<a href="javascript:void(0);" onClick="alert(555);"><i ';
    if (item.Liked) {
        card += 'style="color:red;" ';
    } else {
        card += 'style="color:black;" '
    }
    card += 'class="small mdi-action-favorite"></i></a>';
    card += '<a href=""><i style="color:black;" class="small mdi-social-share"></i></a>';
    card += '</div></div><!-- card//-->';
    return card;
}
function getPosts(page) {
    $.ajax({
        url: "./posts/all/" + page,
        type: "get",
        datatype: 'json',
        beforeSend: function () {
            $('#loadingSpinner').show();
        },
        success: function (data) {
            $('#loadingSpinner').hide();

            $.each(data, function (index, item) {
                Toast(item.FeedFile,6000);
                $('#postsContainer').append(postsCard(item));
            });
        },
        error: function () {
            Toast('Oops Something Went Wrong', 6000);
        }
    });
}