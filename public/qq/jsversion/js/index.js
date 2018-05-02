/********
ajax获取数据，实际项目建议使用jQuery的ajax获取
********/ 
var url = "http://www.liping768.com/qq/jsversion/info.php";
creatLi();
/* 获取数据，生成内容，参数isLoad是布尔类型 */
function creatLi(isLoad){  //isLoad用来判断是打开页面时加载数据，还是后面的下拉时生成数据，后者则需要给load修改一些加载的提示
	var list = document.querySelector('#list');
	var first = getFirst();  //下拉加载添加的消息放在已经置顶了的消息的下面
	ajax("get",url,"",function(data){
		//console.log(data);
		data = JSON.parse(data);
		for(var i = 0; i < data.length; i++){
			//console.log(typeof(data[i].date_time))
			var li = document.createElement('li');
			//工具函数getDate把时间戳转换为中国时间格式：2018-03-03，刚刚发的则显示为刚刚，类似微博显示的时间
			li.innerHTML = '<div class="swiper"><div class="view"><span class="avatar" style="background-image:url('+data[i].avatar+');"></span><div class="info"><h3>'+data[i].username+'</h3><p>'+data[i].new_message+'</p></div><aside class="aside"><time>'+getDate(parseFloat(data[i].date_time))+'</time> <mark style="display:'+(data[i].message_number > 1?'block':'none')+'">'+data[i].message_number+'</mark></aside></div><nav class="btns"><a href="javascript:;">置顶</a> <a href="javascript:;">'+(data[i].message_number > 1?'标记已读':'标记未读')+'</a> <a href="javascript:;">删除</a></nav></div>';
			list.insertBefore(li,first);  //insertBefore把数据添加在前面，appendchild(li)则是加到后面
			setEv(li);
		}
		//如果是下拉加载生成的内容则更改load动画状态
		if(isLoad){
			var swiper = document.querySelector('#swiper');
			var load = document.querySelector('#load');
			var loadImg = load.querySelector('.loadImg');
			var loadImg2 = load.querySelector('.loadImg2');
			var loadText = load.querySelector('.loadText');
			loadImg2.style.display = "none";
			loadText.innerHTML = "刷新完成";
			setTimeout(function(){  //还原load的初始状态
				css(swiper,"translateY",0);
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
	});
}

/* 给生成的li添加左右滑屏及按钮点击事件 */

function setEv(li){
	var swiper = li.querySelector('.swiper');
	var max = li.clientWidth - swiper.offsetWidth;  //左右滑动的最大距离，避免超出边界
	var list = document.querySelector('#list');
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
		css(swiper,"translateX",0);
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
		css(swiper,"translateX",0);
	});
	tap(btn[2],function(){
		list.removeChild(li);
		var main = document.querySelector('#main');
		var swiper = document.querySelector('#swiper');
		var max = main.clientHeight - swiper.offsetHeight;
		var now = css(swiper,"translateY");
		console.log(max,now);
		if(now < max){  //当滑到最下面删除了消息出现白屏时，滑让滑屏元素动最底部
			cancelAnimationFrame(swiper.timer);
			swiper.style.transition = ".3s";
			css(swiper,"translateY",max);
		}
	});
	/*********
	左右滑屏
	 *********/
	mScroll({
		el:li,
		dir:"x",
		start: function(){  //
			var swipers = list.querySelectorAll('.swiper');
			for(var i = 0; i < swipers.length; i++){  //如果滑动元素滑出显示了按钮，而没有右滑还原，当滑动任意元素把上一个滑块返回初始状态
				if(swipers[i] != swiper){  //排除掉当前操作的滑屏元素
					var now = css(swipers[i],"translateX");
					if(now < 0) {
						swipers[i].style.transition = ".3s";
						css(swipers[i],"translateX",0);
					}
				}

			}
			swiper.style.transition = "none";
		},
		move: function(){  //控制左右滑动的最大距离，避免超出边界
			var now = css(swiper,"translateX");
			if(now > 0){
				now = 0;
			} else if(now < max){
				now = max;
			}
			css(swiper,"translateX",now);
		},
		end: function(dir){//dir 判断最后一次滑动的方向，做松开手指滑动到边界的动画效果
			cancelAnimationFrame(swiper.timer); //解决松开不滑动的bug，由于touchend中的startmove动画导致的，松开手指不能滑动到边界
			swiper.style.transition = ".3s";
			var now = css(swiper,"translateX");
			if(now == 0){  //防止手指抖动触发了点击事件
				return;
			}
			if(dir > 0){ //如果x方向，并且lastDir为正值，则是右滑
				css(swiper,"translateX",0);
			} else if(dir < 0){
				css(swiper,"translateX",max);
			}
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
//阻止浏览器默认行为，如浏览器默认的回弹行为，如拖到到最顶部时会显示网页由某某提供等
document.addEventListener('touchstart', function(event) {
    // 判断默认行为是否可以被禁用
    if (event.cancelable) {
        // 判断默认行为是否已经被禁用
        if (!event.defaultPrevented) {
            event.preventDefault();
        }
    }
}, false);

/*********
上下滑屏，需添加当向上滑动到translateY为0时，添加滑动阻力，让网上不超出边界太远,另外加滑屏的缓冲动画，即滑动的距离越大则滚动的速度越快
 *********/
(function(){
	var main = document.querySelector('#main');
	var swiper = document.querySelector('#swiper');
	var load = document.querySelector('#load');
	var loadImg = load.querySelector('.loadImg');
	var loadImg2 = load.querySelector('.loadImg2');
	var loadText = load.querySelector('.loadText');
	var loadH = load.offsetHeight;
	// css(main,"translateY",100)
	// console.log(css(main,"translateY"));
	loadImg.style.transition = ".3s";
	mScroll({
		el: main,
		dir:'y',
		start:function(){
			swiper.style.transition = "none";  //在touchmove时如果有transition过度效果是很诡异的(滑屏时与晃的感觉)，因为touchend时添加了，所以需在touchstart时清除掉
		},
		move: function(){  //获取手指移动时的位置，当超出消息列表的高度大于等于div#load的高度时，箭头变为向上，更改文字的内容
			var now = css(swiper,"translateY");
			if(now > loadH){
				css(loadImg,"rotate",-180);  //箭头变为向上
				loadText.innerHTML = "释放立即刷新";
			} else {  //默认load效果
				css(loadImg,"rotate",0);
				loadText.innerHTML = "下拉刷新";
			}
		},
		end: function(){  //下拉刷新动态效果的变化
			var now = css(swiper,"translateY");
			if(now > loadH){
				cancelAnimationFrame(swiper.timer);
				swiper.style.transition = ".3s";
				css(swiper,"translateY",loadH);  //松开后translateY先固定到div#load的高度，显示正在刷新，等数据加载完后在
				loadImg.style.display = "none";
				loadImg2.style.display = "block";
				loadText.innerHTML = "正在刷新";
				//在transition动画结束之后调用end方法来加载数据，transitionend与WebkitTransitionEnd是用来监听transition结束触发的
				swiper.addEventListener('WebkitTransitionEnd', end); 
				swiper.addEventListener('transitionend', end);
				function end(){
					swiper.removeEventListener('WebkitTransitionEnd', end);
					swiper.removeEventListener('transitionend', end);
					creatLi(true);
				}
			}
		}
	})
})();
/*********
滑屏函数
参数init:一个对象
init{
	el:element  滑屏元素
	dir:'x'|'y' 滑屏方向 
	start:fn,(手指按下的回调)
	move:fn,(手指移动的回调),
	end:fn(手指抬起的回调)
}
 *********/
function mScroll(init){
	var swiper = init.el.children[0]; //实际发生位移的元素
	var startPoint = {};  //手指按下时的坐标
	var startEl = {};  //手指按下时实际发生位移的元素的位置
	var lastPoint = {};  ////保存上一次手指触摸的坐标，如果跟这次(即开始移动时)的坐标一致则表示并没有发生touchmove事件，解决安卓手机大面积触屏会触发touchmove的问题 
	var dir = init.dir; //滑动的方向
	var lastDir = 0;  //记录touchmove过程中最后一次滑动的方向(判断是左、右、上、还是下滑动)，用来做手指松开时滑动到边界的动画效果
	//可滑动的最大距离
	max = {
		x: parseInt(css(init.el,"width") - css(swiper,"width")),
		y: parseInt(css(init.el,"height") - css(swiper,"height"))
	}
	//根据滑动的风险来设置对应的样式
	var translate = {
		x: "translateX",
		y: "translateY"
	}
	//判断滑动的方向，避免左右滑动与上下滑动发生冲突，默认都为假
	var isMove = {
		x: false,
		y: false
	};
	var isFrist = true; //记录第一次滑动，只记录第一次滑动时的坐标，解决用户故意左右来回滑动时，因为x方向的差值可能为0小于y方向的差值而触发了上下滑动的条件 
	//var startLocation = $('#swiper').css("transform").replace(/[^0-9\-,]/g,'').split(','); //jquery获取transform的值，第四个值(startLocation[4])是translateX，第五个值是translateY
	//css(swiper,"translateX",0);  //封装的工具函数只能获取数字类样式，而且transform必需先设置才能获取
	//css(swiper,"translateY",0); //设置值css(swiper,"translateY",100)
	css(swiper,translate[dir],0); 
	init.el.addEventListener("touchstart", function(e) {
		init.start&&init.start();  //如果调用时声明了手指按下的回调，则调用start方法
		var touch = e.changedTouches[0];
		//console.log(touch);
		startPoint = {
			x: Math.round(touch.pageX),
			y: Math.round(touch.pageY)
		}
		//同步手指按下时的坐标为上一次触摸的坐标，跟touchmove开始时的坐标对比，如果两者一致则是安卓机大面积触屏时(touchstart)会触发touchmove的bug
		lastPoint= {
			x: startPoint.x,
			y: startPoint.y
		};
		//手指滑动时滑动元素的位置
		startEl = {
			x: css(swiper,"translateX"),
			y: css(swiper,"translateY")
		}
		//可滑动的最大距离，因为数据是通过ajax加载的，所有height值是变化的，每次手指开始滑动需要重新计算
		max = {
			x: parseInt(css(init.el,"width") - css(swiper,"width")),
			y: parseInt(css(init.el,"height") - css(swiper,"height"))
		}
		//console.log(startEl);
		lastDir = 0;  //最后滑动的方向为0 则表示哪个方向都不动
    });

    init.el.addEventListener("touchmove", function(e) {
    	//e.stopPropagation();
    	//e.preventDefault();
        var touch = e.changedTouches[0];
		//手指开始移动时当前坐标，用来判断开始移动时的坐标
		nowPoint = {
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
		/* 如果y方向的差值大于x方向的差值，则表示是上下滑动，解决上下滑屏与左右滑屏同时触发的问题，这个判断只在手指按下时，第一次move时才会执行 */
		if(Math.abs(dis.x) - Math.abs(dis.y) > 2 && isFrist){  //水平方向滑动
			isMove.x = true;
			isFrist = false; //把第一次滑动的条件设为假，避免来回滑动时产生滑屏方向的错误
		} else if(Math.abs(dis.y) - Math.abs(dis.x) > 2 && isFrist){  //垂直方向滑动
			isMove.y = true;
			isFrist = false;
		}
		lastDir = nowPoint.x - lastPoint.x;  //获取最后滑动的距离，如果x方向，并且lastDir为正值，则是右滑
		//滑屏元素的当前位置=滑屏元素的起始位置+手指滑动的距离
		var target = {};
		target[dir] = dis[dir] + startEl[dir];
		isMove[dir]&&css(swiper,translate[dir],target[dir]);
		//console.log(dis);
		init.move&&init.move();  //如果调用时声明了手指滑动的回调，则调用move方法
		lastPoint.x = nowPoint.x;  ////滑动后同步为上次开始触摸的坐标
		lastPoint.y = nowPoint.y;
    });

    init.el.addEventListener("touchend", function(e) {
		var now = css(swiper,translate[dir]);  //手指松开时滑动元素的translate值，控制滑动超出边界问题
		if(now < max[dir]){
			now =  max[dir];
		} else if(now > 0){
			now = 0;
		}
        //使用动画当滑动的距离超出边界时回到边界
		var target = {};
		target[translate[dir]] = now;
		startMove({
			el: swiper,
			target:target,
			type: "easeOut",
			time: 300
		});
		
		//滑动完后，充值滑屏的方向都为false
		isMove = {
			x: false,
			y: false
		}
		isFrist = true;  //重置未第一次滑动条件为真
		init.end&&init.end(lastDir);  //如果调用时声明了手指松开的回调，则调用end方法，传入参数lastDir判断滑动的方向

    });

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
