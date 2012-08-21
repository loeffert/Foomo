<?php

/*
 * This file is part of the foomo Opensource Framework.
 * 
 * The foomo Opensource Framework is free software: you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General Public License as
 * published  by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * The foomo Opensource Framework is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License along with
 * the foomo Opensource Framework. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Foomo\Jobs;
 
/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author Jan Halfar jan@bestbytes.com
 */
class JobStatus
{
	const STATUS_RUNNING = 'running';
	const STATUS_NOT_RUNNING = 'not running';	
	const ERROR_DIED = 'died';
	const ERROR_TIMEOUT = 'timeout';
	/**
	 * one of self::STATUS_...
	 * @var string
	 */
	public $status;
	/**
	 * one of self::ERROR_...
	 * @var string
	 */
	public $error;
}