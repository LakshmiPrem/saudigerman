<script type="text/javascript">
// pagination
apply_pagination();
function apply_pagination() {

var pageSize = 12;
var incremSlide = 4;
var startPage = 0;
var numberPage = 0;


var pageCount =  $(".searchCard").length / pageSize;
var totalSlidepPage = Math.floor(pageCount / incremSlide);
    
for(var i = 0 ; i<pageCount;i++){
    $("#pagin").append('<li><a href="#">'+(i+1)+'</a></li> ');
    if(i>pageSize){
       $("#pagin li").eq(i).hide();
    }
}

var prev = $("<li/>").addClass("prev").html("Prev").click(function(){
   startPage-=5;
   incremSlide-=5;
   numberPage--;
   slide();
});

prev.hide();

var next = $("<li/>").addClass("next").html("Next").click(function(){
   startPage+=5;
   incremSlide+=5;
   numberPage++;
   slide();
});

//$("#pagin").prepend(prev).append(next);

$("#pagin li").first().find("a").addClass("current");

slide = function(sens){
   $("#pagin li").hide();
   
   for(t=startPage;t<incremSlide;t++){
     $("#pagin li").eq(t+1).show();
   }
   if(startPage == 0){
     next.show();
     prev.hide();
   }else if(numberPage == totalSlidepPage ){
     next.hide();
     prev.show();
   }else{
     next.show();
     prev.show();
   }
   
    
    }

    showPage = function(page) {
         $(".searchCard").hide();
         $(".searchCard").each(function(n) {
             if (n >= pageSize * (page - 1) && n < pageSize * page)
                 $(this).show();
         });        
    }
        
    showPage(1);
    $("#pagin li a").eq(0).addClass("current");

    $("#pagin li a").click(function() {
        $("#pagin li a").removeClass("current");
        $(this).addClass("current");
        showPage(parseInt($(this).text()));
    });
}    


$("#search_").on("keyup", function() {
  var SearchCount = 0;  
  var input = $(this).val().toUpperCase();
  $(".searchCard").each(function() {
    if ($(this).data("string").toUpperCase().indexOf(input) < 0) {
      $(this).hide();
    } else {
        SearchCount++;
      $(this).show();
    }
  });
  if(SearchCount == 0){
    $('.no_result').toggleClass('hide');
  }else{
    $('.no_result').addClass('hide');
  }

});
</script>