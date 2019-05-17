<?php namespace Modules\Page\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Page\Events\PageWasCreated;
use Modules\Page\Events\PageWasDeleted;
use Modules\Page\Events\PageWasUpdated;
use Modules\Page\Repositories\PageRepository;
use Modules\Media\Repositories\FileRepository;

class EloquentPageRepository extends EloquentBaseRepository implements PageRepository
{
    /**
     * Find the page set as homepage
     * @return object
     */
    public function findHomepage()
    {
        return $this->model->where('is_home', 1)->first();
    }

    /**
     * Count all records
     * @return int
     */
    public function countAll()
    {
        return $this->model->count();
    }

    /**
     * @param  mixed  $data
     * @return object
     */
    public function create($data)
    {
        if (array_get($data, 'is_home') === '1') {
            $this->removeOtherHomepage();
        }
        $page = $this->model->create($data);

        if( isset($data["medias_single"]) && !empty($data["medias_single"]) ) {
            $eventImg = $data["medias_single"];
            
            $page->pageTranslation->files()->sync([ $eventImg['photo'] => ['imageable_type' => 'Modules\\Page\\Entities\\PageTranslation', 'zone' => 'background']]);

            //inject file to media module view
            $file = app(FileRepository::class);
            $image = $file->findFileByZoneForEntity('background', $page);
            if($image){
                $page->photo = $this->getPathImage($image);
                $page->save();
            }
        }

        event(new PageWasCreated($page->id, $data));

        return $page;
    }

    /**
     * @param $model
     * @param  array  $data
     * @return object
     */
    public function update($model, $data)
    {
        if (array_get($data, 'is_home') === '1') {
            $this->removeOtherHomepage($model->id);
        }
        $path = "";
        $file = app(FileRepository::class);
        $image = $file->findFileByZoneForEntity('background', $model->pageTranslation);
        if($image){
            $path = $this->getPathImage($image);            
        }
        $data['en'] = array_merge($data['en'], ['photo' => $path]);

        $model->update($data);

        event(new PageWasUpdated($model->id, $data));

        return $model;
    }

    public function destroy($model)
    {
        event(new PageWasDeleted($model));

        return $model->delete();
    }

    /**
     * @param $slug
     * @param $locale
     * @return object
     */
    public function findBySlugInLocale($slug, $locale)
    {
        if (method_exists($this->model, 'translations')) {
            return $this->model->whereHas('translations', function (Builder $q) use ($slug, $locale) {
                $q->where('slug', $slug);
                $q->where('locale', $locale);
            })->with('translations')->first();
        }

        return $this->model->where('slug', $slug)->where('locale', $locale)->first();
    }

    /**
     * Set the current page set as homepage to 0
     * @param null $pageId
     */
    private function removeOtherHomepage($pageId = null)
    {
        $homepage = $this->findHomepage();
        if ($homepage === null) {
            return;
        }
        if ($pageId === $homepage->id) {
            return;
        }

        $homepage->is_home = 0;
        $homepage->save();
    }

    public function convertLinkS3ToHttp($url){
        if(substr($url, 0,5) == 'https'){
            return str_replace('https', 'http', substr($url,0,5)) . substr($url,5);
        }
        return $url;
    }

    public function getPathImage($file){
        return Config('asgard.media.config.files-path') . $file->filename;
    }
}
