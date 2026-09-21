import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::index
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:15
* @route '/admin/unidades'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/unidades',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::index
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:15
* @route '/admin/unidades'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::index
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:15
* @route '/admin/unidades'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::index
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:15
* @route '/admin/unidades'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::index
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:15
* @route '/admin/unidades'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::index
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:15
* @route '/admin/unidades'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::index
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:15
* @route '/admin/unidades'
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
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::store
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:45
* @route '/admin/unidades'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/unidades',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::store
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:45
* @route '/admin/unidades'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::store
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:45
* @route '/admin/unidades'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::store
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:45
* @route '/admin/unidades'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::store
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:45
* @route '/admin/unidades'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::update
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:54
* @route '/admin/unidades/{unidad}'
*/
export const update = (args: { unidad: string | number | { id: string | number } } | [unidad: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/unidades/{unidad}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::update
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:54
* @route '/admin/unidades/{unidad}'
*/
update.url = (args: { unidad: string | number | { id: string | number } } | [unidad: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { unidad: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { unidad: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            unidad: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        unidad: typeof args.unidad === 'object'
        ? args.unidad.id
        : args.unidad,
    }

    return update.definition.url
            .replace('{unidad}', parsedArgs.unidad.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::update
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:54
* @route '/admin/unidades/{unidad}'
*/
update.put = (args: { unidad: string | number | { id: string | number } } | [unidad: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::update
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:54
* @route '/admin/unidades/{unidad}'
*/
update.patch = (args: { unidad: string | number | { id: string | number } } | [unidad: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::update
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:54
* @route '/admin/unidades/{unidad}'
*/
const updateForm = (args: { unidad: string | number | { id: string | number } } | [unidad: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::update
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:54
* @route '/admin/unidades/{unidad}'
*/
updateForm.put = (args: { unidad: string | number | { id: string | number } } | [unidad: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::update
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:54
* @route '/admin/unidades/{unidad}'
*/
updateForm.patch = (args: { unidad: string | number | { id: string | number } } | [unidad: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::destroy
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:66
* @route '/admin/unidades/{unidad}'
*/
export const destroy = (args: { unidad: string | number | { id: string | number } } | [unidad: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/unidades/{unidad}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::destroy
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:66
* @route '/admin/unidades/{unidad}'
*/
destroy.url = (args: { unidad: string | number | { id: string | number } } | [unidad: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { unidad: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { unidad: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            unidad: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        unidad: typeof args.unidad === 'object'
        ? args.unidad.id
        : args.unidad,
    }

    return destroy.definition.url
            .replace('{unidad}', parsedArgs.unidad.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::destroy
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:66
* @route '/admin/unidades/{unidad}'
*/
destroy.delete = (args: { unidad: string | number | { id: string | number } } | [unidad: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::destroy
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:66
* @route '/admin/unidades/{unidad}'
*/
const destroyForm = (args: { unidad: string | number | { id: string | number } } | [unidad: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\UnidadAcademicaController::destroy
* @see app/Http/Controllers/Admin/UnidadAcademicaController.php:66
* @route '/admin/unidades/{unidad}'
*/
destroyForm.delete = (args: { unidad: string | number | { id: string | number } } | [unidad: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const unidades = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default unidades