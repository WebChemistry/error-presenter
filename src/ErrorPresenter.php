<?php declare(strict_types = 1);

namespace WebChemistry\ErrorPresenter;

use Nette\Application\IPresenter;
use Tracy\ILogger;
use Nette;
use Nette\Application\Responses;

class ErrorPresenter implements IPresenter {

	/** @var ILogger */
	protected $logger;

	/** @var bool */
	protected $log400 = false;

	/** @var string|null */
	public static $homepage = null;

	/** @var string */
	public static $home = 'Domovská stránka';

	/** @var array */
	public static $messages = [
		400 => [
			'title' => 'Bad request',
			'desc' => 'The server cannot process the request due to something that is perceived to be a client error.',
			'image' => __DIR__ . '/../templates/404.png',
		],
		401 => [
			'title' => 'Unauthorized',
			'desc' => 'The requested resource requires an authentication.',
			'image' => __DIR__ . '/../templates/404.png',
		],
		403 => [
			'title' => 'Access denied',
			'desc' => 'The requested resource requires an authentication.',
			'image' => __DIR__ . '/../templates/404.png',
		],
		404 => [
			'title' => 'Oops page not found',
			'desc' => 'The page you are looking for does not exist or has been moved.',
			'image' => __DIR__ . '/../templates/404.png',
		],
		500 => [
			'title' => 'Internal server error',
			'desc' => 'Something goes wrong with our servers, please try again later.',
			'image' => __DIR__ . '/../templates/500.png',
		],
	];

	public function __construct(ILogger $logger) {
		$this->logger = $logger;
	}

	/**
	 * @param bool $log400
	 */
	public function setLog400(bool $log400 = true): void {
		$this->log400 = $log400;
	}

	public static function getHomepage(): ?string {
		if (self::$homepage === null) {
			return ErrorHelper::$basePath;
		}

		return self::$homepage;
	}

	/**
	 * @return Nette\Application\IResponse
	 */
	public function run(Nette\Application\Request $request): Nette\Application\IResponse {
		$e = $request->getParameter('exception');
		if ($e instanceof Nette\Application\BadRequestException) {
			if ($this->log400) {
				$this->logger->log("HTTP code {$e->getCode()}: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}", 'access');
			}
			$code = $e->getHttpCode();

			return new Responses\CallbackResponse(function () use ($code) {
				$data = self::$messages[$code] ?? self::$messages[400];

				require __DIR__ . '/templates/4xx.phtml';
			});
		}
		$this->logger->log($e, ILogger::EXCEPTION);

		return new Responses\CallbackResponse(function () {
			require __DIR__ . '/templates/500.phtml';
		});
	}

}
