<?php namespace CodeIgniter\Session\Handlers;

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014-2018 British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package    CodeIgniter
 * @author     CodeIgniter Dev Team
 * @copyright  2014-2018 British Columbia Institute of Technology (https://bcit.ca/)
 * @license    https://opensource.org/licenses/MIT	MIT License
 * @link       https://codeigniter.com
 * @since      Version 3.0.0
 * @filesource
 */

use CodeIgniter\Config\BaseConfig;
use Psr\Log\LoggerAwareTrait;

/**
 * Base class for session handling
 */
abstract class BaseHandler implements \SessionHandlerInterface
{

	use LoggerAwareTrait;

	/**
	 * The Data fingerprint.
	 *
	 * @var bool
	 */
	protected $fingerprint;

	/**
	 * Lock placeholder.
	 *
	 * @var mixed
	 */
	protected $lock = false;

	/**
	 * Cookie prefix
	 *
	 * @var string
	 */
	protected $cookiePrefix = '';

	/**
	 * Cookie domain
	 *
	 * @var string
	 */
	protected $cookieDomain = '';

	/**
	 * Cookie path
	 *
	 * @var string
	 */
	protected $cookiePath = '/';

	/**
	 * Cookie secure?
	 *
	 * @var bool
	 */
	protected $cookieSecure = false;

	/**
	 * Cookie name to use
	 *
	 * @var string|null
	 */
	protected $cookieName;

	/**
	 * Match IP addresses for cookies?
	 *
	 * @var bool
	 */
	protected $matchIP = false;

	/**
	 * Current session ID
	 *
	 * @var string|null
	 */
	protected $sessionID;

	/**
	 * The 'save path' for the session
	 * varies between
	 *
	 * @var mixed
	 */
	protected $savePath;

	/**
	 * @var string
	 */
	protected $ipAddress;

	//--------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @param BaseConfig $config
	 * @param string $ipAddress
	 */
	public function __construct(BaseConfig $config, string $ipAddress)
	{
		// TODO fmertins: these props cookie* and session* don't exist in BaseConfig class, should we create them there?
		$this->cookiePrefix = $config->cookiePrefix;
		$this->cookieDomain = $config->cookieDomain;
		$this->cookiePath   = $config->cookiePath;
		$this->cookieSecure = $config->cookieSecure;
		$this->cookieName   = $config->sessionCookieName;
		$this->matchIP      = $config->sessionMatchIP;
		$this->savePath     = $config->sessionSavePath;
		$this->ipAddress    = $ipAddress;
	}

	//--------------------------------------------------------------------

	/**
	 * Internal method to force removal of a cookie by the client
	 * when session_destroy() is called.
	 *
	 * @return bool
	 */
	protected function destroyCookie(): bool
	{
		return setcookie(
				$this->cookieName, null, 1, $this->cookiePath, $this->cookieDomain, $this->cookieSecure, true
		);
	}

	//--------------------------------------------------------------------

	/**
	 * A dummy method allowing drivers with no locking functionality
	 * (databases other than PostgreSQL and MySQL) to act as if they
	 * do acquire a lock.
	 *
	 * @param string $sessionID Dummy, isn't used here
	 *
	 * @return bool
	 */
	protected function lockSession(string $sessionID): bool
	{
		$this->lock = true;
		return true;
	}

	//--------------------------------------------------------------------

	/**
	 * Releases the lock, if any.
	 *
	 * @return bool
	 */
	protected function releaseLock(): bool
	{
		$this->lock = false;
		return true;
	}

	//--------------------------------------------------------------------

	/**
	 * Fail
	 *
	 * Drivers other than the 'files' one don't (need to) use the
	 * session.save_path INI setting, but that leads to confusing
	 * error messages emitted by PHP when open() or write() fail,
	 * as the message contains session.save_path ...
	 * To work around the problem, the drivers will call this method
	 * so that the INI is set just in time for the error message to
	 * be properly generated.
	 *
	 * @return bool
	 */
	protected function fail(): bool
	{
		ini_set('session.save_path', $this->savePath);
		return false;
	}
}
