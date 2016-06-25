<?php namespace Modules\Media\Presenters;

use Laracasts\Presenter\Presenter;
use Modules\Media\Entities\Category;

class MediaPresenter extends Presenter
{
    /**
     * @var \Modules\Media\Entities\Category
     */
    protected $categories;
    /**
     * @var \Modules\Media\Repositories\FileRepository
     */
    private $category;

    public function __construct($entity)
    {
        parent::__construct($entity);
        $this->file = app('Modules\Media\Repositories\FileRepository');
        $this->category = app('Modules\Media\Entities\Category');
    }

    /**
     * Get the file category
     * @return string
     */
    public function category()
    {
        return $this->file->get($this->entity->category_id);
    }

    /**
     * Getting the label class for the appropriate category
     * @return string
     */
    public function categoryLabelClass()
    {
        switch ($this->entity->status) {
            case Category::NONE:
                return 'bg-grey';
                break;
            case Category::ACTIVITY:
                return 'bg-green';
                break;
            case Category::PRODUCT_CARTOON:
                return 'bg-orange';
                break;
            case Category::PRODUCT_PEOPLE:
                return 'bg-blue';
                break;
            default:
                return 'bg-grey';
                break;
        }
    }
}
