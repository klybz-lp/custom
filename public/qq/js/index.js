//阻止默认行为,如上下滑动页面到顶部或底部时，页面就会出现黑色的空白(如该网页由某某提供)
/*$('body').on('touchmove', function (event) {
	event.preventDefault();
});*/



$(function(){

	createData(); //加载列表信息
	var main = $('#main');;  //y方向手指滑动的元素
	var swiper = document.querySelector('#swiperContainer');
	var load = document.querySelector('#load');
	var loadImg = load.querySelector('.loadImg');
	var loadImg2 = load.querySelector('.loadImg2');
	var loadText = load.querySelector('.loadText');
	var loadH = load.offsetHeight;
	loadImg.style.transition = ".3s";

	//上下滑屏
	mScroll({
		el: main,
		dir: "y",
		start:function(){
			swiper.style.transition = "none";  //在touchmove时如果有transition过度效果是很诡异的(滑屏时与晃的感觉)，因为touchend时添加了，所以需在touchstart时清除掉
		},
		move: function(){  //获取手指移动时的位置，当超出消息列表的高度大于等于div#load的高度时，箭头变为向上，更改文字的内容
			var now = cssTooL(swiper,"translateY");
			if(now > loadH){
				cssTooL(loadImg,"rotate",-180);  //箭头变为向上
				loadText.innerHTML = "释放立即刷新";
			} else {  //默认load效果
				cssTooL(loadImg,"rotate",0);
				loadText.innerHTML = "下拉刷新";
			}
		},
		end: function(){  //下拉刷新动态效果的变化
			var now = cssTooL(swiper,"translateY");
			console.log(now,loadH)
			if(now > loadH){
				//cancelAnimationFrame(swiper.timer);
				swiper.style.transition = ".3s";
				cssTooL(swiper,"translateY",loadH);  //松开后translateY先固定到div#load的高度，显示正在刷新，等数据加载完后在
				loadImg.style.display = "none";
				loadImg2.style.display = "block";
				loadText.innerHTML = "正在刷新";
				//在transition动画结束之后调用end方法来加载数据，transitionend与WebkitTransitionEnd是用来监听transition结束触发的
				swiper.addEventListener('WebkitTransitionEnd', end); 
				swiper.addEventListener('transitionend', end);
				function end(){
					swiper.removeEventListener('WebkitTransitionEnd', end);
					swiper.removeEventListener('transitionend', end);
					createData(true);
				}
			}
		}
	})


})
//加载列表信息,isLoad用来判断是打开页面时加载数据，还是后面的下拉时生成数据，后者则需要给load修改一些加载的提示
function createData(isLoad){
	if(isLoad){  //下拉刷新请求数据
		alert(1)
			    var swiper = document.querySelector('#swiper');
				var load = document.querySelector('#load');
				var loadImg = load.querySelector('.loadImg');
				var loadImg2 = load.querySelector('.loadImg2');
				var loadText = load.querySelector('.loadText');
				loadImg2.style.display = "none";
				loadText.innerHTML = "刷新完成";
				setTimeout(function(){  //还原load的初始状态
					cssTool(swiper,"translateY",0);
					swiper.addEventListener('WebkitTransitionEnd', end);
					swiper.addEventListener('transitionend', end);
					function end(){
						swiper.removeEventListener('WebkitTransitionEnd', end);
						swiper.removeEventListener('transitionend', end);
						loadImg.style.display = "block";
						loadText.innerHTML = "下拉刷新";
					}
				},500);
			}
	var swiperContainer = $('#swiperContainer ul');
	//var first = getFirst();  //下拉加载添加的消息放在已经置顶了的消息的下面
	$.ajax({
	    type: "post",
	    url: "http://www.liping768.com/qq/info.php",
	    data: {item:"talkinfo"},
	    dataType: "json",
	    beforeSend: function(result){
	     	
	    },
	    success: function(result){
	    			if(!isLoad){  //第一次打开页面或刷新请求数据
			     		$('#loading').hide();
			    		$('#swiperContainer').css('opacity', '1');
			     	}
	                for(var i = 0; i < result.length; i++){
						var li = document.createElement('li');
						//工具函数getDate把时间戳转换为中国时间格式：2018-03-03，刚刚发的则显示为刚刚，类似微博显示的时间
						li.innerHTML = '<div class="scrollContainer"><div class="view"><span class="avatar" style="background-image:url('+result[i].avatar+');"></span><div class="info"><h3>'+result[i].username+'</h3><p>'+result[i].new_message+'</p></div><aside class="aside"><time>'+result[i].date_time+'</time> <mark style="display:'+(result[i].message_number > 1?'block':'none')+'">'+result[i].message_number+'</mark></aside></div><nav class="btns"><a href="javascript:;">置顶</a> <a href="javascript:;">'+(result[i].message_number > 1?'标记已读':'标记未读')+'</a> <a href="javascript:;">删除</a></nav></div>';
						$(li).prependTo(swiperContainer);
						setEv(li);
					}
	             },
	     error: function(result){
	     	alert('数据请求超时');
	     }
	});
}

