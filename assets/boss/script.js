$(".menu-bar").click(function(){
  $(".slide-menu").addClass("slided");
});
$(".back-btn").click(function(){
  $(".slide-menu").removeClass("slided");
});

 
$("html").easeScroll({
  frameRate: 50,
  animationTime: 2000,
  stepSize: 120,
  pulseAlgorithm: 1,
  pulseScale: 8,
  pulseNormalize: 1,
  accelerationDelta: 20,
  accelerationMax: 1,
  keyboardSupport: true,
  arrowScroll: 50,
  touchpadSupport: true,
  fixedBackground: true
});