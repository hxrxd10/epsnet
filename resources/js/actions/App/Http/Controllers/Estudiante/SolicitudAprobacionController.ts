import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Estudiante\SolicitudAprobacionController::store
* @see app/Http/Controllers/Estudiante/SolicitudAprobacionController.php:21
* @route '/estudiante/expedientes/{expediente}/solicitud-aprobacion'
*/
export const store = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/estudiante/expedientes/{expediente}/solicitud-aprobacion',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Estudiante\SolicitudAprobacionController::store
* @see app/Http/Controllers/Estudiante/SolicitudAprobacionController.php:21
* @route '/estudiante/expedientes/{expediente}/solicitud-aprobacion'
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
* @see \App\Http\Controllers\Estudiante\SolicitudAprobacionController::store
* @see app/Http/Controllers/Estudiante/SolicitudAprobacionController.php:21
* @route '/estudiante/expedientes/{expediente}/solicitud-aprobacion'
*/
store.post = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\SolicitudAprobacionController::store
* @see app/Http/Controllers/Estudiante/SolicitudAprobacionController.php:21
* @route '/estudiante/expedientes/{expediente}/solicitud-aprobacion'
*/
const storeForm = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Estudiante\SolicitudAprobacionController::store
* @see app/Http/Controllers/Estudiante/SolicitudAprobacionController.php:21
* @route '/estudiante/expedientes/{expediente}/solicitud-aprobacion'
*/
storeForm.post = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

const SolicitudAprobacionController = { store }

export default SolicitudAprobacionController