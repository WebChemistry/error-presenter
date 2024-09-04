<?php declare(strict_types = 1);

namespace WebChemistry\ErrorPresenter;

final class ErrorTemplateSingleton
{
	
	private static ErrorTemplate $errorTemplate;
	
	public static function intialize(ErrorTemplate $errorTemplate): void
	{
		self::$errorTemplate = $errorTemplate;
	}

	public static function get(): ErrorTemplate
	{
		return self::$errorTemplate;
	}

}
