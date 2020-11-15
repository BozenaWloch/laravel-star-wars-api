<?php
declare(strict_types=1);

namespace App\Http\Resources\Response;

use App\Http\Resources\ResourceCollection;
use App\Http\Resources\ResourceItem;
use App\Http\Resources\Transformers\UserTransformer;
use Exception;
use Illuminate\Http\Request;

class UserResourceResponse implements ResourceResponseInterface
{
    /**
     * @var \App\Http\Resources\Transformers\UserTransformer
     */
    private $transformer;

    public function __construct(UserTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function transform(Request $request, $data): array
    {
        $item = new ResourceItem($request, $data, $this->transformer);

        return $item->toArray();
    }

    public function transformCollection(Request $request, $data): array
    {
        $collection = new ResourceCollection($request, $data, $this->transformer);

        return $collection->toArray();
    }

    public function transformPaginatedCollection(Request $request, array $data): array
    {
        throw new Exception('Method not implemented');
    }
}
