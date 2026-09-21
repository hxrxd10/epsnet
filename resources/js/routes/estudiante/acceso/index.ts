import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
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

const acceso = {
    store: Object.assign(store, store),
}

export default acceso