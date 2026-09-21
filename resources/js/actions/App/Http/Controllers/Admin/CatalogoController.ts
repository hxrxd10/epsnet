import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\CatalogoController::index
* @see app/Http/Controllers/Admin/CatalogoController.php:15
* @route '/admin/catalogos/{catalogo}'
*/
export const index = (args: { catalogo: string | number } | [catalogo: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/catalogos/{catalogo}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CatalogoController::index
* @see app/Http/Controllers/Admin/CatalogoController.php:15
* @route '/admin/catalogos/{catalogo}'
*/
index.url = (args: { catalogo: string | number } | [catalogo: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { catalogo: args }
    }

    if (Array.isArray(args)) {
        args = {
            catalogo: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        catalogo: args.catalogo,
    }

    return index.definition.url
            .replace('{catalogo}', parsedArgs.catalogo.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CatalogoController::index
* @see app/Http/Controllers/Admin/CatalogoController.php:15
* @route '/admin/catalogos/{catalogo}'
*/
index.get = (args: { catalogo: string | number } | [catalogo: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CatalogoController::index
* @see app/Http/Controllers/Admin/CatalogoController.php:15
* @route '/admin/catalogos/{catalogo}'
*/
index.head = (args: { catalogo: string | number } | [catalogo: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CatalogoController::index
* @see app/Http/Controllers/Admin/CatalogoController.php:15
* @route '/admin/catalogos/{catalogo}'
*/
const indexForm = (args: { catalogo: string | number } | [catalogo: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CatalogoController::index
* @see app/Http/Controllers/Admin/CatalogoController.php:15
* @route '/admin/catalogos/{catalogo}'
*/
indexForm.get = (args: { catalogo: string | number } | [catalogo: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CatalogoController::index
* @see app/Http/Controllers/Admin/CatalogoController.php:15
* @route '/admin/catalogos/{catalogo}'
*/
indexForm.head = (args: { catalogo: string | number } | [catalogo: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Admin\CatalogoController::store
* @see app/Http/Controllers/Admin/CatalogoController.php:48
* @route '/admin/catalogos/{catalogo}'
*/
export const store = (args: { catalogo: string | number } | [catalogo: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/catalogos/{catalogo}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\CatalogoController::store
* @see app/Http/Controllers/Admin/CatalogoController.php:48
* @route '/admin/catalogos/{catalogo}'
*/
store.url = (args: { catalogo: string | number } | [catalogo: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { catalogo: args }
    }

    if (Array.isArray(args)) {
        args = {
            catalogo: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        catalogo: args.catalogo,
    }

    return store.definition.url
            .replace('{catalogo}', parsedArgs.catalogo.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CatalogoController::store
* @see app/Http/Controllers/Admin/CatalogoController.php:48
* @route '/admin/catalogos/{catalogo}'
*/
store.post = (args: { catalogo: string | number } | [catalogo: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CatalogoController::store
* @see app/Http/Controllers/Admin/CatalogoController.php:48
* @route '/admin/catalogos/{catalogo}'
*/
const storeForm = (args: { catalogo: string | number } | [catalogo: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CatalogoController::store
* @see app/Http/Controllers/Admin/CatalogoController.php:48
* @route '/admin/catalogos/{catalogo}'
*/
storeForm.post = (args: { catalogo: string | number } | [catalogo: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Admin\CatalogoController::update
* @see app/Http/Controllers/Admin/CatalogoController.php:57
* @route '/admin/catalogos/{catalogo}/{item}'
*/
export const update = (args: { catalogo: string | number, item: string | number } | [catalogo: string | number, item: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/catalogos/{catalogo}/{item}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\CatalogoController::update
* @see app/Http/Controllers/Admin/CatalogoController.php:57
* @route '/admin/catalogos/{catalogo}/{item}'
*/
update.url = (args: { catalogo: string | number, item: string | number } | [catalogo: string | number, item: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            catalogo: args[0],
            item: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        catalogo: args.catalogo,
        item: args.item,
    }

    return update.definition.url
            .replace('{catalogo}', parsedArgs.catalogo.toString())
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CatalogoController::update
* @see app/Http/Controllers/Admin/CatalogoController.php:57
* @route '/admin/catalogos/{catalogo}/{item}'
*/
update.put = (args: { catalogo: string | number, item: string | number } | [catalogo: string | number, item: string | number ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\CatalogoController::update
* @see app/Http/Controllers/Admin/CatalogoController.php:57
* @route '/admin/catalogos/{catalogo}/{item}'
*/
const updateForm = (args: { catalogo: string | number, item: string | number } | [catalogo: string | number, item: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CatalogoController::update
* @see app/Http/Controllers/Admin/CatalogoController.php:57
* @route '/admin/catalogos/{catalogo}/{item}'
*/
updateForm.put = (args: { catalogo: string | number, item: string | number } | [catalogo: string | number, item: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Admin\CatalogoController::destroy
* @see app/Http/Controllers/Admin/CatalogoController.php:69
* @route '/admin/catalogos/{catalogo}/{item}'
*/
export const destroy = (args: { catalogo: string | number, item: string | number } | [catalogo: string | number, item: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/catalogos/{catalogo}/{item}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\CatalogoController::destroy
* @see app/Http/Controllers/Admin/CatalogoController.php:69
* @route '/admin/catalogos/{catalogo}/{item}'
*/
destroy.url = (args: { catalogo: string | number, item: string | number } | [catalogo: string | number, item: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            catalogo: args[0],
            item: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        catalogo: args.catalogo,
        item: args.item,
    }

    return destroy.definition.url
            .replace('{catalogo}', parsedArgs.catalogo.toString())
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CatalogoController::destroy
* @see app/Http/Controllers/Admin/CatalogoController.php:69
* @route '/admin/catalogos/{catalogo}/{item}'
*/
destroy.delete = (args: { catalogo: string | number, item: string | number } | [catalogo: string | number, item: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Admin\CatalogoController::destroy
* @see app/Http/Controllers/Admin/CatalogoController.php:69
* @route '/admin/catalogos/{catalogo}/{item}'
*/
const destroyForm = (args: { catalogo: string | number, item: string | number } | [catalogo: string | number, item: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CatalogoController::destroy
* @see app/Http/Controllers/Admin/CatalogoController.php:69
* @route '/admin/catalogos/{catalogo}/{item}'
*/
destroyForm.delete = (args: { catalogo: string | number, item: string | number } | [catalogo: string | number, item: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const CatalogoController = { index, store, update, destroy }

export default CatalogoController