<?php

declare(strict_types=1);

namespace LiquidLight\Anthology\Domain\Repository;

use LiquidLight\Anthology\Domain\Filter\CategoryFilter;
use LiquidLight\Anthology\Domain\Model\Category;
use LiquidLight\Anthology\Domain\Model\Filter;
use LiquidLight\Anthology\Factory\FilterFactory;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

final class FilterRepository extends Repository
{
	public function __construct(
		private FilterFactory $filterFactory,
	) {
		parent::__construct();
	}

	public function findByUids(array $uids): QueryResultInterface
	{
		// Set a value that will return nothing to prevent `in()` throwing an exception
		$uids[] = 0;

		$query = $this->createQuery();

		return $query
			->matching(
				$query->in('uid', $uids)
			)->execute()
		;
	}

	public function findCategoryFilterByPage(
		Category $category,
		int $pageUid
	): ?Filter {
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(false);
		$query->matching(
			$query->logicalAnd(
				$query->equals('pid', $pageUid),
				$query->equals('filterType', $this->getFilterIdentifier()),
			),
		);

		/** @var Filter[] */
		$listPageFilters = $query->execute()->toArray();

		$matchingFilters = array_filter(
			$listPageFilters,
			static fn (Filter $filter): bool => (
				(int)($filter->getParsedSettings()['category'] ?? 0) === $category->getParent()?->getUid()
			),
		);

		if (count($matchingFilters) === 0) {
			return null;
		}

		return reset($matchingFilters);
	}

	private function getFilterIdentifier(): ?string
	{
		return array_search(
			CategoryFilter::class,
			$this->filterFactory->getFilters(),
			true
		) ?: null;
	}
}
