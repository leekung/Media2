<?php namespace Modules\Media\Http\Controllers\Admin;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\LaravelLocalization;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Media\Entities\File;
use Modules\Media\Http\Requests\UpdateMediaRequest;
use Modules\Media\Image\Imagy;
use Modules\Media\Image\ThumbnailsManager;
use Modules\Media\Repositories\FileRepository;
use Pingpong\Modules\Facades\Module;
use Yajra\Datatables\Datatables;
use Modules\Media\Entities\Category;

/**
 * @property  category
 */
class MediaController extends AdminBaseController
{
    /**
     * @var FileRepository
     */
    private $file;
    /**
     * @var Repository
     */
    private $config;
    /**
     * @var Category
     */
    private $category;
    /**
     * @var Imagy
     */
    private $imagy;
    /**
     * @var ThumbnailsManager
     */
    private $thumbnailsManager;

    public function __construct(FileRepository $file, Repository $config, Imagy $imagy, ThumbnailsManager $thumbnailsManager, Category $category)
    {
        parent::__construct();
        $this->file = $file;
        $this->config = $config;
        $this->imagy = $imagy;
        $this->thumbnailsManager = $thumbnailsManager;
        $this->category = $category;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request, LaravelLocalization $locale, Imagy $imagy)
    {
        $columns = $request->get('columns');

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
                'media__files.category_id',
                'media__files.youtube_url',
                'media__file_translations.alt_attribute',
                'media__file_translations.description',
                'media__file_translations.keywords',
                'media__files.created_at',
            ]);

            return Datatables::of($items)
                ->addColumn('thumbnail', function ($file) use ($imagy) {
                    $image_extensions = ['jpg', 'png', 'jpeg', 'gif'];
                    $map_icons = [
                        'xls' => 'fa-file-excel-o',
                        'xlsx' => 'fa-file-excel-o',
                        'doc' => 'fa-file-word-o',
                        'docx' => 'fa-file-word-o',
                        'pdf' => 'fa-file-pdf-o',
                        'zip' => 'fa-file-archive-o',
                        'rar' => 'fa-file-archive-o',
                        'gz' => 'fa-file-archive-o',
                        'mp4' => 'fa-file-video-o',
                        '3gp' => 'fa-file-video-o',
                        'ogv' => 'fa-file-video-o',
                        'webm' => 'fa-file-video-o',
                        'txt' => 'fa-file-text-o',
                    ];
                    $extension = pathinfo($file->path, PATHINFO_EXTENSION);

                    if (in_array($extension, $image_extensions)) {
                        return '<a href="'.$file->path.'" class="modal-link" target="_blank"><img src="'.$imagy->getThumbnail($file->path, 'smallThumb').'" alt=""/></a>';
                    } else {
                        return '<a href="'.$file->path.'" target="_blank"><i class="fa fa-file '.(isset($map_icons[$extension]) ? $map_icons[$extension] : '').'" style="font-size: 20px;"></i></a>';
                    }
                })
                ->make(true);
        }
        //$files = $this->file->all();
        $this->assetPipeline->requireJs('datatables.js')->after('jquery');
        $this->assetPipeline->requireJs('datatables-bs.js')->after('datatables.js');
        $this->assetPipeline->requireCss('datatables-bs.css')->after('bootstrap');

        $this->assetManager->addAsset('bootstrap-editable.css', Module::asset('translation:vendor/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css'));
        $this->assetManager->addAsset('bootstrap-editable.js', Module::asset('translation:vendor/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min.js'));
        $this->assetPipeline->requireJs('bootstrap-editable.js');
        $this->assetPipeline->requireCss('bootstrap-editable.css');

        $config = $this->config->get('asgard.media.config');
        $categories = $this->category->lists();

        return view('media::admin.index', compact('files', 'config', 'categories'));
    }

    public function isImage()
    {
        return in_array(pathinfo($this->path, PATHINFO_EXTENSION), $this->imageExtensions);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('media.create');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  File     $file
     * @return Response
     */
    public function edit(File $file)
    {
        $thumbnails = $this->thumbnailsManager->all();
        $categories = $this->category->lists();

        return view('media::admin.edit', compact('file', 'thumbnails', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  File               $file
     * @param  UpdateMediaRequest $request
     * @return Response
     */
    public function update(File $file, UpdateMediaRequest $request)
    {
        $this->file->update($file, $request->all());

        flash(trans('media::messages.file updated'));

        return redirect()->route('admin.media.media.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  File     $file
     * @internal param int $id
     * @return Response
     */
    public function destroy(File $file)
    {
        $this->imagy->deleteAllFor($file);
        $this->file->destroy($file);

        flash(trans('media::messages.file deleted'));

        return redirect()->route('admin.media.media.index');
    }
}
