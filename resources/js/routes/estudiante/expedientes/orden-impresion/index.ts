import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Estudiante\OrdenImpresionController::show
* @see app/Http/Controllers/Estudiante/OrdenImpresionController.php:20
* @route '/estudiante/expedientes/{expediente}/orden-impresion'
*/
export const show = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/estudiante/expedientes/{expediente}/orden-impresion',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Estudiante\OrdenImpresionController::show
* @see app/Http/Controllers/Estudiante/OrdenImpresionController.php:20
* @route '/estudiante/expedientes/{expediente}/orden-impresion'
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
* @see \App\Http\Controllers\Estudiante\OrdenImpresionController::show
* @see app/Http/Controllers/Estudiante/OrdenImpresionController.php:20
* @route '/estudiante/expedientes/{expediente}/orden-impresion'
*/
show.get = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\OrdenImpresionController::show
* @see app/Http/Controllers/Estudiante/OrdenImpresionController.php:20
* @route '/estudiante/expedientes/{expediente}/orden-impresion'
*/
show.head = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Estudiante\OrdenImpresionController::show
* @see app/Http/Controllers/Estudiante/OrdenImpresionController.php:20
* @route '/estudiante/expedientes/{expediente}/orden-impresion'
*/
const showForm = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\OrdenImpresionController::show
* @see app/Http/Controllers/Estudiante/OrdenImpresionController.php:20
* @route '/estudiante/expedientes/{expediente}/orden-impresion'
*/
showForm.get = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\OrdenImpresionController::show
* @see app/Http/Controllers/Estudiante/OrdenImpresionController.php:20
* @route '/estudiante/expedientes/{expediente}/orden-impresion'
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

/**
* @see \App\Http\Controllers\Estudiante\OrdenImpresionController::store
* @see app/Http/Controllers/Estudiante/OrdenImpresionController.php:29
* @route '/estudiante/expedientes/{expediente}/orden-impresion'
*/
export const store = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/estudiante/expedientes/{expediente}/orden-impresion',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Estudiante\OrdenImpresionController::store
* @see app/Http/Controllers/Estudiante/OrdenImpresionController.php:29
* @route '/estudiante/expedientes/{expediente}/orden-impresion'
*/
store.url = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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
* @see \App\Http\Controllers\Estudiante\OrdenImpresionController::store
* @see app/Http/Controllers/Estudiante/OrdenImpresionController.php:29
* @route '/estudiante/expedientes/{expediente}/orden-impresion'
*/
store.post = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\OrdenImpresionController::store
* @see app/Http/Controllers/Estudiante/OrdenImpresionController.php:29
* @route '/estudiante/expedientes/{expediente}/orden-impresion'
*/
const storeForm = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\OrdenImpresionController::store
* @see app/Http/Controllers/Estudiante/OrdenImpresionController.php:29
* @route '/estudiante/expedientes/{expediente}/orden-impresion'
*/
storeForm.post = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Estudiante\OrdenImpresionController::destroy
* @see app/Http/Controllers/Estudiante/OrdenImpresionController.php:66
* @route '/estudiante/expedientes/{expediente}/orden-impresion'
*/
export const destroy = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/estudiante/expedientes/{expediente}/orden-impresion',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Estudiante\OrdenImpresionController::destroy
* @see app/Http/Controllers/Estudiante/OrdenImpresionController.php:66
* @route '/estudiante/expedientes/{expediente}/orden-impresion'
*/
destroy.url = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{expediente}', parsedArgs.expediente.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Estudiante\OrdenImpresionController::destroy
* @see app/Http/Controllers/Estudiante/OrdenImpresionController.php:66
* @route '/estudiante/expedientes/{expediente}/orden-impresion'
*/
destroy.delete = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Estudiante\OrdenImpresionController::destroy
* @see app/Http/Controllers/Estudiante/OrdenImpresionController.php:66
* @route '/estudiante/expedientes/{expediente}/orden-impresion'
*/
const destroyForm = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\OrdenImpresionController::destroy
* @see app/Http/Controllers/Estudiante/OrdenImpresionController.php:66
* @route '/estudiante/expedientes/{expediente}/orden-impresion'
*/
destroyForm.delete = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const ordenImpresion = {
    show: Object.assign(show, show),
    store: Object.assign(store, store),
    destroy: Object.assign(destroy, destroy),
}

export default ordenImpresion