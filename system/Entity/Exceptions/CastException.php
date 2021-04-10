<?php

/**
 * This file is part of the CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CodeIgniter\Entity\Exceptions;

use CodeIgniter\Exceptions\FrameworkException;

class CastException extends FrameworkException
{
	public static function forInvalidCastMethod()
	{
		return new static(lang('Cast.invalidCastMethod'));
	}

	public static function forInvalidInterface($class)
	{
		return new static(lang('Cast.BaseCastMissing', [$class]));
	}

	public static function forInvalidJsonFormat(int $error)
	{
		switch($error)
		{
			case JSON_ERROR_DEPTH:
				return new static(lang('Cast.jsonErrorDepth'));
			case JSON_ERROR_STATE_MISMATCH:
				return new static(lang('Cast.jsonErrorStateMismatch'));
			case JSON_ERROR_CTRL_CHAR:
				return new static(lang('Cast.jsonErrorCtrlChar'));
			case JSON_ERROR_SYNTAX:
				return new static(lang('Cast.jsonErrorSyntax'));
			case JSON_ERROR_UTF8:
				return new static(lang('Cast.jsonErrorUtf8'));
			default:
				return new static(lang('Cast.jsonErrorUnknown'));
		}
	}

	public static function forInvalidTimestamp()
	{
		return new static(lang('Cast.invalidTimestamp'));
	}
}
