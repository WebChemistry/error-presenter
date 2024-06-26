<?php declare(strict_types = 1);

namespace WebChemistry\ErrorPresenter;

use Nette;
use Nette\Application\IPresenter;
use Nette\Application\Responses;
use Tracy\Debugger;
use Tracy\ILogger;

class ErrorPresenter implements IPresenter {

	protected bool $log400 = false;

	public function __construct(
		protected ?ILogger $logger,
	)
	{
		$this->logger ??= Debugger::getLogger();
	}

	/**
	 * @param bool $log400
	 */
	public function setLog400(bool $log400 = true): void
	{
		$this->log400 = $log400;
	}

	public function run(Nette\Application\Request $request): Nette\Application\IResponse
	{
		$e = $request->getParameter('exception');
		if ($e instanceof Nette\Application\BadRequestException) {
			if ($this->log400 && $this->logger) {
				$this->logger->log("HTTP code {$e->getCode()}: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}", 'access');
			}
			$code = $e->getHttpCode();
		} else {
			$code = 500;

			if ($this->logger) {
				$this->logger->log($e, ILogger::EXCEPTION);
			}
		}

		return new Responses\CallbackResponse(function () use ($code) {
			$template = ErrorTemplateSingleton::get();
			
			require $template->values['template']['file'];
		});
	}

}
