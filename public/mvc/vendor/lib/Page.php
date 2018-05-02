<?php

	/**
	* 分页类
	*/
    namespace vendor;
	class Page
	{
		protected $number;  	     //每页显示的记录数 
		protected $total;  		    //记录总数 
		protected $page;  		   //当前页
		protected $totalPage;     //总页数 
		protected $url; 		 //链接地址
		
		function __construct($number, $total)
		{
			$this->number = $number;
			$this->total  = $total;
			$this->totalPage  = ceil($this->total/$this->number);
			$this->page  = $this->getPage();  //当前页数
			$this->url  = $this->getUrl();  //得到url
		}

		//获取当前页数
		protected function getPage()
		{
			if (empty($_GET['page'])) {
				$page = 1;
			} elseif($_GET['page'] > $this->totalPage) {
				$page = $this->totalPage;
			} elseif($_GET['page'] < 1) {
				$page = 1;
			} else {
				$page = $_GET['page'];
			}

			return $page;
			
		}

		//获取链接地址
		protected function getUrl()
		{
			//$scheme 	= $_SERVER['REQUEST_SCHEME'];  //协议名，如http、FTP
			//$host 		= $_SERVER['SERVER_NAME'];  //主机名，如www.liping.com
			//$port 	    = $_SERVER['SERVER_PORT'];  //端口号
			$uri    	= $_SERVER['REQUEST_URI'];  //路径及请求字符串,如/case/index.php?name=liping&page=18

			//拼接url，如果URI中已有page参数则先删除
			$uriArray = parse_url($uri); //转成数组，path(/case/index.php)及请求参数query部分(name=liping&page=18)
			//var_dump($uriArray);
			$path = $uriArray['path'];
			if (!empty($uriArray['query'])) {
				parse_str($uriArray['query'], $arr);  //将请求参数转成关联数组，如['name'=>'liping', 'page'=>18]
				unset($arr['page']);  //删除page参数
				$query = http_build_query($arr);  //重新将数组拼接成请求参数形式的字符串
				if ($query != '') {
					$path = $path.'?'.$query;  //将请求字符串拼接到路径后面
				}
			}
			return $path;
			
		}

		//分页地址
		public function allUrl()
		{
			return [
				'frist' => $this->first(),
				'prev' => $this->prev(),
				'next' => $this->next(),
				'end' => $this->end(),
			];
		}

		//第一页地址
		public function first()
		{
			return $this->setUrl('page=1');
		}

		//上一页地址
		public function prev()
		{
			//根据当前页数判断上一页
			if (($this->page-1) < 1) {
				$page = 1;
			} else {
				$page = $this->page-1;
			}
			return $this->setUrl('page='.$page);
		}

		//下一页地址
		public function next()
		{
			//根据当前页数判断下一页
			if (($this->page+1) > $this->totalPage) {
				$page = $this->totalPage;
			} else {
				$page = $this->page+1;
			}
			
			return $this->setUrl('page='.$page);
		}

		//最后一页地址
		public function end()
		{
			return $this->setUrl('page='.$this->totalPage);
		}

		//设置url地址
		protected function setUrl($page)
		{
			if (strstr($this->url, '?')) {  //判断url中是否包含请求参数
				$url = $this->url.'&'.$page;
			} else {
				$url = $this->url.'?'.$page;
			}
			return $url;
		}

		//limit接口
		public function limit()
		{
			//形式如limit 0,5
			$offset = ($this->page-1)*$this->number;
			return $offset.','.$this->number;
		}

	}
?>
