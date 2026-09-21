import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Estudiante\AccesoController::create
* @see app/Http/Controllers/Estudiante/AccesoController.php:32
* @route '/estudiante/acceso'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/estudiante/acceso',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Estudiante\AccesoController::create
* @see app/Http/Controllers/Estudiante/AccesoController.php:32
* @route '/estudiante/acceso'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Estudiante\AccesoController::create
* @see app/Http/Controllers/Estudiante/AccesoController.php:32
* @route '/estudiante/acceso'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\AccesoController::create
* @see app/Http/Controllers/Estudiante/AccesoController.php:32
* @route '/estudiante/acceso'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Estudiante\AccesoController::create
* @see app/Http/Controllers/Estudiante/AccesoController.php:32
* @route '/estudiante/acceso'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\AccesoController::create
* @see app/Http/Controllers/Estudiante/AccesoController.php:32
* @route '/estudiante/acceso'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\AccesoController::create
* @see app/Http/Controllers/Estudiante/AccesoController.php:32
* @route '/estudiante/acceso'
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
* @see \App\Http\Controllers\Estudiante\AccesoController::store
* @see app/Http/Controllers/Estudiante/AccesoController.php:50
* @route '/estudiante/acceso'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/estudiante/acceso',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Estudiante\AccesoController::store
* @see app/Http/Controllers/Estudiante/AccesoController.php:50
* @route '/estudiante/acceso'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Estudiante\AccesoController::store
* @see app/Http/Controllers/Estudiante/AccesoController.php:50
* @route '/estudiante/acceso'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\AccesoController::store
* @see app/Http/Controllers/Estudiante/AccesoController.php:50
* @route '/estudiante/acceso'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\AccesoController::store
* @see app/Http/Controllers/Estudiante/AccesoController.php:50
* @route '/estudiante/acceso'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const AccesoController = { create, store }

export default AccesoController