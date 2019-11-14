// Disparition Page loading une fois le DOM chargé //
window.addEventListener('load', function () {
        document.getElementById('loading').parentNode.removeChild(document.getElementById('loading'));
});

// Nettoyeur de classes inutilsées (https://github.com/philipwalton/html-inspector) //

/*
HTMLInspector.rules.extend("unused-classes", function(config) {
    config.whitelist.push(/^sf\-/);
    return config
});

HTMLInspector.rules.extend("unused-classes", function(config) {
    config.whitelist.push(/^far/);
    return config
});

HTMLInspector.rules.extend("unused-classes", function(config) {
    config.whitelist.push(/^clear-fix/);
    return config
});

HTMLInspector.rules.extend("unused-classes", function(config) {
    config.whitelist.push(/^fas/);
    return config
});

HTMLInspector.rules.extend("unused-classes", function(config) {
    config.whitelist.push(/^fa\-/);
    return config
});

HTMLInspector.inspect(["unused-classes"]); */

// Retour au top //

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

// Aller a la section Podcast /
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
            $('body,html').animate({scrollTop: $(window).height()}, 800);
            return false;
        });
    })
}


// Google Analytics //
function gtags() {
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments)
    }

    gtag('js', new Date());
    gtag('config', 'UA-143368877-1')
}

// Menu déroulant Select1 //
function Select2() {
    $('select').select2()
}

BackToTop();
GoToPodcast();
Select2();
gtags();


// Intersect Observer pour animation apparition au scroll //
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


// Counter caracteres form contact //

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


// Tri index Ajax //

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

