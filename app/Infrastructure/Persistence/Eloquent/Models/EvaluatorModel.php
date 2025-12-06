<?php

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\EvaluatorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $specialization
 * @property int $max_applications
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class EvaluatorModel extends Model
{
    /** @use HasFactory<EvaluatorFactory> */
    use HasFactory;

    protected $table = 'evaluators';

    protected $fillable = [
        'name',
        'email',
        'specialization',
        'max_applications',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'max_applications' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function newFactory(): EvaluatorFactory
    {
        return EvaluatorFactory::new();
    }

    /**
     * @return HasMany<ApplicationModel, EvaluatorModel>
     */
    public function applications(): HasMany
    {
        return $this->hasMany(ApplicationModel::class, 'evaluator_id');
    }
}
