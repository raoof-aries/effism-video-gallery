<!--<tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr>-->
<br><br><br><br>
<div align="center" class="footer">Copyright &COPY; 2015 - <?=date('Y')?>  TIME
<!--<tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr>-->
<div class="scrollWrap">
    <a class='pageNav' id="return-to-top"><i class="fa fa-chevron-up"></i></a>
    <a class='pageNav' id="go-to-bottom"><i class="fa fa-chevron-down"></i></a><br><br>
</div> 
</div> 
 
<?php $current_file_name = basename($_SERVER['PHP_SELF']);?>
<!--<br><br>-->
<?php if($current_file_name!="employee_health_history.php"){?>
<!--<div class="scrollWrap">-->
<!--    <a class='pageNav' id="return-to-top"><i class="fa fa-chevron-up"></i></a>-->
<!--    <a class='pageNav' id="go-to-bottom"><i class="fa fa-chevron-down"></i></a>-->
<!--</div>-->
<?php }?>


 <div class="my-alert"></div>

<style type="text/css">

    :root {

        --p_top: 15px; 

    }

   

    .scrollWrap {

        position: fixed;

        right: 10px;

        bottom: 30px;

        display: block;

        z-index: 9;

    }

 

    .pageNav {

        width: 30px;

        height: 40px;

        margin: 5px 0;

        display: block;

        text-decoration: none;

       background: rgba(0, 0, 0, 0.7);

        position: relative;

        -webkit-border-radius: 35px;

        -moz-border-radius: 35px;

        border-radius: 35px;

        -webkit-transition: all 0.3s linear;

        -moz-transition: all 0.3s ease;

        -ms-transition: all 0.3s ease;

        -o-transition: all 0.3s ease;

        transition: all 0.3s ease;

    }

 

    .pageNav i {

        color: #fff;

        margin: 0;

        position: relative;

        top: var(--p_top);

        left: 1px;

        font-size: 20px;

        -webkit-transition: all 0.3s ease;

        -moz-transition: all 0.3s ease;

        -ms-transition: all 0.3s ease;

        -o-transition: all 0.3s ease;

        transition: all 0.3s ease;

   }

 

    .pageNav:hover {

        background: rgba(0, 0, 0, 0.9);

    }

 

    #return-to-top:hover i {

        top: calc(var(--p_top) - 10px);

    }

 

    #go-to-bottom:hover i {

        top: calc(var(--p_top) + 10px);

    }

 

    .footer{

        position: fixed;

        bottom:0px;

        width:100%;

        background:#34495e;

        padding:3px;

        color:#ffffff;

        font-size:13px;

        opacity:0.8;

    }

</style>
<!--<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>-->

<script>
// $(function(){
//   $(".my-alert").load("../alert.php");
// });//Commented by SARANYA ON 31-JULY SINCE IT THROWS ERROR
    window.onscroll = function() {myFunction()};
    var header = document.getElementById("myHeader");
var sticky = header.offsetTop;

    function myFunction() { 
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {//alert("e")
       $('#return-to-top').fadeIn();
    }
        var header = document.getElementById("myHeader");
        var winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        var height = document.documentElement.clientHeight;
        var scrolled = (winScroll / height) * 100;//alert(scrolled)
        
        if(scrolled >= 20) {
            $('#return-to-top').fadeIn();
        } else {
            $('#return-to-top').fadeOut();
        }

        if (scrolled >= 65) {
            $('#go-to-bottom').fadeOut();
        } else {
            $('#go-to-bottom').fadeIn();
        }
        
        
        
        
         if (window.pageYOffset > sticky) {
    // header.classList.add("sticky");//Commented
  } else {
    header.classList.remove("sticky");
  }
    }
    
    $('#go-to-bottom').click(function(e) {
        e.preventDefault();
        $('body,html').animate({
            scrollTop : $(document).height()
        }, 500);
    });
    
    $('#return-to-top').click(function(e) {
        e.preventDefault();
        $('body,html').animate({
            scrollTop : 0
        }, 500);
    });
    window.onscroll = function() {myFunction()};

// var header = document.getElementById("myHeader");
// var sticky = header.offsetTop;

// function myFunction() {
//   if (window.pageYOffset > sticky) {
//     header.classList.add("sticky");
//   } else {
//     header.classList.remove("sticky");
//   }
// }
</script>