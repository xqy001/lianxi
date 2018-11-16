<?php
$redis=new Redis;
$redis->connect('127.0.0.1',6379);
$city=$_GET['city'];		
if($redis->exists($city))
{
	$str=$redis->get($city);
	echo $str;		
}
else
{
	$key='dafee26d83214794a37155037c9c69b2';
	$url="https://free-api.heweather.com/s6/weather/forecast?location=$city&key=$key";
	$str=curl_get($url);
	$data=json_decode($str,true);
	$data=$data['HeWeather6'][0]['daily_forecast'];		
	$pdo=new PDO('mysql:host=localhost;dbname=test','root','root');
	foreach ($data as $key => $value) 
	{
		$date=$value['date'];
		$maxTemp=$value['tmp_max'];		
		$minTemp=$value['tmp_min'];		
		$sql="insert into weather(city,date,maxtemp,mintemp) values('$city','$date','$maxTemp','$minTemp')";
		$pdo->exec($sql);
	}	
	$str=json_encode($data);		
	$redis->set($city,$str);
	echo $str;
}

function curl_get($url)
{
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	$str=curl_exec($ch);
	curl_close($ch);
	return $str;
}