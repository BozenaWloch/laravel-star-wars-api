<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class ResourceCollection extends AbstractResource
{
    public function __construct(Request $request, $data, TransformerAbstract $transformer)
    {
        $this->data = new Collection($data, $transformer);

        parent::__construct($request);
    }
}
