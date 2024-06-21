<?php declare(strict_types = 1);

namespace WebChemistry\ErrorPresenter;

final class ErrorTemplateSingleton
{
	
	private static ErrorTemplate $errorTemplate;
	
	public function __construct(ErrorTemplate $errorTemplate)
	{
		self::$errorTemplate = $errorTemplate;
	}

	public static function get(): ErrorTemplate
	{
		return self::$errorTemplate;
	}

}