/* 给生成的li添加左右滑屏及按钮点击事件 */
function setEv(li){
	var main = document.querySelector('#main');
	var swiper = li.querySelector('.scrollContainer');
	var max = swiper.offsetWidth - li.clientWidth;  //左右滑动的最大距离，避免超出边界
	var list = document.querySelector('#list');
	var btnW = $('.btns').width();
	var mark = li.querySelector('mark');
	var btns = li.querySelector('.btns');
	var btn = btns.children;
	var isReader = false;//是否读取
	var isTop = false;  //是否指定
	if(btn[1].innerHTML == "标记未读"){
		isReader = true;
	}
	//封装click事件，如果移动端使用鼠标事件会有300ms的延迟，另外如果使用了阻止默认事件，所有的鼠标事件都会失效
	tap(btn[0],function(){
		if(isTop){
			this.innerHTML = "置顶";
			li.className = "";
		} else {
			this.innerHTML = "取消置顶";
			li.className = "active";	
		}
		isTop = !isTop;
		swiper.addEventListener('WebkitTransitionEnd', end);
		swiper.addEventListener('transitionend', end);
		swiper.style.transition = ".3s";
		cssTooL(swiper,"translateX",0);
		function end(){
			swiper.removeEventListener('WebkitTransitionEnd', end);
			swiper.removeEventListener('transitionend', end);
			if(isTop){  //置顶
				var first = list.children[0];
			} else {  //取消置顶
				var first = getFirst();
			}
			list.insertBefore(li,first);
		}
	});
	//标记事件
	tap(btn[1],function(){
		if(isReader){
			this.innerHTML = "标记已读";
			mark.innerHTML = "1";
			mark.style.display = "block";
		} else {
			mark.style.display = "none";
			this.innerHTML = "标记未读";
		}
		isReader = !isReader;
		swiper.style.transition = ".3s";
		cssTooL(swiper,"translateX",0);
	});
	//删除事件
	tap(btn[2],function(){
		list.removeChild(li);
		var swiperd = document.querySelector('#scrollContainer');
		//var max = main.clientHeight - swiperd.offsetHeight;
		//console.log(main.clientHeight,swiperd.offsetHeight)
		//var now = cssTooL(swiper,"translateY");
		
		/*if(now < max){  //当滑到最下面删除了消息出现白屏时，滑让滑屏元素动最底部
			swiper.style.transition = ".3s";
			cssTooL(swiper,"translateY",max);
		}*/
	});

	//左右滑屏
	mScroll({
		el: $(li),
		dir: "x",
		start:function(){
			var swipers = list.querySelectorAll('.scrollContainer');
			for(var i = 0; i < swipers.length; i++){  //如果滑动元素滑出显示了按钮，而没有右滑还原，当滑动任意元素把上一个滑块返回初始状态
				if(swipers[i] != swiper){  //排除掉当前操作的滑屏元素
					var now = cssTooL(swipers[i],"translateX");
					if(now < 0) {
						swipers[i].style.transition = ".3s";
						cssTooL(swipers[i],"translateX",0);
					}
				}

			}
			swiper.style.transition = "none";
		},
		move: function(lastDir){  //控制左右滑动的最大距离，避免超出边界
			var now = cssTooL(swiper,"translateX");
			if(now > 0){
				now = 0;
			} else if (now < -btnW) {
				now = -btnW;
			}
			cssTooL(swiper,"translateX",now);
		},
		end: function(lastDir){  //控制左右滑动的最大距离，避免超出边界
			var now = cssTooL(swiper,"translateX");
			if (lastDir < 0 && Math.abs(now) <  btnW/3 ) {
				now = 0;
			} else if(lastDir < 0 && Math.abs(now) >  btnW/3 ) {
				now = -btnW;
			} else if(lastDir > 0 ) {
				now = 0;
			}
			cssTooL(swiper,"translateX",now);
		}
	})
}
/****
mScroll  滑屏函数
inti = {
	el: element,  		滑屏元素
	dir: "x"||"y",		滑屏方向
	start:fn, 			可选参数，手指按下的回调
	move:fn,			可选参数，手指移动的回调
	end:fn 				可选参数，手指抬起的回调
}

****/ 
function mScroll(init){

	var el = init.el;  //手指滑动的元素
	var scroll = el.children().eq(0); //实际发生位移的元素
	var startPoint = {};  //手指按下时的坐标
	var startEl = {};  //手指按下时实际发生位移的元素的位置
	var lastPoint = {};  //保存上一次手指触摸的坐标，如果跟这次(即开始移动时)的坐标一致则表示并没有发生touchmove事件，解决安卓手机大面积触屏会触发touchmove的问题 
	var dir = init.dir; //滑动的方向
	var lastDir = 0;  //记录touchmove过程中最后一次滑动的方向(判断是左、右、上、还是下滑动)，为0则表示没有滑动
	var percent = 1;  //滑动的阻力，0到1之间，手机滑动的距离与滑动元素滑动距离的百分比，值越小越难滑动
	var lastTime = 0; //按下的时间，做手指松开后的惯性移动  

	//可滑动的最大距离
	max = {
		x: parseInt(el.width() - scroll.width()),
		y: parseInt(el.height() - scroll.height()),
	}

	//根据滑动的方向来设置对应的样式
	var translate = {
		x: "translateX",
		y: "translateY"
	}
	//判断滑动的方向，避免左右滑动与上下滑动发生冲突，默认都为假
	var isMove = {
		x: false,
		y: false
	};
	var isFrist = true; //记录第一次滑动时的坐标，解决左右来回滑动时，因为x方向的差值可能为0小于y方向的差值而触发了上下滑动的条件 

	cssTooL(scroll[0], translate[dir], 0);  //调用工具函数需吧JQ对象转成dom对象

	el.on('touchstart',function(e){

		init.start&&init.start();  //如果调用时声明了手指按下的回调，则调用start方法

		var touch=e.originalEvent.targetTouches[0];

		scroll[0].style.webkitTransition = "none";    //去掉过渡
		scroll[0].style.transition = "none";  //touchmove如果使用transition过度效果，则很难拖动
 
		percent = 1;  //重置滑动的阻力
		//手指按下时的坐标
		startPoint = {
			x: Math.round(touch.pageX),
			y: Math.round(touch.pageY)
		}

		//同步手指按下时的坐标为上一次触摸的坐标
		lastPoint= {
			x: startPoint.x,
			y: startPoint.y
		};

		startEl = {
			x: cssTooL(scroll[0], "translateX"),
			y: cssTooL(scroll[0], "translateY")
		}
		//可滑动的最大距离，因为数据是通过ajax加载的，height值是变化的，每次手指开始滑动需要重新计算
		max = {
			x: parseInt(el.width() - scroll.width()),
			y: parseInt(el.height() - scroll.height()),
		}

		lastTime = Date.now();  //获取手指按下的时间，时间戳
		lastDir = 0;  //鼠标按下时没有移动方向
		
	}).on('touchmove',function(e){

		e.preventDefault();
		
		var touch = e.originalEvent.changedTouches[0];
		var nowPoint = {
			x: Math.round(touch.pageX),
			y: Math.round(touch.pageY)
		}
		//判断开始移动时的坐标跟手指按下时的坐标是否一致，一致则表明是安卓手机大面积触屏会触发touchmove的问题
		if(lastPoint.x == nowPoint.x && lastPoint.y == nowPoint.y){
			return;
		}
		var dis = {
			x: nowPoint.x - startPoint.x,
			y: nowPoint.y - startPoint.y
		}

		//判断滑动的方向
		if(Math.abs(dis.x) - Math.abs(dis.y) > 2 && isFrist){  //水平方向滑动
			isMove.x = true;
			isFrist = false; //把第一次滑动的条件设为假，避免来回滑动时产生滑屏方向的错误
		} else if(Math.abs(dis.y) - Math.abs(dis.x) > 2 && isFrist){  //垂直方向滑动
			isMove.y = true;
			isFrist = false;
		}

		lastDir = nowPoint[dir] - lastPoint[dir];  //获取最后滑动的距离，如果x方向，并且lastDir为正值，则是右滑,y方向负数是向下滑，正数向上滑
		//console.log(lastDir)
		lastPoint.x = nowPoint.x;  //滑动后同步为上次开始触摸的坐标，touchmove过程中不停的进行比较
		lastPoint.y = nowPoint.y;

		//滑屏元素的当前位置=滑屏元素的起始位置+手指滑动的距离
		var target = {};
		target[dir] = dis[dir] + startEl[dir];

		//超出边界则增加滑动难度
		if (isMove.y) {
			var currentPos = cssTooL(scroll[0], translate[dir]);
			if (currentPos > 0) {  //上边界
				percent = 0.3;
				target[dir] = target[dir]*percent
			}else if(target[dir] < max[dir]) {  //下边界
				percent = 0.3;
				target[dir] = max[dir] + dis[dir]*percent;
			}

		}
		//console.log(percent,target[dir],dis[dir],max[dir])
		isMove[dir]&&cssTooL(scroll[0], translate[dir], target[dir]);
		init.move&&init.move(lastDir);  //如果调用时声明了手指按下的回调，则调用move方法,需放在滑动元素滑动之后才能调用

	}).on('touchend',function(e){

		/*  
            惯性原理:  
            产生的速度 = 移动距离 / 移动时间  
            距离 = 松开的坐标 - 上次的坐标  (距离差)  
            时间 = 松开的时间 - 按下的时间  (时间差)  
         */  
        var touch = e.originalEvent.changedTouches[0];
		var endPoint = {
			x: Math.round(touch.pageX),
			y: Math.round(touch.pageY)
		}
		var dis = {
			x: endPoint.x - startPoint.x,
			y: endPoint.y - startPoint.y
		}

        var timeDis = Date.now() - lastTime;  //时间差  
        var speed = (Math.abs(dis[dir]) / timeDis).toFixed(2);  //toFixed保留两位 
        //console.log(speed)
        if (lastDir >0) { //下滑 右滑,这里的惯性条件待优化
        	if (speed < 1) {
        		speed = 1;
        	} else if (1 < speed <2){
        		speed=speed-1;
        	} else {
        		speed=speed;
        	}
        } else {  //上滑 右滑
        	(speed > 2) ? speed=speed : speed=1;   //通过控制3元表达式前面的条件来控制惯性移动的距离，值越大越难发生惯性移动

        }
        if (isMove.x) {  //x方向不使用惯性滑动
        	speed=1;
        }
		//console.log(speed)
		//判断滑动是否超出边界
		var now = cssTooL(scroll[0],translate[dir]);

		now = now*speed;  //做手指松开后的惯性移动
		
		if (isMove.y) { 
			if(now < max[dir]){
				now =  max[dir];
			} else if(now > 0){  //上边界
				now = 0;
			}
		}

		//$(this).animate({"transform": "translateY(0px)"}, 500) //animate方法不支持transform属性

		scroll[0].style.transition = ".3s";
		cssTooL(scroll[0], translate[dir], now);

		//滑动完后，重置滑屏的方向
		isMove = {
			x: false,
			y: false
		}
		isFrist = true;  //重置未第一次滑动条件为真

		init.end&&init.end(lastDir); 
	})
}

