<?php declare(strict_types = 1);

namespace WebChemistry\ErrorPresenter;

use Nette\Http\IRequest;

/**
 * @internal
 */
final class ErrorHelper {

	/** @var string */
	public static $basePath = '/';

	/** @var array */
	protected static $config = [
		'home' => 'Homepage',
		'homepage' => null,
		'messages' => [
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
		],
		'colors' => [
			'primary' => '#ff6f68',
			'secondary' => '#ffa34f',
		],
		'layout' => null,
	];

	public function __construct(IRequest $request) {
		self::$basePath = $request->getUrl()->getBasePath();
	}

	public static function setConfig(array $config) {
		self::$config = $config;
	}

	public static function getConfig(): array {
		return self::$config;
	}

	public static function getHomepage(): ?string {
		return self::$config['homepage'] ?? ErrorHelper::$basePath;
	}

}
