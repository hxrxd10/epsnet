import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\EstadisticaPdfController::index
* @see app/Http/Controllers/EstadisticaPdfController.php:26
* @route '/estadisticas/pdf'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/estadisticas/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EstadisticaPdfController::index
* @see app/Http/Controllers/EstadisticaPdfController.php:26
* @route '/estadisticas/pdf'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EstadisticaPdfController::index
* @see app/Http/Controllers/EstadisticaPdfController.php:26
* @route '/estadisticas/pdf'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaPdfController::index
* @see app/Http/Controllers/EstadisticaPdfController.php:26
* @route '/estadisticas/pdf'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\EstadisticaPdfController::index
* @see app/Http/Controllers/EstadisticaPdfController.php:26
* @route '/estadisticas/pdf'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaPdfController::index
* @see app/Http/Controllers/EstadisticaPdfController.php:26
* @route '/estadisticas/pdf'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaPdfController::index
* @see app/Http/Controllers/EstadisticaPdfController.php:26
* @route '/estadisticas/pdf'
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
* @see \App\Http\Controllers\EstadisticaPdfController::departamento
* @see app/Http/Controllers/EstadisticaPdfController.php:53
* @route '/estadisticas/departamentos/{departamento}/pdf'
*/
export const departamento = (args: { departamento: string | number | { codigo: string | number } } | [departamento: string | number | { codigo: string | number } ] | string | number | { codigo: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: departamento.url(args, options),
    method: 'get',
})

departamento.definition = {
    methods: ["get","head"],
    url: '/estadisticas/departamentos/{departamento}/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EstadisticaPdfController::departamento
* @see app/Http/Controllers/EstadisticaPdfController.php:53
* @route '/estadisticas/departamentos/{departamento}/pdf'
*/
departamento.url = (args: { departamento: string | number | { codigo: string | number } } | [departamento: string | number | { codigo: string | number } ] | string | number | { codigo: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { departamento: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'codigo' in args) {
        args = { departamento: args.codigo }
    }

    if (Array.isArray(args)) {
        args = {
            departamento: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        departamento: typeof args.departamento === 'object'
        ? args.departamento.codigo
        : args.departamento,
    }

    return departamento.definition.url
            .replace('{departamento}', parsedArgs.departamento.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\EstadisticaPdfController::departamento
* @see app/Http/Controllers/EstadisticaPdfController.php:53
* @route '/estadisticas/departamentos/{departamento}/pdf'
*/
departamento.get = (args: { departamento: string | number | { codigo: string | number } } | [departamento: string | number | { codigo: string | number } ] | string | number | { codigo: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: departamento.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaPdfController::departamento
* @see app/Http/Controllers/EstadisticaPdfController.php:53
* @route '/estadisticas/departamentos/{departamento}/pdf'
*/
departamento.head = (args: { departamento: string | number | { codigo: string | number } } | [departamento: string | number | { codigo: string | number } ] | string | number | { codigo: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: departamento.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\EstadisticaPdfController::departamento
* @see app/Http/Controllers/EstadisticaPdfController.php:53
* @route '/estadisticas/departamentos/{departamento}/pdf'
*/
const departamentoForm = (args: { departamento: string | number | { codigo: string | number } } | [departamento: string | number | { codigo: string | number } ] | string | number | { codigo: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: departamento.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaPdfController::departamento
* @see app/Http/Controllers/EstadisticaPdfController.php:53
* @route '/estadisticas/departamentos/{departamento}/pdf'
*/
departamentoForm.get = (args: { departamento: string | number | { codigo: string | number } } | [departamento: string | number | { codigo: string | number } ] | string | number | { codigo: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: departamento.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaPdfController::departamento
* @see app/Http/Controllers/EstadisticaPdfController.php:53
* @route '/estadisticas/departamentos/{departamento}/pdf'
*/
departamentoForm.head = (args: { departamento: string | number | { codigo: string | number } } | [departamento: string | number | { codigo: string | number } ] | string | number | { codigo: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: departamento.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

departamento.form = departamentoForm

const EstadisticaPdfController = { index, departamento }

export default EstadisticaPdfController