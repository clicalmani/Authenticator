<?php
namespace Clicalmani\Authenticator;

use Clicalmani\Database\DB;

abstract class Authenticator implements \ArrayAccess 
{
	/**
	 * Max session inactivity time in minutes
	 * 
	 * @var int
	 */
	protected int $turnarround = 0;

	/**
	 * Authenticated user
	 * 
	 * @var mixed
	 */
	protected $user;

	/**
	 * JWT Object
	 * 
	 * @var \Clicalmani\Flesco\Auth\JWT
	 */
	private $jwt;
	 
	/**
	 * Constructor
	 *
	 * @param mixed $user_id 
	 */
	public function __construct(protected $user_id)
	{ 
		/**
		 * Set user to user model
		 */
		$this->user  = \App\Models\User::find($this->user_id);

		$this->jwt = new JWT($this->user_id, $this->turnarround ? $this->turnarround/(60*24): 1);
	}
	
	/**
	 * @override
	 * 
	 * @param string $attribute
	 * @return mixed
	 */
	public function __get(string $attribute)
	{
		return $this->user->{$attribute};
	}

	/**
	 * Authenticate user
	 * 
	 * @return void
	 */
	public function authenticate() : void
	{
		DB::table('auth_access')->insertOrUpdate([
			['user_id' => $this->user_id, 'token' => $this->jwt->generateToken()]
		]);
	}

	/**
	 * Is user online
	 * 
	 * @return bool
	 */
	public function isOnline() : bool
	{
		$auth = DB::table('auth_access')->where('user_id = :user_id', 'AND', ['user_id' => $this->user_id])->get('token')->first();
		if ($auth) return $this->jwt->verifyToken($auth['token']);

		return false;
	}
}
