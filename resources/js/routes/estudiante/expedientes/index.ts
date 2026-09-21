import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
import bienesServicios from './bienes-servicios'
import publicaciones from './publicaciones'
import transferencias from './transferencias'
import territorio from './territorio'
import actores from './actores'
import seguimiento from './seguimiento'
import alianzas from './alianzas'
import programa from './programa'
import cierre from './cierre'
import ordenImpresion from './orden-impresion'
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
export const show = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
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
show.url = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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
show.get = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::show
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:112
* @route '/estudiante/expedientes/{expediente}'
*/
show.head = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::show
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:112
* @route '/estudiante/expedientes/{expediente}'
*/
const showForm = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::show
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:112
* @route '/estudiante/expedientes/{expediente}'
*/
showForm.get = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Estudiante\ExpedienteController::show
* @see app/Http/Controllers/Estudiante/ExpedienteController.php:112
* @route '/estudiante/expedientes/{expediente}'
*/
showForm.head = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

const expedientes = {
    store: Object.assign(store, store),
    show: Object.assign(show, show),
    bienesServicios: Object.assign(bienesServicios, bienesServicios),
    publicaciones: Object.assign(publicaciones, publicaciones),
    transferencias: Object.assign(transferencias, transferencias),
    territorio: Object.assign(territorio, territorio),
    actores: Object.assign(actores, actores),
    seguimiento: Object.assign(seguimiento, seguimiento),
    alianzas: Object.assign(alianzas, alianzas),
    programa: Object.assign(programa, programa),
    cierre: Object.assign(cierre, cierre),
    ordenImpresion: Object.assign(ordenImpresion, ordenImpresion),
}

export default expedientes