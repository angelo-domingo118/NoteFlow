<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Source extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'notebook_id',
        'name',
        'type',
        'data',
        'file_path',
        'file_type',
        'has_extracted_text',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'has_extracted_text' => 'boolean',
    ];

    /**
     * Valid source types.
     *
     * @var array<string>
     */
    public const TYPES = [
        'text',
        'file',
        'website',
        'youtube',
    ];

    /**
     * Get the notebook that owns the source.
     */
    public function notebook(): BelongsTo
    {
        return $this->belongsTo(Notebook::class);
    }

    /**
     * Store a file and update the source record.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return void
     */
    public function storeFile($file): void
    {
        $path = $file->store('sources', 'public');
        $this->file_path = $path;
        $this->file_type = $file->getMimeType();
        $this->save();
    }

    /**
     * Delete the associated file when the source is deleted.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($source) {
            if ($source->file_path) {
                Storage::disk('public')->delete($source->file_path);
            }
        });
    }

    /**
     * Get the full URL for the stored file.
     *
     * @return string|null
     */
    public function getFileUrl(): ?string
    {
        if (!$this->file_path) {
            return null;
        }
        
        return asset(Storage::disk('public')->path($this->file_path));
    }

    /**
     * Check if the source is a YouTube video.
     *
     * @return bool
     */
    public function isYouTube(): bool
    {
        return $this->type === 'youtube';
    }

    /**
     * Check if the source is a website.
     *
     * @return bool
     */
    public function isWebsite(): bool
    {
        return $this->type === 'website';
    }

    /**
     * Check if the source is a file.
     *
     * @return bool
     */
    public function isFile(): bool
    {
        return $this->type === 'file';
    }

    /**
     * Check if the source is text.
     *
     * @return bool
     */
    public function isText(): bool
    {
        return $this->type === 'text';
    }

    /**
     * Get the website content data.
     *
     * @return array|null
     */
    public function getWebsiteContent(): ?array
    {
        if (!$this->isWebsite()) {
            return null;
        }

        try {
            $data = json_decode($this->data, true);
            return [
                'url' => $data['url'] ?? null,
                'content' => $data['content'] ?? null,
                'metadata' => $data['metadata'] ?? [],
                'error' => $data['error'] ?? null,
            ];
        } catch (\Exception $e) {
            return [
                'url' => $this->data,
                'content' => null,
                'metadata' => [],
                'error' => 'Failed to parse website data',
            ];
        }
    }

    /**
     * Check if the source has an extraction error.
     *
     * @return bool
     */
    public function hasExtractionError(): bool
    {
        if (!$this->isWebsite()) {
            return false;
        }

        try {
            $data = json_decode($this->data, true);
            return isset($data['error']);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get the extraction error message.
     *
     * @return string|null
     */
    public function getExtractionError(): ?string
    {
        if (!$this->isWebsite()) {
            return null;
        }

        try {
            $data = json_decode($this->data, true);
            return $data['error'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
