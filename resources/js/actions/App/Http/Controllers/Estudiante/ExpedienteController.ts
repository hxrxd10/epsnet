import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::index
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:33
* @route '/estudiante/carreras'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/estudiante/carreras',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::index
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:33
* @route '/estudiante/carreras'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::index
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:33
* @route '/estudiante/carreras'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::index
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:33
* @route '/estudiante/carreras'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::index
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:33
* @route '/estudiante/carreras'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::index
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:33
* @route '/estudiante/carreras'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::index
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:33
* @route '/estudiante/carreras'
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
* @see \App\Http\Controllers\Estudiante\ExpedienteController::store
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:74
* @route '/estudiante/expedientes'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/estudiante/expedientes',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::store
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:74
* @route '/estudiante/expedientes'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::store
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:74
* @route '/estudiante/expedientes'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::store
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:74
* @route '/estudiante/expedientes'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::store
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:74
* @route '/estudiante/expedientes'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::show
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:112
* @route '/estudiante/expedientes/{expediente}'
*/
export const show = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/estudiante/expedientes/{expediente}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::show
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:112
* @route '/estudiante/expedientes/{expediente}'
*/
show.url = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { expediente: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { expediente: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            expediente: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        expediente: typeof args.expediente === 'object'
        ? args.expediente.id
        : args.expediente,
    }

    return show.definition.url
            .replace('{expediente}', parsedArgs.expediente.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::show
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:112
* @route '/estudiante/expedientes/{expediente}'
*/
show.get = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::show
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:112
* @route '/estudiante/expedientes/{expediente}'
*/
show.head = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::show
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:112
* @route '/estudiante/expedientes/{expediente}'
*/
const showForm = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::show
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:112
* @route '/estudiante/expedientes/{expediente}'
*/
showForm.get = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::show
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:112
* @route '/estudiante/expedientes/{expediente}'
*/
showForm.head = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

const ExpedienteController = { index, store, show }

export default ExpedienteController