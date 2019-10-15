$(function() {
        // 局部调用
	$.fn.manhua_msgTips = function(options) {
		var defaults = {
                    Event : "click",			//响应的事件
                    timeOut : 3000,				//提示层显示的时间
                    msg : "消息提示 - www.17sucai.com",			//显示的消息
                    speed : 300,				//滑动速度
                    type : "success"			//提示类型（1、success 2、error 3、warning）
		};
		var options = $.extend(defaults,options);
		var bid = parseInt(Math.random()*100000);
		$("body").prepend('<div id="tip_container'+bid+'" class="container tip_container"><div id="tip'+bid+'" class="mtip"><i class="micon"></i><span id="tsc'+bid+'"></span><i id="mclose'+bid+'" class="mclose"></i></div></div>');
		var $this = $(this);
		var $tip_container = $("#tip_container"+bid);
		var $tip = $("#tip"+bid);
		var $tipSpan = $("#tsc"+bid);
		var $colse = $("#mclose"+bid);		
		//先清楚定时器
		clearTimeout(window.timer);
		//主体元素绑定事件
		$this.off().on(options.Event,function(){
                    $tip.attr("class", options.type).addClass("mtip");	
                    $tipSpan.html(options.msg);			
                    $tip_container.slideDown(options.speed);
                    //提示层隐藏定时器
                    window.timer = setTimeout(function (){
                            $tip_container.slideUp(options.speed);
                    }, options.timeOut);
		});	
		//鼠标移到提示层时清除定时器
		$tip_container.on("mouseover",function() {
                    clearTimeout(window.timer);
		});
		
		//鼠标移出提示层时启动定时器
		$tip_container.on("mouseout",function() {
                    window.timer = setTimeout(function (){
                            $tip_container.slideUp(options.speed);
                    }, options.timeOut);
		});
	
		//关闭按钮绑定事件
		$colse.on("click",function() {
                    $tip_container.slideUp(options.speed);
		});
	};
        
        // 全局调用
       $.extend({
            AngelaAutoTips: function(options) {
                var defaults = {
                    timeOut : 3000,				//提示层显示的时间
                    msg : "消息提示 - www.17sucai.com",			//显示的消息
                    speed : 300,				//滑动速度
                    type : "success"			//提示类型（1、success 2、error 3、warning）
		};
		var options = $.extend(defaults,options); // 合并对象
                
                // 构建页面显示对象
                var bid = parseInt(Math.random()*100000);
                $("body").prepend('<div id="tip_container'+bid+'" class="container tip_container"><div id="tip'+bid+'" class="mtip"><i class="micon"></i><span id="tsc'+bid+'"></span><i id="mclose'+bid+'" class="mclose"></i></div></div>');
                var $tip_container = $("#tip_container"+bid);
                var $tip = $("#tip"+bid);
                var $tipSpan = $("#tsc"+bid);
                var $colse = $("#mclose"+bid);
                $tip.attr("class", options.type).addClass("mtip");	
                $tipSpan.html(options.msg);
                
                //先清楚定时器
		clearTimeout(window.timer);
                
                // 显示提示
                $tip_container.slideDown(options.speed);
                
                //提示层隐藏定时器
                window.timer = setTimeout(function (){
                        $tip_container.slideUp(options.speed);
                }, options.timeOut);
                
                //关闭按钮绑定事件
		$colse.on("click",function() {
                    $tip_container.slideUp(options.speed);
		});
            }
        });
        
});
/*www.sucaijiayuan.com*/