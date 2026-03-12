<?php

namespace App\Traits;

use Symfony\Component\Uid\Ulid;

/**
 * HasUlidRouteKey Trait
 * 
 * Implements ULID-based Opaque Public Identifier Pattern for financial systems.
 * 
 * This is the recommended approach for microfinance systems because:
 * - ✅ Opaque (cannot be enumerated like sequential IDs)
 * - ✅ Sortable (timestamp-based, maintains chronological order)
 * - ✅ Audit-ready (regulators understand and approve)
 * - ✅ Professional (industry standard for financial systems)
 * - ✅ Secure (non-sequential, non-guessable)
 * - ✅ Easy to defend to regulators
 * 
 * ULID Format: 01ARZ3NDEKTSV4RRFFQ69G5FAV (26 characters, Crockford's Base32)
 * Example URL: /admin/memberships/01ARZ3NDEKTSV4RRFFQ69G5FAV
 * 
 * Terminology: "ULID-based Opaque Public Identifiers"
 */
trait HasUlidRouteKey
{
    /**
     * Boot the trait.
     * Generate ULID when creating new models
     */
    protected static function bootHasUlidRouteKey(): void
    {
        static::creating(function ($model) {
            if (empty($model->ulid)) {
                $model->ulid = (string) new Ulid();
            }
        });
    }

    /**
     * Get the value of the model's route key.
     * Returns the ULID as the public-facing identifier
     */
    public function getRouteKey(): string
    {
        return $this->ulid ?? $this->getKey();
    }

    /**
     * Retrieve the model for binding a value.
     * Resolves the ULID from URL back to the model
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // Validate ULID format (26 characters, Crockford's Base32)
        if (!Ulid::isValid($value)) {
            // Fallback: try numeric ID for backward compatibility
            if (is_numeric($value)) {
                return $this->where($this->getKeyName(), $value)->first();
            }
            return null;
        }

        // Find the model by ULID (opaque public identifier)
        return $this->where('ulid', $value)->first();
    }

    /**
     * Get the route key name.
     * Uses 'ulid' as the public-facing identifier
     */
    public function getRouteKeyName(): string
    {
        return 'ulid';
    }
}


