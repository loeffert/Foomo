<?php

/*
 * This file is part of the foomo Opensource Framework.
 *
 * The foomo Opensource Framework is free software: you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General Public License as
 * published  by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * The foomo Opensource Framework is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * the foomo Opensource Framework. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Foomo;

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author jan <jan@bestbytes.de>
 */
class MailerTest extends \PHPUnit_Framework_TestCase {
	const TEST_SUB_DOMAIN = 'mailTest';
	/**
	 * my mailer
	 *
	 * @var Foomo\Mailer;
	 */
	protected $mailer;
	private $mailEnabled;
	private $mailLogLast;
	public function setUp()
	{
		\PHPUnit_Framework_Error_Notice::$enabled = false;
		$this->mailer = new Mailer;
		$this->mailEnabled = Mailer::$enabled;
		$this->mailLogLast = Mailer::$logLast;
		Mailer::$enabled = false;
		Mailer::$logLast = true;
		
	}
	public function testSmtpConf()
	{
		// create a smtp conf and give it a test
		$smtpConfig = new \Foomo\Config\Smtp();
		$smtpConfig->host = $host = '127.0.0.1';
		$smtpConfig->port = $port = 587;
		$smtpConfig->username = $username = 'testname';
		$smtpConfig->password = $password = 'testpassword';
		/* @var $smtpConfig \Foomo\Config\Smtp */
		Config::setConf($smtpConfig, \Foomo\Module::NAME, self::TEST_SUB_DOMAIN);
		$config = Config::getConf(\Foomo\Module::NAME, \Foomo\Config\Smtp::NAME, self::TEST_SUB_DOMAIN);
		$this->assertEquals(
			array(
				'host' => $host,
				'port' => $port,
				'username' => $username,
				'password' => $password,
				'auth' => true
			),
			$smtpConfig->toPearMailerFactoryArray()
		);

	}
	public function tearDown()
	{
		Mailer::$enabled = $this->mailEnabled;
		Mailer::$logLast = $this->mailLogLast;
		Config::removeConf(\Foomo\Module::NAME, \Foomo\Config\Smtp::NAME, self::TEST_SUB_DOMAIN);
	}
	public function testMail()
	{
		$success = $this->mailer->sendMail('dev@null', $subject = 'testSubject', $plainBody = 'plainBody');
		$this->assertTrue($success);
		$this->assertEquals($subject, Mailer::$lastSubject, 'wrong subject');
		$this->assertEquals($plainBody, Mailer::$lastPlain, 'plain body');
	}
	public function testAttachment()
	{
		$doc = new HTMLDocument();
		$attachment = new Mailer\Attachment();
		$attachment->fileName = $this->getFilename('test.txt');
		$attachment->mimeType = 'text/plain';
		$attachment->disposition = Mailer\Attachment::DISPOSITION_ATTACHMENT;
		$doc->addBody('<p>bla</p>');
		$mailSuccess = $this->mailer->sendMail('dev@null', 'test', 'plaintext', $doc->output(), array('From' => 'foomo@null'), array($attachment));
		$this->assertTrue($mailSuccess);
		$this->assertEquals('', $this->mailer->getLastError());
	}
	public function testHTMLImage()
	{
		$doc = new HTMLDocument();
		$image = new Mailer\HTMLImage();
		$image->fileName = $this->getFilename('test.jpg');
		$image->mimeType = 'image/jpeg';
		$doc->addBody('<p>bla</p><img style="border: solid green 20px;" src="cid:' . $image->contentId . '">');
		$this->assertTrue($this->mailer->sendMail('dev@null', 'test', 'plaintext', $doc->output(), array('From' => 'foomo@null'), array(), array($image)));
		$this->assertEquals('', $this->mailer->getLastError());
	}
	private function getFilename($name)
	{
		return __DIR__ . \DIRECTORY_SEPARATOR . 'mailerResources' . \DIRECTORY_SEPARATOR . $name;
	}
	public static function tearDownAfterClass()
	{
		\PHPUnit_Framework_Error_Notice::$enabled = true;
	}
}