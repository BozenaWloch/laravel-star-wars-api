<?php
declare(strict_types=1);

namespace App\Http\Resources\Transformers;

use DateTime;
use League\Fractal\TransformerAbstract;

abstract class AbstractTransformer extends TransformerAbstract
{
    public const DEFAULT_DATE_FORMAT = 'Y-m-d H:i:s';

    public function getTransformer(string $className): self
    {
        return app()->make($className);
    }

    protected function formatDate(?DateTime $date = null, string $format = self::DEFAULT_DATE_FORMAT): ?string
    {
        return $date ? $date->format($format) : null;
    }
}
