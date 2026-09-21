import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
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

const registro = {
    store: Object.assign(store, store),
}

export default registro