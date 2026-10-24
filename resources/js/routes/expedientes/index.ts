import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\FotoExpedienteController::foto
* @see app/Http/Controllers/FotoExpedienteController.php:13
* @route '/expedientes/{expediente}/foto'
*/
export const foto = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: foto.url(args, options),
    method: 'get',
})

foto.definition = {
    methods: ["get","head"],
    url: '/expedientes/{expediente}/foto',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\FotoExpedienteController::foto
* @see app/Http/Controllers/FotoExpedienteController.php:13
* @route '/expedientes/{expediente}/foto'
*/
foto.url = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return foto.definition.url
            .replace('{expediente}', parsedArgs.expediente.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\FotoExpedienteController::foto
* @see app/Http/Controllers/FotoExpedienteController.php:13
* @route '/expedientes/{expediente}/foto'
*/
foto.get = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: foto.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\FotoExpedienteController::foto
* @see app/Http/Controllers/FotoExpedienteController.php:13
* @route '/expedientes/{expediente}/foto'
*/
foto.head = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: foto.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\FotoExpedienteController::foto
* @see app/Http/Controllers/FotoExpedienteController.php:13
* @route '/expedientes/{expediente}/foto'
*/
const fotoForm = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: foto.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\FotoExpedienteController::foto
* @see app/Http/Controllers/FotoExpedienteController.php:13
* @route '/expedientes/{expediente}/foto'
*/
fotoForm.get = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: foto.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\FotoExpedienteController::foto
* @see app/Http/Controllers/FotoExpedienteController.php:13
* @route '/expedientes/{expediente}/foto'
*/
fotoForm.head = (args: { expediente: number | { id: number } } | [expediente: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: foto.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

foto.form = fotoForm

const expedientes = {
    foto: Object.assign(foto, foto),
}

export default expedientes