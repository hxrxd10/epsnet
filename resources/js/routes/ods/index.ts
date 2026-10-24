import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\OdsController::index
* @see app/Http/Controllers/OdsController.php:23
* @route '/ods'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/ods',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OdsController::index
* @see app/Http/Controllers/OdsController.php:23
* @route '/ods'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\OdsController::index
* @see app/Http/Controllers/OdsController.php:23
* @route '/ods'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OdsController::index
* @see app/Http/Controllers/OdsController.php:23
* @route '/ods'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\OdsController::index
* @see app/Http/Controllers/OdsController.php:23
* @route '/ods'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OdsController::index
* @see app/Http/Controllers/OdsController.php:23
* @route '/ods'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OdsController::index
* @see app/Http/Controllers/OdsController.php:23
* @route '/ods'
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
* @see \App\Http\Controllers\OdsController::excel
* @see app/Http/Controllers/OdsController.php:37
* @route '/ods/excel'
*/
export const excel = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: excel.url(options),
    method: 'get',
})

excel.definition = {
    methods: ["get","head"],
    url: '/ods/excel',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OdsController::excel
* @see app/Http/Controllers/OdsController.php:37
* @route '/ods/excel'
*/
excel.url = (options?: RouteQueryOptions) => {
    return excel.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\OdsController::excel
* @see app/Http/Controllers/OdsController.php:37
* @route '/ods/excel'
*/
excel.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: excel.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OdsController::excel
* @see app/Http/Controllers/OdsController.php:37
* @route '/ods/excel'
*/
excel.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: excel.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\OdsController::excel
* @see app/Http/Controllers/OdsController.php:37
* @route '/ods/excel'
*/
const excelForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: excel.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OdsController::excel
* @see app/Http/Controllers/OdsController.php:37
* @route '/ods/excel'
*/
excelForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: excel.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OdsController::excel
* @see app/Http/Controllers/OdsController.php:37
* @route '/ods/excel'
*/
excelForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: excel.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

excel.form = excelForm

const ods = {
    index: Object.assign(index, index),
    excel: Object.assign(excel, excel),
}

export default ods