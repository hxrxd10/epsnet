import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\CarreraController::index
* @see app/Http/Controllers/Admin/CarreraController.php:27
* @route '/admin/carreras'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/carreras',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarreraController::index
* @see app/Http/Controllers/Admin/CarreraController.php:27
* @route '/admin/carreras'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarreraController::index
* @see app/Http/Controllers/Admin/CarreraController.php:27
* @route '/admin/carreras'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarreraController::index
* @see app/Http/Controllers/Admin/CarreraController.php:27
* @route '/admin/carreras'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarreraController::index
* @see app/Http/Controllers/Admin/CarreraController.php:27
* @route '/admin/carreras'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarreraController::index
* @see app/Http/Controllers/Admin/CarreraController.php:27
* @route '/admin/carreras'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarreraController::index
* @see app/Http/Controllers/Admin/CarreraController.php:27
* @route '/admin/carreras'
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
* @see \App\Http\Controllers\Admin\CarreraController::store
* @see app/Http/Controllers/Admin/CarreraController.php:82
* @route '/admin/carreras'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/carreras',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\CarreraController::store
* @see app/Http/Controllers/Admin/CarreraController.php:82
* @route '/admin/carreras'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarreraController::store
* @see app/Http/Controllers/Admin/CarreraController.php:82
* @route '/admin/carreras'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CarreraController::store
* @see app/Http/Controllers/Admin/CarreraController.php:82
* @route '/admin/carreras'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CarreraController::store
* @see app/Http/Controllers/Admin/CarreraController.php:82
* @route '/admin/carreras'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Admin\CarreraController::destroy
* @see app/Http/Controllers/Admin/CarreraController.php:116
* @route '/admin/carreras/{carrera}'
*/
export const destroy = (args: { carrera: number | { id: number } } | [carrera: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/carreras/{carrera}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\CarreraController::destroy
* @see app/Http/Controllers/Admin/CarreraController.php:116
* @route '/admin/carreras/{carrera}'
*/
destroy.url = (args: { carrera: number | { id: number } } | [carrera: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { carrera: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { carrera: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            carrera: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        carrera: typeof args.carrera === 'object'
        ? args.carrera.id
        : args.carrera,
    }

    return destroy.definition.url
            .replace('{carrera}', parsedArgs.carrera.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarreraController::destroy
* @see app/Http/Controllers/Admin/CarreraController.php:116
* @route '/admin/carreras/{carrera}'
*/
destroy.delete = (args: { carrera: number | { id: number } } | [carrera: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Admin\CarreraController::destroy
* @see app/Http/Controllers/Admin/CarreraController.php:116
* @route '/admin/carreras/{carrera}'
*/
const destroyForm = (args: { carrera: number | { id: number } } | [carrera: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CarreraController::destroy
* @see app/Http/Controllers/Admin/CarreraController.php:116
* @route '/admin/carreras/{carrera}'
*/
destroyForm.delete = (args: { carrera: number | { id: number } } | [carrera: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const CarreraController = { index, store, destroy }

export default CarreraController