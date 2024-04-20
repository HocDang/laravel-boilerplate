# Repository - Service

## Methods

### App\Repositories\Contracts\BaseRepositoryInterface

- `with(array $with): self` - define eager loading relations to append to the query
- `withCount(array $withCount): self` - define eager loading relations count to append to the query
- `withTrashed(): self` - add trashed models to the query result (if model supports Soft Delete)
- `onlyTrashed(): self` - return only trashed models as a query result (if model supports Soft Delete)
- `withoutTrashed(): self` - remove trashed models from the query result (if model supports Soft Delete)
- `find(mixed $key): ?Model` - find model by primary key
- `findOrFail(mixed $key, ?string $column = null): ?Model` - find or fail model by PK or another field
- `findFirst(array $attributes): ?Model` - find first model by given attributes, e.g `[['email', 'test@email.com'], ['anotherProperty', '>=', 'val']]`
- `findMany(array $attributes): ?Model` - find many models by given attributes, e.g `[['email', 'test@email.com'], ['anotherProperty', '>=', 'val']]`
- `getAll(array $search = []): Collection` - get collection of models (and apply filters)
- `getAllCursor(array $search = []): LazyCollection` - get collection of models as cursor (and apply filters)
- `getAllPaginated(array $search = [], int $pageSize): LengthAwarePaginator` - get collection of models with pagination (and apply filters), pageSize can be changed dynamically by passing query param `page_size`
- `count(array $search = []): int` - get count of models which fit search criteria
- `create(array $data): ?Model` - create model entity
- `insert(array $data): bool` - bulk data insert
- `update(mixed $keyOrModel, array $data): Model` - update model entity
- `updateOrCreate(array $attributes, array $data): ?Model` - update or create model if not exists
- `delete(mixed $keyOrModel): bool` - delete model (or forceDelete if model supports Soft Delete)
- `softDelete(mixed $keyOrModel): void` - soft delete model (if model supports Soft Delete)
- `restore(mixed $keyOrModel): void` - restore model (if model supports Soft Delete)

### App\Repositories\Contracts\BaseCachableRepositoryInterface
This one supports the same methods, the only difference that it supports caching models & collections

### App\Services\Contracts\BaseCrudServiceInterface

- `with(array $with): self` - define eager loading relations to append to the query
- `withCount(array $withCount): self` - define eager loading relations count to append to the query
- `withTrashed(): self` - add trashed models to the query result (if model supports Soft Delete)
- `onlyTrashed(): self` - return only trashed models as a query result (if model supports Soft Delete)
- `withoutTrashed(): self` - remove trashed models from the query result (if model supports Soft Delete)
- `getAll(array $search = []): Collection` - get collection of models (and apply filters)
- `getAllCursor(array $search = []): LazyCollection` - get collection of models as cursor (and apply filters)
- `getAllPaginated(array $search = [], int $pageSize): LengthAwarePaginator` - get collection of models with pagination (and apply filters), pageSize can be changed dynamically by passing query param `page_size`
- `count(array $search = []): int` - get count of models which fit search criteria
- `find(mixed $key): ?Model` - find model by primary key
- `findOrFail(mixed $key, ?string $column = null): ?Model` - find or fail model by PK or another field
- `create(array $data): ?Model` - create model entity
- `createMany(array $data): Collection` - create many models
- `insert(array $data): bool` - bulk data insert
- `update(mixed $keyOrModel, array $data): Model` - update model entity
- `updateOrCreate(array $attributes, array $data): ?Model` - update or create model if not exists
- `delete(mixed $keyOrModel): bool` - delete model (or forceDelete if model supports Soft Delete)
- `deleteMany(array $keysOrModels): void` - delete models (or forceDelete if model supports Soft Delete)
- `softDelete(mixed $keyOrModel): void` - soft delete model (if model supports Soft Delete)
- `restore(mixed $keyOrModel): void` - restore model (if model supports Soft Delete)

## Usage

### Create a Model

Create your model e.g `Post`

```php
namespace App;

class Post extends Model {

    protected $fillable = [
        'title',
        'author',
        ...
     ];

     ...
}
```

### Create Repository

```php
namespace App;

use App\Repositories\BaseRepository;

class PostRepository extends BaseRepository implements PostRepositoryInterface {

    /**
     * Specify Model class name
     *
     * @return string
     */
    protected function getModelClass(): string
    {
        return Post::class;
    }
}
```

### Create Service

```php
namespace App;

use App\Repositories\BaseRepository;

class PostService extends BaseCrudService implements PostServiceInerface {

    /**
     * Specify Repository class name
     *
     * @return string
     */
    protected function getRepositoryClass(): string
    {
        return PostRepositoryInteface::class;
    }
}
```

### Link Service to its contract in ServiceProvider

```php
class AppServiceProvider extends ServiceProvider {

    /**
     * Specify Repository class name
     *
     * @return string
     */
    public function register(): void
    {
        $this->app->singleton(PostRepositoryInterface::class, PostRepository::class);
        $this->app->singleton(PostServiceInterface::class, PostService::class);
    }
}
```

Now the Service is ready for work.

### Use methods

