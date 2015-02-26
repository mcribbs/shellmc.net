/* Countdown Timer */

$('#countdown').countdown(clockEndDate, function(event) {
    
     var $this = $(this).html(event.strftime(''
         
         + '<div id="countdown-days" >%D <span>Days</span></div> '
         + '<div id="countdown-hours" >%H <span>Hrs</span></div>  '
         + '<div id="countdown-minutes" >%M <span>Min</span></div> '
         + '<div id="countdown-seconds" >%S <span>Sec</span></div> '));
 });
 
 /* Slider Backgrounds */
 
//$.vegas('slideshow', {
//	 delay:5000,
//  backgrounds:[
//    { src:'img/1.jpg', fade:10000 },
//    { src:'img/2.jpg', fade:10000 },
//    { src:'img/3.jpg', fade:10000 }
//  ]
//})('overlay', {
//  src:'img/overlay.png'
//});

