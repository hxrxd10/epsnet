import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::index
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:16
* @route '/admin/instituciones'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/instituciones',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::index
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:16
* @route '/admin/instituciones'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::index
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:16
* @route '/admin/instituciones'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::index
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:16
* @route '/admin/instituciones'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::index
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:16
* @route '/admin/instituciones'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::index
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:16
* @route '/admin/instituciones'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::index
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:16
* @route '/admin/instituciones'
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
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::store
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:59
* @route '/admin/instituciones'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/instituciones',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::store
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:59
* @route '/admin/instituciones'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::store
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:59
* @route '/admin/instituciones'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::store
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:59
* @route '/admin/instituciones'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::store
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:59
* @route '/admin/instituciones'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::update
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:68
* @route '/admin/instituciones/{institucion}'
*/
export const update = (args: { institucion: number | { id: number } } | [institucion: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/instituciones/{institucion}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::update
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:68
* @route '/admin/instituciones/{institucion}'
*/
update.url = (args: { institucion: number | { id: number } } | [institucion: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { institucion: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { institucion: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            institucion: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        institucion: typeof args.institucion === 'object'
        ? args.institucion.id
        : args.institucion,
    }

    return update.definition.url
            .replace('{institucion}', parsedArgs.institucion.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::update
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:68
* @route '/admin/instituciones/{institucion}'
*/
update.put = (args: { institucion: number | { id: number } } | [institucion: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::update
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:68
* @route '/admin/instituciones/{institucion}'
*/
update.patch = (args: { institucion: number | { id: number } } | [institucion: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::update
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:68
* @route '/admin/instituciones/{institucion}'
*/
const updateForm = (args: { institucion: number | { id: number } } | [institucion: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::update
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:68
* @route '/admin/instituciones/{institucion}'
*/
updateForm.put = (args: { institucion: number | { id: number } } | [institucion: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::update
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:68
* @route '/admin/instituciones/{institucion}'
*/
updateForm.patch = (args: { institucion: number | { id: number } } | [institucion: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::destroy
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:80
* @route '/admin/instituciones/{institucion}'
*/
export const destroy = (args: { institucion: number | { id: number } } | [institucion: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/instituciones/{institucion}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::destroy
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:80
* @route '/admin/instituciones/{institucion}'
*/
destroy.url = (args: { institucion: number | { id: number } } | [institucion: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { institucion: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { institucion: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            institucion: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        institucion: typeof args.institucion === 'object'
        ? args.institucion.id
        : args.institucion,
    }

    return destroy.definition.url
            .replace('{institucion}', parsedArgs.institucion.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::destroy
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:80
* @route '/admin/instituciones/{institucion}'
*/
destroy.delete = (args: { institucion: number | { id: number } } | [institucion: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::destroy
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:80
* @route '/admin/instituciones/{institucion}'
*/
const destroyForm = (args: { institucion: number | { id: number } } | [institucion: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\InstitucionAliadaController::destroy
* @see app/Http/Controllers/Admin/InstitucionAliadaController.php:80
* @route '/admin/instituciones/{institucion}'
*/
destroyForm.delete = (args: { institucion: number | { id: number } } | [institucion: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const InstitucionAliadaController = { index, store, update, destroy }

export default InstitucionAliadaController