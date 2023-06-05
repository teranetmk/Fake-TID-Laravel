<?php

/**
 * Framework
 *
 * @copyright   Copyright (c) 2021, BADDI Services. (https://baddi.info)
 */

namespace BADDIServices\Framework\Entities;

use BADDIServices\Framework\Interfaces\EntityInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model implements EntityInterface
{
    use HasTimestamps;

    public const UPDATED_AT_COLUMN = self::UPDATED_AT;
    public const CREATED_AT_COLUMN = self::CREATED_AT;

    /** @var bool */
    public $incrementing = true;

    /** @var string */
    protected $primaryKey = 'id';
    protected $keyType = 'int';

    /** @var array */
    protected $guarded = [];

    public function getId(): string
    {
        return $this->getAttribute(self::ID_COLUMN);
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->getAttribute(self::CREATED_AT) ?? null;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->getAttribute(self::UPDATED_AT) ?? null;
    }

    public function getDeletedAt(): ?Carbon
    {
        return $this->getAttribute(self::DELETED_AT_COLUMN) ?? null;
    }
}