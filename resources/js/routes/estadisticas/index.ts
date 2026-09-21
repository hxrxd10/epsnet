import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
import departamento4cfa8c from './departamento'
/**
* @see \App\Http\Controllers\EstadisticaController::index
* @see app/Http/Controllers/EstadisticaController.php:19
* @route '/estadisticas'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/estadisticas',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EstadisticaController::index
* @see app/Http/Controllers/EstadisticaController.php:19
* @route '/estadisticas'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EstadisticaController::index
* @see app/Http/Controllers/EstadisticaController.php:19
* @route '/estadisticas'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaController::index
* @see app/Http/Controllers/EstadisticaController.php:19
* @route '/estadisticas'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\EstadisticaController::index
* @see app/Http/Controllers/EstadisticaController.php:19
* @route '/estadisticas'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaController::index
* @see app/Http/Controllers/EstadisticaController.php:19
* @route '/estadisticas'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaController::index
* @see app/Http/Controllers/EstadisticaController.php:19
* @route '/estadisticas'
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
* @see \App\Http\Controllers\EstadisticaPdfController::pdf
* @see app/Http/Controllers/EstadisticaPdfController.php:26
* @route '/estadisticas/pdf'
*/
export const pdf = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(options),
    method: 'get',
})

pdf.definition = {
    methods: ["get","head"],
    url: '/estadisticas/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EstadisticaPdfController::pdf
* @see app/Http/Controllers/EstadisticaPdfController.php:26
* @route '/estadisticas/pdf'
*/
pdf.url = (options?: RouteQueryOptions) => {
    return pdf.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EstadisticaPdfController::pdf
* @see app/Http/Controllers/EstadisticaPdfController.php:26
* @route '/estadisticas/pdf'
*/
pdf.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaPdfController::pdf
* @see app/Http/Controllers/EstadisticaPdfController.php:26
* @route '/estadisticas/pdf'
*/
pdf.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: pdf.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\EstadisticaPdfController::pdf
* @see app/Http/Controllers/EstadisticaPdfController.php:26
* @route '/estadisticas/pdf'
*/
const pdfForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: pdf.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaPdfController::pdf
* @see app/Http/Controllers/EstadisticaPdfController.php:26
* @route '/estadisticas/pdf'
*/
pdfForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: pdf.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaPdfController::pdf
* @see app/Http/Controllers/EstadisticaPdfController.php:26
* @route '/estadisticas/pdf'
*/
pdfForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: pdf.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

pdf.form = pdfForm

/**
* @see \App\Http\Controllers\EstadisticaController::departamento
* @see app/Http/Controllers/EstadisticaController.php:33
* @route '/estadisticas/departamentos/{departamento}'
*/
export const departamento = (args: { departamento: string | { codigo: string } } | [departamento: string | { codigo: string } ] | string | { codigo: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: departamento.url(args, options),
    method: 'get',
})

departamento.definition = {
    methods: ["get","head"],
    url: '/estadisticas/departamentos/{departamento}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EstadisticaController::departamento
* @see app/Http/Controllers/EstadisticaController.php:33
* @route '/estadisticas/departamentos/{departamento}'
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
* @see \App\Http\Controllers\EstadisticaController::departamento
* @see app/Http/Controllers/EstadisticaController.php:33
* @route '/estadisticas/departamentos/{departamento}'
*/
departamento.get = (args: { departamento: string | { codigo: string } } | [departamento: string | { codigo: string } ] | string | { codigo: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: departamento.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaController::departamento
* @see app/Http/Controllers/EstadisticaController.php:33
* @route '/estadisticas/departamentos/{departamento}'
*/
departamento.head = (args: { departamento: string | { codigo: string } } | [departamento: string | { codigo: string } ] | string | { codigo: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: departamento.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\EstadisticaController::departamento
* @see app/Http/Controllers/EstadisticaController.php:33
* @route '/estadisticas/departamentos/{departamento}'
*/
const departamentoForm = (args: { departamento: string | { codigo: string } } | [departamento: string | { codigo: string } ] | string | { codigo: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: departamento.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaController::departamento
* @see app/Http/Controllers/EstadisticaController.php:33
* @route '/estadisticas/departamentos/{departamento}'
*/
departamentoForm.get = (args: { departamento: string | { codigo: string } } | [departamento: string | { codigo: string } ] | string | { codigo: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: departamento.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaController::departamento
* @see app/Http/Controllers/EstadisticaController.php:33
* @route '/estadisticas/departamentos/{departamento}'
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

const estadisticas = {
    index: Object.assign(index, index),
    pdf: Object.assign(pdf, pdf),
    departamento: Object.assign(departamento, departamento4cfa8c),
}

export default estadisticas