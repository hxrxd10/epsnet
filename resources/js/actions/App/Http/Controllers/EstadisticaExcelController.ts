import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\EstadisticaExcelController::index
* @see app/Http/Controllers/EstadisticaExcelController.php:22
* @route '/estadisticas/excel'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/estadisticas/excel',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EstadisticaExcelController::index
* @see app/Http/Controllers/EstadisticaExcelController.php:22
* @route '/estadisticas/excel'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EstadisticaExcelController::index
* @see app/Http/Controllers/EstadisticaExcelController.php:22
* @route '/estadisticas/excel'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaExcelController::index
* @see app/Http/Controllers/EstadisticaExcelController.php:22
* @route '/estadisticas/excel'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\EstadisticaExcelController::index
* @see app/Http/Controllers/EstadisticaExcelController.php:22
* @route '/estadisticas/excel'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaExcelController::index
* @see app/Http/Controllers/EstadisticaExcelController.php:22
* @route '/estadisticas/excel'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaExcelController::index
* @see app/Http/Controllers/EstadisticaExcelController.php:22
* @route '/estadisticas/excel'
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
* @see \App\Http\Controllers\EstadisticaExcelController::departamento
* @see app/Http/Controllers/EstadisticaExcelController.php:34
* @route '/estadisticas/departamentos/{departamento}/excel'
*/
export const departamento = (args: { departamento: string | { codigo: string } } | [departamento: string | { codigo: string } ] | string | { codigo: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: departamento.url(args, options),
    method: 'get',
})

departamento.definition = {
    methods: ["get","head"],
    url: '/estadisticas/departamentos/{departamento}/excel',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EstadisticaExcelController::departamento
* @see app/Http/Controllers/EstadisticaExcelController.php:34
* @route '/estadisticas/departamentos/{departamento}/excel'
*/
departamento.url = (args: { departamento: string | { codigo: string } } | [departamento: string | { codigo: string } ] | string | { codigo: string }, options?: RouteQueryOptions) => {
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
* @see \App\Http\Controllers\EstadisticaExcelController::departamento
* @see app/Http/Controllers/EstadisticaExcelController.php:34
* @route '/estadisticas/departamentos/{departamento}/excel'
*/
departamento.get = (args: { departamento: string | { codigo: string } } | [departamento: string | { codigo: string } ] | string | { codigo: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: departamento.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaExcelController::departamento
* @see app/Http/Controllers/EstadisticaExcelController.php:34
* @route '/estadisticas/departamentos/{departamento}/excel'
*/
departamento.head = (args: { departamento: string | { codigo: string } } | [departamento: string | { codigo: string } ] | string | { codigo: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: departamento.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\EstadisticaExcelController::departamento
* @see app/Http/Controllers/EstadisticaExcelController.php:34
* @route '/estadisticas/departamentos/{departamento}/excel'
*/
const departamentoForm = (args: { departamento: string | { codigo: string } } | [departamento: string | { codigo: string } ] | string | { codigo: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: departamento.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaExcelController::departamento
* @see app/Http/Controllers/EstadisticaExcelController.php:34
* @route '/estadisticas/departamentos/{departamento}/excel'
*/
departamentoForm.get = (args: { departamento: string | { codigo: string } } | [departamento: string | { codigo: string } ] | string | { codigo: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: departamento.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaExcelController::departamento
* @see app/Http/Controllers/EstadisticaExcelController.php:34
* @route '/estadisticas/departamentos/{departamento}/excel'
*/
departamentoForm.head = (args: { departamento: string | { codigo: string } } | [departamento: string | { codigo: string } ] | string | { codigo: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: departamento.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

departamento.form = departamentoForm

const EstadisticaExcelController = { index, departamento }

export default EstadisticaExcelController