import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Auth\RegistroController::create
* @see app/Http/Controllers/Auth/RegistroController.php:21
* @route '/registro'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/registro',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\RegistroController::create
* @see app/Http/Controllers/Auth/RegistroController.php:21
* @route '/registro'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\RegistroController::create
* @see app/Http/Controllers/Auth/RegistroController.php:21
* @route '/registro'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\RegistroController::create
* @see app/Http/Controllers/Auth/RegistroController.php:21
* @route '/registro'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\RegistroController::create
* @see app/Http/Controllers/Auth/RegistroController.php:21
* @route '/registro'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\RegistroController::create
* @see app/Http/Controllers/Auth/RegistroController.php:21
* @route '/registro'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\RegistroController::create
* @see app/Http/Controllers/Auth/RegistroController.php:21
* @route '/registro'
*/
createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

/**
* @see \App\Http\Controllers\Auth\RegistroController::store
* @see app/Http/Controllers/Auth/RegistroController.php:26
* @route '/registro'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/registro',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Auth\RegistroController::store
* @see app/Http/Controllers/Auth/RegistroController.php:26
* @route '/registro'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\RegistroController::store
* @see app/Http/Controllers/Auth/RegistroController.php:26
* @route '/registro'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\RegistroController::store
* @see app/Http/Controllers/Auth/RegistroController.php:26
* @route '/registro'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Auth\RegistroController::store
* @see app/Http/Controllers/Auth/RegistroController.php:26
* @route '/registro'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const RegistroController = { create, store }

export default RegistroController