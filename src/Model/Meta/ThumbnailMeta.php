<?php

namespace TinyPixel\AcornDB\Model\Meta;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use TinyPixel\AcornDB\Model\Attachment;
use TinyPixel\AcornDB\Model\Meta\PostMeta;
use TinyPixel\AcornDB\Exceptions\EloquentException;

/**
 * Thumbnail Meta Model
 *
 * @author     Kelly Mears <kelly@tinypixel.dev>
 * @license    MIT
 * @version    1.0.0
 * @since      1.0.0
 *
 * @package    AcornDB
 * @subpackage Model
 */
class ThumbnailMeta extends PostMeta
{
    /**
     * Thumbnail sizes.
     *
     * @var string
     */
    const SIZE_THUMBNAIL = 'thumbnail';

    /** @var string */
    const SIZE_MEDIUM    = 'medium';

    /** @var string */
    const SIZE_LARGE     = 'large';

    /** @var string */
    const SIZE_FULL      = 'full';

    /**
     * A thumbnail belongs to an attachmnent.
     *
     * @return BelongsTo
     */
    public function attachment()
    {
        return $this->belongsTo(Attachment::class, 'meta_value');
    }

    /**
     * Get thumbnail at a particular size.
     *
     * @param string $size
     * @return array
     */
    public function size($size)
    {
        if ($size == self::SIZE_FULL) {
            return $this->attachment->url;
        }

        $meta = unserialize($this->attachment->meta->_wp_attachment_metadata);

        $sizes = Arr::get($meta, 'sizes');

        if (!isset($sizes[$size])) {
            return $this->attachment->url;
        }

        $data = Arr::get($sizes, $size);

        return array_merge($data, [
            'url' => dirname($this->attachment->url) . '/' . $data['file'],
        ]);
    }

    /**
     * Magically return value as string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->attachment->guid;
    }
}