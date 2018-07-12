<?php declare(strict_types = 1);

namespace WebChemistry\ErrorPresenter;

use Thunbolt\Application\IPresenterMapping;

class PresenterMapping implements IPresenterMapping {

	public function format(array $parts): string {
		return ErrorPresenter::class;
	}

	public function unformat(string $class): ?string {
		if ($class === ErrorPresenter::class) {
			return 'Error:Error';
		}

		return null;
	}

}
