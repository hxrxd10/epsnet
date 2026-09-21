import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\ManejoDatosController::index
* @see app/Http/Controllers/Admin/ManejoDatosController.php:20
* @route '/admin/datos'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/datos',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\ManejoDatosController::index
* @see app/Http/Controllers/Admin/ManejoDatosController.php:20
* @route '/admin/datos'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\ManejoDatosController::index
* @see app/Http/Controllers/Admin/ManejoDatosController.php:20
* @route '/admin/datos'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\ManejoDatosController::index
* @see app/Http/Controllers/Admin/ManejoDatosController.php:20
* @route '/admin/datos'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\ManejoDatosController::index
* @see app/Http/Controllers/Admin/ManejoDatosController.php:20
* @route '/admin/datos'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\ManejoDatosController::index
* @see app/Http/Controllers/Admin/ManejoDatosController.php:20
* @route '/admin/datos'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\ManejoDatosController::index
* @see app/Http/Controllers/Admin/ManejoDatosController.php:20
* @route '/admin/datos'
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

const ManejoDatosController = { index }

export default ManejoDatosController