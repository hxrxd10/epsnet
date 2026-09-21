import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Panel\VerificacionExpedienteController::store
* @see app/Http/Controllers/Panel/VerificacionExpedienteController.php:18
* @route '/estudiantes/{expediente}/verificacion'
*/
export const store = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/estudiantes/{expediente}/verificacion',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Panel\VerificacionExpedienteController::store
* @see app/Http/Controllers/Panel/VerificacionExpedienteController.php:18
* @route '/estudiantes/{expediente}/verificacion'
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
* @see \App\Http\Controllers\Panel\VerificacionExpedienteController::store
* @see app/Http/Controllers/Panel/VerificacionExpedienteController.php:18
* @route '/estudiantes/{expediente}/verificacion'
*/
store.post = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Panel\VerificacionExpedienteController::store
* @see app/Http/Controllers/Panel/VerificacionExpedienteController.php:18
* @route '/estudiantes/{expediente}/verificacion'
*/
const storeForm = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Panel\VerificacionExpedienteController::store
* @see app/Http/Controllers/Panel/VerificacionExpedienteController.php:18
* @route '/estudiantes/{expediente}/verificacion'
*/
storeForm.post = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Panel\VerificacionExpedienteController::destroy
* @see app/Http/Controllers/Panel/VerificacionExpedienteController.php:34
* @route '/estudiantes/{expediente}/verificacion'
*/
export const destroy = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/estudiantes/{expediente}/verificacion',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Panel\VerificacionExpedienteController::destroy
* @see app/Http/Controllers/Panel/VerificacionExpedienteController.php:34
* @route '/estudiantes/{expediente}/verificacion'
*/
destroy.url = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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
* @see \App\Http\Controllers\Panel\VerificacionExpedienteController::destroy
* @see app/Http/Controllers/Panel/VerificacionExpedienteController.php:34
* @route '/estudiantes/{expediente}/verificacion'
*/
destroy.delete = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Panel\VerificacionExpedienteController::destroy
* @see app/Http/Controllers/Panel/VerificacionExpedienteController.php:34
* @route '/estudiantes/{expediente}/verificacion'
*/
const destroyForm = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Panel\VerificacionExpedienteController::destroy
* @see app/Http/Controllers/Panel/VerificacionExpedienteController.php:34
* @route '/estudiantes/{expediente}/verificacion'
*/
destroyForm.delete = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const VerificacionExpedienteController = { store, destroy }

export default VerificacionExpedienteController