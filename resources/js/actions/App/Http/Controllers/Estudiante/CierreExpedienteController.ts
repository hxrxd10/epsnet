import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Estudiante\CierreExpedienteController::index
* @see app/Http/Controllers/Estudiante/CierreExpedienteController.php:24
* @route '/estudiante/expedientes/{expediente}/cierre'
*/
export const index = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/estudiante/expedientes/{expediente}/cierre',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Estudiante\CierreExpedienteController::index
* @see app/Http/Controllers/Estudiante/CierreExpedienteController.php:24
* @route '/estudiante/expedientes/{expediente}/cierre'
*/
index.url = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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

    return index.definition.url
            .replace('{expediente}', parsedArgs.expediente.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Estudiante\CierreExpedienteController::index
* @see app/Http/Controllers/Estudiante/CierreExpedienteController.php:24
* @route '/estudiante/expedientes/{expediente}/cierre'
*/
index.get = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\CierreExpedienteController::index
* @see app/Http/Controllers/Estudiante/CierreExpedienteController.php:24
* @route '/estudiante/expedientes/{expediente}/cierre'
*/
index.head = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Estudiante\CierreExpedienteController::index
* @see app/Http/Controllers/Estudiante/CierreExpedienteController.php:24
* @route '/estudiante/expedientes/{expediente}/cierre'
*/
const indexForm = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\CierreExpedienteController::index
* @see app/Http/Controllers/Estudiante/CierreExpedienteController.php:24
* @route '/estudiante/expedientes/{expediente}/cierre'
*/
indexForm.get = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\CierreExpedienteController::index
* @see app/Http/Controllers/Estudiante/CierreExpedienteController.php:24
* @route '/estudiante/expedientes/{expediente}/cierre'
*/
indexForm.head = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Estudiante\CierreExpedienteController::store
* @see app/Http/Controllers/Estudiante/CierreExpedienteController.php:53
* @route '/estudiante/expedientes/{expediente}/cierre'
*/
export const store = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/estudiante/expedientes/{expediente}/cierre',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Estudiante\CierreExpedienteController::store
* @see app/Http/Controllers/Estudiante/CierreExpedienteController.php:53
* @route '/estudiante/expedientes/{expediente}/cierre'
*/
store.url = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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

    return store.definition.url
            .replace('{expediente}', parsedArgs.expediente.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Estudiante\CierreExpedienteController::store
* @see app/Http/Controllers/Estudiante/CierreExpedienteController.php:53
* @route '/estudiante/expedientes/{expediente}/cierre'
*/
store.post = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\CierreExpedienteController::store
* @see app/Http/Controllers/Estudiante/CierreExpedienteController.php:53
* @route '/estudiante/expedientes/{expediente}/cierre'
*/
const storeForm = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\CierreExpedienteController::store
* @see app/Http/Controllers/Estudiante/CierreExpedienteController.php:53
* @route '/estudiante/expedientes/{expediente}/cierre'
*/
storeForm.post = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

const CierreExpedienteController = { index, store }

export default CierreExpedienteController