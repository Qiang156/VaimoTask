define([], function(){
    var oBody = document.getElementsByTagName("body")[0];
    var aBanner = document.getElementsByClassName("banner");
    var aSpan = document.getElementsByClassName("tab")[0].getElementsByTagName("span");
    var oNext = document.getElementsByClassName("next")[0];
    var Oprev = document.getElementsByClassName("prev")[0];
    var Oon = document.getElementsByClassName("on")[0];

    aBanner[0].style.opacity = "1";
    aSpan[0].className = "on";

    var num = 0;
    for(var i = 0;i < aSpan.length;i++) {
        aSpan[i].index = i;
        aSpan[i].onclick = function(){
            //click point to show specific picture
            for(var j = 0 ;j < aSpan.length; j++){
                num = this.index;
                aSpan[j].className = "";
                aBanner[j].style.opacity = "0";
            }
            aSpan[num].className = "on";
            aBanner[num].style.opacity = "1";
        }

        oNext.onclick = function() {
            //trigger event to show next
            for(var j = 0 ;j < aSpan.length; j++){
                if(aSpan[j].className == "on"){
                    aSpan[j].className = "";
                    aBanner[j].style.opacity = "0";
                    j++;
                    num++;
                    if(j > 4){
                        j = 0;
                    }
                    aSpan[j].className = "on";
                    aBanner[j].style.opacity = "1";
                }
            }
        }

        Oprev.onclick = function() {
            //trigger event to show last
            for(var j = 0 ;j < aSpan.length; j++){
                if(aSpan[j].className == "on"){
                    aSpan[j].className = "";
                    aBanner[j].style.opacity = "0";
                    j--;
                    num--;
                    if(j < 0){
                        j = 4;
                    }
                    aSpan[j].className = "on";
                    aBanner[j].style.opacity = "1";
                }
            }
        }
    }
    /*function for timer */
    function startTimer() {
        num++;
        if(num < 5){
            for(var j = 0 ;j < aSpan.length; j++){
                aSpan[j].className = "";
                aBanner[j].style.opacity = "0";
            }
            aSpan[num].className = "on";
            aBanner[num].style.opacity = "1";
        }else {
            num = -1;
        }
    }
    clearInterval(timer);
    var timer = setInterval("startTimer",2000);/*call timer*/

    oBody.onmouseover = function(){/*mouse movein，clear timer，stop rotate*/
        clearInterval(timer);
    };
    oBody.onmouseout = function(){/*mouse moveout，recall timer*/
        clearInterval(timer);
        timer = setInterval("startTimer",2000);
    };
});
