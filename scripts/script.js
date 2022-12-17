//burger
$('#menu-toggle').on('click', () => {
        if($('body').css('overflow-y') == 'auto'){
             $('body').css('overflow-y', 'hidden');
             $('#menu').css('display', 'block');
         }
        else{
             $('body').css('overflow-y', 'auto');
             $('#menu').css('display', 'none');
        }
});

$(document).ready(function(){
    $(".ToSection").on("click", function (event) {
        event.preventDefault();
        var id  = $(this).attr('href'),
            top = $(id).offset().top;
        $('body,html').animate({scrollTop: top}, 1500);
        $("#menu-toggle").click();
        $('body').css('overflow-y', 'auto');
    });
});
$(document).ready(function(){
    $(".ToSection2").on("click", function (event) {
        event.preventDefault();
        var id  = $(this).attr('href'),
            top = $(id).offset().top;
        $('body,html').animate({scrollTop: top}, 1500);
        
        
    });
});
function resize(){
    if ($('body').width() > 770 && $("#menu-toggle").prop("checked")){
        $("#menu-toggle").click();
         $('body').css('overflow-y', 'auto');
    }
}
$(document).ready(function(){
    setInterval('resize()',10);

});
//swipers
var swiper = new Swiper('.newcont1', {
      spaceBetween: 30,
      centeredSlides: true,
      loop: true,
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      direction: 'vertical',
     
      
    });
var swiper2 = new Swiper('.newcont2', {
      loop: true,
      spaceBetween: 30,
      centeredSlides: true,
      
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
      
    });
$('#path span').on('click', function(el){
    id = $(this).attr('id');
    id = id.substring(id.indexOf("_") + 1);
    if(id == '14'){
        $('.newwrap1').css('transform', 'translate3d(0px, 0px, 0px)');
    }
    else if(id == '1'){
        $('.newwrap1').css('transform', 'translate3d(0px, -580px, 0px)');
    }
    else if(id == '2'){
        $('.newwrap1').css('transform', 'translate3d(0px, -1160px, 0px)');
    }
    else if(id == '3'){
        $('.newwrap1').css('transform', 'translate3d(0px, -1740px, 0px)');
    }
    else if(id == '4'){
        $('.newwrap1').css('transform', 'translate3d(0px, -2320px, 0px)');
    }
    else if(id == '5'){
        $('.newwrap1').css('transform', 'translate3d(0px, -2900px, 0px)');
    }
    else if(id == '6'){
        $('.newwrap1').css('transform', 'translate3d(0px, -3480px, 0px)');
    }
    else if(id == '7'){
        $('.newwrap1').css('transform', 'translate3d(0px, -4060px, 0px)');
    }
    else if(id == '8'){
        $('.newwrap1').css('transform', 'translate3d(0px, -4640px, 0px)');
    }
    else if(id == '9'){
        $('.newwrap1').css('transform', 'translate3d(0px, -5220px, 0px)');
    }
    else if(id == '10'){
        $('.newwrap1').css('transform', 'translate3d(0px, -5800px, 0px)');
    }
    else if(id == '11'){
        $('.newwrap1').css('transform', 'translate3d(0px, -6380px, 0px)');
    }
    else if(id == '12'){
        $('.newwrap1').css('transform', 'translate3d(0px, -6960px, 0px)');
    }
    else if(id == '13'){
        $('.newwrap1').css('transform', 'translate3d(0px, -7540px, 0px)');
    }
    $('.swiper-wrapper').css('transition', 'all 300ms ease 0s');
;});
//login
$('.loginingmenu').on('click', function(){
        $('#loginingmenu').css('display', 'block');
});
$('.loginingmenu2').on('click', function(){
        $('#loginingmenu').css('display', 'block');
});
$('#loginingmenuExit').on('click', function(){
    $('#loginingmenu').css('display', 'none');
});
const signUpButton = $('#signUp');
const signInButton = $('#signIn');
const containers = $('#containerr');

signUpButton.on('click', () => {
    containers.addClass('right-panel-active');
    

});
$('#messsendbut').on('click', () =>{
    $('#loginingmenu').css('display', 'block');
});
$('#messinput').on('click', () =>{
    $('#loginingmenu').css('display', 'block');
});
signInButton.on('click', () => {
    containers.removeClass('right-panel-active');
});
$('#loginingmenuExit').on('click', () => {
$('#loginingmenu').css('display', 'none');
});
$(document).ready(function(){
$('.exit_button').attr('value', '');
});
// login ajax
$(document).ready(function(){
    $('.ajax_login_form').submit(function(e) {
    
    e.preventDefault();
    $.ajax({
      type: "POST",
      url: '/php/login.php',
      data: $(this).serialize(),

      success: function(data) {
        alert(data);
        location.reload();

      },
      error: function() {
        alert('There was some error performing the AJAX call! ');

      }
        });

    });
});
//download count
var counters = document.getElementsByClassName('number-ticker');
var defaultDigitNode = document.createElement('div');
defaultDigitNode.classList.add('digit');

for (var i = 0; i < 10; i++) {
    defaultDigitNode.innerHTML += i + '<br>';
}

[].forEach.call(counters, function (counter) {
    var currentValue = parseInt(counter.getAttribute('data-value')) || 0;
    var digits = [];

    generateDigits(currentValue.toString().length);
    setValue(currentValue);
    $(window).scroll(function() { 
  if ($(this).scrollTop() > 3400) {
    setTimeout(function () {
        setValue(Math.floor(777777));
    }, 1000);

  }

});
    

    function setValue (number) {
        var s = number.toString().split('').reverse().join('');
        var l = s.length;

        if (l > digits.length) {
            generateDigits(l - digits.length);
        }

        for (var i = 0; i < digits.length; i++) {
            setDigit(i, s[i] || 0);
        }
    }

    function setDigit (digitIndex, number) {
        digits[digitIndex].style.marginTop = '-' + number + 'em';
    }

    function generateDigits (amount) {
        for (var i = 0; i < amount; i++) {
            var d = defaultDigitNode.cloneNode(true);
            counter.appendChild(d);
            digits.unshift(d);
        }
    }
});
// russian -> inglish