<?php declare(strict_types = 1);

namespace WebChemistry\ErrorPresenter;

use Nette\Application\IPresenter;
use Nette\Application\Request;
use Nette\Application\Response;
use Nette\Application\Responses\CallbackResponse;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Tracy\ILogger;

abstract class Error5xxPresenter implements IPresenter
{
	
	public function __construct(
		private ILogger $logger,
	)
	{
	}

	public function run(Request $request): Response
	{
		// Log the exception
		$exception = $request->getParameter('exception');
		$this->logger->log($exception, ILogger::EXCEPTION);

		// Display a generic error message to the user
		return new CallbackResponse(function (IRequest $httpRequest, IResponse $httpResponse): void {
			if (preg_match('#^text/html(?:;|$)#', (string) $httpResponse->getHeader('Content-Type'))) {
				$template = ErrorTemplateSingleton::get();
				$code = 500;

				require $template->values['template']['file'];
			}
		});
	}

}
