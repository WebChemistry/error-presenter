<?php declare(strict_types = 1);

namespace WebChemistry\ErrorPresenter;

final class ErrorTemplate
{

	/**
	 * @param array{
	 *     messages: array<int, array{title: string, desc: string}>,
	 *     home: string,
	 *     colors: array{primary: string, secondary: string},
	 *     template: array{file: string, options: array<string, mixed>},
	 * } $values
	 */
	public function __construct(
		public readonly array $values,
	)
	{
	}

}
