window.onload = function(){

	document.addEventListener("touchstart", function(e) {
		e.preventDefault(); //阻止默认事件
	},false)

	//createLi()

	//var wrap = document.querySelector("#wrap");
	//var inner = document.querySelector("#inner");
	// mScroll({
	// 	el: wrap,
	// 	start: function(e){  //touchstart回调函数
	// 		console.log("touchstart回调函数");
	// 	},
	// 	move: function(e){  //touchmove回调函数
	// 		console.log("touchmove回调函数");
	// 	},
	// 	end: function(e){  //touchend回调函数
	// 		console.log("touchend回调函数");
	// 	},
	// 	over: function(e){  //滚动结束(包含惯性运动)回调函数
	// 		console.log("滚动结束回调函数");
	// 	}
	// })
}
//生成内容
function createLi(){
	var li = document.createElement("li");
	for (var i = 0; i < 101; i++) {
		li.innerHTML += '<li><a href="#"><img src="iphone.jpg" alt=""> <span class="spanWrap"><span class="sTitle">苹果(Apple)iphone'+i+'(A1586) 32G金色移动 联通电信4G手机</span> <span class="sPrice">¥4888.00</span> <span class="sComment">好评96% 59091</span></span></a></li>';
	}
	inner.append(li);
}

//滚动函数
function mScroll(init){

	if (!init.el) {
		return;
	}

	//var inner = document.querySelector("#inner");
	var inner = init.el.children[0];
	var scrollBar = document.createElement("div");  //自定义滚动条
	init.el.appendChild(scrollBar);
	//cssTooL(inner,"translateY",200);
	var startPoint = 0;  //手指按下时的y坐标
	var startEl = 0;  //手指按下时元素的translateY的值
	cssTooL(inner,"translateY",0);
	cssTooL(inner,"translateZ",0.01); //开启3d硬件加速，提升效率
	var lastY = 0; //上一次的元素位置，用来获取最后一次滑动的距离
	var lastDis = 0; //最后一次滑动的距离，用来做滑动缓冲效果
	var lastTime = 0; //上一次滑动的时间
	var lastimeDis = 0; //时间差
	var percent = 0.3; //超出边界时滑动阻力
	//滑动缓冲，根据最后一次的滑动速度(滑动距离/滑动时间)来计算，最后一次滑动的速度越快，松开手指后惯性滑动的距离越长
	var maxY = init.el.clientHeight - inner.offsetHeight; //上滑最大的距离，offsetHeight是内容高+padding+边框，但不加margin，clientHeight在页面上返回内容的可视高度
	var scale = init.el.clientHeight/inner.offsetHeight; //根据比例计算滚动条的高度
	scrollBar.style.cssText = "width:4px;background:rgba(0,0,0,.5);position:absolute;right:0;top:0;border-radius:2px;opacity:0;transition:.3s;";
	inner.style.MinHeight = "100%";
	init.el.addEventListener("touchstart", function(e) {
		/*if(inner.style.height < Math.abs(maxY)){ //内容高度小于一屏时

			return;
		}*/
		scrollBar.style.opacity = 1;
		maxY = init.el.clientHeight - inner.offsetHeight; //如果是动态添加了内容，则需要修改下最大的滑动距离
		if (maxY >= 0) { //内容高度小于一屏时
			scrollBar.style.display = "none";
		} else {
			scrollBar.style.display = "block";
		}
		scale = init.el.clientHeight/inner.offsetHeight;
		scrollBar.style.height = init.el.clientHeight*scale + "px";
		clearInterval(inner.timer); //如果鼠标按下时在发生缓冲动画，则清除
		//inner.style.webkitTransition = "none";    //去掉过渡
		//inner.style.transition = "none";  //touchmove如果使用transition过度效果，则很难拖动
		var touch = e.changedTouches[0];
		startPoint = touch.pageY;
		startEl = cssTooL(inner,"translateY");
		lastY = startEl;
		lastTime = new Date().getTime();
		lastimeDis = lastDis = 0;
		//init.start&&init.start(); //如果声明了回调函数则调用	
		init.start&&init.start.call(init.el,e); //修改this指向为init.el
	},false)

	init.el.addEventListener("touchmove", function(e) {
		var touch = e.changedTouches[0];
		nowPoint = touch.pageY;
		var nowTime = new Date().getTime();
		var disY = nowPoint - startPoint;
		var translateY = disY + startEl;
		//超出边界增加滑动的阻力
		if (translateY > 0) {  //上边界
			translateY = translateY*percent
		}else if(translateY < maxY) {  //下边界
			translateY = maxY + disY*percent;
		}
		cssTooL(inner,"translateY",translateY);
		cssTooL(scrollBar,"translateY",-translateY*scale);  //滚动条的滚动距离
		lastDis = translateY - lastY;  //得到最后一次滑动的距离，滑动速度越快，差值越大，与滑动的距离无关
		lastY = translateY;  //每次滑动同步上一次的元素位置为当前位置
		lastimeDis = nowTime - lastTime;
		lastTime = nowTime;  //同步当前时间为上一次时间
		init.move&&init.move.call(init.el,e); //修改this指向为init.el
	},false)

	init.el.addEventListener("touchend", function(e) {
		//var touch = e.changedTouches[0];
		var type = "easeOut"; //动画类型
		var speed = Math.round(lastDis/lastimeDis*10);  //最后一次的滑动速度，做缓冲效果,如果手指按下就松开，不进行移动，lastDis则是0，lastimeDis也会出现0的情况，导致得到的值是NAN或无穷大infinite
		if (Math.abs(speed) < 5 || lastimeDis <= 0) speed =0;
		var target = Math.round(speed*30 + cssTooL(inner,"translateY")); //缓冲的距离
		//判断是否滑动到边界
		if (target > 0) {
			target = 0;
			type = "backOut"; //超出边界时改为回弹动画，有一点抖动的感觉
		} else if(target < maxY) {
			target = maxY;
			type = "backOut"; 
		}
		//console.log(speed);
		//console.log(target);
		
		startMove({
			el: inner,
			target:{"translateY":target},
			type: type,
			time: Math.round(Math.abs(target-cssTooL(inner,"translateY"))*1.8), //如不流畅可设置为固定值如300
			callIn: function(){  //动画执行中的回调函数
				var target = cssTooL(inner,"translateY");
				cssTooL(scrollBar,"translateY",-target*scale);
				init.move&&init.move.call(init.el,e); //惯性滚动时也需执行touchmove的回调函数
			},
			callBack: function(){  //动画执行后的回调函数
				scrollBar.style.opacity = 0;
				init.over&&init.over.call(init.el,e); //惯性滚动结束后执行滚动完成的回调函数
			}
		});
		
		init.end&&init.end.call(init.el,e); //修改this指向为init.el
	},false)
}