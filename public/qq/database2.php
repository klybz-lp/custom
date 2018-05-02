<?php
class database
{
  private $hostname;
  private $user;
  private $pass;
  private $dbname;
  private $linkflag;
  private $charset;

  function __construct()
  {
    $this->hostname="localhost";
    $this->user="root";
    $this->pass="root";
    $this->dbname="yaoyue";
    $this->charset="utf8";  //gb2312 GBK utf8
    $this->linkflag=mysql_connect($this->hostname,$this->user,$this->pass);
    mysql_select_db($this->dbname,$this->linkflag) or die($this->error());
    mysql_query("set names ".$this->charset);
  }

  function __set($property_name,$value)
  {
    return $this->$property_name=$value;
  }

  function __get($property_name)
  {
    if(isset($this->$property_name))
    {
      return $this->$property_name;
    }
    else return null;
  }

  function __call($function_name, $args)
  {
    echo "<br><font color=#ff0000>你所调用的方法 $function_name 不存在</font><br>\n";
  }

  function query($sql)
  {
    $res=mysql_query($sql) or die($this->error());
    return $res;
  }
  //返回SQL语句查询的所有结果的当前指针结果，需在外边遍历 
  function fetch_array($res)
  {
    return mysql_fetch_array($res);
  }
  //返回SQL语句查询的所有结果 
  function fetch_array2($res)
  {
    $result = array();
    while ($row = mysql_fetch_array($res)) {
       $result[] = $row;
    }
    return $result;
  }

  function fetch_object($res)
  {
    return mysql_fetch_object($res);
  }

  function fetch_obj_arr($sql)
  {
    $obj_arr=array();
    $res=$this->query($sql);
    while($row=mysql_fetch_object($res))
    {
      $obj_arr[]=$row;
    }
    return $obj_arr;
  }

  function error()
  {
    if($this->linkflag)
    {
      return mysql_error($this->linkflag);
    }
    else    return mysql_error();
  }

  function errno()
  {
    if($this->linkflag)
    {
      return mysql_errno($this->linkflag);
    }
    else    return mysql_errno();
  }

  function affected_rows()
  {
    return mysql_affected_rows($this->linkflag);
  }

  function num_rows($sql)
  {
    $res=$this->execute($sql);
    return mysql_num_rows($res);
  }

  function num_fields($res)
  {
    return mysql_num_fields($res);
  }

  function insert_id()
  {
    $previous_id=mysql_insert_id($this->linkflag);
    return $previous_id;
  }

  function result($res,$row,$field=null)
  {
    if($field===null)
    {
      $res=mysql_result($res,$row);
    }
    else    $res=mysql_result($res,$row,$field);
    return $res;
  }

  function version()
  {
    return mysql_get_server_info($this->linkflag);
  }

  function data_seek($res,$rowNum)
  {
    return mysql_data_seek($res,$rowNum);
  }

  function __destruct()
  {
    //mysql_close($this->linkflag);
  }

}

?>
