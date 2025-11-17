<?php
require('JWT.php');
class impliment_jwt
{
	
	/**
	-------------------------------------------------
	   The function generate token
	-------------------------------------------------
	**/
	PRIVATE $key = "subscribe_my_channel";
	public function GenerateToken($data)
	{
		$jwt = JWT::encode($data,$this->key);
		return $jwt;
	}
	/**
	-------------------------------------------------------
	This function decode the token
	-------------------------------------------------------
	**/
	public function DecodeToken($token)
	{
		$decodeData = JWT::decode($token,$this->key,array('HS256'));
		return $decodeData;
	}
}
?>