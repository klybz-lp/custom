$(function(){
	/*$('body').on('touchmove', function (event) {
	    event.preventDefault();
	});*/
	var startY,startX,endX,endY,posY;
	var main = $('#main');  //手指滑动的元素
	var swiper = $('#swiper'); //实际发生滑动的元素
	var view = $('.view');
	var list = $('#list li');
	var btnsW = $('.btns').width();
	var mainH = main.height();
	var swiperEl = document.getElementById('swiper');

	//判断滑动的方向
	main.on('touchstart',function(e){

		var touch=e.originalEvent.targetTouches[0];

		startX=touch.pageX;

		startY=touch.pageY;
		
		posY = getTranslateY(swiperEl);

	}).on('touchmove',function(e){

		e.stopPropagation();  //阻止事件的冒泡，不让事件向document上蔓延，但是默认事件任然会执行
		e.preventDefault();  //阻止默认事件的方法,如上下滑动页面到顶部或底部时，页面就会出现黑色的空白(如该网页由某某提供)，即是boyd、html系统默认的touchmove事件

		var touch = e.originalEvent.changedTouches[0];

   		endX= touch.pageX,

    	endY= touch.pageY,

    	X= endX - startX,

    	Y = endY - startY;
		
		if( Math.abs(X) > Math.abs(Y) && X > 0 ) {  //右滑

	        //console.log("left 2 right");
	        
	        list.css("transform","translateX(0px)");

	    }else if ( Math.abs(X) > Math.abs(Y) && X < 0 ) { //左滑

			swiper.on('touchmove','li', function(e){

				/*if (Math.abs(X) < btnsW/3) { //向左滑动

					$(this).eq(0).css("transform","translateX("+X*3+"px)");

				}*/

				if (Math.abs(X) > btnsW/3) { //向左滑动

					$(this).css("transform","translateX("+-btnsW+"px)");

				}else if(Math.abs(X) > 10 && Math.abs(X) > btnsW/3){
	
					$(this).css("transform","translateX("+X*3+"px)");

				}else if(Math.abs(X) < 10){
	
					return;
				}

			})



    	}else if ( Math.abs(Y) > Math.abs(X) && Y > 0) { //下滑

        	if (Y < 50) {

				swiper.css("transform","translateY("+Y+"px)");

			}

			$(this).on('touchend',function(e){

				//$(this).animate({"transform": "translateY(0px)"}, 500) //animate方法不支持transform属性
				//swiper.css("transform","translateY(0px)");

			})

    	}else if ( Math.abs(Y) > Math.abs(X) && Y < 0 ) { //上滑

    		var currentY = eval(Y + posY); //强制进行运算，不然返回的结果是类似50-100
    		//console.log(currentY)
    		//元素Y方向位置=手指按下时元素的translateY值+手指滑动的距离
   			//if (mainH <= Math.abs(currentY)) {
   				swiper.css("transform","translateY("+-mainH+"px)");   
   			//}else{
   				//swiper.css("transform","translateY("+currentY+"px)"); 
   			//}

    	}else{  //点击

        	//console.log("just touch");

    	}	

	})


	
	

})

//获取元素translateY值函数
var getTranslateY = function(node){
    var regRule = /translate(Y|\dd)?\(\s*(\w+\s*,)?\s*([^,]+)(\s*,[^)]+)?\s*\)/;
    var regRule2 = /matrix\(.*,\s*(\w+)\s*\)/;
    var transform = node.style.transform;
    var reg;
    if(!transform){
        return null;
    }
    reg = regRule.exec(transform);
    if(null === reg){
        reg = regRule2.exec(transform);
        return reg ? reg[1] : null;
    }

    var transY = reg[3].substring(0, reg[3].length-2)  //去掉px
    return transY;
}
