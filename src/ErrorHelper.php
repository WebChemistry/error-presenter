<?php declare(strict_types = 1);

namespace WebChemistry\ErrorPresenter;

use Nette\Http\IRequest;

/**
 * @internal
 */
final class ErrorHelper {

	public static string $basePath = '/';

	/** @var mixed[] */
	protected static array $config = [
		'home' => 'Homepage',
		'homepage' => null,
		'messages' => [
			null => [
				'title' => 'Unknown error',
				'desc' => 'Something goes wrong, please try again later.',
			],
			400 => [
				'title' => 'Bad request',
				'desc' => 'The server cannot process the request due to something that is perceived to be a client error.',
			],
			401 => [
				'title' => 'Unauthorized',
				'desc' => 'The requested resource requires an authentication.',
			],
			403 => [
				'title' => 'Access denied',
				'desc' => 'The requested resource requires an authentication.',
			],
			404 => [
				'title' => 'Oops page not found',
				'desc' => 'The page you are looking for does not exist or has been moved.',
			],
			500 => [
				'title' => 'Internal server error',
				'desc' => 'Something goes wrong with our servers, please try again later.',
			],
		],
		'colors' => [
			'primary' => '#ff6f68',
			'secondary' => '#ffa34f',
		],
		'layout' => null,
	];

	public function __construct(IRequest $request)
	{
		self::$basePath = $request->getUrl()->getBasePath();
	}

	public static function setConfig(array $config): void
	{
		self::$config = $config;
	}

	/**
	 * @return mixed[]
	 */
	public static function getConfig(): array
	{
		return self::$config;
	}

	public static function getHomepage(): ?string
	{
		return self::$config['homepage'] ?? ErrorHelper::$basePath;
	}

}
