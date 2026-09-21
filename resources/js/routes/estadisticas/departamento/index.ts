import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\EstadisticaPdfController::pdf
* @see app/Http/Controllers/EstadisticaPdfController.php:53
* @route '/estadisticas/departamentos/{departamento}/pdf'
*/
export const pdf = (args: { departamento: string | { codigo: string } } | [departamento: string | { codigo: string } ] | string | { codigo: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(args, options),
    method: 'get',
})

pdf.definition = {
    methods: ["get","head"],
    url: '/estadisticas/departamentos/{departamento}/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EstadisticaPdfController::pdf
* @see app/Http/Controllers/EstadisticaPdfController.php:53
* @route '/estadisticas/departamentos/{departamento}/pdf'
*/
pdf.url = (args: { departamento: string | { codigo: string } } | [departamento: string | { codigo: string } ] | string | { codigo: string }, options?: RouteQueryOptions) => {
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

    return pdf.definition.url
            .replace('{departamento}', parsedArgs.departamento.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\EstadisticaPdfController::pdf
* @see app/Http/Controllers/EstadisticaPdfController.php:53
* @route '/estadisticas/departamentos/{departamento}/pdf'
*/
pdf.get = (args: { departamento: string | { codigo: string } } | [departamento: string | { codigo: string } ] | string | { codigo: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaPdfController::pdf
* @see app/Http/Controllers/EstadisticaPdfController.php:53
* @route '/estadisticas/departamentos/{departamento}/pdf'
*/
pdf.head = (args: { departamento: string | { codigo: string } } | [departamento: string | { codigo: string } ] | string | { codigo: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: pdf.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\EstadisticaPdfController::pdf
* @see app/Http/Controllers/EstadisticaPdfController.php:53
* @route '/estadisticas/departamentos/{departamento}/pdf'
*/
const pdfForm = (args: { departamento: string | { codigo: string } } | [departamento: string | { codigo: string } ] | string | { codigo: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: pdf.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaPdfController::pdf
* @see app/Http/Controllers/EstadisticaPdfController.php:53
* @route '/estadisticas/departamentos/{departamento}/pdf'
*/
pdfForm.get = (args: { departamento: string | { codigo: string } } | [departamento: string | { codigo: string } ] | string | { codigo: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: pdf.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EstadisticaPdfController::pdf
* @see app/Http/Controllers/EstadisticaPdfController.php:53
* @route '/estadisticas/departamentos/{departamento}/pdf'
*/
pdfForm.head = (args: { departamento: string | { codigo: string } } | [departamento: string | { codigo: string } ] | string | { codigo: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: pdf.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

pdf.form = pdfForm

const departamento = {
    pdf: Object.assign(pdf, pdf),
}

export default departamento