<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\TransformerAbstract;

class ResourcePaginateCollection extends ResourceCollection
{
    public function __construct(Request $request, $data, TransformerAbstract $transformer)
    {
        parent::__construct($request, $data, $transformer);

        $this->data->setPaginator(new IlluminatePaginatorAdapter($data));
    }
}
