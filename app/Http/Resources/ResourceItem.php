<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class ResourceItem extends AbstractResource
{
    public function __construct(Request $request, $data, TransformerAbstract $transformer)
    {
        $this->data = new Item($data, $transformer);

        parent::__construct($request);
    }
}
