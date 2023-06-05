<?php

/**
 * Framework
 *
 * @copyright   Copyright (c) 2021, BADDI Services. (https://baddi.info)
 */

namespace App\Services\ShortLink;

use App\Models\ShortLink\ShortLink;
use App\Services\Service;
use Illuminate\Support\Str;
use Illuminate\Auth\AuthManager;
use Illuminate\Pagination\LengthAwarePaginator;
use BADDIServices\Framework\Repositories\ShortLink\ShortLinkRepository;

class ShortLinkService extends Service
{
    /** @var AuthManager */
    private $authManager;

    public function __construct(ShortLinkRepository $shortLinkRepository, AuthManager $authManager) 
    {
        $this->repository = $shortLinkRepository;
        $this->authManager = $authManager;
    }

    public function paginate(?int $page = null): LengthAwarePaginator
    {
        return $this->repository->paginate($page, ['analytics']);
    }
    
    public function findById(int $id): ?ShortLink
    {
        return $this->repository->findById($id);
    }
    
    public function findByCode(string $code): ?ShortLink
    {
        return $this->repository->first([ShortLink::CODE_COLUMN => $code]);
    }

    public function createForHomePage(string $name): ShortLink
    {
        $code = Str::lower(Str::random(8));

        return $this->repository
            ->create([
                ShortLink::NAME_COLUMN          => $name,
                ShortLink::CODE_COLUMN          => $code,
                ShortLink::TO_HOME_PAGE_COLUMN  => true,
                ShortLink::USER_ID_COLUMN       => $this->authManager->id(),
            ]);
    }
    
    public function create(array $attributes): ShortLink
    {
        $filteredAttributes = collect($attributes)
            ->filter(function ($value) {
                return $value !== null;
            })
            ->only([
                ShortLink::NAME_COLUMN,
                ShortLink::CODE_COLUMN,
                ShortLink::LINK_COLUMN,
                ShortLink::TO_HOME_PAGE_COLUMN,
            ]);

        if (! $filteredAttributes->has(ShortLink::LINK_COLUMN)) {
            $filteredAttributes->put(ShortLink::TO_HOME_PAGE_COLUMN, true);
        }

        if (! $filteredAttributes->has(ShortLink::CODE_COLUMN)) {
            $filteredAttributes->put(ShortLink::CODE_COLUMN, Str::lower(Str::random(8)));
        }

        $filteredAttributes->put(ShortLink::USER_ID_COLUMN, $this->authManager->id());

        return $this->repository->create($filteredAttributes->toArray());
    }
    
    public function update(ShortLink $shortLink, array $attributes): bool
    {
        $filteredAttributes = collect($attributes)
            ->filter(function ($value) {
                return $value !== null;
            })
            ->only([
                ShortLink::CODE_COLUMN,
                ShortLink::NAME_COLUMN,
                ShortLink::LINK_COLUMN,
                ShortLink::TO_HOME_PAGE_COLUMN,
            ]);

        if (! $filteredAttributes->has(ShortLink::LINK_COLUMN)) {
            $filteredAttributes->put(ShortLink::TO_HOME_PAGE_COLUMN, true);
        }

        if (! $filteredAttributes->has(ShortLink::CODE_COLUMN)) {
            $filteredAttributes->put(ShortLink::CODE_COLUMN, Str::lower(Str::random(8)));
        }

        $filteredAttributes->put(ShortLink::USER_ID_COLUMN, $this->authManager->id());

        return $this->repository->update([ShortLink::ID_COLUMN => $shortLink->getId()], $filteredAttributes->toArray());
    }

    public function delete(ShortLink $shortLink): bool
    {
        return $this->repository->delete($shortLink->getId());
    }
}