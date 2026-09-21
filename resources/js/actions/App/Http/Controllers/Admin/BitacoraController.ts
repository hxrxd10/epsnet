import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\BitacoraController::index
* @see app/Http/Controllers/Admin/BitacoraController.php:20
* @route '/admin/bitacora'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/bitacora',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\BitacoraController::index
* @see app/Http/Controllers/Admin/BitacoraController.php:20
* @route '/admin/bitacora'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\BitacoraController::index
* @see app/Http/Controllers/Admin/BitacoraController.php:20
* @route '/admin/bitacora'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\BitacoraController::index
* @see app/Http/Controllers/Admin/BitacoraController.php:20
* @route '/admin/bitacora'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\BitacoraController::index
* @see app/Http/Controllers/Admin/BitacoraController.php:20
* @route '/admin/bitacora'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\BitacoraController::index
* @see app/Http/Controllers/Admin/BitacoraController.php:20
* @route '/admin/bitacora'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\BitacoraController::index
* @see app/Http/Controllers/Admin/BitacoraController.php:20
* @route '/admin/bitacora'
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

const BitacoraController = { index }

export default BitacoraController