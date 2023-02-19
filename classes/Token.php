<?php
/**
* 2020 C. Hodor - OS Private Community
*/

/**
 * Genereaza si verifica un token.
 */
class Token
{
	public static function generate()
	{
		echo Session::put(Session::setup('session/token_name'), hash_hmac('sha256', uniqid('', true), Tools::hash(Keys::get('cookie'))));
	}

	public static function check($token)
	{
		$tokenName = Session::setup('session/token_name');

		if (Session::exists($tokenName) && $token === Session::get($tokenName))
		{
			 Session::delete($tokenName);
			 return true;
		}

		return false;
	}
}