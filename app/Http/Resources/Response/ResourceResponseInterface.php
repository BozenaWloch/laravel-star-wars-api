<?php
declare(strict_types=1);

namespace App\Http\Resources\Response;

use Illuminate\Http\Request;

interface ResourceResponseInterface
{
    public function transform(Request $request, $data): array;

    public function transformCollection(Request $request, array $data): array;

    public function transformPaginatedCollection(Request $request, array $data): array;
}
