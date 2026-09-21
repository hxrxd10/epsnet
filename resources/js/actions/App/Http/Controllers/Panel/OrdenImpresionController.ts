import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Panel\OrdenImpresionController::show
* @see app/Http/Controllers/Panel/OrdenImpresionController.php:14
* @route '/estudiantes/{expediente}/orden-impresion'
*/
export const show = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/estudiantes/{expediente}/orden-impresion',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Panel\OrdenImpresionController::show
* @see app/Http/Controllers/Panel/OrdenImpresionController.php:14
* @route '/estudiantes/{expediente}/orden-impresion'
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
* @see \App\Http\Controllers\Panel\OrdenImpresionController::show
* @see app/Http/Controllers/Panel/OrdenImpresionController.php:14
* @route '/estudiantes/{expediente}/orden-impresion'
*/
show.get = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Panel\OrdenImpresionController::show
* @see app/Http/Controllers/Panel/OrdenImpresionController.php:14
* @route '/estudiantes/{expediente}/orden-impresion'
*/
show.head = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Panel\OrdenImpresionController::show
* @see app/Http/Controllers/Panel/OrdenImpresionController.php:14
* @route '/estudiantes/{expediente}/orden-impresion'
*/
const showForm = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Panel\OrdenImpresionController::show
* @see app/Http/Controllers/Panel/OrdenImpresionController.php:14
* @route '/estudiantes/{expediente}/orden-impresion'
*/
showForm.get = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Panel\OrdenImpresionController::show
* @see app/Http/Controllers/Panel/OrdenImpresionController.php:14
* @route '/estudiantes/{expediente}/orden-impresion'
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

const OrdenImpresionController = { show }

export default OrdenImpresionController