```php
namespace App\Http\Controllers;

use App\PostServiceInterface;

class PostsController extends Controller {

    /**
     * @var PostServiceInterface
     */
    protected PostServiceInterface $service;

    public function __construct(PostServiceInterface $service) 
    {
        $this->service = $service;
    }
    ....
}
```
CRUD Controller Actions Example 


Index 

```php
public function index(SearchRequest $request): AnonymousResourceCollection
{
    return PostResource::collection($this->service->withTrashed()->getAllPaginated($request->validated(), 25));
}
```

Show

```php
public function show(int $postId): PostResource
{
    return PostResource::make($this->service->findOrFail($postId));
}
```

Store

```php
public function store(StoreRequest $request): PostResource
{
    return PostResource::make($this->service->create($request->validated()));
}
```

Update

```php
public function update(Post $post, UpdateRequest $request): PostResource
{
    return PostResource::make($this->service->update($post, $request->validated()));
}
```

Destroy

```php
public function destroy(Post $post): JsonResponse
{
    $this->service->delete($post);
    // Or  
    $this->service->softDelete($post); 
       
    return Response::json(null, 204);
}
```

Restore

```php
public function restore(Post $deletedPost): PostResource
{
    $this->service->restore($deletedPost);
       
    return PostResource::make($deletedPost->refresh());
}
```

### Soft Deletes

You need to add at least soft delete column (`deleted_at`) to the table to start using soft deletes from the service.

Also, it is possible to use it together with `SoftDeletes` trait

By default soft delete column name is `deleted_at`, you may override it by defining variable inside your repository

`protected $deletedAtColumnName = 'custom_deleted_at';`

By default, soft deleted records excluded from the query result data 
```php
$posts = $this->service->getAll();
// Those are equivalent
$posts = $this->service->withoutTrashed()->getAll();
```

Showing only soft deleted records

```php
$posts = $this->service->onlyTrashed()->getAll();
```

Showing only NOT soft deleted records

```php
$posts = $this->service->withoutTrashed()->getAll();
```

### Loading the Model relationships

```php
$post = $this->service->with(['author'])->withCount(['readers'])->getAll();
```

### Query results filtering

By default filtering will be handled by `applyFilterConditions()`, but you may probably need to do custom filtering, so override `applyFilters` method in your repository if you need custom filtering

```php
class PostRepository extends BaseRepository implements PostRepositoryInterface {
   
   /**
    * Override this method in your repository if you need custom filtering
    * 
    * @param array $searchParams
    * @return Builder
    */
    protected function applyFilters(array $searchParams = []): Builder 
    {
        return $this
            ->getQuery()
            ->when(isset($searchParams['title']), function (Builder $query) use ($searchParams) {
                $query->where('title', 'like', "%{$searchParams['title']}%");
            })
            ->orderBy('id');
    }
}
```

Find many models by multiple fields

```php
$posts = $this->repository->findMany([
    'field' => 'val' // where('field', '=', 'val')
    ['field', 'val'], // where('field', '=', 'val')
    ['field' => 'val'], // where('field', '=', 'val')
    ['field', '=', 'val'], // where('field', '=', 'val')
    ['field', '>', 'val'], // where('field', '>', 'val')
    ['field', 'like', '%val%'], // where('field', 'like', '%val%')
    ['field', 'in', [1,2,3]], // whereIn('field', [1,2,3])
    ['field', 'not_in', [1,2,3]], // whereNotIn('field', [1,2,3])
    ['field', 'null'], // whereNull($field)
    ['field', 'not_null'], // whereNotNull($field)
    ['field', 'date', '2022-01-01'], // whereDate($field, '=', '2022-01-01')
    ['field', 'date <=', '2022-01-01'], // whereDate($field, '<=', '2022-01-01')
    ['field', 'date >=', '2022-01-01'], // whereDate($field, '>=', '2022-01-01')
    ['field', 'day >=', '01'], // whereDay($field, '>=', '01')
    ['field', 'day', '01'], // whereDay($field, '=', '01')
    ['field', 'month', '01'], // whereMonth($field, '=', '01')
    ['field', 'month <', '01'], // whereMonth($field, '<', '01')
    ['field', 'year <', '2022'], // whereYear($field, '<', '2022')
    ['field', 'year', '2022'], // whereYear($field, '=', '2022')
    ['relation', 'has', function($query) {// your query}], // whereHas('relation', function($query) { // your query}})
    ['relation', 'DOESNT_HAVE', function($query) {// your query}], // whereDoesntHave('relation', function($query) { // your query}})
    ['relation', 'HAS_MORPH', function($query) {// your query}], // whereHasMorph('relation', function($query) { // your query}})
    ['relation', 'DOESNT_HAVE_MORPH', function($query) {// your query}], // whereDoesntHaveMorph('relation', function($query) { // your query}})
    ['field', 'between', [1,5]], // whereBetween('field', [1,5])
    ['field', 'NOT_BETWEEN', [1,5]], // whereNotBetween('field', [1,5])
]);
```

### Caching

If you want to apply caching to your models - extend your entity repository with the `BaseCacheableRepository.php`