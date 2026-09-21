import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Estudiante\CuentaController::create
* @see app/Http/Controllers/Estudiante/CuentaController.php:24
* @route '/estudiante/cuenta'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/estudiante/cuenta',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Estudiante\CuentaController::create
* @see app/Http/Controllers/Estudiante/CuentaController.php:24
* @route '/estudiante/cuenta'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Estudiante\CuentaController::create
* @see app/Http/Controllers/Estudiante/CuentaController.php:24
* @route '/estudiante/cuenta'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\CuentaController::create
* @see app/Http/Controllers/Estudiante/CuentaController.php:24
* @route '/estudiante/cuenta'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Estudiante\CuentaController::create
* @see app/Http/Controllers/Estudiante/CuentaController.php:24
* @route '/estudiante/cuenta'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\CuentaController::create
* @see app/Http/Controllers/Estudiante/CuentaController.php:24
* @route '/estudiante/cuenta'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\CuentaController::create
* @see app/Http/Controllers/Estudiante/CuentaController.php:24
* @route '/estudiante/cuenta'
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
* @see \App\Http\Controllers\Estudiante\CuentaController::store
* @see app/Http/Controllers/Estudiante/CuentaController.php:43
* @route '/estudiante/cuenta'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/estudiante/cuenta',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Estudiante\CuentaController::store
* @see app/Http/Controllers/Estudiante/CuentaController.php:43
* @route '/estudiante/cuenta'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Estudiante\CuentaController::store
* @see app/Http/Controllers/Estudiante/CuentaController.php:43
* @route '/estudiante/cuenta'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\CuentaController::store
* @see app/Http/Controllers/Estudiante/CuentaController.php:43
* @route '/estudiante/cuenta'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\CuentaController::store
* @see app/Http/Controllers/Estudiante/CuentaController.php:43
* @route '/estudiante/cuenta'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const cuenta = {
    create: Object.assign(create, create),
    store: Object.assign(store, store),
}

export default cuenta