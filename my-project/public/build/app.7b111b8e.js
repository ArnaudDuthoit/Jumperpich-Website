let element = document.querySelector('.js-filter');
let pagination = document.querySelector('.js-filter-pagination');
let content = document.querySelector('.js-filter-content');
let loader = document.querySelector('.js-loading');

function Filter(element) {

    if (element !== null) {
        this.pagination = element.querySelector('.js-filter-pagination');
        this.content = element.querySelector('.js-filter-content');
        this.form = element.querySelector('.js-filter-form');
        this.page = parseInt(new URLSearchParams(window.location.search).get('page') || 1);
        this.bindEvents();
    }
}

function bindEvents() {

    const aClickListener = e => {
        if (e.target.tagName === 'A') {
            e.preventDefault();
            this.loadURL(e.target.getAttribute('href'))
        }
    };


    this.form.querySelectorAll('input[type=checkbox]').forEach(input => {
        input.addEventListener('change', this.loadForm.bind(this))
    });
    this.form.querySelectorAll('input[type=text]').forEach(input => {
        input.addEventListener('keyup', this.loadForm.bind(this))
    });

        this.pagination.addEventListener('click', aClickListener)
}


async function loadForm() {

    const data = new FormData(this.form);
    const url = new URL(this.form.getAttribute('action') || window.location.href);
    const params = new URLSearchParams();
    data.forEach((value, key) => {
        params.append(key, value)
    });

    return this.loadURL(url.pathname + '?' + params.toString())

}

async function loadURL(url) {

    showLoader();

    const params = new URLSearchParams(url.split('?')[1] || '');
    params.set('ajax', '1');
    const response = await fetch(url.split('?')[0] + '?' + params.toString(), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    if (response.status >= 200 && response.status < 300) {
        const data = await response.json();
        this.content.innerHTML = data.content;
        let cards = Array.from(document.querySelectorAll('.mix'));
        cards.forEach((card) => {
            card.classList.add('is-animated')
        });
        this.pagination.innerHTML = data.pagination;

        params.delete('ajax');
        history.replaceState({}, '', url.split('?')[0] + '?' + params.toString())
    } else {
        console.error(response)
    }

    hideLoader();
}


Filter(element);

function showLoader() {
    document.getElementById('loader').style.display = null;
    document.getElementById('loader').setAttribute('aria-hidden', 'false');
    document.querySelector('.js-filter-form').classList.add('is-loading');
}


function hideLoader() {
    document.getElementById('loader').style.display = 'none';
    document.getElementById('loader').setAttribute('aria-hidden', 'true');
    document.querySelector('.js-filter-form').classList.remove('is-loading');
}


// Disparition Page loading une fois le DOM chargé //
window.addEventListener('load', function () {
    document.getElementById('loading').parentNode.removeChild(document.getElementById('loading'));
});


// Retour au top //

function BackToTop() {
    $(document).ready(function () {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 300) {
                $('#back-to-top').fadeIn()
            } else {
                $('#back-to-top').fadeOut()
            }
        });
        $('#back-to-top').click(function () {
            $('body,html').animate({scrollTop: 0}, 800);
            return false;
        })
    })
}

BackToTop();



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








