<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;

abstract class AbstractResource
{
    /**
     * @var \League\Fractal\Resource\ResourceAbstract
     */
    protected $data;

    /**
     * @var \League\Fractal\Manager
     */
    private $manager;

    public function __construct(Request $request)
    {
        $this->manager = new Manager();
        $this->manager->setSerializer(new ArraySerializer())->parseIncludes($request->get('include') ?: []);
    }

    public function toArray(): array
    {
        return $this->manager->createData($this->data)->toArray();
    }
}
