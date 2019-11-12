window.addEventListener('load', function () {
        document.getElementById('loading').parentNode.removeChild(document.getElementById('loading'));
});

var headerHeight = $("#nav").height();

function BackToTop() {
    $(document).ready(function () {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 50) {
                $('#back-to-top').fadeIn()
            } else {
                $('#back-to-top').fadeOut()
            }
        });
        $('#back-to-top').click(function () {
            $('#back-to-top').tooltip('hide');
            $('body,html').animate({scrollTop: 0}, 800);
            return !1
        })
    })
}


function GoToPodcast() {
    $(document).ready(function () {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 50) {
                $('#go-to-podcast').fadeOut()
            } else {
                $('#go-to-podcast').fadeIn()
            }
        });
        $('#go-to-podcast').click(function () {
            $('body,html').animate({scrollTop: $(window).height() - headerHeight}, 800);
            return false;
        });
    })
}



function gtags() {
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments)
    }

    gtag('js', new Date());
    gtag('config', 'UA-143368877-1')
}

function Select2() {
    $('select').select2()
}

BackToTop();
GoToPodcast();
Select2();
gtags();
const ratio = .1;
const options = {root: null, rootMargin: '0px', threshold: .1};
const handleIntersect = function (entries, observer) {
    entries.forEach(function (entry) {
        if (entry.intersectionRatio > ratio) {
            entry.target.classList.remove('reveal');
            observer.unobserve(entry.target)
        } else {
        }
    })
};
const observer = new IntersectionObserver(handleIntersect, options);
document.querySelectorAll('.reveal').forEach(function (r) {
    observer.observe(r)
});
counter = function () {
    var value = $('#form_message').val();
    if (value.length === 0) {
        $('#text').html("Message " + "(" + 0 + "/255)");
        return
    }
    var totalChars = value.length;
    $('#text').html("Message " + "(" + totalChars + "/255)");
    if (value.length >= 240) {
        document.getElementById('text').style.color = 'red'
    }
    if (value.length < 240) {
        document.getElementById('text').style.color = 'black'
    }
};
$(document).ready(function () {
    $('#form_message').change(counter);
    $('#form_message').keydown(counter);
    $('#form_message').keypress(counter);
    $('#form_message').keyup(counter);
    $('#form_message').blur(counter);
    $('#form_message').focus(counter)
});

$('button').click(function () {
    $('button').not($(this)).css('color', 'grey');
    $(this).css('color', 'white');
});


$("#tag_all").on("click", function () {
    recupTag_all();
});

$("#tag_1").on("click", function () {
    recupTag_1();
});

$("#tag_2").on("click", function () {
    recupTag_2();
});

$("#tag_3").on("click", function () {
    recupTag_3();
});

function recupTag_all() {
    $.ajax({
        url: "/api/getmixtag",
        data: {
            'tag': null
        },
        success: function ($result) {
            $("#spawn").hide().html($result).fadeIn('slow');
            //document.getElementById('row').innerHTML = $result;
        }
    });
}


function recupTag_1() {
    $.ajax({
        url: "/api/getmixtag",
        data: {
            'tag': 1
        },
        success: function ($result) {
            $("#spawn").hide().html($result).fadeIn('slow');
        }
    })
}

function recupTag_2() {
    $.ajax({
        url: "/api/getmixtag",
        data: {
            'tag': 2
        },
        success: function ($result) {
            $("#spawn").hide().html($result).fadeIn('slow');
        }
    });
}

function recupTag_3() {
    $.ajax({
        url: "/api/getmixtag",
        data: {
            'tag': 3
        },
        success: function ($result) {
            $("#spawn").hide().html($result).fadeIn('slow');
        }
    });
}

