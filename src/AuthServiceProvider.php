<?php
namespace Clicalmani\Flesco\Providers;

abstract class AuthServiceProvider extends ServiceProvider
{
	/**
	 * User authenticator service
	 * 
	 * @return mixed
	 */
    public static function userAuthenticator() : mixed
	{
		return @ static::$kernel['auth']['user'];
	}
}
