<?php namespace Igniter\Pages\Models;

/**
 * Pages Model Class
 *
 * @package Admin
 */
class Pages_model extends \System\Models\Pages_model
{
    protected $fillable = ['language_id', 'name', 'title', 'heading', 'content', 'meta_description',
        'meta_keywords', 'layout_id', 'navigation', 'date_added', 'date_updated', 'status'];

    public function getContentAttribute($value)
    {
        return html_entity_decode($value);
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['heading'] = $value;
        $this->attributes['name'] = $value;
    }

    public function getMorphClass()
    {
        return 'pages';
    }
}