import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\RepositorioController::index
* @see app/Http/Controllers/RepositorioController.php:20
* @route '/repositorio'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/repositorio',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RepositorioController::index
* @see app/Http/Controllers/RepositorioController.php:20
* @route '/repositorio'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RepositorioController::index
* @see app/Http/Controllers/RepositorioController.php:20
* @route '/repositorio'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RepositorioController::index
* @see app/Http/Controllers/RepositorioController.php:20
* @route '/repositorio'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\RepositorioController::index
* @see app/Http/Controllers/RepositorioController.php:20
* @route '/repositorio'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RepositorioController::index
* @see app/Http/Controllers/RepositorioController.php:20
* @route '/repositorio'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RepositorioController::index
* @see app/Http/Controllers/RepositorioController.php:20
* @route '/repositorio'
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
* @see \App\Http\Controllers\RepositorioController::show
* @see app/Http/Controllers/RepositorioController.php:78
* @route '/repositorio/{expediente}'
*/
export const show = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/repositorio/{expediente}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RepositorioController::show
* @see app/Http/Controllers/RepositorioController.php:78
* @route '/repositorio/{expediente}'
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
* @see \App\Http\Controllers\RepositorioController::show
* @see app/Http/Controllers/RepositorioController.php:78
* @route '/repositorio/{expediente}'
*/
show.get = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RepositorioController::show
* @see app/Http/Controllers/RepositorioController.php:78
* @route '/repositorio/{expediente}'
*/
show.head = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\RepositorioController::show
* @see app/Http/Controllers/RepositorioController.php:78
* @route '/repositorio/{expediente}'
*/
const showForm = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RepositorioController::show
* @see app/Http/Controllers/RepositorioController.php:78
* @route '/repositorio/{expediente}'
*/
showForm.get = (args: { expediente: string | number | { id: string | number } } | [expediente: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RepositorioController::show
* @see app/Http/Controllers/RepositorioController.php:78
* @route '/repositorio/{expediente}'
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

const RepositorioController = { index, show }

export default RepositorioController