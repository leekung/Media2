<?php namespace Modules\Media\Entities;

/**
 * Class Category
 * @package Modules\Media\Entities
 */
class Category
{
    const NONE = 0;
    const ACTIVITY = 1;
    const PRODUCT_CARTOON = 2;
    const PRODUCT_PEOPLE= 3;

    /**
     * @var array
     */
    private $categories = [];

    public function __construct()
    {
        $this->categories = [
            self::NONE => trans('media::media.category.none'),
            self::ACTIVITY => trans('media::media.category.activity'),
            self::PRODUCT_CARTOON => trans('media::media.category.product cartoon'),
            self::PRODUCT_PEOPLE => trans('media::media.category.product people'),
        ];
    }

    /**
     * Get the available categories
     * @return array
     */
    public function lists()
    {
        return $this->categories;
    }

    /**
     * Get the file category
     * @param int $category_id
     * @return string
     */
    public function get($category_id)
    {
        if (isset($this->categories[$category_id])) {
            return $this->categories[$category_id];
        }

        return $this->categories[self::NONE];
    }
}
