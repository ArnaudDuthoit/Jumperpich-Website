// Disparition Page loading une fois le DOM chargé //
window.addEventListener('load', function () {
        document.getElementById('loading').parentNode.removeChild(document.getElementById('loading'));
});


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
/* const ratio = .1;
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
}); */


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





