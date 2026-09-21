import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\MunicipioController::index
* @see app/Http/Controllers/Admin/MunicipioController.php:17
* @route '/admin/municipios'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/municipios',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\MunicipioController::index
* @see app/Http/Controllers/Admin/MunicipioController.php:17
* @route '/admin/municipios'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MunicipioController::index
* @see app/Http/Controllers/Admin/MunicipioController.php:17
* @route '/admin/municipios'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\MunicipioController::index
* @see app/Http/Controllers/Admin/MunicipioController.php:17
* @route '/admin/municipios'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\MunicipioController::index
* @see app/Http/Controllers/Admin/MunicipioController.php:17
* @route '/admin/municipios'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\MunicipioController::index
* @see app/Http/Controllers/Admin/MunicipioController.php:17
* @route '/admin/municipios'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\MunicipioController::index
* @see app/Http/Controllers/Admin/MunicipioController.php:17
* @route '/admin/municipios'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Admin\MunicipioController::store
* @see app/Http/Controllers/Admin/MunicipioController.php:63
* @route '/admin/municipios'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/municipios',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\MunicipioController::store
* @see app/Http/Controllers/Admin/MunicipioController.php:63
* @route '/admin/municipios'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MunicipioController::store
* @see app/Http/Controllers/Admin/MunicipioController.php:63
* @route '/admin/municipios'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\MunicipioController::store
* @see app/Http/Controllers/Admin/MunicipioController.php:63
* @route '/admin/municipios'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\MunicipioController::store
* @see app/Http/Controllers/Admin/MunicipioController.php:63
* @route '/admin/municipios'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Admin\MunicipioController::update
* @see app/Http/Controllers/Admin/MunicipioController.php:72
* @route '/admin/municipios/{municipio}'
*/
export const update = (args: { municipio: number | { id: number } } | [municipio: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/municipios/{municipio}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\MunicipioController::update
* @see app/Http/Controllers/Admin/MunicipioController.php:72
* @route '/admin/municipios/{municipio}'
*/
update.url = (args: { municipio: number | { id: number } } | [municipio: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { municipio: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { municipio: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            municipio: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        municipio: typeof args.municipio === 'object'
        ? args.municipio.id
        : args.municipio,
    }

    return update.definition.url
            .replace('{municipio}', parsedArgs.municipio.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MunicipioController::update
* @see app/Http/Controllers/Admin/MunicipioController.php:72
* @route '/admin/municipios/{municipio}'
*/
update.put = (args: { municipio: number | { id: number } } | [municipio: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\MunicipioController::update
* @see app/Http/Controllers/Admin/MunicipioController.php:72
* @route '/admin/municipios/{municipio}'
*/
update.patch = (args: { municipio: number | { id: number } } | [municipio: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\MunicipioController::update
* @see app/Http/Controllers/Admin/MunicipioController.php:72
* @route '/admin/municipios/{municipio}'
*/
const updateForm = (args: { municipio: number | { id: number } } | [municipio: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\MunicipioController::update
* @see app/Http/Controllers/Admin/MunicipioController.php:72
* @route '/admin/municipios/{municipio}'
*/
updateForm.put = (args: { municipio: number | { id: number } } | [municipio: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\MunicipioController::update
* @see app/Http/Controllers/Admin/MunicipioController.php:72
* @route '/admin/municipios/{municipio}'
*/
updateForm.patch = (args: { municipio: number | { id: number } } | [municipio: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Admin\MunicipioController::destroy
* @see app/Http/Controllers/Admin/MunicipioController.php:84
* @route '/admin/municipios/{municipio}'
*/
export const destroy = (args: { municipio: number | { id: number } } | [municipio: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/municipios/{municipio}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\MunicipioController::destroy
* @see app/Http/Controllers/Admin/MunicipioController.php:84
* @route '/admin/municipios/{municipio}'
*/
destroy.url = (args: { municipio: number | { id: number } } | [municipio: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { municipio: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { municipio: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            municipio: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        municipio: typeof args.municipio === 'object'
        ? args.municipio.id
        : args.municipio,
    }

    return destroy.definition.url
            .replace('{municipio}', parsedArgs.municipio.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MunicipioController::destroy
* @see app/Http/Controllers/Admin/MunicipioController.php:84
* @route '/admin/municipios/{municipio}'
*/
destroy.delete = (args: { municipio: number | { id: number } } | [municipio: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Admin\MunicipioController::destroy
* @see app/Http/Controllers/Admin/MunicipioController.php:84
* @route '/admin/municipios/{municipio}'
*/
const destroyForm = (args: { municipio: number | { id: number } } | [municipio: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\MunicipioController::destroy
* @see app/Http/Controllers/Admin/MunicipioController.php:84
* @route '/admin/municipios/{municipio}'
*/
destroyForm.delete = (args: { municipio: number | { id: number } } | [municipio: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const MunicipioController = { index, store, update, destroy }

export default MunicipioController