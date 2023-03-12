<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\Data\FactoriesFormData;
use App\Data\FactoryData;
use App\Data\FactoryDataSource;
use App\Data\FactoryFilter;
use App\Data\ListFactoriesParams;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class FactoriesController
{
    private const PRODUCTS_SEPARATOR = '; ';
    private const PAGE_SIZE = 10;

    public function table(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $formData = FactoriesFormData::fromArray($request->getQueryParams());
        $listParams = $this->getListFactoriesParams($formData);
        $view = Twig::fromRequest($request);
        $dataSource = new FactoryDataSource();
        $factories = $dataSource->listFactories($listParams);
        $statusOptions = $dataSource->listFactoryStatusSelectOptions();
        $depthOptions = $dataSource->listDepthSelectOptions();
        $productOptions = $dataSource->listProductSelectOptions();

        return $view->render($response, 'factories_page.twig', [
            'form' => [
                'status_options' => $statusOptions,
                'depth_options' => $depthOptions,
                'product_options' => $productOptions,
                'values' => $formData->toArray(),
            ],
            'table_rows' => array_map(fn($factory) => $this->getRowData($factory), $factories),
            'sort_type' => $listParams->isSortAscending(),
            'sort_field' => $listParams->getSortByField(),
            'page_count' => $dataSource->getPageCount($listParams, self::PAGE_SIZE),
            'page_no' => $listParams->getPageNo()
        ]);
    }

    private function getRowData(FactoryData $data): array
    {
        return [
            'full_name' => $data->getFullName(),
            'short_name' => $data->getShortName(),
            'legal_address' => $data->getLegalAddress(),
            'actual_address' => $data->getActualAddress(),
            'processing_depth' => $data->getProcessingDepth(),
            'register_info' => $data->getRegisterInfo(),
            'status_text' => $data->getStatusText(),
            'products' => implode(self::PRODUCTS_SEPARATOR, $data->getProducts()),
        ];
    }

    private function getListFactoriesParams(FactoriesFormData $data): ListFactoriesParams
    {
        $filters = [];
        if ($value = $data->getFilterByStatus())
        {
            $filters[] = new FactoryFilter(FactoryFilter::FILTER_BY_STATUS, $value);
        }
        if ($value = $data->getFilterByDepth())
        {
            $filters[] = new FactoryFilter(FactoryFilter::FILTER_BY_DEPTH, $value);
        }
        if ($value = $data->getFilterByProduct())
        {
            $filters[] = new FactoryFilter(FactoryFilter::FILTER_BY_PRODUCT, $value);
        }

        return new ListFactoriesParams(
            $data->getSearchQuery(),
            $filters,
            $data->getSortByField(),
            $data->isSortAscending(),
            self::PAGE_SIZE,
            $data->getPageNo()
        );
    }
}
