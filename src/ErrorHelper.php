<?php declare(strict_types = 1);

namespace WebChemistry\ErrorPresenter;

use Nette\Http\IRequest;

/**
 * @internal
 */
final class ErrorHelper {

	/** @var string */
	public static $basePath = '/';

	public function __construct(IRequest $request) {
		self::$basePath = $request->getUrl()->getBasePath();
	}

}