/* 
    如果移动端使用鼠标事件会有300ms的延迟，另外如果使用了阻止默认事件，所有的鼠标事件都会失效，在真机上才能测试出效果
	tap 移动端点击事件 
	el  点击的元素
	fn  事件函数
*/
function tap(el,fn){
	var startPoint = {};
	el.addEventListener('touchstart', function(e) {
		var touch = e.changedTouches[0];
		startPoint = {
			x: touch.pageX,
			y: touch.pageY
		}
	});
	el.addEventListener('touchend', function(e) {
		var touch = e.changedTouches[0];
		var nowPoint = {
			x: touch.pageX,
			y: touch.pageY
		};
		var dis = {
			x: Math.abs(nowPoint.x - startPoint.x),
			y: Math.abs(nowPoint.y - startPoint.y)
		} 
		if(dis.x < 5 && dis.y < 5){
			fn.call(el,e); //修改this的指向为发生点击事件的元素el
		}
	});
}
//获取所有置顶的消息，点击取置顶的时候，把该消息放在所有置顶消息的最后一条下面
function getFirst(){
	var list = document.querySelector('#list');
	var active = list.querySelectorAll('li.active');
	if(active.length > 0){
		return active[active.length-1].nextElementSibling;
	}
	return list.children[0];  //如果没有置顶的消息则还是放在第一条
}
//获取和设置元素数值类样式的值函数，如width、left等，注意transform的必须先设置才能获取
function cssTooL(el,attr,val){
	var transform = ['rotate','rotateX','rotateY','rotateZ','scale','scaleX','scaleY','skewX','skewY','translateX','translateY','translateZ'];
	for(var i = 0; i < transform.length; i++){
		if(attr == transform[i]){
			return setTransform(el,attr,val);
		}
	}
	if(arguments.length == 2){
		if(el.currentStyle){
			val = el.currentStyle[attr];
		} else {
			val = getComputedStyle(el)[attr];
		}
		if(attr == "opacity"){
			return val*100;
		}
		return parseFloat(val);
	} else {
		if(attr == "opacity"){
			el.style.opacity = val/100;
			el.style.filter = "alpha(opacity= "+val+")";
		} else if(attr == "zIndex"){
			el.style[attr] = Math.round(val);
		}else {
			el.style[attr] = val + "px";
		}
	}
}
function setTransform(el,attr,val){
	if(!el.transform){
		el.transform = {};
		//如果元素没有这个自定义属性我们就创建一下，格式是个对象
	}
	if(typeof val == "undefined"){
		return el.transform[attr];
	} else {
		el.transform[attr] = val;
		var value = "";
		for(var s in el.transform){
			//console.log(s,el.transform[s]);
			switch(s){
				case "rotate":
				case "rotateX":
				case "rotateY":
				case "rotateZ":
				case "skewX":
				case "skewY":
					value += (s+"("+el.transform[s]+"deg) ");	
					break;
				case "translateX":
				case "translateY":
				case "translateZ":	
					value += (s+"("+el.transform[s]+"px) ");	
					break;
				case "scale":
				case "scaleX":
				case "scaleY":	
					value += (s+"("+el.transform[s]/100+") ");	
					break;		
			}
		}
		el.style.WebkitTransform = value;
		el.style.MozTransform = value;
		el.style.msTransform = value;
		el.style.transform = value;
	}
}

//时间转换函数，类似微博时间功能，刚刚发布的则显示为刚刚
function getDate(time){
	var now = new Date();
	var last = new Date(time);
	var nowMin =  now.getTime()/1000/60;
	var lastMin =  time/1000/60;
	var day = 1000*60*60*24;
	var lastDate = last.getFullYear() + "-" + toDB(last.getMonth()) + "-" + toDB(last.getDate());
	var weebText = ["日","一","二","三","四","五","六"];
	if(now.getFullYear() > last.getFullYear()){
		return lastDate;
	} 
	if(now.getMonth() > last.getMonth()){
		return lastDate;
	}
	if(now.getTime() - time > day*(now.getDay()+1)){
		return lastDate;
	}
	if(now.getDate() - 1 > last.getDate()){
		return "星期" + weebText[last.getDay()];
	}
	if(now.getDate() > last.getDate()){
		return "昨天";
	} 
	if(nowMin - lastMin < 1){
		return "刚刚";
	}
	if(nowMin - lastMin < 60){
		return Math.floor(nowMin - lastMin) + "分钟前";
	}
	return toDB(last.getHours()) + ":" + toDB(last.getMinutes()); 
}
function toDB(nub){
	return nub < 10?"0"+nub:""+nub;
}
