<?php namespace Modules\Media\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\LaravelLocalization;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Media\Image\Imagy;
use Modules\Media\Image\ThumbnailsManager;
use Modules\Media\Repositories\FileRepository;
use Yajra\Datatables\Datatables;

class MediaGridController extends AdminBaseController
{
    /**
     * @var FileRepository
     */
    private $file;
    /**
     * @var ThumbnailsManager
     */
    private $thumbnailsManager;

    public function __construct(FileRepository $file, ThumbnailsManager $thumbnailsManager)
    {
        parent::__construct();

        $this->file = $file;
        $this->thumbnailsManager = $thumbnailsManager;
    }

    /**
     * A grid view for the upload button
     * @return \Illuminate\View\View
     */
    public function index(Request $request, LaravelLocalization $locale, Imagy $imagy)
    {
        $columns = $request->get('columns');
        $thumbnails = $this->thumbnailsManager->all();

        if (!empty($columns)) {
            $items = DB::table('media__files')
                ->Join('media__file_translations', function ($join) use ($locale) {
                    $join->on('media__file_translations.file_id', '=', 'media__files.id');
                    $join->on('media__file_translations.locale', '=', DB::raw('\''.$locale->getCurrentLocale().'\''));
                }, null, null, 'left outer')
                ->select([
                    'media__files.id',
                    'media__files.path',
                    'media__files.filename',
                    'media__file_translations.alt_attribute',
                    'media__file_translations.description',
                    'media__file_translations.keywords',
                    'media__files.created_at',
                ]);

            return Datatables::of($items)
                ->addColumn('thumbnail', function ($file) use ($imagy) {
                    $image_extensions = ['jpg', 'png', 'jpeg', 'gif'];
                    if (in_array(pathinfo($file->path, PATHINFO_EXTENSION), $image_extensions)) {
                        return '<a href="'.$file->path.'" class="modal-link" target="_blank"><img src="'.$imagy->getThumbnail($file->path, 'smallThumb').'" alt=""/></a>';
                    } else {
                        return '<i class="fa fa-file" style="font-size: 20px;"></i>';
                    }
                })
                ->addColumn('action', function ($file) use ($imagy, $thumbnails) {
                    $image_extensions = ['jpg', 'png', 'jpeg', 'gif'];
                    $buffer = '<div class="btn-group">';
                    if (in_array(pathinfo($file->path, PATHINFO_EXTENSION), $image_extensions)) {
                        $buffer .= '<button type="button" class="btn btn-primary btn-flat dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            '.trans('media::media.insert').' <span class="caret"></span></button><ul class="dropdown-menu" role="menu">';
                        foreach ($thumbnails as $thumbnail) {
                            $buffer .= '<li data-file="' . $imagy->getThumbnail($file->path, $thumbnail->name()) . '" data-id="' . $file->id . '" class="jsInsertImage">
                                <a href="">' . $thumbnail->name() . ' (' . $thumbnail->size() . ')</a></li>';
                        }
                        $buffer .= '<li class="divider"></li><li data-file="'.url($file->path).'" data-id="'.$file->id.'" data-file-path="'.$file->path.'" class="jsInsertImage">
                            <a href="">Original</a></li></ul>';
                    } else {
                        $buffer .= '<a href="" class="btn btn-primary jsInsertImage" data-id="'.$file->id.'" data-file="'.$file->path.'>'.trans('media::media.insert').'</a>';
                    }
                    $buffer .= '</div>';

                    return $buffer;
                })
                ->make(true);
        }
        //$files = $this->file->all();

        return view('media::admin.grid.general', compact('files', 'thumbnails'));
    }

    /**
     * A grid view of uploaded files used for the wysiwyg editor
     * @return \Illuminate\View\View
     */
    public function ckIndex()
    {
        $files = $this->file->all();
        $thumbnails = $this->thumbnailsManager->all();

        return view('media::admin.grid.ckeditor', compact('files', 'thumbnails'));
    }
}
